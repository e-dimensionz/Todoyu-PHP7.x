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
 * Data source for static events
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarDataSourceStatic extends TodoyuCalendarDataSource {

	/**
	 * Get event IDs which match to the given selection
	 *
	 * @return	TodoyuCalendarEventStatic[]
	 */
	public function getEvents() {
		$eventIDs	= $this->getEventIDs();

		return TodoyuRecordManager::getRecordList('TodoyuCalendarEventStatic', $eventIDs);
	}



	/**
	 * Get amount of events
	 *
	 * @return	Integer
	 */
	public function getEventCount() {
		$eventIDs	= $this->getEventIDs();

		return !empty($eventIDs) ? sizeof($eventIDs) : 0;
	}



	/**
	 * Search events
	 *
	 * @param	String		$searchWord
	 * @return	TodoyuCalendarEventStatic[]
	 */
	public function searchEvents($searchWord) {
		return array();
	}



	/**
	 * Get event IDs which match to the given selection
	 *
	 * @return	Integer[]
	 */
	protected function getEventIDs() {
		$dateStart		= $this->getRange()->getStart();
		$dateEnd		= $this->getRange()->getEnd();
		$personIDs		= $this->getPersonIDs();
		$eventTypeIDs	= $this->getEventTypeIDs();
		$dayEvents		= $this->getDayEventsFlag();

		$field	= 'e.id';
		$tables	= 'ext_calendar_event e';
			// Add join to person mm table
		if( !empty($personIDs)) {
			$tables .= ' LEFT JOIN ext_calendar_mm_event_person mmep ON e.id = mmep.id_event';
		}

		$where	= '		 e.deleted	= 0
					AND (
							e.date_start	BETWEEN ' . $dateStart . ' AND ' . $dateEnd . '
						OR	e.date_end		BETWEEN ' . $dateStart . ' AND ' . $dateEnd . '
						OR (e.date_start <= ' . $dateStart . ' AND e.date_end >= ' . $dateEnd . ')
					)';
		$group	= '	e.id';
		$order	= '	e.date_start ASC';

			// DayEvents: null = both, true = only, false = without
		if( ! is_null($dayEvents) ) {
			$where .= ' AND ( e.is_dayevent = ' . ($dayEvents ? 1 : 0);

			if( $dayEvents === true ) {
					// Events than intersect more than one day are also displayed as day-events
				$where .= ' OR DATE_FORMAT(FROM_UNIXTIME(e.date_start), \'%y-%j\') != DATE_FORMAT(FROM_UNIXTIME(e.date_end), \'%y-%j\') ';
			} else if( $dayEvents === false ) {
				$where .= ' AND DATE_FORMAT(FROM_UNIXTIME(e.date_start), \'%y-%j\') = DATE_FORMAT(FROM_UNIXTIME(e.date_end), \'%y-%j\') ';
			}

			$where .= ' ) ';
		}

			// Limit to given event types
		if( !empty($eventTypeIDs) ) {
			$where .= ' AND e.eventtype IN(' . implode(',', $eventTypeIDs) . ')';
		}

			// Limit to given assigned persons
		if( !empty($personIDs)) {
			$where	.= ' AND mmep.id_person IN(' . implode(',', $personIDs) . ')';
		}

		return Todoyu::db()->getColumn($field, $tables, $where, $group, $order, '', 'id');
	}



	/**
	 * Get person IDs from filter
	 *
	 * @return	Array
	 */
	protected function getPersonIDs() {
		return $this->getFilter('persons', true);
	}



	/**
	 * Get event type IDs from filter
	 *
	 * @return	Array
	 */
	protected function getEventTypeIDs() {
		return $this->getFilter('eventtypes', true);
	}



	/**
	 * Get day events filter
	 *
	 * @return	Boolean|Null
	 */
	protected function getDayEventsFlag() {
		return $this->getFilter('dayevents');
	}



	/**
	 * Get static event
	 *
	 * @param	Integer		$idEvent
	 * @return	TodoyuCalendarEventStatic
	 */
	public static function getEvent($idEvent) {
		return TodoyuRecordManager::getRecord('TodoyuCalendarEventStatic', $idEvent);
	}

}

?>