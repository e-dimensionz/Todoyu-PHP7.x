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
 * Quickcreate task controller
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectQuickCreateTaskActionController extends TodoyuActionController {

	/**
	 * Initialize quickCreate controller: restrict access (project extension must be allowed)
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('project', 'general:use');
	}



	/**
	 * Render form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		$idProject	= intval($params['project']);
		$isUpdate	= intval($params['update']) === 1;

		if( $idProject > 0 ) {
			TodoyuProjectTaskRights::restrictAddToProject($idProject);
		} else {
			TodoyuProjectTaskRights::restrictShowPopupForm();
		}

		if( $isUpdate ) {
				// Get form data to preserve entered data
			parse_str(trim($params['data']), $formData);
			$formData	= TodoyuArray::assure($formData['task']);
		} else {
			TodoyuHookManager::callHook('project', 'quickcreatetask', array($idProject));
			$formData	= array();
		}

		$form	= TodoyuProjectTaskManager::getQuickCreateForm($idProject, $formData);

		return $form->render();
	}



	/**
	 * Save task
	 *
	 * @param	Array			$params
	 * @return	Void|String		Failure returns re-rendered form with error messages
	 */
	public function saveAction(array $params) {
		$data		= $params['task'];

		$idTask		= intval($data['id']);
		$idProject	= intval($data['id_project']);

		if( $idProject == 0 ) {
			$form	= TodoyuProjectTaskManager::getQuickCreateForm();
			$form->setFormData($data);
		} else {
			if( $idTask > 0 ) {
				TodoyuProjectTaskRights::restrictEdit($idTask);
			} else {
				TodoyuProjectTaskRights::restrictAddToProject($idProject);
			}

				// Create a cache record for the buildform hooks
			$task = TodoyuProjectTaskManager::getTask(0);
			$task->injectData($data);
			$cacheKey	= TodoyuRecordManager::makeClassKey('TodoyuProjectTask', 0);
			TodoyuCache::set($cacheKey, $task);

				// Get form object, call save hooks, set form data
			$form	= TodoyuProjectTaskManager::getQuickCreateForm();

			$form->setFormData($data);
		}
			// Check if form is valid
		if( $form->isValid() ) {
				// If form is valid, get form storage data and update task
			$storageData= $form->getStorageData();

				// Save task
			$idTaskNew	= TodoyuProjectTaskManager::saveTask($storageData);

			TodoyuHeader::sendTodoyuHeader('idTask', $idTaskNew);
			TodoyuHeader::sendTodoyuHeader('idTaskOld', $idTask);
			TodoyuHeader::sendTodoyuHeader('idProject', $idProject);
		} else {
				// Form data not valid
			TodoyuHeader::sendTodoyuHeader('idTask', $idTask);
			TodoyuHeader::sendTodoyuHeader('idProject', $idProject);
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}

}

?>