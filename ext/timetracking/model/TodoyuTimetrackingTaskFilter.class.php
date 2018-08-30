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
 * Timetracking task filter
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTaskFilter {

	/**
	 * Filter condition: Task which have tracks of the person
	 *
	 * @param	Integer		$idPerson
	 * @param	Boolean		$negate
	 * @return	Array		Or FALSE
	 */
	public static function Filter_timetrackedPerson($idPerson, $negate = false) {
		$idPerson	= intval($idPerson);
		$queryParts	= false;

		if( $idPerson !== 0 ) {
			$tables	= array(
				'ext_project_task',
				'ext_timetracking_track'
			);
			$compare= $negate ? '!=' : '=';
			$where	= 'ext_timetracking_track.id_person_create ' . $compare . ' ' . $idPerson;
			$join	= array(
				'ext_timetracking_track.id_task = ext_project_task.id'
			);

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: Task which have track of person of a group
	 *
	 * @param	Array			$groupIDs
	 * @param	Boolean			$negate
	 * @return	Array|Boolean
	 */
	public static function Filter_timetrackedRoles($groupIDs, $negate = false) {
		$queryParts	= false;
		$groupIDs	= TodoyuArray::intExplode(',', $groupIDs, true, true);

		if( sizeof($groupIDs) > 0 ) {
			$tables	= array(
				'ext_project_task',
				'ext_timetracking_track',
				'ext_contact_mm_person_role'
			);
			$where	= 'ext_contact_mm_person_role.id_role IN(' . implode(',', $groupIDs) . ')';
			$join	= array(
				'ext_timetracking_track.id_task = ext_project_task.id',
				'ext_timetracking_track.id_person_create = ext_contact_mm_person_role.id_person'
			);

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * @param	Integer			$min
	 * @param	Boolean			$negate
	 * @return	Array | Boolean
	 */
	public static function Filter_overbookedAbsolute($min, $negate = false) {
		if( ! is_numeric($min) ) {
			return false;
		}

		$min = intval($min);
		$sec = $min * 60;

		$table = '(SELECT id_task, SUM(ext_timetracking_track.workload_tracked) as sum_tracked_time FROM ext_timetracking_track GROUP BY ext_timetracking_track.id_task) as track';

		$tables = array(
			'ext_project_task',
			$table
		);

		$where = 'track.sum_tracked_time > (ext_project_task.estimated_workload + ' . $sec . ')';

		$join = array(
			'track.id_task = ext_project_task.id'
		);

		return array(
			'tables'=> $tables,
			'where'	=> $where,
			'join'	=> $join,
		);
	}



	/**
	 * @param	Integer			$percent
	 * @param	Boolean			$negate
	 * @return	Array | Boolean
	 */
	public static function Filter_overbookedPercent($percent, $negate = false) {
		if( ! is_numeric($percent) ) {
			return false;
		}

		$percent = intval($percent);
		$table = '(SELECT id_task, SUM(ext_timetracking_track.workload_tracked) as sum_tracked_time FROM ext_timetracking_track GROUP BY ext_timetracking_track.id_task) as track';

		$tables = array(
			'ext_project_task',
			$table
		);

		$where = '	   track.sum_tracked_time '
				.'	   >= ('. (1 + intval($percent / 100)) . ' * ext_project_task.estimated_workload ) ';

		$join = array(
			'track.id_task = ext_project_task.id'
		);

		return array(
			'tables'=> $tables,
			'where'	=> $where,
			'join'	=> $join
		);
	}



	/**
	 * Filter for tasks being currently tracked
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array					Query parts
	 */
	public static function Filter_isBeingTracked($value, $negate = false) {
		$trackedTaskIDs	= TodoyuTimetracking::getCurrentTrackingTaskIDs();

		if( ! (sizeof($trackedTaskIDs) > 0)) {
			return array(
				'where'	=> $negate ? '1 = 1' : '0 = 1'
			);
		}

		$trackedTaskIDs	= TodoyuArray::intImplode($trackedTaskIDs, ',');
		$tables	= array('ext_project_task');

		$where	=  ($negate ? ' NOT ' : '') . ' ext_project_task.id IN (' . $trackedTaskIDs . ')';

		return array(
			'tables'	=> $tables,
			'where'		=> $where
		);
	}
}

?>