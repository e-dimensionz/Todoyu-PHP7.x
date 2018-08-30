<?php
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
 * Context menu configuration for calendar extension
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */

/**
 * Context menu for calendar area (not clicked on event)
 */
Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['Area']	= array(
	'add'	=> array(
		'key'		=> 'add',
		'label'		=> 'calendar.event.contextmenu.addEvent',
		'jsAction'	=> 'Todoyu.Ext.calendar.addEvent(#ID#)',
		'class'		=> 'eventContextMenu eventAdd',
		'position'	=> 10
	)
);



/**
 * General event context menu
 */
Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['Event']	= array(
	'show'	=> array(
		'key'		=> 'show',
		'label'		=> 'core.global.showDetails',
		'jsAction'	=> 'Todoyu.Ext.calendar.Event.show(#ID#)',
		'class'		=> 'eventContextMenu eventShow',
		'position'	=> 10
	),
	'edit'	=> array(
		'key'		=> 'edit',
		'label'		=> 'calendar.event.contextmenu.editEvent',
		'jsAction'	=> 'Todoyu.Ext.calendar.Event.edit(#ID#)',
		'class'		=> 'eventContextMenu eventEdit',
		'position'	=> 20
	),
	'remove'	=> array(
		'key'		=> 'delete',
		'label'		=> 'calendar.event.contextmenu.deleteEvent',
		'jsAction'	=> 'Todoyu.Ext.calendar.Event.remove(#ID#)',
		'class'		=> 'eventContextMenu eventRemove',
		'position'	=> 30
	),
	'add'	=> array(
		'key'		=> 'add',
		'label'		=> 'calendar.event.contextmenu.addEvent',
		'jsAction'	=> 'Todoyu.Ext.calendar.Event.addEventOnSameTime(#ID#)',
		'class'		=> 'eventContextMenu eventAdd',
		'position'	=> 40
	)
);


