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
 * Various helper functions common for email and popup reminders
 *
 * @package			Todoyu
 * @subpackage		Calendar
 */
class TodoyuCalendarReminderManager {

	/**
	 * @var String
	 */
	const TABLE = 'ext_calendar_mm_event_person';



	/**
	 * Get reminder object to given event/person
	 *
	 * @param	Integer		$idReminder
	 * @return	TodoyuCalendarReminder
	 */
	public static function getReminder($idReminder) {
		$idReminder	= intval($idReminder);

		return TodoyuRecordManager::getRecord('TodoyuCalendarReminder', $idReminder);
	}



	/**
	 * Get reminder object to given event/person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	TodoyuCalendarReminder
	 */
	public static function getReminderByAssignment($idEvent, $idPerson) {
		$idReminder	= self::getReminderIDByAssignment($idEvent, $idPerson);

		return self::getReminder($idReminder);
	}



	/**
	 * Get record ID of event-person MM relation of given event/person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getReminderIDByAssignment($idEvent, $idPerson = 0) {
		return TodoyuCalendarEventAssignmentManager::getAssignmentIdByAssignment($idEvent, $idPerson);
	}



	/**
	 * Get prefix ('email' / 'popup') of given reminder type
	 *
	 * @param	Integer		$reminderType
	 * @return	String
	 */
	public static function getReminderTypePrefix($reminderType) {
		$reminderType	= intval($reminderType);

		return $reminderType === CALENDAR_TYPE_EVENTREMINDER_EMAIL ? 'email' : 'popup';
	}



	/**
	 * Get reminder object of given type, event and person
	 *
	 * @param	Integer		$reminderType
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return TodoyuCalendarReminderEmail|TodoyuCalendarReminderPopup
	 */
	public static function getReminderTypeByAssignment($reminderType, $idEvent, $idPerson = 0) {
		$reminderType	= intval($reminderType);
		$idPerson		= Todoyu::personid($idPerson);

		if( $reminderType == CALENDAR_TYPE_EVENTREMINDER_EMAIL ) {
			return TodoyuCalendarReminderEmailManager::getReminderByAssignment($idEvent, $idPerson);
		} else {
			return TodoyuCalendarReminderPopupManager::getReminderByAssignment($idEvent, $idPerson);
		}
	}



	/**
	 * Update event-person MM record of given ID
	 *
	 * @param	Integer		$idReminder
	 * @param	Array		$data
	 */
	public static function updateReminder($idReminder, array $data) {
		TodoyuCalendarEventAssignmentManager::updateAssignment($idReminder, $data);
	}



	/**
	 * Update a reminder by assignment
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateReminderByAssignment($idEvent, $idPerson, array $data) {
		return TodoyuCalendarEventAssignmentManager::updateAssignmentByAssignment($idEvent, $idPerson, $data);
	}



	/**
	 * Check whether given/current person can schedule a reminder for the event of the given type / ID
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isPersonAssigned($idEvent, $idPerson = 0) {
		$idEvent		= intval($idEvent);
		$idPerson		= Todoyu::personid($idPerson);

		return TodoyuCalendarEventStaticManager::getEvent($idEvent)->isPersonAssigned($idPerson);
	}



	/**
	 * Deactivate (set time to 0) given reminder of given type, person and event
	 *
	 * @param	Integer		$reminderType
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 */
	public static function deactivateReminder($reminderType, $idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);

