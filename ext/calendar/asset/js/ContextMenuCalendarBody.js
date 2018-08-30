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
 * Context menu for tasks
 * 
 * @namespace	Todoyu.Ext.calendar.ContextMenuCalendarBody
 */
Todoyu.Ext.calendar.ContextMenuCalendarBody	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,



	/**
	 * Attach task context menu
	 *
	 * @method	attach
	 */
	attach: function() {
		this.detach();
		Todoyu.ContextMenu.attach('calendarbody', '.contextmenucalendarbody', this.getTime.bind(this));
	},



	/**
	 * Detach task context menu
	 *
	 * @method	detach
	 */
	detach: function() {
		Todoyu.ContextMenu.detach('.contextmenucalendarbody');
	},



	/**
	 * Get (clicked) time
	 *
	 * @method	getTime
	 * @param	{Element}	element
	 * @param	{Event}		event
	 * @return	{Number}
	 */
	getTime: function(element, event) {
		return this.getClickedTime(event);
	},



	/**
	 * Get timestamp at clicked coordinates / element
	 *
	 * @method	getClickedTime
	 * @param	{Event}		event
	 * @return	{Number}
	 */
	getClickedTime: function(event) {
		var tab	= this.ext.getActiveTab();
		var time;

		if( tab === 'month' ) {
			return Todoyu.Time.date2Time(event.findElement('td').id.replace('createEventAt-', ''));
		} else {
			return this.ext.CalendarBody.getTimeForPosition(event.pointerX(), event.pointerY());
		}
	}

};