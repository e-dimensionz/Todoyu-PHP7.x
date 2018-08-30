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
 * Task time track object
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTrack extends TodoyuBaseObject {

	/**
	 * @param	Integer		$idTrack
	 */
	public function __construct($idTrack) {
		parent::__construct($idTrack, 'ext_timetracking_track');
	}



	/**
	 * Get tracking person
	 *
	 * @return	TodoyuContactPerson
	 */
	public function getTrackingPerson() {
		return $this->getPersonCreate();
	}



	/**
	 * Get date of tracking
	 *
	 * @return	Integer
	 */
	public function getDateTrack() {
		return $this->getInt('date_track');
	}



	/**
	 * Get task ID on which was tracked
	 *
	 * @return	Integer
	 */
	public function getTaskID() {
		return $this->getInt('id_task');
	}



	/**
	 * Get task on which was tracked
	 *
	 * @return	TodoyuProjectTask
	 */
	public function getTask() {
		return TodoyuProjectTaskManager::getTask($this->getTaskID());
	}



	/**
	 * Get amount of tracked workload of track
	 *
	 * @return	Integer
	 */
	public function getWorkloadTracked() {
		return $this->getInt('workload_tracked');
	}



	/**
	 * Get amount of chargeable workload of track
	 *
	 * @return	Integer
	 */
	public function getWorkloadChargeable() {
		return $this->getInt('workload_chargeable');
	}



	/**
	 * Get track comment
	 *
	 * @return	Mixed
	 */
	public function getComment() {
		return $this->get('comment');
	}
}

?>