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
 * Timetracking rights manager
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingRights {

	/**
	 * Deny access
	 * Shortcut for timetracking
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('timetracking', $right);
	}



	/**
	 * Check whether user has edit rights for this track
	 * Deny access if right is missing
	 *
	 * @param	Integer		$idTrack
	 */
	public static function restrictEdit($idTrack) {
		if( ! self::isEditAllowed($idTrack) ) {
			self::deny('track:edit');
		}
	}



	/**
	 * Check whether user has edit rights for this track
	 *
	 * @param	Integer		$idTrack
	 * @return	Boolean
	 */
	public static function isEditAllowed($idTrack) {
		$idTrack	= intval($idTrack);
		$track		= TodoyuTimetracking::getTrack($idTrack);
		$idTask		= $track->getTaskID();


		if( ! TodoyuProjectTaskManager::isLocked($idTask) && TodoyuProjectTaskRights::isSeeAllowed($idTask) ) {
			if( $track->isCurrentPersonCreator() ) {
				return Todoyu::allowedAny('timetracking', 'task:editOwn,task:editOwnChargeable');
			} else {
				return Todoyu::allowedAny('timetracking', 'task:editAll,task:editAllChargeable');
			}
		}

		return false;
	}



	/**
	 * Checks whether timetracking generally allowed and in particular for current task
	 *
	 * @param	Integer	$idTask
	 * @return	Boolean
	 */
	public static function isTrackAllowed($idTask) {
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		$idTask	= intval($idTask);
		$running= TodoyuTimetracking::isTaskTrackedByMe($idTask);

		if( $running ) {
			return true;
		}

		return Todoyu::allowed('timetracking', 'task:track') && TodoyuProjectTaskRights::isSeeAllowed($idTask);
	}



	/**
	 * Restricts access to track time on given task
	 *
	 * @param	Integer	$idTask
	 */
	public static function restrictTrack($idTask) {
		if( ! self::isTrackAllowed($idTask)) {
			self::deny('task:track');
		}
	}



	/**
	 * Restricts seeing of given track (depends on see-right of task)
	 *
	 * @param	Integer	$idTrack
	 */
	public static function restrictSee($idTrack) {
		$idTrack= intval($idTrack);
		$idTask	= TodoyuTimetracking::getTrack($idTrack)->getTaskID();

		TodoyuProjectTaskRights::restrictSee($idTask);
	}

}

?>