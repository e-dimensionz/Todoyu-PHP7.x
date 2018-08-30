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
 * Manage event assignments
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventAssignmentManager {

	/**
	 * Default table for database requests
	 */
	const TABLE	= 'ext_calendar_mm_event_person';



	/**
	 * Get assignment by person-event combination
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	TodoyuCalendarEventAssignment
	 */
	public static function getAssignmentByEventPerson($idEvent, $idPerson = 0) {
		$idAssignment	= self::getAssignmentIdByAssignment($idEvent, $idPerson);

		return self::getAssignment($idAssignment);
	}



	/**
	 * Get ID of assignment by event and person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getAssignmentIdByAssignment($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);

		$field	= 'id';
		$where	= '		id_event	= ' . $idEvent
				. ' AND id_person	= ' . $idPerson;

		$idAssignment	= Todoyu::db()->getFieldValue($field, self::TABLE, $where);

		return intval($idAssignment);
	}



	/**
	 * Get person event assignment
	 *
	 * @param	Integer		$idAssignment
	 * @return	TodoyuCalendarEventAssignment
	 */
	public static function getAssignment($idAssignment) {
		$idAssignment	= intval($idAssignment);

		return TodoyuRecordManager::getRecord('TodoyuCalendarEventAssignment', $idAssignment);
	}



	/**
	 * Update reminder dates
	 *
	 * @param	Integer			$idEvent
	 * @param	Integer			$idPerson
	 * @param	Integer|Boolean	$dateEmail
	 * @param	Integer|Boolean	$datePopup
	 */
	public static function updateReminderDates($idEvent, $idPerson = 0, $dateEmail = false, $datePopup = false) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);

		$data	= array(
			'is_updated'	=> 1
		);

		if( $dateEmail !== false ) {
			$data['date_remindemail']	= intval($dateEmail);
		}

		if( $datePopup !== false ) {
			$data['date_remindpopup']			= intval($datePopup);
			$data['is_remindpopupdismissed']	= 0;
		}

		$where	= '		id_event	= ' . $idEvent
				. ' AND id_person	= ' . $idPerson;

		Todoyu::db()->doUpdate(self::TABLE, $where, $data);
	}



	/**
	 * Remove the given person's assignment from the given event
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 */
	public static function removeAssignment($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);

		$where		= '		id_event	= ' . $idEvent .
					  ' AND	id_person	= ' . $idPerson;

		Todoyu::db()->doDelete(self::TABLE, $where);
	}



	/**
	 * Reset acknowledge flag of event assignment. Event will be show as "new"
	 * By default, the current user will not be reset, he should already now
	 *
	 * @param	Integer		$idEvent
	 * @param	Boolean		$resetForCurrentUser		Reset also for current user
	 */
	public static function resetAcknowledgment($idEvent, $resetForCurrentUser = false) {
		$idEvent= intval($idEvent);
		$where	= '	id_event = ' . $idEvent;
		$data	= array(
			'is_acknowledged'	=> 0
		);

		if( ! $resetForCurrentUser ) {
			$where .= ' AND	id_person != ' . Todoyu::personid();
		}

		Todoyu::db()->doUpdate(self::TABLE, $where, $data);
	}



	/**
	 * Get IDs of persons which are assigned to the event
	 *
	 * @param	Integer		$idEvent
	 * @return	Array
	 */
	public static function getAssignedPersonIDs($idEvent) {
		$idEvent= intval($idEvent);

		$field	= 'id_person';
		$where	= 'id_event = ' . $idEvent;

		return Todoyu::db()->getColumn($field, self::TABLE, $where);
	}



	/**
	 * Remove all person assignments from an event
	 *
	 * @param	Integer		$idEvent
	 */
	public static function removeAllAssignments($idEvent) {
		$idEvent= intval($idEvent);
		$where	= 'id_event = ' . $idEvent;

		Todoyu::db()->doDelete(self::TABLE, $where);
	}



	/**
	 * Set given event acknowledged by given person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 */
	public static function acknowledgeEvent($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);

		$data	= array(
			'is_acknowledged'	=> 1
		);
		$where	= '		id_event	= ' . $idEvent .
				  ' AND	id_person	= ' . $idPerson;

		Todoyu::db()->doUpdate(self::TABLE, $where, $data);
	}



	/**
	 * Update an assignment
	 *
	 * @param	Integer		$idAssignment
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateAssignment($idAssignment, array $data) {
		$idAssignment	= intval($idAssignment);

		return Todoyu::db()->updateRecord(self::TABLE, $idAssignment, $data) == 1;
	}



	/**
	 * Update an assignment by event person combination
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateAssignmentByAssignment($idEvent, $idPerson, array $data) {
		$idEvent	= intval($idEvent);
		$idPerson	= intval($idPerson);

		$where	= '		id_event	= ' . $idEvent .
				  ' AND	id_person	= ' . $idPerson;

		return Todoyu::db()->doUpdate(self::TABLE, $where, $data) == 1;
	}

}

?>