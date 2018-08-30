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
 * Render class for task elements
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskRenderer {

	/**
	 * Render task for listing
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderListingTask($idTask) {
		$idTask		= intval($idTask);

				// Get some task information
		$isExpanded	= TodoyuProjectTaskManager::isTaskExpanded($idTask);
		$taskData	= TodoyuProjectTaskManager::getTaskInfoArray($idTask, 3);

			// Prepare data array for template
		$tmpl	= 'ext/project/view/task-listing-item.tmpl';
		$data	= array(
			'task'				=> $taskData,
			'isExpanded'		=> $isExpanded,
			'subtasks'			=> '',
			'taskIcons'			=> TodoyuProjectTaskManager::getAllTaskIcons($idTask),
		);

			// Render details if task is expanded
		if( $isExpanded ) {
			$activeTab		= TodoyuProjectPreferences::getActiveItemTab($idTask, TodoyuProjectTaskManager::getTask($idTask)->getTypeKey());
			$data['details']= TodoyuProjectTaskRenderer::renderTaskDetail($idTask, $activeTab);
			$data['task']['class'] .= ' expanded';
		}

		$data	= TodoyuHookManager::callHookDataModifier('project', 'task.dataBeforeRendering', $data, array($idTask, 'renderListingTask'));

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render task header
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$isListing		Render header for listing item (not for project tree view)
	 * @return	String
	 */
	public static function renderHeader($idTask, $isListing = false) {
		$idTask		= intval($idTask);

		if( $isListing ) {
			$tmpl	= 'ext/project/view/task-listing-header.tmpl';
		} else {
			$tmpl	= 'ext/project/view/task-header.tmpl';
		}

		$data	= array(
			'task'		=> TodoyuProjectTaskManager::getTaskInfoArray($idTask, 3),
			'taskIcons'	=> TodoyuProjectTaskManager::getAllTaskIcons($idTask),
		);

		$data	= TodoyuHookManager::callHookDataModifier('project', 'task.dataBeforeRendering', $data, array($idTask, 'renderHeader'));

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render task list
	 *
	 * @param	Array	$taskIDs
	 * @return	String
	 */
	public static function renderTaskListing(array $taskIDs) {
		$taskIDs	= TodoyuArray::intval($taskIDs, true, true);
		$tasksHTML	= array();

		foreach($taskIDs as $idTask) {
			if( TodoyuProjectTaskRights::isSeeAllowed($idTask) ) {
				$tasksHTML[] = self::renderListingTask($idTask);
			}
		}

		$tmpl	= 'ext/project/view/task-listing.tmpl';
		$data	= array(
			'tasks'		=> $tasksHTML
		);

			// Add context menu init scripts
		$data['javascript'] = 'Todoyu.Ext.project.ContextMenuTask.attach();';

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render details of given task
	 *
	 * @param	Integer		$idTask
	 * @param	String		$activeTab
	 * @return	String
	 */
	public static function renderTaskDetail($idTask, $activeTab = '') {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		$tmpl	= 'ext/project/view/task-details.tmpl';
		$data	= array(
			'task'		=> $task,
			'idTask'	=> $idTask,
			'taskData'	=> self::renderTaskData($idTask)
		);

			// Add tabs from registered tab configurations
		if( $task->hasTabs() ) {
			$data['tabs'] = TodoyuContentItemTabRenderer::renderTabs('project', $task->getTypeKey(), $idTask, $activeTab);
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render the task data
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTaskData($idTask) {
		$idTask		= intval($idTask);
		$task		= TodoyuProjectTaskManager::getTask($idTask);

		$attributes	= TodoyuProjectTaskManager::getAllTaskAttributes($idTask);
		$attributes	= TodoyuArray::sortByLabel($attributes, 'position');

		$tmpl	= 'ext/project/view/task-data.tmpl';
		$data	= array(
			'task'		=> $task->getTemplateData(0),
			'attributes'=> $attributes
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render the task edit form
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$type		Task type (container/task)
	 * @param	Integer		$idProject
	 * @param	Integer		$idParentTask
	 * @return	String
	 */
	public static function renderTaskEditForm($idTask, $type = TASK_TYPE_TASK, $idProject = 0, $idParentTask = 0) {
		$idTask			= intval($idTask);
		$idProject		= intval($idProject);
		$idParentTask	= intval($idParentTask);

		$form	= TodoyuProjectTaskManager::getTaskEditForm($idTask, $type, $idProject, $idParentTask);

			// Render
		$tmpl	= 'ext/project/view/task-edit.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'formhtml'	=> $form->render()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get edit form for task
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$type
	 * @return	TodoyuForm
	 */
	public static function getEditTaskForm($idTask, $type) {
		return TodoyuProjectTaskManager::getTaskEditForm($idTask, $type);
	}



	/**
	 * Render edit form to edit a new task or container. This form is wrapped by
	 * the "detail" and "data" div as used in detail view
	 *
	 * @param	Integer		$idProject
	 * @param	Integer		$idParentTask
	 * @param	Integer		$type
	 * @param	Integer		$status
	 * @return	String
	 */
	public static function renderNewTaskEditForm($idProject, $idParentTask = 0, $type = TASK_TYPE_TASK, $status = STATUS_OPEN) {
		$idTask		= 0;

			// Render form for new empty task
		$formHtml	= self::renderTaskEditForm($idTask, $type, $idProject, $idParentTask);

			// Render form into detail wrapper
		$tmpl	= 'ext/project/view/task-detail-data-wrap.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'task'	=> array(
				'status'	=> $status
			),
			'formHtml'	=> $formHtml
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>