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
 * Event reminder (popup and email) rights functions
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventReminderRights {

	/**
	 * Deny access
	 * Shortcut for calendar
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('calendar', $right);
	}



	/**
	 * Check whether current person is allowed to use popup reminders of given event
	 *
	 * @param	Integer		$idEvent
	 * @return	Boolean
	 */
	public static function isPopupSchedulingAllowed($idEvent) {
		return TodoyuCalendarReminderPopupManager::isReminderAllowed($idEvent);
	}



	/**
	 * Check whether current person is allowed to use emailed reminders of given event
	 *
	 * @param	Integer		$idEvent
	 * @return	Boolean
	 */
	public static function isEmailSchedulingAllowed($idEvent) {
		return TodoyuCalendarReminderEmailManager::isReminderAllowed($idEvent);
	}


}
?>