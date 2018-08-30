/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * @module	Core
 */

/**
 * Notification
 *
 * @class		Notification
 * @namespace	Todoyu
 */

Todoyu.Notification = {

	/**
	 * @property	SUCCESS
	 * @type		String
	 */
	SUCCESS:	'success',

	/**
	 * @property	ERROR
	 * @type		String
	 */
	ERROR:		'error',

	/**
	 * @property	INFO
	 * @type		String
	 */
	INFO:		'info',

	/**
	 * Close delay
	 * @property	closeDelay
	 * @type		Number
	 */
	closeDelay: 3,

	/**
	 * Close delay for sticky messages
	 *
	 * @property	closeDelaySticky
	 * @type		Number
	 */
	closeDelaySticky: 15,

	/**
	 * Template object
	 * @property	template
	 * @type		Template
	 */
	template: null,

	/**
	 * Current ID for note, incremented
	 * @property	id
	 * @type		Number
	 */
	id: 1,



	/**
	 * Add new notification
	 *
	 * @method	notify
	 * @param	{String}		type
	 * @param	{String}		message
	 * @param	{Boolean}		[sticky]		Don't hide note
	 * @param	{Number}		[delay]			Overwrite default delay time
	 * @param	{String}		[identifier]	Optional identifier to remove preceding notifications of the same event
	 */
	notify: function(type, message, sticky, delay, identifier) {
		delay			= delay || this.closeDelay;
		var identClass	= this.getIdentifierClass(identifier);

		this.loadTemplate();

		var id	= this.id++;
		var data= {
			id:			id,
			type:		type,
			message:	message,
			identifier:	identClass
		};

		var note				= this.template.evaluate(data);
		var delayBeforeClose	= sticky ? this.closeDelaySticky : delay;
		var delayBeforeAppend	= 0;

			// Close preceding note(s) if any
		if( identClass ) {
			if( this.closeTypeNotes(identClass) ) {
				delayBeforeAppend = 0.5;
			}
		}

			// Append new note delayed to visually happen after old one(s) are closed / immediately if no preceding notes
		this.appendNote.bind(this, id, note).delay(delayBeforeAppend);

			// Setup timeout to auto-close the note
		if( ! sticky ) {
			this.closeNote.bind(this, id).delay(delayBeforeClose);
		}
	},



	/**
	 * Convert identifier to ident class
	 * Replace points with dashes
	 *
	 * @method	getIdentifierClass
	 * @param	{String}	[identifier]
	 * @return	{String}
	 */
	getIdentifierClass: function(identifier) {
		identifier	= identifier || 'r' + Math.round(Math.random() * 1000);

		return identifier.replace(/\./g, '-');
	},


	
	/**
	 * Init notification HTML template
	 *
	 * @method	loadTemplate
	 */
	loadTemplate: function() {
		if( this.template === null ) {
			this.template = new Template(
				'<div class="note #{type} note-#{identifier}" id="notification-note-#{id}">'
				+	'<table width="100%"><tr>'
				+		'<td class="icon">&nbsp;</td>'
				+		'<td class="message">#{message}</td>'
				+	'</tr></table></div>'
			);
		}
	},



	/**
	 * Remove notification from DOM
	 *
	 * @method	remove
	 * @param	{Number}	id
	 */
	remove: function(id) {
		$('notification-note-' + id).remove();
	},



	/**
	 * Shortcut to show info notification
	 *
	 * @method	notifyInfo
	 * @param	{String}		message
	 * @param	{Boolean}		[sticky]
	 * @param	{Number}		[delay]
	 * @param	{String}		[identifier]
	 */
	notifyInfo: function(message, sticky, delay, identifier) {
		this.notify(this.INFO, message, sticky, delay, identifier);
	},



	/**
	 * Shortcut to show error notification
	 *
	 * @method	notifyError
	 * @param	{String}		message
	 * @param	{String}		[identifier]
	 */
	notifyError: function(message, identifier) {
		this.notify(this.ERROR, message, true, this.closeDelay, identifier);
	},



	/**
	 * Shortcut to show success notification
	 *
	 * @method	notifySuccess
	 * @param	{String}		message
	 * @param	{Boolean}		[sticky]
	 * @param	{Number}		[delay]
	 * @param	{String}		[identifier]
	 */
	notifySuccess: function(message, sticky, delay, identifier) {
		this.notify(this.SUCCESS, message, sticky, delay, identifier);
	},



	/**
	 * Close when clicking in the close button
	 *
	 * @method	close
	 * @param	{Element}		closeButton
	 */
	close: function(closeButton) {
		var idNote = $(closeButton).up('div.note').id.split('-').last();

		this.closeNote(idNote);
	},



	/**
	 * Close note by ID
	 * @todo	watch out for a bugfix of scriptaculous' malfunctioning 'afterFinish' callback
	 *
	 * @method	closeNote
	 * @param	{Number}	idNote
	 */
	closeNote: function(idNote) {
		var duration	= 0.3;
		var noteHtmlId	= 'notification-note-' + idNote;

		if( Todoyu.exists(noteHtmlId) ) {
			new Effect.Move('notification-note-' + idNote, {
				y:		-80,
				mode:	'absolute'
			});

			this.onNoteClosed.bind(this).delay(duration + 0.1, idNote);
		}
	},



	/**
	 * Fade-out all notifications
	 *
	 * @method	fadeAllNotes
	 */
	fadeAllNotes: function() {
		$$('.note').each(function(note){
			Effect.Fade(note.id, {'duration': 0.3});
		}.bind(this));
	},



	/**
	 * Close first (topmost) of the currently displayed notifications
	 *
	 * @method	closeFirstNote
	 */
	closeFirstNote: function() {
		var notes = $('notes').select('div.note');

		if( notes.size() > 0 ) {
			var idNote = notes.first().id.split('-').last();
			this.closeNote(idNote);
		}
	},



	/**
	 * Handler being evoked when a note is closed (fade-out finished)
	 *
	 * @method	onNoteClosed
	 * @param	{Number}		id
	 */
	onNoteClosed: function(id) {
		var noteElement	= $('notification-note-' + id);
		if( noteElement ) {
			noteElement.remove();
		}
	},



	/**
	 * Close any (identifiable) notifications related to the given event (called prior to showing new ones of that same event)
	 *
	 * @method	closeTypeNotes
	 * @param	{String}	identifier	Identifier of note related event
	 * @return	{Boolean}				Any old notes found and closed?
	 */
	closeTypeNotes: function(identifier) {
		identifier	= this.getIdentifierClass(identifier);
		var notes	= $('notes').select('.note-' + identifier);

		if( notes.size() > 0 ) {
			notes.each(function(note){
				var idNote	= note.id.split('-').last();
				this.closeNote(idNote);
			}, this);

			return true;
		}

		return false;
	},



	/**
	 * Append new note
	 *
	 * @method	appendNote
	 * @param	{Number}		idNote				Unique numeric (counter) for note element ID
	 * @param	{String}		code				HTML code of note
	 */
	appendNote: function(idNote, code) {
		$('notes').insert({
			'top':	code
		});

		var htmlID	= 'notification-note-' + idNote;

			// Observe mouse over of note
		$(htmlID).on('mouseover', this.onMouseOver.bind(this, idNote));
			// Hide the note before appearing
		$(htmlID).hide();
			// Appear with effect
		$(htmlID).appear({
			'duration': 0.5
		});
	},



	/**
	 * Handler for note mouse over
	 *
	 * @method	onMouseOver
	 * @param	{Number}	idNote
	 * @param	{Event}		event
	 */
	onMouseOver: function(idNote, event) {
		this.closeNote(idNote);
	},



	/**
	 * Check whether the todoyu specific 'note' HTTP header was received
	 *
	 * @method	checkNoteHeader
	 * @param	{Ajax.Response}		response
	 */
	checkNoteHeader: function(response) {
		if( response.hasTodoyuHeader('note') ) {
			var info	= response.getTodoyuHeader('note').evalJSON();

			this.notify(info.type, info.message);
		}
	}

};