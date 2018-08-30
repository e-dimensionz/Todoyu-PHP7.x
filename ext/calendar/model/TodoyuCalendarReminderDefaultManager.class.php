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
 * Manage reminder defaults in profile
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarReminderDefaultManager {

	/**
	 * Get default reminder time for email
	 *
	 * @return	Integer
	 */
	public static function getEmailDefaultAdvanceTime() {
		return self::getDefaultAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_EMAIL);
	}



	/**
	 * Get default reminder time for popup
	 *
	 * @return	Integer
	 */
	public static function getPopupDefaultAdvanceTime() {
		return self::getDefaultAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_POPUP);
	}



	/**
	 * Get default advance (time before event) reminding time of given reminder type
	 *
	 * @param	Integer		$type			Reminder type (constant)
	 * @return	Integer
	 */
	public static function getDefaultAdvanceTime($type) {
		$type		= intval($type);
		$typePrefix	= TodoyuCalendarReminderManager::getReminderTypePrefix($type);

		$extConf	= TodoyuSysmanagerExtConfManager::getExtConf('calendar');
		$configName	= 'reminder' . $typePrefix . '_advancetime';

		return intval($extConf[$configName]);
	}

}

?>