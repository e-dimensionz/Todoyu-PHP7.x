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
 * Event Reminder
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarReminderPopup extends TodoyuCalendarReminder {

	/**
	 * Get amount of time before event when to send reminder email
	 *
	 * @return	Boolean|Integer
	 */
	public function getAdvanceTime() {
		return parent::getAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_POPUP);
	}



	/**
	 * Get scheduled popup reminder time
	 *
	 * @return	Integer
	 */
	public function getDateRemind() {
		return parent::getDateRemind(CALENDAR_TYPE_EVENTREMINDER_POPUP);
	}



	/**
	 * Get dismission state
	 *
	 * @return	String
	 */
	public function isDismissed() {
			// Already dismissed or not scheduled at all?
		$isDismissed	= $this->get('is_remindpopupdismissed') || ($this->getDateRemind() == 0);

		return $isDismissed;
	}


	/**
	 * Check whether email reminding for this event/person is disabled
	 *
	 * @return	Boolean
	 */
	public function isDisabled() {
		return parent::isDisabled(CALENDAR_TYPE_EVENTREMINDER_POPUP);
	}

}

?>