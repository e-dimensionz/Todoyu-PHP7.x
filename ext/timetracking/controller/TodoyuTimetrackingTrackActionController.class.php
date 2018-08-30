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
 * Timetrack task action controller
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTrackActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('timetracking', 'general:use');
	}



	/**
	 * Start and stop tracking
	 *
	 * @param	Array		$params
	 * @return	String		JSON encoded data array
	 */
	public function trackAction(array $params) {
		TodoyuHeader::sendTypeJSON();

		$start	= intval($params['start']) === 1;
		$idTask	= intval($params['task']);
		$data	= TodoyuArray::assureFromJSON($params['data']);

		TodoyuTimetrackingRights::restrictTrack($idTask);

			// Response data
		$response	= array();

			// Start time tracking of task
		if( $start ) {
			TodoyuTimetracking::startTask($idTask);

			$task	= TodoyuProjectTaskManager::getTask($idTask);

			$response['taskData']		= $task->getTemplateData();
			$response['trackedTotal']	= TodoyuTimeTracking::getTrackedTaskTime($idTask);;
			$response['trackedToday']	= TodoyuTimetracking::getTrackedTaskTimeOfDay($idTask, NOW, Todoyu::personid());
		} else {
				// Stop time tracking of task
			TodoyuTimetracking::stopTask();
		}

		$response['data']	= TodoyuTimetrackingCallbackManager::callAll($idTask, $data);

		return json_encode($response);
	}



	/**
	 * Start timetracking for task
	 *
	 * @param	Array		$params
	 */
	public function startAction(array $params) {
		$idTask	= intval($params['task']);

		TodoyuTimetrackingRights::restrictTrack($idTask);

		TodoyuTimetracking::startTask($idTask);

		$task			= TodoyuProjectTaskManager::getTask($idTask);

		$trackedTotal	= TodoyuTimeTracking::getTrackedTaskTime($idTask);
		$trackedToday	= TodoyuTimetracking::getTrackedTaskTimeOfDay($idTask, NOW);

		TodoyuHeader::sendTodoyuHeader('trackedTotal', $trackedTotal);
		TodoyuHeader::sendTodoyuHeader('trackedToday', $trackedToday);
		TodoyuHeader::sendTodoyuHeader('taskData', $task->getTemplateData());
	}



	/**
	 * Stop currently tracked task
	 *
	 * @param	Array		$params
	 */
	public function stopAction(array $params) {
		TodoyuTimetracking::stopTask();
	}



	/**
	 * Render given track of task
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		$idTask	= intval($params['task']);
		$idTrack= intval($params['track']);

		TodoyuProjectTaskRights::restrictSee($idTask);

		return TodoyuTimetrackingRenderer::renderTrack($idTrack);
	}

}

?>