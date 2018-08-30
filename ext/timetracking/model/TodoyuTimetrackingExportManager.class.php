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
 * Timetracking ExportManager
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingExportManager {

	/**
	 * Parses timetracking task - data for Export
	 *
	 * @param	Array				$exportData
	 * @param	TodoyuProjectTask	$task
	 * @return	Array
	 */
	public static function parseTaskDataForExport(array $exportData, TodoyuProjectTask $task) {
		$task	= TodoyuTimetrackingTaskManager::getTask($task->getID());

			// Tracked Time
		$exportData[Todoyu::Label('timetracking.ext.attr.workload_tracked')] = TodoyuTime::formatTime($task->getTrackedTime());
			// Chargeable Time
		$exportData[Todoyu::Label('timetracking.ext.attr.workload_chargeable')] = TodoyuTime::formatTime($task->getChargeableTime());

		return $exportData;
	}

}

?>