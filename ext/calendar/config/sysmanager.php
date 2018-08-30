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
 * Administration for calendar extension
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */

// Add holiday sets to records area of sysadmin
TodoyuSysmanagerExtManager::addRecordConfig('calendar', 'holidayset', array(
	'label'			=> 'calendar.ext.records.holidayset',
	'description'	=> 'calendar.ext.records.holidayset.desc',
	'list'			=> 'TodoyuCalendarHolidaySetManager::getRecords',
	'form'			=> 'ext/calendar/config/form/admin/holidayset.xml',
	'object'		=> 'TodoyuCalendarHolidaySet',
	'delete'		=> 'TodoyuCalendarHolidaySetManager::deleteHolidaySet',
	'save'			=> 'TodoyuCalendarHolidaySetManager::saveHolidaySet',
	'table'			=> 'ext_calendar_holidayset'
));

// Add holidays to records area of sysadmin
TodoyuSysmanagerExtManager::addRecordConfig('calendar', 'holiday', array(
	'label'			=> 'calendar.ext.holiday',
	'description'	=> 'calendar.ext.records.holiday.desc',
	'list'			=> 'TodoyuCalendarHolidayManager::getRecords',
	'form'			=> 'ext/calendar/config/form/admin/holiday.xml',
	'object'		=> 'TodoyuCalendarHoliday',
	'delete'		=> 'TodoyuCalendarHolidayManager::deleteHoliday',
	'save'			=> 'TodoyuCalendarHolidayManager::saveHoliday',
	'table'			=> 'ext_calendar_holiday'
));

?>