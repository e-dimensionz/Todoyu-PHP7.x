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
 * Renderer for timetracking extension
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingRenderer {

	/**
	 * Render timetracking tab in task when tab is active
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTaskTab($idTask) {
		$idTask		= intval($idTask);
		$control	= TodoyuTimetrackingRenderer::renderTaskTabControl($idTask);
		$list		= TodoyuTimetrackingRenderer::renderTaskTabList($idTask);

		$tmpl	= 'ext/timetracking/view/tasktab.tmpl';
		$data	= array(
			'idTask'			=> $idTask,
			'trackingControl'	=> $control,
			'tracksList'		=> $list
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render controll box in the task tab
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTaskTabControl($idTask) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		$tmpl	= 'ext/timetracking/view/tasktab-control.tmpl';
		$data	= array(
			'idTask' => $idTask,
			'totalTrackedTime'		=> TodoyuTimetracking::getTrackedTaskTimeTotal($idTask),
			'totalChargeableTime'	=> TodoyuTimetracking::getTrackedTaskTimeTotal($idTask, true),
			'trackable'				=> TodoyuTimetracking::isTrackableStatus($task->getStatus())
		);

			// Task is being tracked by the current user?
		$trackingForMe	= TodoyuTimetracking::isTaskTrackedByMe($idTask);
		if( $trackingForMe ) {
			$data['buttonLabel']	= 'core.global.stop';
			$data['function']		= 'stop';
			$data['class']			= 'stopTracking';
			$data['running']		= true;
			$data['trackedTime']	= TodoyuTimetracking::getTrackedTime();
		} else {
			$data['buttonLabel']	= 'core.global.start';
			$data['function']		= 'start';
			$data['class']			= 'startTracking';
			$data['running']		= false;
			$data['trackedTime']	= 0;

			if( TodoyuTimeTracking::isTaskTrackedByOthers($idTask) ) {
				$data['runningother']	= true;
			}
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render track list in task tab
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTaskTabList($idTask) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);
		$tracks	= TodoyuTimetrackingTaskManager::getTaskTracks($idTask);

		foreach($tracks as $index => $track) {
			$tracks[$index]['editable'] = TodoyuTimetrackingManager::isTrackEditable($track['id']);
		}

		$tmpl	= 'ext/timetracking/view/tasktab-list.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'tracks'	=> $tracks,
			'task'		=> $task->getTemplateData()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render form to edit a track in task tab
	 *
	 * @param	Integer		$idTrack
	 * @return	String
	 */
	public static function renderTaskTabForm($idTrack) {
		$idTrack	= intval($idTrack);
		$track		= TodoyuTimetracking::getTrack($idTrack);
		$trackData	= $track->getTemplateData();

			// Construct form object
		$xmlPath= PATH_EXT_TIMETRACKING . '/config/form/track.xml';
		$form	= TodoyuFormManager::getForm($xmlPath, $idTrack);

			// Load form data
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $trackData, $idTrack);

			// Set form data
		$form->setFormData($formData);
		$form->setRecordID($idTrack);

			// Render
		return $form->render();
	}



	/**
	 * Render a single track (as list item)
	 *
	 * @param	Integer		$idTrack
	 * @return	String
	 */
	public static function renderTrack($idTrack) {
		$idTrack	= intval($idTrack);
		$track		= TodoyuTimetracking::getTrack($idTrack);
		$trackData	= $track->getTemplateData();
		$trackData['editable']	= TodoyuTimeTrackingManager::isTrackEditable($idTrack);

		$tmpl	= 'ext/timetracking/view/tasktab-listitem.tmpl';
		$data	= array(
			'idTask'	=> $trackData['id_task'],
			'track'		=> $trackData,
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>