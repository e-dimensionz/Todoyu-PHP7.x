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
 * Manager for timetracking
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingManager {

	/**
	 * Working table
	 *
	 * @var	String
	 */
	const TABLE		= 'ext_timetracking_track';



	/**
	 * Add time tracking specific information to task array
	 *
	 * @param	Array		$taskData		Task data array
	 * @param	Integer		$idTask			Task ID
	 * @param	Integer		$infoLevel		Task info level
	 * @return	Array
	 */
	public static function addTimetrackingInfosToTask(array $taskData, $idTask, $infoLevel = 0) {
		$idTask		= intval($idTask);
		$task		= TodoyuTimetrackingTaskManager::getTask($idTask);
		$infoLevel	= intval($infoLevel);
		
			// Is task? (there's no timetracking for containers)
		if( $task->isTask() ) {
			if( $task->isTrackedByMe() ) {
				$taskData['class'] .= ' running';
			} elseif( TodoyuRightsManager::isAllowed('timetracking', 'task:seeCurrentTracking') && $task->isTrackedByOthers() ) {
				$taskData['class'] .= ' runningother';
			}

			if( $infoLevel >= 3 ) {
				$task	= TodoyuProjectTaskManager::getTask($idTask);
				$taskData['tracked_time']	= TodoyuTimetracking::getTrackedTaskTime($task->getID());
				$taskData['billable_time']	= TodoyuTimetracking::getTrackedTaskTime($task->getID(), 0, 0, true);
			}
		}

		return $taskData;
	}



	/**
	 * Add timetracking infos to task info data. -More time tracked than estimated? add marking CSS class
	 *
	 * @param	Array		$taskInfos
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function hookAddWorkloadOverbookedWarning(array $taskInfos, $idTask) {
		$idTask	= intval($idTask);

			// Is task? (there's no timetracking for containers)
		$task = TodoyuTimetrackingTaskManager::getTask($idTask);

		if( $task->isTask() ) {
			if( TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance($task->getEstimatedWorkload(), $task->getTrackedTime()) ) {
				$taskInfos['estimated_workload']['className'] .= ' overtimed';
			}
		}

		return $taskInfos;
	}



	/**
	 * Add billable time to taskHeaderExtra of tasks
	 * Hook: dataModifier
	 *
	 * @param	Array		$extras
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function addTimetrackingHeaderExtrasToTask(array $extras, $idTask) {
			// Is task? (there's no timetracking for containers)
		if( TodoyuProjectTaskManager::getTask($idTask)->isTask() ) {
			$time	= TodoyuTimeTracking::getTrackedTaskTime($idTask, 0, 0, true);

			$extras['billableTime']	= array(
				'key'		=> 'billingtime',
				'content'	=> TodoyuTime::formatHours($time)
			);
		}

		return $extras;
	}



	/**
	 * Calculates the string given in format hh:mm:ss (hh:mm) in seconds
	 *
	 * @param	String	$string
	 * @return	Integer
	 */
	public static function calculateTrackedTimeFromString($string) {
		$timeArray = explode(':', $string);

		return is_array($timeArray) ? ($timeArray[0] * TodoyuTime::SECONDS_HOUR + $timeArray[1] * TodoyuTime::SECONDS_MIN + $timeArray[2]) : 0;
	}



	/**
	 * Save workload record
	 *
	 * @param	Array $data
	 */
	public static function saveWorkloadRecord(array $data) {
		Todoyu::db()->doInsert(self::TABLE, $data);
	}



	/**
	 * Check whether task is over-timed
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskOvertimed($idTask) {
		$idTask		= intval($idTask);

		$trackedTime= TodoyuTimetracking::getTrackedTaskTimeTotal($idTask);
		$task		= TodoyuProjectTaskManager::getTask($idTask);

		return $trackedTime > $task->getEstimatedWorkload();
	}



	/**
	 * Add time tracking JS init to page
	 */
	public static function addTimetrackingJsInitToPage() {
		if( TodoyuTimetracking::isTrackingActive() && ! TodoyuRequest::isAjaxRequest() ) {
			$idTask			= TodoyuTimetracking::getTaskID();
			$taskData		= TodoyuTimetracking::getTask()->getTemplateData();
			$trackedTotal	= TodoyuTimeTracking::getTrackedTaskTime($idTask);
			$trackedToday	= TodoyuTimetracking::getTrackedTaskTimeOfDay($idTask, NOW, Todoyu::personid());
			$trackedCurrent	= TodoyuTimetracking::getTrackedTime();

			$jsInitCode	= 'Todoyu.Ext.timetracking.initWithTask(' . json_encode($taskData) . ', ' . $trackedTotal . ', ' . $trackedToday . ', ' . $trackedCurrent . ')';
		} else {
			$jsInitCode	= 'Todoyu.Ext.timetracking.initWithoutTask()';
		}

		TodoyuPage::addJsInit($jsInitCode);
	}



	/**
	 * Get task contextmenu item for start/stop tracking
	 *
	 * @param	{Integer}	$idTask
	 * @return	Array
	 */
	public static function getContextMenuItemStartStop($idTask) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		$items	= array();

			// Ignore deleted tasks
		if( ! $task->isDeleted() ) {
				// Check if task has a trackable status
			if( TodoyuTimetracking::isTrackable($task->getType(), $task->getStatus(), $idTask) && Todoyu::allowed('timetracking', 'task:track') ) {
					// Add stop or start button
				if( TodoyuTimetracking::isTaskTrackedByMe($idTask) ) {
					$items['timetrackstop'] = Todoyu::$CONFIG['EXT']['timetracking']['ContextMenu']['Task']['timetrackstop'];
				} else {
					$items['timetrackstart'] = Todoyu::$CONFIG['EXT']['timetracking']['ContextMenu']['Task']['timetrackstart'];
				}
			}
		}

		return $items;
	}



	/**
	 * Formhook
	 * Add time tracking fields to quickTask
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idTask
	 */
	public static function addWorkloadFieldToQuicktask(TodoyuForm $form, $idTask) {
		$xmlPath	= 'ext/timetracking/config/form/quicktask-tracked.xml';
		$insertForm	= TodoyuFormManager::getForm($xmlPath);

		$workloadDone	= $insertForm->getField('workload_done');
		$startTracking	= $insertForm->getField('start_tracking');

		$form->getFieldset('main')->addField('workload_done', $workloadDone, 'after:id_activity');
		$form->getFieldset('main')->addField('start_tracking', $startTracking, 'after:workload_done');
	}



	/**
	 * Formhook: Handle (save) special fields added to quickTask by time tracking
	 *
	 * @param	Array		$data
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function handleQuicktaskFormSave(array $data, $idTask) {
		$idTask			= intval($idTask);
		$workloadDone	= intval($data['workload_done']);

			// Save already done workload
		if( $workloadDone > 0 ) {
			self::addTrackedWorkload($idTask, $workloadDone);
		}
		unset($data['workload_done']);

			// 'Start tracking' checked? set status accordingly
		if( intval($data['start_tracking']) === 1 ) {
			$data['status'] = STATUS_PROGRESS;
		}

		unset($data['start_tracking']);

		return $data;
	}



	/**
	 * Add already tracked (seconds of) workload to workload record of given task.
	 *
	 * @param	Integer	$idTask
	 * @param	Integer	$workload
	 */
	protected static function addTrackedWorkload($idTask, $workload) {
		$idTask		= intval($idTask);
		$workload	= intval($workload);

		$data	= array(
			'id_person_create'	=> TodoyuAuth::getPersonID(),
			'id_task'			=> $idTask,
			'date_create'		=> NOW,
			'date_update'		=> NOW,
			'date_track'		=> NOW,
			'workload_tracked'	=> $workload
		);

		self::saveWorkloadRecord($data);
	}



	/**
	 * Hook when quick task is saved
	 * Check whether the option 'start tracking' was checked when saving
	 * Start tracking on server and send tracking header
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idProject
	 * @param	Array		$data
	 */
	public static function hookQuickTaskSaved($idTask, $idProject, array $data) {
		if( intval($data['start_tracking']) === 1 ) {
			TodoyuTimetracking::startTask($idTask);

			TodoyuHeader::sendTodoyuHeader('startTracking', 1);
		}
	}



	/**
	 * Remove form field if the user only can edit the chargeable time
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idTrack
	 * @return	Void|Boolean
	 */
	public static function hookModifyTrackFields(TodoyuForm $form, $idTrack) {
		$idTrack	= intval($idTrack);

		if( TodoyuAuth::isAdmin() ) {
			return false;
		}

		if( $idTrack !== 0 ) {
			$track	= TodoyuTimetracking::getTrack($idTrack);

			if( ! $track->isCurrentPersonCreator() ) {
				$form->removeField('date_track', true);
				$form->removeField('comment', true);

				if( ! Todoyu::allowed('timetracking', 'task:editAll') ) {
					$form->removeField('workload_tracked', true);
				}

				if( ! Todoyu::allowed('timetracking', 'task:editAllChargeable') ) {
					$form->removeField('workload_chargeable', true);
				}
			} else {
				if( ! Todoyu::allowed('timetracking', 'task:editOwn') ) {
					$form->removeField('workload_tracked', true);
				}

				if( ! Todoyu::allowed('timetracking', 'task:editOwnChargeable') ) {
					$form->removeField('workload_chargeable', true);
				}

			}
		}
	}



	/**
	 * Check whether a track is editable for the current person
	 *
	 * @param	Integer		$idTrack
	 * @return	Boolean
	 */
	public static function isTrackEditable($idTrack) {
		$idTrack	= intval($idTrack);
		$track		= TodoyuTimetracking::getTrack($idTrack);
		$task		= $track->getTask();

			// Locked overrules admin right
		if( $task->isLocked() ) {
			return false;
		}

		return TodoyuTimetrackingRights::isEditAllowed($idTrack);
		}



	/**
	 * Callback to render content for all requested task tabs
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$info		List of task IDs to render
	 * @return	Array		Content of task tab for requested tasks
	 */
	public static function callbackTaskTab($idTask, array $info) {
		$taskIDs	= TodoyuArray::intval($info);
		$response	= array();

		foreach($taskIDs as $idTask) {
			$response[$idTask] = TodoyuTimetrackingRenderer::renderTaskTab($idTask);
		}

		return $response;
	}



	/**
	 * Callback to render the content for the tracking headlet
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$info		Don't care
	 * @return	String		Content of the headlet
	 */
	public static function callbackHeadletOverlayContent($idTask, $info) {
		$headlet	= new TodoyuTimetrackingHeadletTracking();

		return $headlet->renderOverlayContent();
	}



	/**
	 * Add to attributes array of project preset data list
	 *
	 * @param	Integer		$idProject
	 * @return	Array
	 */
	public static function getProjectPresetDataAttributes($idProject) {
		$idProject	= intval($idProject);
		$project	= TodoyuTimetrackingProjectManager::getProject($idProject);
		$info		= array();

		if( $project->hasTaskPreset() ) {
			$taskPreset	= $project->getTaskPreset();

				// Taskpreset set title
			$info[]	= array(
				'label'		=> 'timetracking.ext.start',
				'value'		=> $taskPreset->getStartTimetrackingLabel(),
				'position'	=> 80
			);
		}

		return $info;
	}



	/**
	 * Load configs of timetracking related filter widgets of contact persons
	 */
	public static function hookLoadProjectFilterConfig() {
		TodoyuFileManager::includeFile('ext/timetracking/config/filters-project.php', true);
	}



	/**
	 * Load configs of timetracking related filter widgets of contact persons
	 */
	public static function hookLoadContactFilterConfig() {
		TodoyuFileManager::includeFile('ext/timetracking/config/filters-contact.php', true);
	}

}

?>