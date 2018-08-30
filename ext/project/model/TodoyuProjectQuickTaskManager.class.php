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
 * Manage quicktasks
 *
 * @package		Todoyu
 * @subpackage	QuickTask
 */
class TodoyuProjectQuickTaskManager {

	/**
	 * Get quicktask form which is customized for current user
	 *
	 * @param	Integer			$idProject
	 * @param	Array			$formData
	 * @return	TodoyuForm
	 */
	public static function getQuickTaskForm($idProject = 0, array $formData = array()) {
		$idProject	= intval($idProject);

			// Construct form object
		$xmlPath	= 'ext/project/config/form/quicktask.xml';
		$form		= TodoyuFormManager::getForm($xmlPath);

			// Load form with extra field data
		$xmlPathInsert	= 'ext/project/config/form/field-id_project.xml';
		$insertForm		= TodoyuFormManager::getForm($xmlPathInsert);

			// If person can add tasks in all project, show autocomplete field, else only a select element
		if( Todoyu::allowed('project', 'addtask:addTaskInAllProjects') ) {
			$field	= $insertForm->getField('id_project_ac');
		} else {
			$field	= $insertForm->getField('id_project_select');
		}

			// Remove normal project field
		$form->removeField('id_project', true);

			// Add custom project field
		$form->getFieldset('main')->addField('id_project', $field, 'before:title');

			// Load default data
		$defaultFormData	= TodoyuProjectTaskManager::getTaskDefaultData(0, $idProject, TASK_TYPE_TASK, true);

			// Load data by form hooks
		$defaultFormData	= TodoyuFormHook::callLoadData($xmlPath, $defaultFormData);

			// Check if project id was set by a hook
		if( $idProject === 0 ) {
			$idProject = intval($defaultFormData['id_project']);
		}

			// Set project ID, if given and allowed to user
		if( $idProject !== 0 && TodoyuProjectTaskRights::isAddInProjectAllowed($idProject, false) ) {
			$defaultFormData['id_project']	= $idProject;
		}

			// Ensure the preset project allows for adding tasks
		if( !TodoyuProjectTaskRights::isAddInProjectAllowed($defaultFormData['id_project']) ) {
			$defaultFormData['id_project']	= 0;
		}

		$formData	= TodoyuArray::mergeEmptyFields($formData, $defaultFormData);

		$form->setFormData($formData);

		return $form;
	}



	/**
	 * Adds mandatory task data to that received from quicktask form, saves new task to DB
	 *
	 * @param	Array	$formData
	 * @return	Integer
	 */
	public static function save(array $formData) {
			// Add empty task to have a task ID to work with
		$idProject	= intval($formData['id_project']);
		$project	= TodoyuProjectProjectManager::getProject($idProject);

		$firstData	= array(
			'id_project'	=> $idProject,
			'id_parenttask'	=> 0
		);

		$idTask		= TodoyuProjectTaskManager::addTask($firstData);

		$formData['id']			= $idTask;
		$formData['date_start']	= NOW;

			// Assign to current user. If not allowed, use fallback later
		if( Todoyu::allowed('project', 'edittaskdetail:editPersonAssigned') ) {
			$formData['id_person_assigned']	= Todoyu::personid();
		}

		$durationInDays	= Todoyu::$CONFIG['EXT']['project']['quicktask']['durationDays'];

			// Try to get data from task preset
		if( $project->hasTaskPreset() ) {
			$taskPreset	= $project->getTaskPreset();

			if( $taskPreset->hasQuickTaskDurationDays() ) {
				$durationInDays	= $taskPreset->getQuickTaskDurationDays();
			}
		} else {
			if( intval($formData['task_done']) !== 1 ) {
				$formData['status'] = Todoyu::$CONFIG['EXT']['project']['taskDefaults']['statusQuickTask'];
			}
		}

			// If task already done: set also date_end
		if( intval($formData['task_done']) === 1 ) {
			$formData['status']		= STATUS_DONE;
			$formData['date_end']	= NOW;
		}
		unset($formData['task_done']);

			// Calculate end dates depending on the
		$dateEnd					= TodoyuTime::getDayStart(NOW + ($durationInDays * TodoyuTime::SECONDS_DAY));
		$formData['date_end']		= $dateEnd;
		$formData['date_deadline']	= $dateEnd;

			// Call form hooks to save external data
		$xmlPath	= 'ext/project/config/form/quicktask.xml';
		$formData	= TodoyuFormHook::callSaveData($xmlPath, $formData, $idTask);

			// Save task to DB
		TodoyuProjectTaskManager::saveTask($formData);

		return $idTask;
	}



	/**
	 * Get first available activity ID
	 * If no activity is entered and no default is defined, we just use the first one
	 *
	 * @return	Integer
	 */
	private static function getFirstAvailableActivityID() {
		$field	= 'id';
		$table	= 'ext_project_activity';
		$where	= 'deleted = 0';
		$order	= 'id ASC';
		$limit	= 1;

		return intval(Todoyu::db()->getFieldValue($field, $table, $where, '', $order, $limit));
	}

}

?>