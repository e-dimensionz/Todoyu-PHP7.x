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
 * Event Reminder Email Manager
 *
 * @package			Todoyu
 * @subpackage		Calendar
 */
class TodoyuCalendarReminderEmailManager {

	/**
	 * @var	String		Type of reminder
	 */
	const REMINDERTYPE	= CALENDAR_TYPE_EVENTREMINDER_EMAIL;


	/**
	 *
	 * @param	Integer		$idReminder
	 * @return	TodoyuCalendarReminderEmail
	 */
	public static function getReminder($idReminder) {
		$idReminder	= intval($idReminder);

		return TodoyuRecordManager::getRecord('TodoyuCalendarReminderEmail', $idReminder);
	}



	/**
	 * Get email reminder of given event/person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	TodoyuCalendarReminderEmail
	 */
	public static function getReminderByAssignment($idEvent, $idPerson = 0) {
		$idReminder	= TodoyuCalendarReminderManager::getReminderIDByAssignment($idEvent, $idPerson);

		return self::getReminder($idReminder);
	}



	/**
	 * Update email reminder sending time of given event/person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$timeEmail
	 * @param	Integer		$idPerson
	 */
	public static function updateReminderTime($idEvent, $timeEmail, $idPerson = 0) {
		TodoyuCalendarReminderManager::updateReminderTime(self::REMINDERTYPE, $idEvent, $timeEmail, $idPerson);
	}



	/**
	 * Get timestamp for email reminder of newly assigned event (advance-time from profile, fallback: extconf)
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getNewEventMailTime($dateStart, $idPerson = 0) {
		$dateStart	= intval($dateStart);
		$idPerson	= Todoyu::personid($idPerson);

		return $dateStart  - self::getDefaultAdvanceTime($idPerson);
	}



	/**
	 * Get scheduled reminder mailing time of given event
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function getReminderMailTime($idEvent, $idPerson = 0) {
		if( ! Todoyu::allowed('calendar', 'reminders:email') ) {
			return false;
		}

		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);
		$reminder	= self::getReminderByAssignment($idEvent, $idPerson);

		return $reminder->getDateRemindEmail();
	}



	/**
	 * Get amount of time before event when to send reminder email
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getAdvanceTime($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);

		if( $idEvent === 0 ) {
			return TodoyuCalendarReminderDefaultManager::getEmailDefaultAdvanceTime();
		} else {
			$reminder	= self::getReminderByAssignment($idEvent, $idPerson);

			return $reminder->getAdvanceTime();
		}
	}



	/**
	 * Get current person's event reminder emails advance time from current person prefs, fallback: extconf
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getDefaultAdvanceTime($idPerson = 0) {
		return TodoyuCalendarReminderDefaultManager::getDefaultAdvanceTime(self::REMINDERTYPE, $idPerson);
	}



	/**
	 * Get key of context menu sub-option of selected advance time
	 *
	 * @param	Integer				$idEvent
	 * @return	Integer|Boolean
	 */
	public static function getSelectedAdvanceTimeContextMenuOptionKey($idEvent) {
		$idEvent	= intval($idEvent);

		$scheduledTime	= self::getReminderMailTime($idEvent);
		if( $scheduledTime == 0 ) {
			return false;
		}

		return self::getAdvanceTime($idEvent);
	}



	/**
	 * Check whether given/current person can schedule a reminder for the event of the given ID
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isReminderAllowed($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);

		if( ! Todoyu::allowed('calendar', 'reminders:email') ) {
			return false;
		}

		if( $idEvent === 0 ) {
			return true;
		}

		return TodoyuCalendarReminderManager::isPersonAssigned($idEvent, $idPerson);
	}



	/**
	 * Get context menu items for email reminder in event contextmenu
	 *
	 * @param	Integer		$idEvent
	 * @param	Array		$items
	 * @return	Array
	 */
	public static function getContextMenuItems($idEvent, array $items) {
		$idEvent	= intval($idEvent);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);
		$allowed	= array();

				// Option: email reminder
		if( $event->getDateStart() > NOW && self::isReminderAllowed($idEvent) ) {
			$options	= Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['reminderemail'];

				// Set selected option CSS class
			$selectedTimeOptionKey	= self::getSelectedAdvanceTimeContextMenuOptionKey($idEvent);
			if( !$selectedTimeOptionKey ) {
				$options['submenu'][0]['class'] .= ' selected';
			} elseif( key_exists($selectedTimeOptionKey, $options['submenu']) ) {
				$options['submenu'][$selectedTimeOptionKey]['class'] .= ' selected';
			}
				// Set options disabled which are in the past already
			$options['submenu']	= TodoyuCalendarReminderManager::disablePastTimeKeyOptions($options['submenu'], $idEvent);

			$allowed['reminderemail']	= $options;
		}

		return array_merge_recursive($items, $allowed);
	}


}

?>