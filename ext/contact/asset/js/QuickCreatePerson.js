/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * @module	Contact
 */

Todoyu.Ext.contact.QuickCreatePerson = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.contact,

	/**
	 * @property	person
	 * @type		Object
	 */
	person: Todoyu.Ext.contact.Person,



	/**
	 * Evoked upon opening of person quick create wizard popup
	 *
	 * @method	onPopupOpened
	 */
	onPopupOpened: function() {
		this.person.onEdit(0);
	},



	/**
	 * Save person
	 *
	 * @method	save
	 * @param	{String}		form
	 */
	save: function(form) {
		$(form).request ({
				parameters: {
					action:	'save'
				},
				onComplete: this.onSaved.bind(this)
			});

		return false;
	},



	/**
	 * Handler being evoked upon onComplete of person saving. Checks for and notify error / success, updates display
	 *
	 * @method	onSaved
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(response) {
		var idPerson	= response.getTodoyuHeader('idRecord');
		var notificationIdentifier	= 'contact.quickcreateperson.saved';

		if( response.hasTodoyuError() ) {
				// Saving person failed
			Todoyu.notifyError('[LLL:contact.ext.person.saved.error]', notificationIdentifier);
			Todoyu.Headlets.getHeadlet('todoyuheadletquickcreate').updatePopupContent(response.responseText);

			this.person.onEdit(idPerson);
		} else {
				// Saving succeeded
			Todoyu.Hook.exec('contact.person.saved', idPerson);

			Todoyu.Popups.close('quickcreate');
			Todoyu.notifySuccess('[LLL:contact.ext.person.saved]', notificationIdentifier);

			if( Todoyu.isInArea('contact') ) {
				Todoyu.Ext.contact.Person.showList();
			}
		}
	}

};