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
 * Timetracking headlet
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingHeadletTracking extends TodoyuHeadletTypeOverlay {

	/**
	 * Initialize headlet
	 */
	protected function init() {
		$this->setJsHeadlet('Todoyu.Ext.timetracking.Headlet.Timetracking');

			// Add active class if tracking is running
		if( TodoyuTimetracking::isTrackingActive() ) {
			$this->addButtonClass('tracking');
		}
	}



	/**
	 * Render content for overlay box
	 *
	 * @return	String
	 */
	public function renderOverlayContent() {
		if( TodoyuTimetracking::isTrackingActive() ) {
			return $this->renderOverlayContentActive();
		} else {
			return $this->renderOverlayContentInactive();
		}
	}



	/**
	 * Render overlay content for active timetracking
	 *
	 * @return	String
	 */
	private function renderOverlayContentActive() {
		$task			= TodoyuTimetracking::getTask();
		$totalTracked	= TodoyuTimetracking::getTrackedTaskTimeTotal($task->getID(), false, true);

		$tmpl	= 'ext/timetracking/view/headlet-timetracking-active.tmpl';

		$estimatedWorkload	= $task->getEstimatedWorkload();
		if( $estimatedWorkload > 0 ) {
			$percentTracked = round(($totalTracked / $estimatedWorkload) * 100, 0);
		} else {
			$percentTracked	= 0;
		}

		$data	= array(
			'name'		=> $this->getName(),
			'task'		=> $task->getTemplateData(2),
			'tracked'	=> TodoyuTimetracking::getTrackedTaskTimeTotal($task->getID()),
			'tracking'	=> TodoyuTimetracking::getTrackedTime(),
			'percent'	=> $percentTracked
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render overlay content for inactive timetracking
	 *
	 * @return	String
	 */
	private function renderOverlayContentInactive() {
		$tmpl	= 'ext/timetracking/view/headlet-timetracking-inactive.tmpl';

		$data	= array(
			'name'	=> $this->getName(),
			'tasks'	=> $this->getLastTrackedTasks()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get the last tracked tasks for the inactive box
	 *
	 * @return	Array
	 */
	private function getLastTrackedTasks() {
		$numTasks	= intval(Todoyu::$CONFIG['EXT']['timetracking']['headletLastTasks']);

		$query	= '	SELECT
						track.date_track,
						MAX(track.date_track) as last_update,
						SUM(track.workload_tracked) as trackedtime,
						task.id,
						task.id_project,
						task.tasknumber,
						task.title,
						task.tasknumber,
						task.type,
						task.status,
						project.title as projecttitle
					FROM
						`ext_timetracking_track` track
					LEFT JOIN
						`ext_project_task` task
							ON track.id_task = task.id
					LEFT JOIN
						`ext_project_project` project
							ON task.id_project = project.id
					WHERE
						track.id_person_create	= ' . Todoyu::personid() . ' AND
						task.type				= ' . TASK_TYPE_TASK . ' AND
						task.deleted			= 0 AND
						track.date_track		<= ' . NOW . '
					GROUP BY
						track.id_task
					ORDER BY
						last_update DESC
					LIMIT
						0,' . $numTasks;

		$resource	= Todoyu::db()->query($query);
		$tasks		= Todoyu::db()->resourceToArray($resource);

		foreach($tasks as $index => $task) {
			$tasks[$index]['isTrackable'] = TodoyuTimetracking::isTrackable(TASK_TYPE_TASK, $task['status'], $task['id']);

			if( intval($tasks[$index]['id']) === 0 ) {
				unset($task[$index]);
			}
		}

		return $tasks;
	}



	/**
	 * Get healet label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Todoyu::Label('timetracking.ext.headlet.label');
	}

}

?>