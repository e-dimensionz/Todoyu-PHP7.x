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
 * View helper for task
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskViewHelper {

	/**
	 * Get config array for one status option
	 *
	 * @param	Integer		$index
	 * @param	String		$statusKey
	 * @param	String		$label
	 * @return	Array
	 */
	public static function getStatusOption($index, $statusKey, $label) {
		return TodoyuProjectProjectViewHelper::getStatusOption($index, $statusKey, $label);
	}



	/**
	 * Get task status options
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getTaskStatusOptions(TodoyuFormElement $field) {
		$values			= $field->getValue();
		$currentStatus	= intval($values[0]);
		$idTask			= $field->getForm()->getRecordID();

		$type		= $idTask === 0 ? 'create' : 'changeto';
		$statuses	= TodoyuProjectTaskStatusManager::getStatuses($type, $currentStatus);
		$options	= array();

		foreach($statuses as $statusID => $statusKey) {
			$options[] = array(
				'value'		=> $statusID,
				'label'		=> TodoyuProjectTaskStatusManager::getStatusLabel($statusKey)
			);
		}

		return $options;
	}



	/**
	 * Get options of all persons somehow involved in a task
	 *
	 * @param	TodoyuFormElement $field
	 * @return	Array
	 */
	public static function getTaskPersonOptions(TodoyuFormElement $field) {
		$formData	= $field->getForm()->getFormData();
		$idTask		= intval($formData['id']);

		$options	= array();
		$persons	= TodoyuProjectTaskManager::getTaskPersons($idTask);

		foreach($persons as $person) {
			$options[] = array(
				'value'	=> $person['id'],
				'label'	=> TodoyuContactPersonManager::getLabel($person['id'], false, true)
			);
		}

		return $options;
	}



	/**
	 * Get options array for task owner person selector
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getTaskOwnerOptions(TodoyuFormElement $field) {
		return self::getPersonAssignedGroupedOptions($field);
	}



	/**
	 * Get options array for assigned person selector, options are grouped into: task members, project persons, all staff persons
	 *
	 * @param	TodoyuFormElement		$field
	 * @return	Array
	 */
	public static function getPersonAssignedGroupedOptions(TodoyuFormElement $field) {
		$options	= array();

			// TaskMember persons
		$groupLabel	= Todoyu::Label('comment.ext.group.taskmembers');
		$options[$groupLabel]	= self::getTaskPersonOptions($field);

			// Get project persons
		$groupLabel	= Todoyu::Label('comment.ext.group.projectmembers');
		$options[$groupLabel]	= TodoyuProjectProjectViewHelper::getProjectPersonOptions($field);

			// Get staff persons (employees of internal company)
		if( TodoyuAuth::isInternal() || Todoyu::allowed('contact', 'person:seeAllInternalPersons') ) {
			$groupLabel	= Todoyu::Label('comment.ext.group.employees');
			$options[$groupLabel]	= TodoyuContactViewHelper::getInternalPersonOptions($field);
		}

		return $options;
	}



	/**
	 * Get stored activities as options array
	 *
	 * @param	TodoyuFormElement		$field
	 * @return	Array
	 */
	public static function getTaskActivityOptions(TodoyuFormElement $field) {
		$activities	= TodoyuProjectActivityManager::getAllActivities();
		$reformConfig	= array(
			'title'	=> 'label',
			'id'	=> 'value'
		);

		return TodoyuArray::reform($activities, $reformConfig);
	}



	/**
	 * Get option of task owner as comment email receiver
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getOwnerEmailOption(TodoyuFormElement $field) {
		$idTask		= intval($field->getForm()->getHiddenField('id_task'));
		$taskOwner	= TodoyuProjectTaskManager::getTaskOwner($idTask);

		$option = array(
			array(
				'value'		=> $taskOwner[0]['id'],
				'label'		=> TodoyuContactPersonManager::getLabel($taskOwner[0]['id'], true, true)
			)
		);

		return $option;
	}



	/**
	 * Get filtered task autocompletion suggestions to given input
	 *
	 * @param	String	$input
	 * @param	Array	$formData
	 * @param	String	$name
	 * @return	Array
	 * @deprecated
	 * @see		TodoyuProjectTaskManager
	 */
	public static function autocompleteProjectTasks($input, array $formData, $name = '') {
		return TodoyuProjectTaskManager::autocompleteProjectTasks($input, $formData, $name);
	}

}

?>