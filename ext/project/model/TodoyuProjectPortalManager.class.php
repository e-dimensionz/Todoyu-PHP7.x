<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * Project manager for portal
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectPortalManager {

	/**
	 * Get task IDs for todo tab
	 *
	 * @return	Array
	 */
	public static function getTodoTaskIDs() {
		$conditions	= Todoyu::$CONFIG['EXT']['project']['portalTodoTabFilters']['assigned'];
		$taskFilterAssigned	= new TodoyuProjectTaskFilter($conditions);

		$conditions			= Todoyu::$CONFIG['EXT']['project']['portalTodoTabFilters']['owner'];
		$taskFilterOwner	= new TodoyuProjectTaskFilter($conditions);

		$conditions = array(
			array(
				'filter'	=> 'filterObject',
				'value'		=> array($taskFilterAssigned)
			),
			array(
				'filter'	=> 'filterObject',
				'value'		=> array($taskFilterOwner)
			),
		);

		$taskFilterMerged = new TodoyuProjectTaskFilter($conditions, 'OR');

		$taskIDs	= $taskFilterMerged->getTaskIDs('');

		return self::getTaskIDsSorted($taskIDs);
	}



	/**
	 * Sort a list of task by date_end and date_deadline
	 * Only use date_end if type is task and date_end is not empty
	 *
	 * @param	Array	$taskIDs
	 * @return	Array
	 */
	private static function getTaskIDsSorted(array $taskIDs) {
		if( sizeof($taskIDs) === 0 ) {
			return array();
		}

		$idList	= implode(',', $taskIDs);

		$fields	= '	id,
					date_end,
					date_deadline,
					`type` = ' . TASK_TYPE_TASK . ' as istask';
		$table	= 'ext_project_task';
		$where	= 'id IN(' . $idList . ')';
		$order	= 'ext_project_task.date_deadline, ext_project_task.date_end';

		$tasks	= Todoyu::db()->getArray($fields, $table, $where, '', $order);

		usort($tasks, array('TodoyuProjectPortalManager', 'taskListingSortCompare'));

		return TodoyuArray::getColumn($tasks, 'id');
	}



	/**
	 * Compare tasks by date
	 * Sort by date_end if it's set and if type is task, else sort by date_deadline
	 *
	 * @param	Array		$taskA
	 * @param	Array		$taskB
	 * @return	Integer
	 */
	private static function taskListingSortCompare($taskA, $taskB){
		$dateA	= $taskA['istask'] && $taskA['date_end'] ? $taskA['date_end'] : $taskA['date_deadline'];
		$dateB	= $taskB['istask'] && $taskB['date_end'] ? $taskB['date_end'] : $taskB['date_deadline'];

		return $dateA === $dateB ? 0 : $dateA < $dateB ? -1 : 1;
	}



	/**
	 * Get number of tasks for todo tabs
	 *
	 * @param	Array		$filtersetIDs		Not needed, but standard
	 * @return	Integer
	 */
	public static function getTodoCount(array $filtersetIDs = array()) {
		$taskIDs	= self::getTodoTaskIDs();

		return sizeof($taskIDs);
	}

}

?>