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
 * Assets (JS, CSS, SWF, etc.) requirements for calendar extension
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */

Todoyu::$CONFIG['EXT']['calendar']['assets']	= array(
	'js' => array(
		array(
			'file'		=> 'ext/calendar/asset/js/Ext.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/calendar/asset/js/Profile.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/calendar/asset/js/QuickCreateEvent.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/calendar/asset/js/HolidayEditor.js',
			'position'	=> 102
		),
		array(
			'file'		=> 'ext/calendar/asset/js/Tabs.js',
			'position'	=> 103
		),
		array(
			'file'		=> 'ext/calendar/asset/js/Navi.js',
			'position'	=> 104
		),
		array(
			'file'		=> 'ext/calendar/asset/js/QuickInfo.js',
			'position'	=> 107
		),
		array(
			'file'		=> 'ext/calendar/asset/js/QuickInfoBirthday.js',
			'position'	=> 107
		),
		array(
			'file'		=> 'ext/calendar/asset/js/QuickInfoStatic.js',
			'position'	=> 108
		),
		array(
			'file'		=> 'ext/calendar/asset/js/QuickInfoHoliday.js',
			'position'	=> 109
		),
		array(
			'file'		=> 'ext/calendar/asset/js/Event.js',
			'position'	=> 117
		),
		array(
			'file'		=> 'ext/calendar/asset/js/EventView.js',
			'position'	=> 118
		),
		array(
			'file'		=> 'ext/calendar/asset/js/EventEdit.js',
			'position'	=> 119
		),
		array(
			'file'		=> 'ext/calendar/asset/js/EventMail.js',
			'position'	=> 119
		),
		array(
			'file'		=> 'ext/calendar/asset/js/EventPortal.js',
			'position'	=> 119
		),
		array(
			'file'		=> 'ext/calendar/asset/js/CalendarBody.js',
			'position'	=> 120
		),
		array(
			'file'		=> 'ext/calendar/asset/js/CalendarBodyHourMarker.js',
			'position'	=> 121
		),
		array(
			'file'		=> 'ext/calendar/asset/js/DragDrop.js',
			'position'	=> 121
		),
		array(
			'file'		=> 'ext/calendar/asset/js/ContextMenuCalendarBody.js',
			'position'	=> 121
		),
		array(
			'file'		=> 'ext/calendar/asset/js/ContextMenuEvent.js',
			'position'	=> 122
		),
		array(
			'file'		=> 'ext/calendar/asset/js/ContextMenuEventPortal.js',
			'position'	=> 123
		),
		array(
			'file'		=> 'ext/calendar/asset/js/PanelWidgetCalendar.js',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/calendar/asset/js/PanelWidgetEventTypeSelector.js',
			'position'	=> 140
		),
		array(
			'file'		=> 'ext/calendar/asset/js/PanelWidgetHolidaySetSelector.js',
			'position'	=> 150
		),
		array(
			'file'		=> 'ext/calendar/asset/js/Reminder.js',
			'position'	=> 190
		),
		array(
			'file'		=> 'ext/calendar/asset/js/ReminderPopup.js',
			'position'	=> 200
		),
		array(
			'file'		=> 'ext/calendar/asset/js/ReminderEmail.js',
			'position'	=> 210
		),
		array(
			'file'		=> 'lib/js/scriptaculous/sound.js',
			'position'	=> 220
		),

		array(
			'file'		=> 'ext/calendar/asset/js/Day.js',
			'position'	=> 220
		),
		array(
			'file'		=> 'ext/calendar/asset/js/Week.js',
			'position'	=> 220
		),
		array(
			'file'		=> 'ext/calendar/asset/js/Month.js',
			'position'	=> 220
		),
		array(
			'file'		=> 'ext/calendar/asset/js/EventSeries.js',
			'position'	=> 220
		),
		array(
			'file'		=> 'ext/calendar/asset/js/DialogChoiceSeriesEdit.js',
			'position'	=> 251
		),
		array(
			'file'		=> 'ext/calendar/asset/js/DialogChoiceSeriesDelete.js',
			'position'	=> 251
		),
		array(
			'file'		=> 'ext/calendar/asset/js/DialogChoiceSeriesSave.js',
			'position'	=> 251
		)
	),
	'css' => array(
		array(
			'file'		=> 'ext/calendar/asset/css/contextmenu.scss',
			'position'	=> 80
		),
		array(
			'file'		=> 'ext/calendar/asset/css/quickinfo.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/calendar/asset/css/ext.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/calendar/asset/css/profile.scss',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/calendar/asset/css/calendarbody.scss',
			'poisition'	=> 101
		),
		array(
			'file'		=> 'ext/calendar/asset/css/day.scss',
			'poisition'	=> 102
		),
		array(
			'file'		=> 'ext/calendar/asset/css/week.scss',
			'poisition'	=> 103
		),
		array(
			'file'		=> 'ext/calendar/asset/css/month.scss',
			'poisition'	=> 104
		),
		array(
			'file'		=> 'ext/calendar/asset/css/event.scss',
			'position'	=> 106
		),
		array(
			'file'		=> 'ext/calendar/asset/css/panelwidget-calendar.scss',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/calendar/asset/css/panelwidget-eventtpyeselector.scss',
			'position'	=> 140
		),
		array(
			'file'		=> 'ext/calendar/asset/css/panelwidget-holidaysetselector.scss',
			'position'	=> 150
		),
		array(
			'file'		=> 'ext/calendar/asset/css/series.scss',
			'position'	=> 150
		),
		array(
			'file'		=> 'ext/calendar/asset/css/print.scss',
			'media'		=> 'print',
			'position'	=> 160
		)
	)

);

?>