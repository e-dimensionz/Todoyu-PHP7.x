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
 * @module	Daytracks
 */

/**
 * Context menu for task entries of daytracks panel widget
*/
Todoyu.Ext.daytracks.PanelWidget.Daytracks.ContextMenu = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.daytracks,

	/**
	 * Backreference to widget
	 *
	 * @property	widget
	 * @type		Object
	 */
	widget:	Todoyu.Ext.daytracks.PanelWidget.Daytracks,



	/**
	 * Register panel widget context menu
	 *
	 * @method	init
	 */
	init: function() {
		this.attach();
	},



	/**
	 * Attach context menu
	 *
	 * @method	attach
	 */
	attach: function() {
		Todoyu.ContextMenu.attach('DaytracksPanelwidget', '.contextmenudaytrackspwidget', this.getID.bind(this));
	},



	/**
	 * Detach context menu
	 *
	 * @method	detach
	 */
	detach: function() {
		Todoyu.ContextMenu.detach('.contextmenudaytrackspwidget');
	},



	/**
	 * Get task ID for context menu request
	 *
	 * @method	getID
	 * @param	{Element}	element
	 * @param	{Event}		event
	 */
	getID: function(element, event) {
		return event.findElement('li').id.split('-').last();
	}

};