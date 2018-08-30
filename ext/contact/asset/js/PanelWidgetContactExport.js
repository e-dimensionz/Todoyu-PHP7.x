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

Todoyu.Ext.contact.PanelWidget.ContactExport = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.contact,



	/**
	 * Export contact results
	 *
	 * @method	exportResults
	 */
	exportResults: function() {
		var options = {
			action:		'export',
			tab:		Todoyu.Tabs.getActive('contact').id.replace('contact-tab-',''),
			searchword:	this.ext.getSearchText()
		};

		Todoyu.goTo('contact', 'panelwidgetcontactexport', options , '', false);
	}

};