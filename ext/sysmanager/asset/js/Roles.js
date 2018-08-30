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

/**
 * Role manager
 *
 * @class		Roles
 * @namespace	Todoyu.Ext.sysmanager
 */
Todoyu.Ext.sysmanager.Roles = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.sysmanager,



	/**
	 * Edit given role
	 *
	 * @method	edit
	 * @param	{Number}		idRole
	 */
	edit: function(idRole) {
		var url		= Todoyu.getUrl('sysmanager', 'role');
		var options	= {
			parameters: {
				action:	'edit',
				role:	idRole
			},
			onComplete:	this.onEdit.bind(this, idRole)
		};

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Handler evoked onEdit
	 *
	 * @method	onEdit
	 * @param	{Number}		idRole
	 * @param	{Ajax.Response}		response
	 */
	onEdit: function(idRole, response) {

	},



	/**
	 * Delete given role from DB
	 *
	 * @method	remove
	 * @param	{Number}		idRole
	 */
	remove: function(idRole) {
		if( confirm('[LLL:sysmanager.ext.roles.delete.confirm]') ) {
			var url		= Todoyu.getUrl('sysmanager', 'role');
			var options	= {
				parameters: {
					action:	'delete',
					role:	idRole
				},
				onComplete: this.onRemoved.bind(this, idRole)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler to be evoked after removal of role
	 *
	 * @method	onRemoved
	 * @param	{Number}		idRole
	 * @param	{Ajax.Response}		response
	 */
	onRemoved: function(idRole, response) {
		Todoyu.notifySuccess('[LLL:sysmanager.ext.roles.delete.notify.success]');
		this.updateList();
	},



	/**
	 * Update list of roles
	 *
	 * @method	updateList
	 */
	updateList: function() {
		var url		= Todoyu.getUrl('sysmanager', 'role');
		var options	= {
			parameters: {
				action:	'listing'
			}
		};

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Save role from given form
	 *
	 * @method	save
	 * @param	{Array}	form
	 */
	save: function(form) {
		$(form).request({
			parameters: {
				action:	'save',
				area:	Todoyu.getArea()
			},
			onComplete: this.onSaved.bind(this)
		});
	},



	/**
	 * Handler being evoked after saving of role to database
	 *
	 * @method	onSaved
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(response) {
		var notificationIdentifier	= 'sysmanager.roles.saved';

		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('[LLL:sysmanager.ext.roles.saved.error]', notificationIdentifier);
			Todoyu.Ui.setContentBody(response.responseText);
		} else {
			Todoyu.notifySuccess('[LLL:sysmanager.ext.roles.saved.ok]', notificationIdentifier);
			this.showList();
		}
	},



	/**
	 * Show roles list
	 *
	 * @method	showList
	 */
	showList: function() {
		var url		= Todoyu.getUrl('sysmanager', 'role');
		var options	= {
			parameters: {
				action:	'listing'
			},
			onComplete:	this.onListShowed.bind(this)
		};

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Callback after roles listing is shown
	 *
	 * @method	onListShowed
	 * @param	{Ajax.Response}		response
	 */
	onListShowed: function(response) {

	}

};