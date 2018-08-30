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

Todoyu.Ext.contact.Profile = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.contact,



	/**
	 * Handles Tab events for the profile
	 *
	 * @method	onTabClick
	 * @param	{Event}		event
	 * @param	{String}	tabKey
	 */
	onTabClick: function(event, tabKey) {
		// do nothing
	},



	/**
	 * Sends the save Request for the profile form
	 *
	 * @method	save
	 * @param	{Element}	form
	 */
	save: function(form) {
		this.ext.Person.saveForm(form, this.onSaved.bind(this));
	},



	/**
	 * Handler evoked upon onComplete of person saving: check for and notify success / error, update display
	 *
	 * @method	onSaved
	 * @param	{Number}			idPerson
	 * @param	{Form}				form
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(idPerson, form, response) {
		var notificationIdentifier	= 'contact.profile.person.saved';

		Todoyu.notifySuccess('[LLL:contact.ext.person.saved]', notificationIdentifier);
	}

};