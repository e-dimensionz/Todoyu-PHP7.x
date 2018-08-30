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

/**
 * PanelWidget contact search
 */
Todoyu.Ext.contact.PanelWidget.ContactSearch = Class.create(Todoyu.PanelWidget.SearchBox, {

	/**
	 * Initialize
	 *
	 * @param	{Function}	$super
	 */
	initialize: function($super) {
		$super({
			id: 'contactsearch'
		});
	},



	/**
	 * Search in contacts
	 *
	 */
	search: function() {
		var type	= this.getType().capitalize();

		Todoyu.Ext.contact[type].showList(this.getSearchText());
	},



	/**
	 * Get current selected contact type (tab)
	 *
	 * @method	getType
	 * @return	{String}		e.g. 'person' / 'company'
	 */
	getType: function() {
		return Todoyu.Tabs.getActive('contact').id.split('-').last();
	}

});