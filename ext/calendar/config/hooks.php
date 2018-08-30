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


/* ----------------------------
	Form Hooks
   ---------------------------- */
TodoyuFormHook::registerBuildForm('ext/calendar/config/form/event.xml', 'TodoyuCalendarReminderManager::hookAddReminderFieldsToEvent');
TodoyuFormHook::registerBuildForm('ext/calendar/config/form/event.xml', 'TodoyuCalendarEventSeriesManager::hookSetSeriesFields');
TodoyuFormHook::registerLoadData('ext/calendar/config/form/event.xml', 'TodoyuCalendarEventSeriesManager::hookLoadSeriesData');
TodoyuFormHook::registerSaveData('ext/calendar/config/form/event.xml', 'TodoyuCalendarEventStaticManager::hookSaveEvent');

TodoyuFormHook::registerBuildForm('ext/calendar/config/form/update-mailinfo.xml', 'TodoyuCalendarEventMailManager::hookToggleAutoMailField');

	// Add holiday set selector to company address form
TodoyuFormHook::registerBuildForm('ext/contact/config/form/address.xml', 'TodoyuCalendarManager::hookAddHolidaysetToCompanyAddress');



/* ----------------------------
	Normal Hooks
   ---------------------------- */
TodoyuHookManager::registerHook('calendar', 'event.move.data', 'TodoyuCalendarEventSeriesManager::hookEventMovedDataUpdate');
TodoyuHookManager::registerHook('calendar', 'event.move', 'TodoyuCalendarEventMailManager::hookEventMoved');
TodoyuHookManager::registerHook('calendar', 'event.save', 'TodoyuCalendarEventMailManager::hookEventSaved');
TodoyuHookManager::registerHook('calendar', 'event.delete', 'TodoyuCalendarEventMailManager::hookEventDeleted');

	// Event filters
TodoyuHookManager::registerHook('calendar', 'event.filter', 'TodoyuCalendarManager::hookEventFilterPersons');
TodoyuHookManager::registerHook('calendar', 'event.filter', 'TodoyuCalendarManager::hookEventFilterEventTypes');
TodoyuHookManager::registerHook('calendar', 'event.filter', 'TodoyuCalendarManager::hookEventFilterHolidaySets');

?>