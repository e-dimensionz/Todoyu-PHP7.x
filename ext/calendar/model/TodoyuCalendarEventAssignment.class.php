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
 * Event-Person assignment object
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventAssignment extends TodoyuBaseObject {

	/**
	 * Initialize
	 *
	 * @param	Integer		$idAssignment
	 */
	public function __construct($idAssignment) {
		parent::__construct($idAssignment, 'ext_calendar_mm_event_person');
	}



	/**
	 * Get event ID
	 *
	 * @return	Integer
	 */
	public function getEventID() {
		return $this->getInt('id_event');
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
	 * Get person ID
	 *
	 * @return	Integer
	 */
	public function getPersonID($type) {
		return $this->getInt('id_person');
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
	 * Was event updated since person saw assignment last time
	 *
	 * @return	Boolean
	 */
	public function isUpdated() {
		return $this->isFlagSet('is_updated');
	}



	/**
	 * Was assignment acknowledged
	 *
	 * @return	Boolean
	 */
	public function isAcknowledged() {
		return $this->isFlagSet('is_acknowledged');
	}

}

?>