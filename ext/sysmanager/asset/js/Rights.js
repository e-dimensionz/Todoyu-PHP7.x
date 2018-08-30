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
 * System manager rights
 *
 * @class		Rights
 * @namespace	Todoyu.Ext.sysmanager
 */
Todoyu.Ext.sysmanager.Rights = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.sysmanager,



	/**
	 * Callback for rights tab click: evoke resp. content update
	 *
	 * @method	onTabClick
	 * @param	{Event}		event
	 * @param	{String}		tab
	 */
	onTabClick: function(event, tab) {
		var url		= Todoyu.getUrl('sysmanager', 'rights');
		var options	= {
			parameters: {
				action:	'update',
				'tab':		tab
			},
			onComplete: this.onTabLoaded.bind(this, tab)
		};

		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Callback when rights tab loaded
	 *
	 * @method	onTabLoaded
	 * @param	{String}		tab
	 * @param	{Ajax.Response}		response
	 */
	onTabLoaded: function(tab, response) {

	}

};