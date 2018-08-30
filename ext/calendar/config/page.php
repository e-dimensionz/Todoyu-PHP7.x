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

if( Todoyu::person()->isInternal() || TodoyuAuth::isAdmin() ) {
		// Add main menu planning area entry
	if( Todoyu::allowed('calendar', 'general:area') ) {
		TodoyuFrontend::addMenuEntry('planning', 'calendar.ext.maintab.label', '?ext=calendar', 30);

			// Add sub menu entries
		$subTabsConfig	= Todoyu::$CONFIG['EXT']['calendar']['tabs'];
		$prefix			= Todoyu::Label('calendar.ext.subMenuEntries.prefix') . ' > ';

		TodoyuFrontend::addSubMenuEntriesFromTabsConf('calendar', 'planning', $subTabsConfig, $prefix);
	}

	if( TodoyuExtensions::isInstalled('portal') && Todoyu::allowed('calendar', 'general:use') ) {
		TodoyuPortalManager::addTab('appointment', 'TodoyuCalendarPortalRenderer::getAppointmentTabLabel', 'TodoyuCalendarPortalRenderer::getAppointmentTabContent', 50);
	}
}

	// Add JavaScript init to page body
if( Todoyu::allowed('calendar', 'reminders:popup') ) {
	TodoyuCalendarReminderPopupManager::addReminderJsInitToPage();
}

?>