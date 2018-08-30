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
 * Project View Helper
 * Helper functions for project rendering
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProjectViewHelper {

	/**
	 * Get config array for one status option
	 *
	 * @param	Integer		$index
	 * @param	String		$statusKey
	 * @param	String		$label
	 * @return	Array
	 */
	public static function getStatusOption($index, $statusKey, $label) {
		$option = array(
			'index'		=> $index,
			'value'		=> $index,
			'key'		=> $statusKey,
			'class'		=> 'status' . ucfirst($statusKey),
			'label'		=> $label
		);

		return $option;
	}



	/**
	 * Get options of project persons (assigned)
	 *
	 * @param	TodoyuFormElement	$formElement
	 * @return	Array
	 */
	public static function getProjectPersonOptions(TodoyuFormElement $formElement) {
		$formData	= $formElement->getForm()->getFormData();
		$idProject	= intval($formData['id_project']);

		$persons= TodoyuProjectProjectManager::getProjectPersons($idProject, true);
		$options= array();

		foreach($persons as $person) {
			$options[] = array(
				'value'	=> $person['id'],
				'label'	=> TodoyuContactPersonManager::getLabel($person['id'])
			);
		}

		return $options;
	}



	/**
	 * Get label for person in project
	 *
	 * @param	TodoyuFormElement	$formElement		The form element
	 * @param	Array				$data				Form data for the current record to be labeled
	 * @return	String				Record label
	 */
	public static function getProjectPersonLabel(TodoyuFormElement $formElement, array $data) {
		$idPerson	= intval($data['id']);

		if( $idPerson === 0 ) {
			return Todoyu::Label('project.ext.attr.persons.new');
		} else {
			$idRole		= intval($data['id_role']);
			$projectData= $formElement->getForm()->getFormData();
			$idProject	= intval($projectData['id']);

			return TodoyuProjectProjectManager::getProjectPersonLabel($idPerson, $idProject, $idRole);
		}
	}



	/**
	 * Get project status options. Only allowed "changeto" statuses and currently set one are available
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getProjectStatusOptions(TodoyuFormElement $field) {
		$values		= $field->getValue();
		$idStatus	= intval($values[0]);
		$options	= array();
		$statuses	= TodoyuProjectProjectStatusManager::getStatuses($idStatus);

		foreach($statuses as $statusID => $statusKey) {
			$options[] = array(
				'value'		=> $statusID,
				'label'		=> TodoyuProjectProjectStatusManager::getStatusLabel($statusKey)
			);
		}

		return $options;
	}



	/**
	 * Get projectRoles options
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getProjectroleOptions(TodoyuFormElement $field) {
		$roles			= TodoyuProjectProjectroleManager::getProjectroles();
		$reformConfig	= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);
		$roles	= TodoyuArray::reform($roles, $reformConfig);

		return $roles;
	}



	/**
	 * Get options for all projectRoles
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getExtConfProjectRoles(TodoyuFormElement $field) {
		$roles			= TodoyuProjectProjectroleManager::getProjectroles(true);
		$reformConfig	= array(
			'title'	=> 'label',
			'id'	=> 'value'
		);

		return TodoyuArray::reform($roles, $reformConfig);
	}



	/**
	 * Get work types from extension configuration
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getExtConfActivities(TodoyuFormElement $field) {
		$activities	= TodoyuProjectActivityManager::getAllActivities();
		$reformConfig	= array(
			'title'	=> 'label',
			'id'	=> 'value'
		);

		return TodoyuArray::reform($activities, $reformConfig);
	}



	/**
	 * Get projects which are available for the person to as options
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getAvailableProjectOptions(TodoyuFormElement $field) {
		$projectIDs	= TodoyuProjectProjectManager::getProjectIDsForTaskAdd();
		$options	= array();

		foreach($projectIDs as $idProject) {
			$project	= TodoyuProjectProjectManager::getProject($idProject);

			$options[]	= array(
				'value'	=> $idProject,
				'label'	=> $project->getFullTitle()
			);
		}

		return $options;
	}



	/**
	 * Get project label for quickTask autoCompleter suggestion
	 *
	 * @param	Integer		$idProject
	 * @return	String
	 */
	public static function getQuicktaskAutocompleteLabel($idProject) {
		$idProject	= intval($idProject);
		$label		= '';

		if( $idProject > 0 ) {
			$label	= TodoyuProjectProjectManager::getLabel($idProject);
		}

		return $label;
	}



	/**
	 * Get projectRoles options
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getTaskPresetOptions(TodoyuFormElement $field) {
		$presets	= TodoyuProjectTaskPresetManager::getAllTaskPresets();
		$reformConfig	= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);
		$options	= TodoyuArray::reform($presets, $reformConfig);
		$options	= TodoyuArray::prependSelectOption($options, 'project.ext.taskpreset.noneForProject');

		return $options;
	}

}

?>