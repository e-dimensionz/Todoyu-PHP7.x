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
 * @module	Sysmanager
 */

Todoyu.Ext.sysmanager.Records = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.sysmanager,

	/**
	 * @property	url
	 * @type		String
	 */
	url:	Todoyu.getUrl('sysmanager', 'records'),

	/**
	 * @property	extKey
	 * @type		String
	 */
	extKey:	'',

	/**
	 * @property	type
	 * @type		String
	 */
	type:	'',



	/**
	 * Handler when tab clicked
	 *
	 * @method	onTabClick
	 * @param	{Event}		event
	 * @param	{String}	tab
	 */
	onTabClick: function(event, tab) {
		if( tab === 'record' ) {
			return false;
		} else if( tab === 'all' ) {
			this.update();
		} else {
			var parts	= event.findElement('li').id.split('-').slice(2);

			this.update.apply(this, parts);
		}
	},



	/**
	 * Update given record and call given callback afterwards
	 *
	 * @method	update
	 * @param	{String}	extKey
	 * @param	{String}	type
	 * @param	{Number}	idRecord
	 * @param	{Function}	callback
	 */
	update: function(extKey, type, idRecord, callback) {
		var url		= Todoyu.getUrl('sysmanager', 'records');
		var options	= {
			parameters: {
				action: 'update',
				extkey:	extKey,
				type:	type,
				record:	idRecord
			},
			onComplete: this.onUpdated.bind(this, extKey, type, idRecord, callback)
		};

		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Handler after record update has been saved
	 *
	 * @method	onUpdated
	 * @param	{String}		extKey
	 * @param	{String}		type
	 * @param	{Number}		idRecord
	 * @param	{Function}		callback
	 * @param	{Ajax.Response}	response
	 */
	onUpdated: function(extKey, type, idRecord, callback, response) {
		if( typeof callback === 'function' ) {
			callback(extKey, type, idRecord, response);
		}
	},



	/**
	 * Show types of extension
	 *
	 * @method	showExtensionTypes
	 * @param	{String}	extKey
	 */
	showExtensionTypes: function(extKey) {
		this.update(extKey);
	},



	/**
	 * Show all records of a type
	 *
	 * @method	showTypeRecords
	 * @param	{String}	extKey
	 * @param	{String}	type
	 */
	showTypeRecords: function(extKey, type) {
		this.update(extKey, type);
	},



	/**
	 * Show type list
	 *
	 * @method	showTypeList
	 * @param	{String}	ext
	 */
	showTypeList: function(ext) {
		var options = {
			parameters: {
				action:	'listRecordTypes',
				extkey:	ext
			}
		};

		Todoyu.Ui.replace('list', this.url, options);
	},



	/**
	 * Add record (create and edit)
	 *
	 * @method	add
	 * @param	{String}	ext
	 * @param	{String}	type
	 */
	add: function(ext, type) {
		this.edit(ext, type, -1);
	},



	/**
	 * Open given record's editing
	 *
	 * @method	edit
	 * @param	{String}	ext
	 * @param	{String}	type
	 * @param	{Number}	idRecord
	 */
	edit: function(ext, type, idRecord) {
		this.update(ext, type, idRecord, this.onEdit.bind(this));
	},



	/**
	 * On edit handler
	 *
	 * @method	onEdit
	 * @param	{String}	extKey
	 * @param	{String}	type
	 * @param	{Number}	idRecord
	 * @param	{Array}		response
	 */
	onEdit: function(extKey, type, idRecord, response) {

	},



	/**
	 * Remove record
	 *
	 * @method	remove
	 * @param	{String}	ext
	 * @param	{String}	type
	 * @param	{Number}	idRecord
	 */
	remove: function(ext, type, idRecord) {
		if( confirm('[LLL:sysmanager.ext.records.delete.confirm]') ) {
			var options = {
				parameters: {
					action:	'delete',
					extkey:	ext,
					type:	type,
					record:	idRecord
				},
				onComplete: this.onRemoved.bind(this, ext, type, idRecord)
			};

			Todoyu.send(this.url, options);
		}
	},



	/**
	 * On removed (record) handler
	 *
	 * @method	onRemoved
	 * @param	{String}		extKey
	 * @param	{String}		type
	 * @param	{Number}		idRecord
	 * @param	{Ajax.Response}	response
	 */
	onRemoved: function(extKey, type, idRecord, response) {
		this.showTypeRecords(extKey, type);
	},



	/**
	 * Save record
	 *
	 * @method	save
	 * @param	{String}	idForm
	 * @param	{String}	ext
	 * @param	{String}	type
	 */
	save: function(idForm, ext, type) {
		Todoyu.Form.disableSaveButtons(idForm);
		Todoyu.Ui.saveRTE();

		$(idForm).request ({
			parameters: {
				action:	'save',
				extkey:	ext,
				type:	type
			},
			onComplete: this.onSaved.bind(this, idForm, ext, type)
		});

		return false;
	},



	/**
	 * On saved handler
	 *
	 * @method	onSaved
	 * @param	{String}			form
	 * @param	{String}			ext
	 * @param	{String}			type
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(form, ext, type, response) {
		var notificationIdentifier	= 'sysmanager.record.saved';

		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('[LLL:sysmanager.ext.records.saved.fail]', notificationIdentifier);
			$(form.id).update(response.responseText);
			Todoyu.Form.enableSaveButtons(form);
		} else {
			Todoyu.Form.enableSaveButtons(form);
			Todoyu.notifySuccess('[LLL:sysmanager.ext.records.saved]', notificationIdentifier);
			this.showTypeRecords(ext, type);
		}
	},



	/**
	 * Close form
	 *
	 * @method	closeForm
	 * @param	{String}	extKey
	 * @param	{String}	type
	 */
	closeForm: function(extKey, type) {
		this.showTypeRecords(extKey, type);
	}

};