/**
 * Context menu for events in portal area
 */
Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['EventPortal']	= array(
	'show'	=> array(
		'key'		=> 'show',
		'label'		=> 'calendar.event.contextmenu.showEventInCalendar',
		'jsAction'	=> 'Todoyu.Ext.calendar.Event.goToEventInCalendar(#ID#, #DATE#)',
		'class'		=> 'eventContextMenu eventShowInCalendar',
		'position'	=> 10,
		'submenu'	=> array(
			'day'	=> array(
				'key'		=> 'day',
				'label'		=> 'calendar.event.contextmenu.showEventInCalendar.day',
				'jsAction'	=> 'Todoyu.Ext.calendar.Event.goToEventInCalendar(#ID#, #DATE#, \'day\')',
				'class'		=> 'eventContextMenu showInCalendarDay',
				'position'	=> 10
			),
			'week'	=> array(
				'key'		=> 'week',
				'label'		=> 'calendar.event.contextmenu.showEventInCalendar.week',
				'jsAction'	=> 'Todoyu.Ext.calendar.Event.goToEventInCalendar(#ID#, #DATE#, \'week\')',
				'class'		=> 'eventContextMenu showInCalendarWeek',
				'position'	=> 20
			),
			'month'	=> array(
				'key'		=> 'month',
				'label'		=> 'calendar.event.contextmenu.showEventInCalendar.month',
				'jsAction'	=> 'Todoyu.Ext.calendar.Event.goToEventInCalendar(#ID#, #DATE#, \'month\')',
				'class'		=> 'eventContextMenu showInCalendarMonth',
				'position'	=> 30
			)
		)
	)
);




	// Reminder via email
Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['reminderemail']	= array(
	'key'		=> 'reminderemail',
	'label'		=> 'calendar.event.contextmenu.eventReminderEmail',
	'jsAction'	=> 'void(0)',
	'class'		=> 'eventContextMenu eventReminderEmail',
	'position'	=> 50,
	'submenu'	=> array(
		'0'	=> array(
			'key'		=> 'remindertime-none',
			'label'		=> 'calendar.event.contextmenu.reminder.none',
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.deactivate(#ID#)',
			'class'		=> 'eventContextMenu reminderTimeNone'
		),
			// At the time of the event
		'1'	=> array(
			'key'		=> 'remindertime-1',
			'label'		=> 'calendar.event.contextmenu.reminder.atEventStart',
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 1)',
			'class'		=> 'eventContextMenu reminderTime0'
		),
			// 5 minutes before
		'300'	=> array(
			'key'		=> 'remindertime-300',
			'label'		=> TodoyuTime::formatDuration(300) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 300)',
			'class'		=> 'eventContextMenu reminderTime5m'
		),
			// 15 minutes before
		'900'	=> array(
			'key'		=> 'remindertime-900',
			'label'		=> TodoyuTime::formatDuration(900) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 900)',
			'class'		=> 'eventContextMenu reminderTime15m'
		),
			// 30 minutes before
		'1800'	=> array(
			'key'		=> 'remindertime-1800',
			'label'		=> TodoyuTime::formatDuration(1800) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 1800)',
			'class'		=> 'eventContextMenu reminderTime30m'
		),
			// 1 hour before
		'3600'	=> array(
			'key'		=> 'reminderemail-3600',
			'label'		=> TodoyuTime::formatDuration(3600) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 3600)',
			'class'		=> 'eventContextMenu reminderTime1h'
		),
			// 2 hours before
		'7200'	=> array(
			'key'		=> 'remindertime-7200',
			'label'		=> TodoyuTime::formatDuration(7200) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 7200)',
			'class'		=> 'eventContextMenu reminderTime2h'
		),
			// 12 hours before
		'43200'	=> array(
			'key'		=> 'remindertime-43200',
			'label'		=> TodoyuTime::formatDuration(43200) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 43200)',
			'class'		=> 'eventContextMenu reminderTime12h'
		),
			// 1 day before
		'86400'	=> array(
			'key'		=> 'remindertime-86400',
			'label'		=> TodoyuTime::formatDuration(86400) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 86400)',
			'class'		=> 'eventContextMenu reminderTime1d'
		),
			// 2 days before
		'172800'	=> array(
			'key'		=> 'remindertime-172800',
			'label'		=> TodoyuTime::formatDuration(172800) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 172800)',
			'class'		=> 'eventContextMenu reminderTime2d'
		),
			// 1 week before
		'604800'	=> array(
			'key'		=> 'remindertime-604800',
			'label'		=> TodoyuTime::formatDuration(604800) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Email.updateReminderTime(#ID#, 604800)',
			'class'		=> 'eventContextMenu reminderTime1w'
		)
	)
);

	// Reminders via popup
Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['reminderpopup']	= array(
	'key'		=> 'reminderpopup',
	'label'		=> 'calendar.event.contextmenu.eventReminderPopup',
	'jsAction'	=> 'void(0)',
	'class'		=> 'eventContextMenu eventReminderPopup',
	'position'	=> 60,
	'submenu'	=> array(
		'0'	=> array(
			'key'		=> 'remindertime-none',
			'label'		=> 'calendar.event.contextmenu.reminder.none',
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.deactivate(#ID#)',
			'class'		=> 'eventContextMenu reminderTimeNone'
		),
			// At the time of the event
		'1'	=> array(
			'key'		=> 'remindertime-1',
			'label'		=> 'calendar.event.contextmenu.reminder.atEventStart',
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 1)',
			'class'		=> 'eventContextMenu reminderTime0'
		),
			// 5 minutes before
		'300'	=> array(
			'key'		=> 'remindertime-300',
			'label'		=> TodoyuTime::formatDuration(300) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 300)',
			'class'		=> 'eventContextMenu reminderTime5m'
		),
			// 15 minutes before
		'900'	=> array(
			'key'		=> 'remindertime-900',
			'label'		=> TodoyuTime::formatDuration(900) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 900)',
			'class'		=> 'eventContextMenu reminderTime15m'
		),
			// 30 minutes before
		'1800'	=> array(
			'key'		=> 'remindertime-1800',
			'label'		=> TodoyuTime::formatDuration(1800) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 1800)',
			'class'		=> 'eventContextMenu reminderTime30m'
		),
			// 1 hour before
		'3600'	=> array(
			'key'		=> 'reminderemail-3600',
			'label'		=> TodoyuTime::formatDuration(3600) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 3600)',
			'class'		=> 'eventContextMenu reminderTime1h'
		),
			// 2 hours before
		'7200'	=> array(
			'key'		=> 'remindertime-7200',
			'label'		=> TodoyuTime::formatDuration(7200) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 7200)',
			'class'		=> 'eventContextMenu reminderTime2h'
		),
			// 12 hours before
		'43200'	=> array(
			'key'		=> 'remindertime-43200',
			'label'		=> TodoyuTime::formatDuration(43200) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 43200)',
			'class'		=> 'eventContextMenu reminderTime12h'
		),
			// 1 day before
		'86400'	=> array(
			'key'		=> 'remindertime-86400',
			'label'		=> TodoyuTime::formatDuration(86400) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 86400)',
			'class'		=> 'eventContextMenu reminderTime1d'
		),
			// 2 days before
		'172800'	=> array(
			'key'		=> 'remindertime-172800',
			'label'		=> TodoyuTime::formatDuration(172800) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 172800)',
			'class'		=> 'eventContextMenu reminderTime2d'
		),
			// 1 week before
		'604800'	=> array(
			'key'		=> 'remindertime-604800',
			'label'		=> TodoyuTime::formatDuration(604800) . ' ' . Todoyu::Label('calendar.event.contextmenu.reminder.before'),
			'jsAction'	=> 'Todoyu.Ext.calendar.Reminder.Popup.updateReminderTime(#ID#, 604800)',
			'class'		=> 'eventContextMenu reminderTime1w'
		)
	)
);

?>