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
 * Ext action controller
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectExtActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access (project extension must be allowed)
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('project', 'general:use');
	}



	/**
	 * Default action: render project module page
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		Todoyu::restrict('project', 'general:area');

			// Set project tab
		TodoyuFrontend::setActiveTab('project');

			// Get deepLink parameters
		$idProject	= intval($params['project']);
		$idTask		= intval($params['task']);
		$taskTab	= $params['tab'];

			// Find project if only the task is given as parameter
		if( $idTask !== 0 && $idProject === 0 ) {
			$idProject = TodoyuProjectTaskManager::getProjectID($idTask);
		}

			// Get project if not set by parameter or save the given one in preferences
		if( $idProject === 0 ) {
			TodoyuCache::disable();
			$idProject	= TodoyuProjectPreferences::getActiveProject();
			TodoyuCache::enable();
		}

			// If no project found yet, try to find one the person can see
		if( $idProject === 0 ) {
			$idProject	= TodoyuProjectProjectManager::getAvailableProjectForPerson();
		}

			// Check access rights (if project selected)
		if( $idProject !== 0 ) {
			TodoyuProjectProjectRights::restrictSee($idProject);
		}

			// If task ID set
		if( $idTask !== 0 ) {
				// Check access rights for task if requested
			if( ! TodoyuProjectTaskManager::isTaskVisible($idTask) ) {
					// Reset task ID if not visible
				$idTask = 0;
					// Show message about not available task
				TodoyuPage::addJsInit('Todoyu.notifyError(\'' . Todoyu::Label('project.task.notAvailable') . '\')');
			}
		}

			// Init page
		TodoyuPage::init('ext/project/view/ext.tmpl');

			// Load project
		$project	= TodoyuProjectProjectManager::getProject($idProject);

			// If a project is displayed
		if( $idProject !== 0 && !$project->isDeleted() ) {
				// Prepend current project to list
			TodoyuProjectPreferences::addOpenProject($idProject);

			$title	= Todoyu::Label('project.ext.page.title') . ' - ' . TodoyuString::html2text($project->getFullTitle());
		} else {
			$title		= Todoyu::Label('project.ext.page.title.noSelected');
			$idProject	= 0;
		}

		TodoyuPage::setTitle($title);

			// Render panel widgets and content
		$panelWidgets		= TodoyuProjectProjectRenderer::renderPanelWidgets($idProject, $idTask);
		$projectTabs		= TodoyuProjectProjectRenderer::renderProjectsTabs();
		$projectTaskTree	= TodoyuProjectProjectRenderer::renderProjectsContent($idProject, $idTask, $taskTab);

		TodoyuPage::setPanelWidgets($panelWidgets);
		TodoyuPage::set('projectTabs', $projectTabs);
		TodoyuPage::set('taskTree', $projectTaskTree);

			// Add JS onLoad functions
		TodoyuPage::addJsInit('Todoyu.Ext.project.ContextMenuTask.attach()');
		TodoyuPage::addJsInit('Todoyu.Ext.project.ContextMenuProject.attach()');
		TodoyuPage::addJsInit('Todoyu.Ext.project.ContextMenuProjectInline.attach()');

			// Open any task for editing initially?
		$idTaskEdit	= TodoyuRequest::getParam('edit', true);
		if( $idTaskEdit > 0 ) {
			TodoyuPage::addJsInit('Todoyu.Ext.project.Task.Edit.initEditOnLoaded(' . $idTaskEdit . ')');
		}

		return TodoyuPage::render();
	}



	/**
	 * Controller to handle direct edit access. Calls the default action first to render the whole site.
	 * After loading the site the JS-edit method is called.
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idProject = intval($params['project']);

		TodoyuProjectProjectRights::restrictEdit($idProject);

		TodoyuPage::addJsInit('Todoyu.Ext.project.Project.edit(' . $idProject . ')', 101);

		return $this->defaultAction($params);
	}



	/**
	 * Controller to handle direct add task access. Calls the default action first to render the whole site.
	 * After loading the site the JS-addTask method is called.
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function addtaskAction(array $params) {
		$idProject = intval($params['project']);

		TodoyuProjectTaskRights::restrictAddToProject($idProject);

		TodoyuPage::addJsInit('Todoyu.Ext.project.Project.addTask(' . $idProject . ')', 101);

		return $this->defaultAction($params);
	}



	/**
	 * Controller to handle direct add container access. Calls the default action first to render the whole site
	 * After loading the site the JS-addContainer method is called.
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function addcontainerAction(array $params) {
		$idProject = intval($params['project']);

		TodoyuProjectTaskRights::restrictAddToProject($idProject);

		TodoyuPage::addJsInit('Todoyu.Ext.project.Project.addContainer(' . $idProject . ')', 101);

		return $this->defaultAction($params);
	}

}

?>