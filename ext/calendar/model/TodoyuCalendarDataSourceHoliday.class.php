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
 * Data source for holidays
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarDataSourceHoliday extends TodoyuCalendarDataSource {

	/**
	 * Get holiday events for selected holiday sets
	 *
	 * @return	TodoyuCalendarEventHoliday[]
	 */
	public function getEvents() {
		$events	= array();

		if( !$this->areDayEventsDisabled() ) {
			$holidaySetIDs	= $this->getSelectedHolidaySetIDs();
			$range			= $this->getRange();
			$holidays		= TodoyuCalendarHolidayManager::getHolidaysInRange($range, $holidaySetIDs);
			$holidayIDs		= TodoyuArray::getColumn($holidays, 'id');

			$events	= TodoyuRecordManager::getRecordList('TodoyuCalendarEventHoliday', $holidayIDs);
		}
		
		return $events;
	}



	/**
	 * Get amount of holidays
	 *
	 * @return	Integer
	 */
	public function getEventCount() {
		$holidaySetIDs	= $this->getSelectedHolidaySetIDs();

		return count($holidaySetIDs);
	}



	/**
	 *
	 *
	 * @param	String		$searchWord
	 * @return	TodoyuCalendarEventHoliday[]
	 */
	public function searchEvents($searchWord) {
		return array();
	}



	/**
	 * Check whether day events are disabled by filter
	 *
	 * @return	Boolean
	 */
	private function areDayEventsDisabled() {
		return $this->getDayEventsFlag() === false;
	}



	/**
	 * @return	Boolean|Null
	 */
	private function getDayEventsFlag() {
		return $this->getFilter('dayevents');
	}



	/**
	 * Get selected holiday set IDs
	 *
	 * @return Integer[]
	 */
	protected function getSelectedHolidaySetIDs() {
		$holidaySetIDs	= $this->getFilter('holidaysets', true);

		return TodoyuArray::intval($holidaySetIDs);
	}



	/**
	 * Get holiday event
	 *
	 * @param	Integer		$idHoliday
	 * @return	TodoyuCalendarEventHoliday
	 */
	public static function getEvent($idHoliday) {
		return TodoyuRecordManager::getRecord('TodoyuCalendarEventHoliday', $idHoliday);
	}

}

?>