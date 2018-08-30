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
 * ActionController for project preferences
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProjectActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access (project extension must be allowed)
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('project', 'general:use');
	}



	/**
	 * Edit project
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idProject	= intval($params['project']);

		TodoyuProjectProjectRights::restrictEdit($idProject);

		return TodoyuProjectProjectRenderer::renderProjectEditForm($idProject);
	}



	/**
	 * Save project (new or edit)
	 *
	 * @param	Array		$params
	 * @return	String		Form content if form is invalid
	 */
	public function saveAction(array $params) {
		$data		= $params['project'];
		$idProject	= intval($data['id']);

			// Check rights
		if( $idProject === 0 ) {
			TodoyuProjectProjectRights::restrictAdd();
		} else {
			TodoyuProjectProjectRights::restrictEdit();
		}

			// Construct form object
		$xmlPath	= 'ext/project/config/form/project.xml';
		$form		= TodoyuFormManager::getForm($xmlPath, $idProject);

			// Set form data
		$form->setFormData($data);
		$project = TodoyuProjectProjectManager::getProject($idProject);

		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

				// Save project
			$idProjectNew	= TodoyuProjectProjectManager::saveProject($storageData);

//			TodoyuProjectPreferences::saveExpandedDetails($idProjectNew, true);

			TodoyuHeader::sendTodoyuHeader('idProject', $idProjectNew);
			TodoyuHeader::sendTodoyuHeader('idProjectOld', $idProject);
		} else {
			TodoyuHeader::sendTodoyuHeader('idProjectOld', $idProject);
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}



	/**
	 * Render project details
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function detailsAction(array $params) {
		$idProject	= intval($params['project']);

		TodoyuProjectProjectRights::restrictSee($idProject);

		$tab = TodoyuProjectProjectDetailsTabsManager::getActiveTab($idProject);

		return TodoyuProjectProjectRenderer::renderProjectDetail($idProject, $tab);
	}



	/**
	 * Update project status
	 *
	 * @param	Array	$params
	 */
	public function setstatusAction(array $params) {
		TodoyuProjectProjectRights::restrictEdit();

		$idProject	= intval($params['project']);
		$status		= intval($params['status']);

		TodoyuProjectProjectManager::updateProjectStatus($idProject, $status);
	}



	/**
	 * Delete given project from DB and area view preferences
	 *
	 * @param	Array	$params
	 */
	public function removeAction(array $params) {
		TodoyuProjectProjectRights::restrictEdit();

		$idProject	= intval($params['project']);

		TodoyuProjectProjectManager::deleteProject($idProject);
		TodoyuProjectPreferences::removeOpenProject($idProject);
	}



	/**
	 * Render view when no project selected
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function noProjectViewAction(array $params) {
		return TodoyuProjectProjectRenderer::renderNoProjectSelectedView();
	}



	/**
	 * Add a sub form to the project form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		TodoyuProjectProjectRights::restrictEdit();

		$xmlPath	= 'ext/project/config/form/project.xml';

		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);

		return TodoyuFormManager::renderSubFormRecord($xmlPath, $fieldName, $formName, $index, $idRecord);
	}



	/**
	 * Paste task from clipboard into project
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function pasteInProjectAction(array $params) {
		$idProject	= intval($params['project']);

		TodoyuProjectTaskRights::restrictAddToProject($idProject);

		$idTaskNew = TodoyuProjectTaskClipboard::pasteTaskInProject($idProject);

		TodoyuHeader::sendTodoyuHeader('idTask', $idTaskNew);

		return TodoyuProjectProjectRenderer::renderTask($idTaskNew);
	}



	/**
	 * Load project tab
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function tabloadAction(array $params) {
		$idProject	= intval($params['project']);
		$tabKey		= $params['tab'];

		TodoyuProjectProjectRights::restrictSee($idProject);
		TodoyuProjectPreferences::saveActiveProjectDetailTab($idProject, $tabKey);

		TodoyuHookManager::callHook('project', 'project.detail.tab', array($idProject, $tabKey));

		return TodoyuContentItemTabRenderer::renderTabContent('project', 'projectdetail', $idProject, $tabKey);
	}



	/**
	 * 'tabselected' action method
	 *
	 * @param	Array	$params
	 */
	public function tabselectedAction(array $params) {
		$idProject	= intval($params['idProject']);
		$tabKey		= $params['tab'];

		TodoyuProjectProjectRights::restrictSee($idProject);

		TodoyuProjectPreferences::saveActiveProjectDetailTab($idProject, $tabKey);
	}

}

?>