		self::updateReminderTime($reminderType, $idEvent, 0, $idPerson);
	}



	/**
	 * Update reminder activation (popup / mailing) time of given reminder
	 *
	 * @param	Integer		$reminderType
	 * @param	Integer		$idEvent
	 * @param	Integer		$dateRemind
	 * @param	Integer		$idPerson
	 */
	public static function updateReminderTime($reminderType, $idEvent, $dateRemind, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$dateRemind	= intval($dateRemind);
		$idPerson	= Todoyu::personid($idPerson);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);
		$idReminder	= TodoyuCalendarReminderManager::getReminderIDByAssignment($idEvent, $idPerson);

		if( $event->isPersonAssigned($idPerson) ) {
			$typePrefix	= self::getReminderTypePrefix($reminderType);

				// Update reminding time
			$data	= array(
				'date_remind' . $typePrefix	=> $dateRemind,
			);
			self::updateReminder($idReminder, $data);

				// Update dismission flag
			$isDismissed	= $dateRemind == 0;
			self::updateReminderDismission($reminderType, $idReminder, $isDismissed);
		}
	}



	/**
	 * Set reminder dismissed/active
	 *
	 * @param	Integer		$reminderType
	 * @param	Integer		$idRecord
	 * @param	Boolean		$isDismissed
	 */
	public static function updateReminderDismission($reminderType, $idRecord, $isDismissed = false) {
		$idRecord		= intval($idRecord);
		$isDismissed	= $isDismissed ? 1 : 0;
		$dismissionField= $reminderType == CALENDAR_TYPE_EVENTREMINDER_EMAIL ? 'is_remindemailsent' : 'is_remindpopupdismissed';

		$fieldValues	= array(
			$dismissionField	=> $isDismissed
		);

		self::updateReminder($idRecord, $fieldValues);
	}



	/**
	 * Update scheduled reminders (of all assigned persons of event) relative to shifted time of event
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$offset
	 */
	public static function shiftReminderDates($idEvent, $offset) {
		$idEvent	= intval($idEvent);
		$offset		= intval($offset);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);
		$personIDs	= $event->getAssignedPersonIDs();

		foreach($personIDs as $idPerson) {
			$reminder			= self::getReminderByAssignment($idEvent, $idPerson);
			$dateRemindEmail	= $reminder->getDateRemindEmail();
			$dateRemindPopup	= $reminder->getDateRemindPopup();

			$data	= array();

			if( $dateRemindEmail > 0 ) {
				$data['date_remindemail']	= $dateRemindEmail + $offset;
			} else if( $event->getDateStart() > NOW) {
				$advanceTimeEmail = self::getAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_EMAIL, $idPerson);
				if( $advanceTimeEmail > 0 ) {
					$data['date_remindemail']		= $event->getDateStart() - $advanceTimeEmail;
					$data['is_remindemailsent']		= 0;
				}
			}

			if( $dateRemindPopup > 0 ) {
				$data['date_remindpopup']			= $dateRemindPopup + $offset;
			} else if( $event->getDateStart() > NOW) {
				$advanceTimePopup = self::getAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_POPUP, $idPerson);
				if( $advanceTimePopup > 0 ) {
					$data['date_remindpopup']			= $event->getDateStart() - $advanceTimePopup;
					$data['is_remindpopupdismissed']	= 0;
				}
			}

			if( !empty($data)  ) {
				self::updateReminder($reminder->getID(), $data);
			}
		}
	}



	/**
	 * Set options disabled which are in the past already
	 *
	 * @param	Array	$subOptions
	 * @param	Integer	$idEvent
	 * @return	Array
	 */
	public static function disablePastTimeKeyOptions(array $subOptions, $idEvent) {
		$idEvent		= intval($idEvent);
		$eventDateStart	= TodoyuCalendarEventStaticManager::getEvent($idEvent)->getDateStart();

		foreach( $subOptions as $secondsBefore => $optionConfig ) {
			if( $secondsBefore > 0 ) {
				$timeScheduled	= $eventDateStart - $secondsBefore;
				if( $timeScheduled <= NOW ) {
					$subOptions[$secondsBefore]['class'] .= ' disabled';
				}
			}
		}

		return $subOptions;
	}



	/**
	 * Hook adds reminder fields to event form if they are allowed
	 *
	 * @param	TodoyuForm	$form
	 * @param	Integer		$idEvent
	 */
	public static function hookAddReminderFieldsToEvent(TodoyuForm $form, $idEvent) {
		$idEvent		= intval($idEvent);
		$emailAllowed	= TodoyuCalendarReminderEmailManager::isReminderAllowed($idEvent);
		$popupAllowed	= TodoyuCalendarReminderPopupManager::isReminderAllowed($idEvent);

		if( $emailAllowed || $popupAllowed ) {
			$xmlPathReminders	= 'ext/calendar/config/form/event-inline-user-reminders.xml';
			$remindersForm		= TodoyuFormManager::getForm($xmlPathReminders);
			$remindersFieldset	= $remindersForm->getFieldset('reminders');

			$form->addFieldset('reminders', $remindersFieldset, 'before:buttons');

				// Get advance time for reminders
			if( $idEvent === 0 ) {
				$advanceTimeEmail	= self::getAdvanceTimeEmail();
				$advanceTimePopup	= self::getAdvanceTimePopup();
			} else {
				$event				= TodoyuCalendarEventStaticManager::getEvent($idEvent);
				$advanceTimeEmail	= $event->getReminderAdvanceTimeEmail();
				$advanceTimePopup	= $event->getReminderAdvanceTimePopup();
			}

				// Email
			if( $emailAllowed ) {
				$form->getFieldset('reminders')->getField('reminder_email')->setValue($advanceTimeEmail);
			} else {
				$form->getFieldset('reminders')->removeField('reminder_email');
			}

				// Popup
			if( $popupAllowed ) {
				$form->getFieldset('reminders')->getField('reminder_popup')->setValue($advanceTimePopup);
			} else {
				$form->getFieldset('reminders')->removeField('reminder_popup');
			}
		}
	}



	/**
	 * Get advance time of reminder
	 *
	 * @param	Integer		$type
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	private static function getAdvanceTime($type, $idPerson) {
		$typeKey= self::getReminderTypePrefix($type);
		$pref	= TodoyuCalendarPreferences::getReminderAdvanceTime($typeKey, $idPerson);

		if( $pref ) {
			$pref	= intval($pref);
		} else {
			$pref	= TodoyuCalendarReminderDefaultManager::getDefaultAdvanceTime($type);
		}

		return $pref;
	}



	/**
	 * Get advance time of email reminder (user or default)
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getAdvanceTimeEmail($idPerson = 0) {
		return self::getAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_EMAIL, $idPerson);
	}



	/**
	 * Get advance time of popup reminder (user or default)
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getAdvanceTimePopup($idPerson = 0) {
		return self::getAdvanceTime(CALENDAR_TYPE_EVENTREMINDER_POPUP, $idPerson);
	}


	public static function removeReminderFromCache($idReminder) {
		TodoyuRecordManager::removeRecordCache('TodoyuCalendarReminder', $idReminder);
		TodoyuRecordManager::removeRecordCache('TodoyuCalendarReminderEmail', $idReminder);
		TodoyuRecordManager::removeRecordCache('TodoyuCalendarReminderPopup', $idReminder);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idReminder);
	}


	public static function removeReminderFromCacheByAssignment($idEvent, $idPerson) {
		$idReminder	= self::getReminderIDByAssignment($idEvent, $idPerson);

		self::removeReminderFromCache($idReminder);
	}

}

?>