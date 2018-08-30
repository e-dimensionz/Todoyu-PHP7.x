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
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskExportManager {

	/**
	 * @param	Array	$taskIDs
	 */
	public static function exportCSV(array $taskIDs) {
		$taskIDs	= TodoyuArray::intval($taskIDs);

		$tasksToExport	= self::prepareDataForExport($taskIDs);

		$export		= new TodoyuExportCSV($tasksToExport);

		$export->setFilename('todoyu_task_export_' . date('YmdHis') . '.csv');

		$export->download();
	}



	/**
	 * @param	Array	$taskIDs
	 * @return	Array
	 */
	public static function prepareDataForExport(array $taskIDs) {
		$taskIDs	= TodoyuArray::intval($taskIDs);

		$exportData	= array();

		foreach($taskIDs as $idTask)	 {
			$task			= TodoyuProjectTaskManager::getTask($idTask);

			$exportData[]	= self::parseDataForExport($task);
		}

		return $exportData;
	}



	/**
	 * @param	TodoyuProjectTask	$task
	 * @return	Array
	 */
	protected static function parseDataForExport(TodoyuProjectTask $task) {
		$attributes = TodoyuProjectTaskManager::getTaskAttributes(array(), $task->getID());

		// add export specific data
		$exportData[Todoyu::Label('project.task.attr.id')]		= $task->getID();
		$exportData[Todoyu::Label('core.global.date_update')]	= TodoyuTime::format($task->getDateUpdate(), 'date');
		$exportData[Todoyu::Label('project.task.attr.type')]	= $task->isContainer() ? Todoyu::Label('project.task.container') : Todoyu::Label('project.task.task');
		$exportData[Todoyu::Label('project.ext.project')]		= $task->getProject()->getFullTitle();
		$exportData[Todoyu::Label('project.task.taskno')]		= $task->getTaskNumber(true);
		$exportData[Todoyu::Label('project.task.attr.title')]	= $task->getFullTitle();
		$exportData[Todoyu::Label('project.task.description')]	= TodoyuString::html2text($task->getDescription(), true);

		// add task attributes (with rights check)
		foreach( $attributes as $attribute ) {
			$exportData[Todoyu::Label($attribute['label'])] = $attribute['value'];
		}

		$exportData[Todoyu::Label('project.task.attr.id_parenttask') . ' (' .Todoyu::Label('project.task.attr.id') . ')'] =
				$task->hasParentTask() && TodoyuProjectTaskRights::isSeeAllowed($task->getParentTaskID()) ? $task->getParentTask()->getID() : '';
		$exportData[Todoyu::Label('project.task.attr.id_parenttask')]		=
				$task->hasParentTask() && TodoyuProjectTaskRights::isSeeAllowed($task->getParentTaskID()) ? $task->getParentTask()->getTitle() : '';
		$exportData[Todoyu::Label('project.task.attr.is_acknowledged')]		= $task->isAcknowledged() ? '' : Todoyu::Label('project.task.attr.notAcknowledged');

		if( TodoyuProjectTaskRights::isSeePublicFlagAllowed($task->isPublic())) {
			$publicKey		= $task->isPublic() ? 'public' : 'private';
			$publicTypeKey	= $task->isContainer() ? '.container' : '';
			$exportData[Todoyu::Label('project.task.attr.is_public')]			= Todoyu::Label('project.task.attr.is_public.' . $publicKey . $publicTypeKey);
		}

		$exportData	= TodoyuHookManager::callHookDataModifier('project', 'taskCSVExportParseData', $exportData, array($task));

		return $exportData;
	}
}

?>