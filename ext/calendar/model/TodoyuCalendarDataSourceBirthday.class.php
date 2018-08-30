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
 * Data source for birthdays
 * Calculates birthdays for all users of todoyu
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarDataSourceBirthday extends TodoyuCalendarDataSource {

	/**
	 * Get birthday events
	 *
	 * @return	TodoyuCalendarEventBirthday[]
	 */
	public function getEvents() {
		$events		= array();

		if( $this->areDayEventsEnabled() && $this->isBirthdayEventTypeSelected() ) {
			$personIDs	= $this->getBirthdayPersonIDs();

			foreach($personIDs as $idPerson) {
				$events[] = new TodoyuCalendarEventBirthday($idPerson, $this->getRange());
			}
		}

		return $events;
	}



	/**
	 * Get amount of birthdays
	 *
	 * @return	Integer
	 */
	public function getEventCount() {
		$personIDs	= $this->getBirthdayPersonIDs();

		return sizeof($personIDs);
	}



	/**
	 * Search for birthdays
	 * @param	String		$searchWord
	 * @return	TodoyuCalendarEvent[]
	 */
	public function searchEvents($searchWord) {
		return array();
	}



	/**
	 * Get day event filter value
	 *
	 * @return	Boolean|Null
	 */
	private function getDayEventsFlag() {
		return $this->getFilter('dayevents');
	}



	/**
	 * Check whether day events filter value is true
	 *
	 * @return	Boolean
	 */
	private function areDayEventsEnabled() {
		return $this->getDayEventsFlag() !== false;
	}



	/**
	 * Check whether event type birthday is selected
	 *
	 * @return	Boolean
	 */
	private function isBirthdayEventTypeSelected() {
		return in_array(EVENTTYPE_BIRTHDAY, $this->getEventTypes());
	}



	/**
	 * Get selected event types
	 *
	 * @return	Array
	 */
	private function getEventTypes() {
		return $this->getFilter('eventtypes', true);
	}



	/**
	 * Get IDs of persons which birthdays are in the range
	 *
	 * @return	Integer[]
	 */
	private function getBirthdayPersonIDs() {
		$birthdayPersonsData= TodoyuContactPersonManager::getBirthdayPersons($this->getRange());
		$personIDs			= TodoyuArray::getColumn($birthdayPersonsData, 'id');

		return $personIDs;
	}



	/**
	 * Get a birthday event by person ID
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuCalendarEventBirthday
	 */
	public static function getEvent($idPerson) {
		return TodoyuRecordManager::getRecord('TodoyuCalendarEventBirthday', $idPerson);
	}

}

?>