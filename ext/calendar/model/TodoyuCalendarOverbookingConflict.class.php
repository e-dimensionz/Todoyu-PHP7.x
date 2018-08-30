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
 * Overbooking conflict (event and person)
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarOverbookingConflict {

	/**
	 * Event
	 *
	 * @var	Integer
	 */
	protected $idEvent;

	/**
	 * Person
	 *
	 * @var	Integer
	 */
	protected $idPerson;



	/**
	 * @param $idEvent
	 * @param $idPerson
	 */
	public function __construct($idEvent, $idPerson) {
		$this->idEvent	= intval($idEvent);
		$this->idPerson	= intval($idPerson);
	}



	/**
	 * Get event ID
	 *
	 * @return	Integer
	 */
	public function getEventID() {
		return $this->idEvent;
	}



	/**
	 * Get person ID
	 *
	 * @return	Integer
	 */
	public function getPersonID() {
		return $this->idPerson;
	}



	/**
	 * Get event
	 *
	 * @return	TodoyuCalendarEventStatic
	 */
	public function getEvent() {
		return TodoyuCalendarEventStaticManager::getEvent($this->getEventID());
	}



	/**
	 * Get person
	 *
	 * @return	TodoyuContactPerson
	 */
	public function getPerson() {
		return TodoyuContactPersonManager::getPerson($this->getPersonID());
	}



	/**
	 * Get warning message for conflict
	 *
	 * @param	Boolean		$fullRangeDate
	 * @return	String
	 */
	public function getWarningMessage($fullRangeDate = false) {
		$range	= $this->getEvent()->getRange();

		if( $fullRangeDate ) {
			$date	= $range->getLabelWithTime();
		} else {
			$date	= TodoyuTime::format($range->getStart(), 'DshortD2MshortY2');
		}

		$name	= $this->getPerson()->getFullName();
		$title	= $this->getEvent()->getTitle();

		return $date . ': ' . $name . ' (' . $title . ')';
	}

}

?>