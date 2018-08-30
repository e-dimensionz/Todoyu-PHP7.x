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
 * Calendar event reminder functions
 *
 * @namespace	Todoyu.Ext.calendar.Reminder
 */
Todoyu.Ext.calendar.Reminder	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,

	/**
	 * Update display of reminders of given event if displayed
	 *
	 * @method	refresh
	 * @param	{Number}	idEvent
	 */
	refresh: function(idEvent) {
		var elementID	= 'event-' + idEvent + '-reminders';

		if( Todoyu.exists(elementID) ) {
			var url		= Todoyu.getUrl('calendar', 'reminder');
			var options	= {
				parameters: {
					action:	'details',
					event:	idEvent
				},
				onComplete: this.onRefreshed.bind(this, idEvent)
			};

			Todoyu.Ui.update(elementID, url, options);
		}
	},



	/**
	 * Handler after reminders of event have been refreshed
	 *
	 * @method	onRefreshed
	 * @param	{Number}		idEvent
	 * @param	{Ajax.Response}	response
	 */
	onRefreshed: function(idEvent, response) {

	}

};