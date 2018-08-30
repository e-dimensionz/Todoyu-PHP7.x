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
 * Daytracks manager
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */
class TodoyuDaytracksManager {

	/**
	 * Get information about all tasks which have been tracked today
	 * The following information is provided in the array keys:
	 *	[id]			Task ID
	 *	[title]			Task title
	 *	[tasknumber]	Task number
	 *	[status]		Task status key (integer)
	 *	[id_project]	Project ID
	 *	[project]		Project title
	 *	[company]		Company shortname
	 *	[time]			Tracked time today
	 *
	 * @return	Array
	 */
	public static function getTodayTrackedTasks() {
		$range	= TodoyuTime::getDayRange(NOW);

		$fields	= '	t.id,
					t.title,
					t.status,
					t.tasknumber,
					t.type,
					p.id as id_project,
					p.title as project,
					c.shortname as company,
					SUM(tr.workload_tracked) as `time`';

		$tables	= '	ext_project_task t,
					ext_timetracking_track tr,
					ext_project_project p,
					ext_contact_company c';

		$where	= '		t.id	= tr.id_task
					AND	tr.date_track BETWEEN ' . $range['start'] . ' AND ' . $range['end'] .
				  ' AND	tr.id_person_create	= ' . Todoyu::personid() .
				  ' AND	t.id_project		= p.id
					AND	p.id_company		= c.id';

		$group	= 't.id';
		$order	= 'tr.date_create';

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order);
	}



	/**
	 * Check whether a task has already been tracked today
	 * (has a record in the database)
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskTrackedToday($idTask) {
		$idTask	= intval($idTask);
		$range	= TodoyuTime::getDayRange(NOW);

		$fields	= 'id';
		$table	= TodoyuTimetracking::TABLE;
		$where	= '		id_task	= ' . $idTask .
				  ' AND	date_update BETWEEN ' . $range['start'] . ' AND ' . $range['end'] .
				  ' AND	id_person_create	= ' . Todoyu::personid();

		return Todoyu::db()->hasResult($fields, $table, $where);
	}



	/**
	 * Get information array about the current tracked task if
	 * its not saved already
	 *
	 * @return	Array		Or false if no tracking is running or track is already saved
	 */
	public static function getCurrentTrackedUnsavedTask() {
		$idTask	= TodoyuTimetracking::getTaskID();
		$data	= false;

		if( $idTask > 0 ) {
			if( ! self::isTaskTrackedToday($idTask) ) {
				$fields	= '	t.id,
							t.title,
							t.status,
							t.tasknumber,
							t.type,
							p.id as id_project,
							p.title as project,
							c.shortname as company';
				$tables	= '	ext_project_task t,
							ext_project_project p,
							ext_contact_company c';
				$where	= '		t.id			= ' . $idTask .
						  ' AND	t.id_project	= p.id
							AND	p.id_company	= c.id';

				$data	= Todoyu::db()->getRecordByQuery($fields, $tables, $where);

				$data['time'] = TodoyuTimetracking::getTrackedTime();
			}
		}

		return $data;
	}



	/**
	 * Callback to render the daytracks panelwidget content
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$info		Don't care
	 * @return	String		Content of panelwidget
	 */
	public static function callbackTrackingToggle($idTask, $info) {
		/** @var	TodoyuDaytracksPanelWidgetDaytracks $panelWidget */
		$panelWidget = TodoyuPanelWidgetManager::getPanelWidget('daytracks', 'Daytracks');

		return $panelWidget->getContent();
	}

}

?>