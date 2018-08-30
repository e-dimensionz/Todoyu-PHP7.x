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
 * Timetracking
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetracking {

	/**
	 * Session key
	 *
	 * @var	String
	 */
	const SESS_KEY	= 'timetracking';

	/**
	 * Working table
	 *
	 * @var	String
	 */
	const TABLE		= 'ext_timetracking_track';




	/**
	 * Get current tracking record of current user
	 *
	 * @return	Array		Or FALSE if no task is tracked
	 */
	private static function getCurrentTracking() {
		TodoyuCache::disable();

		$field	= '*';
		$table	= 'ext_timetracking_active';
		$where	= 'id_person_create	= ' . TodoyuAuth::getPersonID();
		$order	= 'date_create DESC';

		$record	= Todoyu::db()->getRecordByQuery($field, $table, $where, '', $order);

		TodoyuCache::enable();

		return $record;
	}



	/**
	 * Get all currently active tracking records
	 *
	 * @param	Boolean	$excludeCurrentUser	Only those tracked by other users?
	 * @return	Array
	 */
	public static function getCurrentTrackingTaskIDs($excludeCurrentUser = false) {
		TodoyuCache::disable();

		$field	= 'id_task';
		if( $excludeCurrentUser ) {
			$where	= 'NOT id_person_create = ' . TodoyuAuth::getPersonID();
		} else {
			$where	= '';
		}
		$table	= 'ext_timetracking_active';
		$order	= 'date_create DESC';

		$records	= Todoyu::db()->getColumn($field, $table, $where, '', $order);

		TodoyuCache::enable();

		return $records;
	}



	/**
	 * Store currently running time track record
	 *
	 * @param	Integer		$idTask
	 */
	private static function setCurrentTracking($idTask) {
		$data	= array(
			'id_task'	=> intval($idTask)
		);

		TodoyuRecordManager::addRecord('ext_timetracking_active', $data);
	}



	/**
	 * Remove running time track record from DB
	 */
	private static function removeCurrentTracking() {
		$table	= 'ext_timetracking_active';
		$where	= 'id_person_create	= ' . TodoyuAuth::getPersonID();

		Todoyu::db()->doDelete($table, $where);
	}



	/**
	 * Get ID of currently tracked task
	 *
	 * @return	Integer
	 */
	public static function getTaskID() {
		$record	= self::getCurrentTracking();

		return intval($record['id_task']);
	}



	/**
	 * Get current tracked task
	 *
	 * @return	TodoyuProjectTask
	 */
	public static function getTask() {
		return TodoyuProjectTaskManager::getTask(self::getTaskID());
	}



	/**
	 * Get starttime of current time tracking
	 *
	 * @return	Integer
	 */
	public static function getCurrentTrackingStart() {
		$record	= self::getCurrentTracking();

		return intval($record['date_create']);
	}


	/**
	 * @deprecated		wrapper
	 * @param	Integer	$idTask
	 * @return	Boolean
	 */
	public static function isTaskRunning($idTask) {
		return self::isTaskTrackedByMe($idTask);
	}

	/**
	 * Check whether task is currently being tracked by the current user
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskTrackedByMe($idTask) {
		$idTask		= intval($idTask);
		$idCurrent	= self::getTaskID();

		return $idTask === $idCurrent;
	}


	/**
	 * Check whether task is currently being tracked by someone else (than the current user)
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskTrackedByOthers($idTask) {
		$idTask	= intval($idTask);

		return in_array($idTask, self::getCurrentTrackingTaskIDs(true));
	}



	/**
	 * Check whether time tracking is active
	 *
	 * @return	Boolean
	 */
	public static function isTrackingActive() {
		return self::getTaskID() > 0;
	}



	/**
	 * Start time tracking for a task
	 *
	 * @param	Integer		$idTask
	 */
	public static function startTask($idTask) {
		$idTask	= intval($idTask);

			// Stop current task if any is running
		if( self::isTrackingActive() ) {
			self::stopTask();
		}

		TodoyuHookManager::callHook('timetracking', 'tracking.beforeStart', array($idTask));

		$task	= TodoyuTimetrackingTaskManager::getTask($idTask);

			// Check if current task status allows timetracking
		if( $task->isTrackable() ) {
				// Update trackable task status to progress
			if( $task->getStatus() !== STATUS_PROGRESS ) {
					// Check whether status should be changed
				$statusChangeIsWanted	= TodoyuTimetrackingTaskManager::isStatusChangeOnTrackingStartWanted($idTask);

				if( $statusChangeIsWanted ) {
					TodoyuProjectTaskManager::updateTaskStatus($idTask, STATUS_PROGRESS);
				}
			}
				// Register task as tracked in session
			self::setRunningTask($idTask);

				// No person+task tracking stored for today yet?
			$dayWorkload= self::getDayWorkloadRecord($idTask, NOW);
			if( !$dayWorkload ) {
					// Store initial '0:00' tracked time so started tracking is shown to other persons
				self::addTracking($idTask, 0);
			}
		} else {
				// Return error status
			//echo "NOT TRACKABLE";
		}

		TodoyuHookManager::callHook('timetracking', 'tracking.start', array($idTask));
	}



	/**
	 * Stop current time tracking
	 *
	 */
	public static function stopTask() {
		if( self::isTrackingActive() ) {
			$idTask		= self::getTaskID();

			TodoyuHookManager::callHook('timetracking', 'tracking.beforeStop', array($idTask));

				// Get today's tracked time of task
			$dayWorkload= self::getDayWorkloadRecord($idTask, NOW);
			$trackedTime= self::getTrackedTime();

			if( !$dayWorkload ) {
					// There's no tracking on this person+task today yet, create a new one
				self::addTracking($idTask, $trackedTime);
			} else {
					// Add tracked time to today's track of person+task
				$workload = $dayWorkload['workload_tracked'] + $trackedTime;
				self::updateTracking($dayWorkload['id'], $workload);
			}

			self::removeCurrentTracking();
			
			TodoyuHookManager::callHook('timetracking', 'tracking.stop', array($idTask));
		}
	}



	/**
	 * Get time of a task between start and end time
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @param	Boolean		$checkChargeableTime
	 * @param	Integer		$idPerson
	 * @param	Boolean		$addCurrentTracking
	 * @return	Integer
	 */
	public static function getTrackedTaskTime($idTask = 0, $dateStart = 0, $dateEnd = 0, $checkChargeableTime = false, $idPerson = 0, $addCurrentTracking = false) {
		$idTask		= intval($idTask);
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$idPerson	= intval($idPerson);

		if( $dateEnd === 0 || $dateEnd < $dateStart ) {
			$dateEnd = NOW;
		}

		$fields	= 'workload_tracked';
		$table	= self::TABLE;

		$where	= '	date_track BETWEEN ' . $dateStart . ' AND ' . $dateEnd	;

		if( $idTask > 0 ) {
			$where .= ' AND id_task	= ' . $idTask;
		}

			// If check is only for a single person, limit result
		if( $idPerson !== 0 ) {
			$where .= ' AND id_person_create = ' . $idPerson;
		}

			// If check for chargeable time is requested, get this column to
		if( $checkChargeableTime ) {
			$fields .= ', workload_chargeable';
		}

		$tracks	= Todoyu::db()->getArray($fields, $table, $where);
		$time	= 0;

		foreach($tracks as $track) {
			$time += $checkChargeableTime && $track['workload_chargeable'] != 0 ? $track['workload_chargeable'] : $track['workload_tracked'];
		}

			// If task is running, add
		if( $addCurrentTracking && ($idTask === 0 || self::isTaskTrackedByMe($idTask)) ) {
			$time += self::getTrackedTime();
		}

		return $time;
	}



	/**
	 * Get total tracked time (in seconds) of a task
	 *
	 * @param	Integer		$idTask				Task ID
	 * @param	Boolean		$checkChargeable	Count chargeable time if available
	 * @param	Boolean		$addCurrentTracking
	 * @return	Integer
	 */
	public static function getTrackedTaskTimeTotal($idTask, $checkChargeable = false, $addCurrentTracking = false) {
		$idTask	= intval($idTask);

		return self::getTrackedTaskTime($idTask, 0, 9999999999, $checkChargeable, 0, $addCurrentTracking);
	}



	/**
	 * Get time the task was running on a specific day
	 *
	 * @param	Integer		$idTask: ID of a task
	 * @param	Integer		$timestamp: timestamp of a specific day. Default will be today
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getTrackedTaskTimeOfDay($idTask, $timestamp = 0, $idPerson = 0) {
		$idTask		= intval($idTask);
		$timestamp	= intval($timestamp);
		$idPerson	= intval($idPerson);

		if( $timestamp === 0 ) {
			$timestamp = NOW;
		}

		$dayRange= TodoyuTime::getDayRange($timestamp);

		return self::getTrackedTaskTime($idTask, $dayRange['start'], $dayRange['end'], false, $idPerson, false);
	}



	/**
	 * Get tracked time of all tasks tracked today by current person
	 *
	 * @return	Integer
	 */
	public static function getTodayTrackedTime() {
		return self::getTrackedTaskTimeOfDay(0, NOW, Todoyu::personid());
	}



	/**
	 * Get all tracks of a person in a date range
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd			Optional, if not given: end of day $dateStart
	 * @param	Integer		$idPerson			Optional, default: logged-in person
	 * @return	Array
	 */
	public static function getPersonTracks($dateStart, $dateEnd = 0, $idPerson = 0) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$idPerson	= Todoyu::personid($idPerson);
		
		if( $dateEnd === 0 ) {
			$dateEnd	= TodoyuTime::getDayEnd($dateStart);
		}

		$fields	= ' track.*, task.deleted is_task_deleted';
		$tables	=	self::TABLE		. ' track,
					ext_project_task	task';
		$where	= '		track.id_person_create	=		' . $idPerson .
				  ' AND	track.date_track		BETWEEN ' . $dateStart . ' AND ' . $dateEnd .
				  ' AND task.id					=			track.id_task ';
		$group	= '';
		$order	= 'track.date_track';

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order);
	}



	/**
	 * Get tracked time of current tracking
	 *
	 * @return	Integer
	 */
	public static function getTrackedTime() {
		$startTime	= self::getCurrentTrackingStart();

		return $startTime === 0 ? 0 : NOW - $startTime;
	}



	/**
	 * Get track
	 *
	 * @param	Integer		$idTrack
	 * @return	TodoyuTimetrackingTrack
	 */
	public static function getTrack($idTrack) {
		return TodoyuRecordManager::getRecord('TodoyuTimetrackingTrack', $idTrack);
	}



	/**
	 * Loads persons firstname and lastname of given track
	 *
	 * @param	Integer		$idTrack
	 * @return	Array
	 */
	public static function getTrackPersonData($idTrack) {
		$idTrack= intval($idTrack);

		$fields	= '	u.firstname,
					u.lastname';
		$tables	=	self::TABLE . ' t,
					ext_contact_person u';
		$where	= '		t.id				= ' . $idTrack .
				  ' AND	t.id_person_create	= u.id';
		$order	= '	t.date_track DESC';

		return Todoyu::db()->getRecordByQuery($fields, $tables, $where, '', $order);
	}



	/**
	 * Add new track record
	 *
	 * @param	Array		$data
	 * @return	Integer		Track record ID
	 */
	public static function addRecord(array $data) {
		$data['date_track']	= NOW;

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a track record
	 *
	 * @param	Integer		$idTrack
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateRecord($idTrack, array $data) {
		$idTrack = intval($idTrack);

		return TodoyuRecordManager::updateRecord(self::TABLE, $idTrack, $data) === 1;
	}



	/**
	 * Add a new timetracking record to database
	 *
	 * @param	Integer		$idTask				Task ID
	 * @param	Integer		$timeTracked		In Seconds
	 * @param	Integer		$timeChargeable		In Seconds
	 * @return	Integer
	 */
	private static function addTracking($idTask, $timeTracked, $timeChargeable = 0) {
		$idTask			= intval($idTask);
		$timeTracked	= intval($timeTracked);
		$timeChargeable	= intval($timeChargeable);

		$data = array(
			'date_track'		=> NOW,
			'id_task'			=> $idTask,
			'workload_tracked'	=> $timeTracked,
			'workload_chargeable'=>$timeChargeable
		);

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a timetrack record. Happens if a task has been tracked again or the workload has been modified manually
	 *
	 * @param	Integer		$idTrack
	 * @param	Integer		$workloadTracked
	 * @param	Integer		$chargeableWorkload
	 * @param	String		$comment
	 * @return	Boolean
	 */
	private static function updateTracking($idTrack, $workloadTracked, $chargeableWorkload = null, $comment = null) {
		$idTrack		= intval($idTrack);
		$workloadTracked= intval($workloadTracked);

		$data = array(
			'date_update'		=> NOW,
			'date_track'		=> NOW,
			'workload_tracked'	=> $workloadTracked
		);

		if( !is_null($chargeableWorkload) ) {
			$data['workload_chargeable'] = intval($chargeableWorkload);
		}
		if( !is_null($comment) ) {
			$data['comment'] = $comment;
		}

		return self::updateRecord($idTrack, $data);
	}



	/**
	 * Check whether status is allowed for more timetracking
	 *
	 * @param	Integer		$status
	 * @return	Boolean
	 */
	public static function isTrackableStatus($status) {
		return in_array($status, TodoyuArray::assure(Todoyu::$CONFIG['EXT']['timetracking']['trackableStatus']));
	}



	/**
	 * Check whether an item is trackable. At the moment, only task are trackable, but not containers
	 *
	 * @param	Integer		$type
	 * @param	Integer		$status
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTrackable($type, $status, $idTask) {
		$type	= intval($type);
		$status	= intval($status);
		$idTask	= intval($idTask);

		if( ! TodoyuProjectTaskManager::isDeleted($idTask) && $type === TASK_TYPE_TASK ) {
			if( self::isTrackableStatus($status) ) {
				return TodoyuProjectTaskManager::isLocked($idTask) === false;
			}
		}

		return false;
	}



	/**
	 * Check whether task is trackable
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskTrackable($idTask) {
		$idTask	= intval($idTask);
		$task	= TodoyuTimetrackingTaskManager::getTask($idTask);

		return $task->isTrackable();
	}



	/**
	 * Save currently tracked task in session
	 *
	 * @param	Integer		$idTask
	 */
	private static function setRunningTask($idTask) {
		$idTask	= intval($idTask);

		self::setCurrentTracking($idTask);
	}



	/**
	 * Get stored workload of a task for a day. Only one record is created for a task per day
	 *
	 * @param	Integer				$idTask
	 * @param	Integer|Boolean		$timestamp
	 * @return	Array|Boolean
	 */
	private static function getDayWorkloadRecord($idTask, $timestamp = false) {
		$idTask	= intval($idTask);
		$range	= TodoyuTime::getDayRange($timestamp);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		id_person_create	= ' . Todoyu::personid() .
				  ' AND	id_task				= ' . $idTask .
				  ' AND	date_track			BETWEEN ' . $range['start'] . ' AND ' . $range['end'];

		return Todoyu::db()->getRecordByQuery($fields, $table, $where);
	}



	/**
	 * Get context menu items for a task
	 *
	 * @param	Integer		$idTask		Task ID
	 * @param	Array		$items		Current items
	 * @return	Array
	 */
	public static function getContextMenuItems($idTask, array $items) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

			// Ignore deleted tasks
		if( $task->isDeleted() ) {
			return $items;
		}

		if( $task->isTask() && ! $task->isLocked() && self::isTrackableStatus($task->getStatus()) ) {
			if( self::isTaskTrackedByMe($idTask) ) {
				$items['timetrackstop'] = Todoyu::$CONFIG['EXT']['timetracking']['ContextMenu']['Task']['timetrackstop'];
			} else {
				$items['timetrackstart'] = Todoyu::$CONFIG['EXT']['timetracking']['ContextMenu']['Task']['timetrackstart'];
			}
		}

		return $items;
	}



	/**
	 * Get tracked task IDs
	 *
	 * @param	Integer		$timeStart
	 * @param	Integer		$timeEnd
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getTrackedTaskIDs($timeStart = 0, $timeEnd = 0, $idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);
		$timeStart	= intval($timeStart);
		$timeEnd	= intval($timeEnd);

		if( $timeEnd === 0 ) {
			$timeEnd = NOW;
		}

		$field	= 'id_task';
		$table	= self::TABLE;
		$where	= '	date_update BETWEEN ' . $timeStart . ' AND ' . $timeEnd .
				  ' AND	id_person_create = ' . $idPerson;
		$group	= 'id_task';
		$order	= 'date_create';

		return Todoyu::db()->getColumn($field, $table, $where, $group, $order);
	}



	/**
	 * Hook. Called when person logs out. If configured, stop tracking
	 */
	public static function onLogout() {
		$extConf	= TodoyuSysmanagerExtConfManager::getExtConf('timetracking');

			// Check if timetracking stop if configured for logout
		if( intval($extConf['stopOnLogout']) === 1 ) {
				// Check if timetracking is active
			if( self::isTrackingActive() ) {
					// Stop current task (and save tracked time)
				self::stopTask();
			}
		}
	}

}
?>