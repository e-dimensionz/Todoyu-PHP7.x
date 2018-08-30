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
 * Helper functions for comment views
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentViewHelper {

	/**
	 * Get option array of persons which can receive the comment email (project members)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getEmailReceiverOptions(TodoyuFormElement $field) {
		$idTask		= intval($field->getForm()->getHiddenField('id_task'));
		$options	= array();

		$rightTask		= Todoyu::allowed('comment', 'sendEmail:task');
		$rightProject	= Todoyu::allowed('comment', 'sendEmail:project');
		$rightInternals	= Todoyu::allowed('comment', 'sendEmail:internal');
		$personIDs		= TodoyuCommentCommentManager::getEmailReceiverIDs($idTask, $rightTask, $rightProject, $rightInternals);

		foreach($personIDs as $idPerson) {
			$person	= TodoyuContactPersonManager::getPerson($idPerson);
			$options[]	= array(
				'value'	=> $idPerson,
				'label'	=> $person->getLabel(true, true)
			);
		}

		$options	= TodoyuArray::sortByLabel($options, 'label');

		return $options;
	}



	/**
	 * Get option of task owner as comment email receiver
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getTaskOwnerEmailOption(TodoyuFormElement $field) {
		return TodoyuProjectTaskViewHelper::getOwnerEmailOption($field);
	}



	/**
	 * Get option array for feedback select in comment edit form
	 * The options are grouped in main groups with contain the options for
	 * the persons
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getFeedbackPersonsGroupedOptions(TodoyuFormElement $field) {
		$formData	= $field->getForm()->getFormData();
		
		$idTask		= intval($formData['id_task']);
		$idProject	= TodoyuProjectTaskManager::getProjectID($idTask);
		$options	= array();

			// Task persons
		if( Todoyu::allowed('comment', 'requestFeedback:task') ) {
			$groupLabelTask	= Todoyu::Label('comment.ext.group.taskmembers');
			$taskPersons	= TodoyuProjectTaskManager::getTaskPersons($idTask, true);
			foreach($taskPersons as $person) {
				$options[$groupLabelTask][$person['id']] = array(
					'value'	=> $person['id'],
					'label'	=> TodoyuContactPersonManager::getLabel($person['id'], false, true)
				);
			}
		}


			// Get project persons
		if( Todoyu::allowed('comment', 'requestFeedback:project') ) {
			$groupLabelProject	= Todoyu::Label('comment.ext.group.projectmembers');
			$projectPersons		= TodoyuProjectProjectManager::getProjectPersons($idProject, true, true);
			foreach($projectPersons as $person) {
				$options[$groupLabelProject][$person['id']] = array(
					'value'	=> $person['id'],
					'label'	=> TodoyuContactPersonManager::getLabel($person['id'])
				);
			}
		}


			// Get staff persons (employees of internal company)
		if( Todoyu::allowed('comment', 'requestFeedback:internal') ) {
			$groupLabelEmployee				= Todoyu::Label('comment.ext.group.employees');
			$options[$groupLabelEmployee]	=  TodoyuContactViewHelper::getInternalPersonOptions($field);;
		}

		return $options;
	}



	/**
	 * Get task owner option for feedback select
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getFeedbackOwnerOption(TodoyuFormElement $field) {
		$idTask		= intval($field->getForm()->getHiddenField('id_task'));
		$taskOwner	= TodoyuProjectTaskManager::getTaskOwner($idTask);

		$option = array(
			0 => array(
				'value'	=> $taskOwner[0]['id'],
				'label'	=> TodoyuContactPersonManager::getLabel($taskOwner[0]['id'])
			)
		);

		return $option;
	}



	/**
	 * Get task person options for fallback config
	 * Container task person labels: owner, creator and assigned
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getFallbackTaskPersonOptions(TodoyuFormElement $field) {
		return array(
			array(
				'value'	=> 'owner',
				'label'	=> 'comment.ext.fallback.task.owner'
			),
			array(
				'value'	=> 'creator',
				'label'	=> 'comment.ext.fallback.task.creator'
			),
			array(
				'value'	=> 'assigned',
				'label'	=> 'comment.ext.fallback.task.assigned'
			)
		);
	}



	/**
	 * Get comment fallbacks as options
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getFallbackOptions(TodoyuFormElement $field) {
		$fallbacks	= TodoyuCommentFallbackManager::getAllFallbacks();
		$reform		= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($fallbacks, $reform);
	}



	/**
	 * Get fallback options for config in project
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getFallbackOptionsForProject(TodoyuFormElement $field) {
		$options	= self::getFallbackOptions($field);
		$options	= TodoyuArray::prependSelectOption($options, 'comment.ext.fallback.noneForProject');

		return $options;
	}

}

?>