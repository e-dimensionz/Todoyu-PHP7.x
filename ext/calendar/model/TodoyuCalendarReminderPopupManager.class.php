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
 * Event Reminder Popups Manager
 *
 * @package			Todoyu
 * @subpackage		Calendar
 */
class TodoyuCalendarReminderPopupManager {

	/**
	 * Type of reminder
	 *
	 * @var	String
	 */
	const REMINDERTYPE	= CALENDAR_TYPE_EVENTREMINDER_POPUP;



	/**
	 * Get reminder
	 *
	 * @param	Integer		$idReminder
	 * @return	TodoyuCalendarReminderPopup
	 */
	public static function getReminder($idReminder) {
		$idReminder	= intval($idReminder);

		return TodoyuRecordManager::getRecord('TodoyuCalendarReminderPopup', $idReminder);
	}



	/**
	 * Get person's reminder to given event
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	TodoyuCalendarReminderPopup
	 */
	public static function getReminderByAssignment($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);
		$idReminder	= TodoyuCalendarReminderManager::getReminderIDByAssignment($idEvent, $idPerson);

		return self::getReminder($idReminder);
	}



	/**
	 * Update scheduled popup reminder display time of given event/person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$timePopup
	 * @param	Integer		$idPerson
	 */
	public static function updateReminderTime($idEvent, $timePopup, $idPerson = 0) {
		TodoyuCalendarReminderManager::updateReminderTime(self::REMINDERTYPE, $idEvent, $timePopup, $idPerson);
	}



	/**
	 * Get timestamp for popup reminder of newly assigned event (advance-time from profile, fallback: extconf)
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getNewEventPopupTime($dateStart, $idPerson = 0) {
		$dateStart	= intval($dateStart);
		$idPerson	= Todoyu::personid($idPerson);

		return $dateStart  - self::getDefaultAdvanceTime($idPerson);
	}



	/**
	 * Get amount of time before event when to show reminder popup
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Integer					Amount of seconds
	 */
	public static function getAdvanceTime($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);
		$reminder	= self::getReminderByAssignment($idEvent, $idPerson);

		if( $idEvent === 0 ) {
			return TodoyuCalendarReminderDefaultManager::getPopupDefaultAdvanceTime();
		}

		return $reminder->getAdvanceTime();
	}



	/**
	 * Check whether given/current person can schedule a reminder for the event of the given ID
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isReminderAllowed($idEvent, $idPerson = 0) {
		if( ! Todoyu::allowed('calendar', 'reminders:popup') ) {
			return false;
		}

		if( $idEvent === 0 ) {
			return true;
		}

		return TodoyuCalendarReminderManager::isPersonAssigned($idEvent, $idPerson);
	}



	/**
	 * Get person's event reminder popups advance time from current person prefs, fallback: extconf
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getDefaultAdvanceTime($idPerson = 0) {
		return TodoyuCalendarReminderDefaultManager::getDefaultAdvanceTime(self::REMINDERTYPE, $idPerson);
	}



	/**
	 * @param	Integer		$idEvent
	 * @return	Integer					Amount of seconds
	 */
	public static function getSelectedAdvanceTimeContextMenuOptionKey($idEvent) {
		$idEvent		= intval($idEvent);
		$scheduledTime	= self::getDateRemind($idEvent);

		if( $scheduledTime == 0 ) {
			return false;
		}

		return self::getAdvanceTime($idEvent);
	}



	/**
	 * Add reminder JS init to page
	 */
	public static function addReminderJsInitToPage() {
		$upcomingEvents	= self::getUpcomingReminderEvents();
		$json			= json_encode($upcomingEvents);
		$jsInitCode		= 'Todoyu.Ext.calendar.Reminder.Popup.init(' . $json . ')';

		TodoyuPage::addJsInit($jsInitCode, 200);
	}



	/**
	 * Get reminder popup settings of upcoming events of current person
	 *
	 * @return	Array
	 */
	public static function getUpcomingReminderEvents() {
			// Get upcoming events
		$dateStart	= NOW - Todoyu::$CONFIG['EXT']['calendar']['EVENT_REMINDER_LOOKBACK'];
		$dateEnd	= NOW + Todoyu::$CONFIG['EXT']['calendar']['EVENT_REMINDER_LOOKAHEAD'];
		$personIDs	= array(Todoyu::personid());
		$eventTypes	= Todoyu::$CONFIG['EXT']['calendar']['EVENTTYPES_REMIND_POPUP'];

		$reminders	= array();
		$events		= TodoyuCalendarEventStaticManager::getEventsInTimespan($dateStart, $dateEnd, $personIDs, $eventTypes);

		foreach($events as $idEvent => $eventData) {
			$reminder	= self::getReminderByAssignment($idEvent);
				// Setup event reminder data / remove dismissed reminders from schedule
			if( ! $reminder->isDismissed() ) {
				$reminders[] = array(
					'id'	=> $idEvent,
					'popup'	=> $reminder->getDateRemind(),
					'start'	=> intval($eventData['date_start'])
				);
			}
		}

		return $reminders;
	}



	/**
	 * Get timestamp when to show reminder of given event (initially / again)
	 *
	 * @param	Integer		$idEvent
	 * @return	Integer					UNIX timestamp when to display the reminder popup
	 */
	public static function getDateRemind($idEvent) {
		$reminder	= self::getReminderByAssignment($idEvent);

		return $reminder->getDateRemind();
	}



	/**
	 * Check whether the given person's reminder of the given event is dismissed already
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isDismissed($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);
		$reminder	= self::getReminderByAssignment($idEvent, $idPerson);

		return $reminder->isDismissed();
	}



	/**
	 * Get URL of sound to be played with given event's reminder popup
	 *
	 * @param	Integer		$idEvent
	 * @return	String
	 */
	public static function getSoundFilename($idEvent) {
		$idEvent	= intval($idEvent);

		$pathDefaultFile= 'ext/calendar/asset/audio/reminder.wav';
		$pathFile		= TodoyuHookManager::callHookDataModifier('calendar', 'getReminderSoundFilename', $pathDefaultFile, array('event'	=> $idEvent));

		return TodoyuFileManager::pathWeb($pathFile, true);
	}



	/**
	 * Set given reminder dismissed
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function setReminderDismissed($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);
		$data		= array(
			'is_remindpopupdismissed'	=> 1
		);

		return TodoyuCalendarReminderManager::updateReminderByAssignment($idEvent, $idPerson, $data);
	}



	/**
	 * Update timestamp when to show given reminder again
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$timeShowAgain
	 * @return	Integer
	 */
	public static function rescheduleReminder($idEvent, $timeShowAgain) {
		$idEvent		= intval($idEvent);
		$idPerson		= TodoyuAuth::getPersonID();
		$timeShowAgain	= intval($timeShowAgain);

		$data	= array(
			'is_remindpopupdismissed'	=> 0,
			'date_remindpopup'			=> $timeShowAgain
		);

		return TodoyuCalendarReminderManager::updateReminderByAssignment($idEvent, $idPerson, $data);
	}



	/**
	 * Check whether the audio reminder is enabled (play sound)
	 *
	 * @return	Boolean
	 */
	public static function isAudioReminderEnabled() {
		return TodoyuSysmanagerExtConfManager::getExtConfValue('calendar', 'audioreminder_active') ? true : false;
	}



	/**
	 * Get context menu items for popup reminders
	 *
	 * @param	String		$idEvent
	 * @param array $items
	 * @return array
	 */
	public static function getContextMenuItems($idEvent, array $items) {
		$idEvent	= intval($idEvent);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);


			// Option: popup reminder
		if( $event->getDateStart() > NOW && self::isReminderAllowed($idEvent) ) {
			$options	= Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['reminderpopup'];
			$allowed	= array();

				// Set selected option CSS class
			$selectedTimeOptionKey	= self::getSelectedAdvanceTimeContextMenuOptionKey($idEvent);
			if( !$selectedTimeOptionKey ) {
				$options['submenu'][0]['class'] .= ' selected';
			} elseif( isset($options['submenu'][$selectedTimeOptionKey]) ) {
				$options['submenu'][$selectedTimeOptionKey]['class'] .= ' selected';
			}
				// Set options disabled which are in the past already
			$options['submenu']	= TodoyuCalendarReminderManager::disablePastTimeKeyOptions($options['submenu'], $idEvent);

			$allowed['reminderpopup']	= $options;

			$items = array_merge_recursive($items, $allowed);
		}

		return $items;
	}

}

?>