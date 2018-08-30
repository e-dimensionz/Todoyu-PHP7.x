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
 * Calendar Portal Manager
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarPortalManager {

	/**
	 * Get events for the portal tab
	 *
	 * @return	Array
	 */
	public static function getAppointments() {
		$timespan	= self::getAppointmentsTimespan();

		return TodoyuCalendarEventStaticManager::getEventsInTimespan($timespan['start'], $timespan['end'], array(Todoyu::personid()));
	}



	/**
	 * Get timestamps (start + end) of timespan the appointments in the portal tab are timed in
	 *
	 * @return	Array
	 */
	public static function getAppointmentsTimespan() {
		$weeksEvents= intval(Todoyu::$CONFIG['EXT']['calendar']['appointmentTabConfig']['weeksEvents']);

		return array(
			'start'	=> TodoyuTime::getDayStart(NOW),
			'end'	=> NOW + ($weeksEvents * TodoyuTime::SECONDS_WEEK)
		);
	}


}

?>