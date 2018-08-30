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
* Staff list panel widget
*/
Todoyu.Ext.contact.PanelWidget.StaffList = Class.create(Todoyu.PanelWidgetSearchList, {

	/**
	 * Initialize
	 *
	 * @method	initialize
	 * @param	{Function}	$super		Parent constructor: Todoyu.PanelWidgetSearchList.initialize
	 * @param	{String}	search
	 */
	initialize: function($super, search) {
		$super({
			id:			'stafflist',
			search:		search,
			ext:		'contact',
			controller:	'panelwidgetstafflist',
			action:		'list'
		});
	},



	/**
	 * Handler when clicked on item
	 *
	 * @method	onItemClick
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onItemClick: function(event, item) {
		var idPerson	= item.id.split('-').last();

		Todoyu.Hook.exec('panelwidget.stafflist.onPersonClick', idPerson);
	}

});