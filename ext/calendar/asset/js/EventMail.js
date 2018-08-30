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
 * @module	Calendar
 */

/**
 * Functions for event mailing
 *
 * @namespace	Todoyu.Ext.calendar.Event.Mail
 */
Todoyu.Ext.calendar.Event.Mail	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,

	/**
	 * Current popup
	 */
	popup: null,

	/**
	 * Options for current popup
	 */
	options: {},



	/**
	 * Handler when changing "send as email" option checkbox inside event form
	 *
	 * @method	onToggleSendAsEmail
	 * @param	{Element}	checkbox
	 */
	onToggleSendAsEmail: function(checkbox) {
		var parts		= checkbox.id.split('-');
		var emailEl;

		if( parts.length == 3 ) {
				// Is a new event (no ID yet)
			emailEl	= $('formElement-event-field-emailreceivers');
		}

		if( checkbox.checked && emailEl ) {
			emailEl.show();
			$('event-field-emailreceivers-search').focus();
		} else {
			emailEl.hide();
		}
	},



	/**
	 * Initialize mailing popup
	 *
	 * @method	afterSaved
	 * @param	{Number}	idEvent
	 * @param	{String}	operation
	 * @param	{Object}	[extraOptions]
	 */
	showPopup: function(idEvent, operation, extraOptions) {
		extraOptions= extraOptions || {};

		this.options = $H(extraOptions).merge({
			event:		idEvent,
			operation:	operation
		}).toObject();

		var url		= Todoyu.getUrl('calendar', 'mail');
		var options	= {
			parameters: {
				action:		'popup',
				event:		idEvent,
				operation:	operation,
				options:	Object.toJSON(extraOptions)
			},
			onComplete: this.onPopupShow.bind(this, idEvent, operation)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler after initialization of event mail popup: show popup if conditions met
	 * (Conditions: not disabled via preference, at least one other participant (with email) then person itself assigned)
	 *
	 * @method	onEventMailPopupInitialized
	 * @param	{Number}			idEvent
	 * @param	{String}			operation
	 * @param	{Ajax.Response}		response
	 */
	onPopupShow: function(idEvent, operation, response) {
		if( response.hasTodoyuHeader('show') ) {
			this.popup = Todoyu.Popups.openContent('Mailing', response.responseText, 'Mailing', 460);
		}
	},



	/**
	 * Close popup
	 *
	 * @method	closePopup
	 */
	closePopup: function() {
		if( this.popup ) {
			this.popup.close();
		}
		this.options = {};
	},



	/**
	 * Button: Don't send mail
	 *
	 * @method	popupNoMail
	 */
	popupNoMail: function() {
		this.closePopup();
	},



	/**
	 * Button: Send mail
	 *
	 * @method	popupMail
	 */
	popupMail: function() {
		var selectedUsers	= this.getSelectedUsers();

		if( selectedUsers.size() > 0 ) {
			this.sendMail(selectedUsers);
		}

		this.closePopup();
	},



	/**
	 * Button: Disable popup
	 *
	 * @method	popupDisable
	 */
	popupDisable: function() {
		this.disablePopup();
		this.closePopup();
	},



	/**
	 * Get selected user IDs
	 *
	 * @method	getSelectedUsers
	 * @return	{Array}
	 */
	getSelectedUsers: function() {
		return $F('event-' + this.options.event + '-field-emailreceivers');
	},



	/**
	 * Store user pref: not to ask whether to send event mail after modification per drag and drop
	 *
	 * @method	deactivatePopup
	 */
	disablePopup: function() {
		var url		= Todoyu.getUrl('calendar', 'mail');
		var options	= {
			parameters: {
				action:	'disablePopup'
			},
			onComplete: this.onPopupDisabled.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler after mailing popup after drag and drop has been deactivated
	 *
	 * @method	onPopupDeactivated
	 */
	onPopupDisabled: function() {
		Todoyu.Notification.notifySuccess('[LLL:calendar.event.mail.notification.popup.deactivated]');
	},



	/**
	 * Send event mail
	 * Used (if active in profile) after changing event per drag&drop
	 *
	 * @method	sendMail
	 * @param	{Array}		personIDs			Persons to send mail to
	 */
	sendMail: function(personIDs) {
		var url		= Todoyu.getUrl('calendar', 'mail');
		var options	= {
			parameters: {
				action:		'send',
				event:		this.options.event,
				persons:	personIDs.join(','),
				operation:	this.options.operation,
				options:	Object.toJSON(this.options)
			},
			onComplete: this.onMailSent.bind(this, this.options.event, this.options.operation)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler after event mail has been sent
	 *
	 * @method	onMailSent
	 * @param	{Number}			idEvent
	 * @param	{String}			operation
	 * @param	{Ajax.Response}		response
	 */
	onMailSent: function(idEvent, operation, response) {
		if( response.getTodoyuHeader('sentEmail') ) {
				// Notify of sent mail
			Todoyu.Notification.notifySuccess('[LLL:calendar.event.mail.notification.sent]');
		}
	}

};