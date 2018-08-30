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
 * Result of series event creation process
 * During the creation, some events may get deleted, event the base event
 * This object contains all data from the process. Used as complex return value
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventSeriesCreateResult {

	/**
	 * IDs of the created events
	 *
	 * @var	Integer[]
	 */
	protected $createdEventIDs = array();

	/**
	 * ÏDs of the deleted events
	 *
	 * @var	Integer[]
	 */
	protected $deletedEventIDs = array();

	/**
	 * Base event ID
	 *
	 * @var	Integer
	 */
	protected $idBaseEvent = 0;

	/**
	 * Flag whether base event was deleted during the process
	 *
	 * @var	Boolean
	 */
	protected $isBaseEventDeleted = false;

	/**
	 * ID of new series
	 *
	 * @var	Integer
	 */
	protected $idSeriesNew = 0;

	/**
	 * ID of old series. If events were updated from a date in the middle
	 *
	 * @var	Integer
	 */
	protected $idSeriesOld = 0;



	/**
	 * Initialize
	 *
	 * @param	Integer		$idBaseEvent
	 * @param	Integer		$idSeriesNew
	 * @param	Integer		$idSeriesOld
	 */
	public function __construct($idBaseEvent, $idSeriesNew, $idSeriesOld = 0) {
		$this->setBaseEventID($idBaseEvent);
		$this->setSeriesNewID($idSeriesNew);
		$this->setSeriesOldID($idSeriesOld);
	}



	/**
	 * Set list of created events
	 *
	 * @param	Integer[]	$eventIDs
	 */
	public function setCreatedEvents(array $eventIDs) {
		$this->createdEventIDs = TodoyuArray::intval($eventIDs);
	}



	/**
	 * Set list of deleted events
	 *
	 * @param	Integer[]	$eventIDs
	 */
	public function setDeletedEvents(array $eventIDs) {
		$this->deletedEventIDs = TodoyuArray::intval($eventIDs);

		if( in_array($this->idBaseEvent, $this->deletedEventIDs) ) {
			$this->setBaseEventDeleted();
		}
	}



	/**
	 * Set base event deleted
	 *
	 * @param	Boolean		$deleted
	 */
	public function setBaseEventDeleted($deleted = true) {
		$this->isBaseEventDeleted = $deleted;
	}



	/**
	 * Set base event ID
	 *
	 * @param	Integer		$idBaseEvent
	 */
	public function setBaseEventID($idBaseEvent) {
		$this->idBaseEvent = intval($idBaseEvent);
	}



	/**
	 * Set old series ID
	 *
	 * @param	Integer		$idSeriesOld
	 */
	public function setSeriesOldID($idSeriesOld) {
		$this->idSeriesOld = intval($idSeriesOld);
	}



	/**
	 * Set new series ID
	 *
	 * @param	Integer		$idSeriesNew
	 */
	public function setSeriesNewID($idSeriesNew) {
		$this->idSeriesNew = intval($idSeriesNew);
	}



	/**
	 * Get base event ID
	 *
	 * @return	Integer
	 */
	public function getBaseEventID() {
		return $this->idBaseEvent;
	}



	/**
	 * Check whether base event is deleted
	 *
	 * @return	Boolean
	 */
	public function isBaseEventDeleted() {
		return $this->isBaseEventDeleted;
	}



	/**
	 * Get old series ID
	 *
	 * @return	Integer
	 */
	public function getSeriesOldID() {
		return $this->idSeriesOld;
	}



	/**
	 * Get new series ID
	 *
	 * @return	Integer
	 */
	public function getSeriesNewID() {
		return $this->idSeriesNew;
	}



	/**
	 * Get created event IDs
	 *
	 * @return	Integer[]
	 */
	public function getCreatedEventIDs() {
		return $this->createdEventIDs;
	}



	/**
	 * Get deleted event IDs
	 *
	 * @return	Integer[]
	 */
	public function getDeletedEventIDs() {
		return $this->deletedEventIDs;
	}



	/**
	 * Get first created event ID
	 *
	 * @return	Integer
	 */
	public function getFirstCreateEvent() {
		return current($this->createdEventIDs);
	}



	/**
	 * Get new base event ID
	 * If base event was deleted, use first created event ID
	 *
	 * @return	Integer
	 */
	public function getNewBaseEventID() {
		return $this->isBaseEventDeleted() ? $this->getFirstCreateEvent() : $this->getBaseEventID();
	}

}

?>