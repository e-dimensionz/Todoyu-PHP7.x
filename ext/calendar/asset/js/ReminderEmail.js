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
 * Calendar event email reminder functions
 *
 * @namespace	Todoyu.Ext.calendar.Reminder.Email
 */
Todoyu.Ext.calendar.Reminder.Email	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,



	/**
	 * Deactivate email reminding of given event for current person
	 *
	 * @method	deactivate
	 * @param	{Number}	idEvent
	 */
	deactivate: function(idEvent) {
		var url		= Todoyu.getUrl('calendar', 'reminder');
		var options	= {
			parameters: {
				action:			'deactivate',
				remindertype:	'email',
				event:			idEvent
			},
			onComplete: this.onDeactivated.bind(this, idEvent)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler called after deactivation of event: notify success
	 *
	 * @method	onDeactivated
	 * @param	{Number}			idEvent
	 * @param	{Ajax.Response}		response
	 */
	onDeactivated: function(idEvent, response) {
		Todoyu.notifySuccess('[LLL:calendar.reminder.notify.email.deactivated]');
	},



	/**
	 * Update email reminder scheduling of given event and current person
	 *
	 * @method	updateReminderTime
	 * @param	{Number}	idEvent
	 * @param	{Number}	secondsBefore
	 */
	updateReminderTime: function(idEvent, secondsBefore) {
		var url		= Todoyu.getUrl('calendar', 'reminder');
		var options	= {
			parameters: {
				action:			'updateremindertime',
				remindertype:	'email',
				event:			idEvent,
				secondsbefore:	secondsBefore
			},
			onComplete: this.onReminderTimeUpdated.bind(this, idEvent)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler called after deactivation of event: notify success
	 *
	 * @method	onDeactivated
	 * @param	{Number}			idEvent
	 * @param	{Ajax.Response}		response
	 */
	onReminderTimeUpdated: function(idEvent, response) {
		Todoyu.notifySuccess('[LLL:calendar.reminder.notify.email.timeupdated]');
	}

};