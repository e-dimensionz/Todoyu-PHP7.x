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
 * Quicktask controller
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectQuicktaskActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access (project extension must be allowed)
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('project', 'general:use');
	}



	/**
	 * Get quicktask form rendered
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		$idProject	= intval($params['project']);
		$isUpdate	= intval($params['update']) === 1;

		if( $idProject !== 0 ) {
			TodoyuProjectTaskRights::restrictAddToProject($idProject);
		} else {
			Todoyu::restrict('project', 'addtask:addTaskInOwnProjects');
		}

		if( $isUpdate ) {
				// Get form data to preserve entered data
			parse_str(trim($params['data']), $formData);
			$formData	= TodoyuArray::assure($formData['quicktask']);
		} else {
			TodoyuHookManager::callHook('project', 'quicktask', array($idProject));
			$formData	= array();
		}

		$form	= TodoyuProjectQuickTaskManager::getQuickTaskForm($idProject, $formData);

		return $form->render();
	}



	/**
	 * Save quick task
	 *
	 * @param	Array			$params
	 * @return	Void|String		Failure returns re-rendered form with error messages
	 */
	public function saveAction(array $params) {
		$params['quicktask']['start_tracking']	= intval($params['quicktask']['start_tracking']);
		$params['quicktask']['task_done']		= intval($params['quicktask']['task_done']);

		$formData	= $params['quicktask'];
		$idProject	= intval($formData['id_project']);

		if( $idProject !== 0 ) {
			TodoyuProjectTaskRights::restrictAddToProject($idProject);
		}

			// Get form object, set data
		$form	= TodoyuProjectQuickTaskManager::getQuickTaskForm();
		$form->setFormData($formData);

			// Validate, save workload record
		if( $form->isValid() ) {
			$storageData	= $form->getStorageData();
			$idTask			= TodoyuProjectQuickTaskManager::save($storageData);

			$idProject	= intval($storageData['id_project']);

			TodoyuHeader::sendTodoyuHeader('idTask', $idTask);
			TodoyuHeader::sendTodoyuHeader('idProject', $idProject);

				// Call hook when quicktask is saved
			TodoyuHookManager::callHook('project', 'quicktask.saved', array($idTask, $idProject, $storageData));
		} else {
				// Error detected, Re-render form
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}

}

?>