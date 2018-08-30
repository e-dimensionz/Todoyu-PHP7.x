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
class TodoyuCalendarReminder extends TodoyuCalendarEventAssignment {

	/**
	 * Get starting time of event of reminder
	 *
	 * @return	Integer
	 */
	public function getEventStartDate() {
		return $this->getEvent()->getDateStart();
	}



	/**
	 * Get scheduled reminder time
	 *
	 * @param	Integer		$reminderType
	 * @return	Integer
	 */
	public function getDateRemind($reminderType = CALENDAR_TYPE_EVENTREMINDER_EMAIL) {
		$typePrefix	= TodoyuCalendarReminderManager::getReminderTypePrefix($reminderType);

		return $this->getInt('date_remind' . $typePrefix);
	}



	/**
	 * Get reminder date for email
	 *
	 * @return	Integer
	 */
	public function getDateRemindEmail() {
		return $this->getDateRemind(CALENDAR_TYPE_EVENTREMINDER_EMAIL);
	}



	/**
	 * Get reminder date for popup
	 *
	 * @return	Integer
	 */
	public function getDateRemindPopup() {
		return $this->getDateRemind(CALENDAR_TYPE_EVENTREMINDER_POPUP);
	}



	/**
	 * Check whether reminder has a reminder date for email
	 *
	 * @return	Boolean
	 */
	public function hasEmailReminder() {
		return $this->getDateRemindEmail() > 0;
	}



	/**
	 * Check whether reminder has a reminder date for popup
	 *
	 * @return	Boolean
	 */
	public function hasPopupReminder() {
		return $this->getDateRemindPopup() > 0;
	}



	/**
	 * Get amount of time before event when given reminder type is scheduled
	 *
	 * @param	Integer		$type
	 * @return	Integer|Boolean
	 */
	public function getAdvanceTime($type = CALENDAR_TYPE_EVENTREMINDER_EMAIL) {
		$dateRemind	= $this->getDateRemind($type);

		if( $dateRemind > 0 ) {
			return $this->getEventStartDate() - $dateRemind;
		} else {
			return false;
		}
	}



	/**
	 * Get advance time for email reminder
	 *
	 * @return	Integer
	 */
	public function getAdvanceTimeEmail() {
		return $this->getAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_EMAIL);
	}



	/**
	 * Get advance time for popup reminder
	 *
	 * @return	Integer
	 */
	public function getAdvanceTimePopup() {
		return $this->getAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_POPUP);
	}



	/**
	 * Check whether email reminding for this event/person is disabled
	 *
	 * @param	Integer		$reminderType
	 * @return	Boolean
	 */
	protected function isDisabled($reminderType) {
		$typePrefix	= TodoyuCalendarReminderManager::getReminderTypePrefix($reminderType);

		return $this->get('date_remind' . $typePrefix) === 0;
	}

}

?>