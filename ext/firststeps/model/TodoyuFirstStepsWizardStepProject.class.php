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
 * Wizard step: project
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepProject extends TodoyuFirstStepsWizardStep {

	/**
	 * Initialize step
	 */
	protected function init() {
		$this->table	= 'ext_contact_company';
		$this->formXml	= 'ext/firststeps/config/form/project.xml';
	}



	/**
	 * Save/update project
	 *
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		$form	= $this->getForm($data);

		if( $form->isValid() ) {
			$projectData	= $form->getStorageData();

			$this->saveProject($projectData);

			return true;
		} else {

			return false;
		}
	}



	/**
	 * Render content
	 *
	 * @return		String
	 */
	public function getContent() {
		if( $this->data === null ) {
			$this->data = $this->getProjectData();
		}

		$this->getForm()->setUseRecordID(false);

		$tmpl	= 'ext/firststeps/view/form-with-list.tmpl';
		$data	= array(
			'items'	=> $this->getListItems(),
			'form'	=> $this->getForm($this->data)->renderContent()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get project members as list items
	 *
	 * @return	Array
	 */
	private function getListItems() {
		$persons	= TodoyuProjectProjectManager::getProjectPersons(1);
		$items		= array();

		foreach($persons as $person) {
			$items[] = array(
				'id'	=> $person['id'],
				'label'	=> $person['firstname'] . ' ' . $person['lastname'] . ', ' . $person['rolelabel']
			);
		}

		return $items;
	}



	/**
	 * Get data of first project
	 *
	 * @return	Array
	 */
	private function getProjectData() {
		if( ! $this->hasProjects() ) {
			$this->createEmptyProject();
		}

		$project	= TodoyuProjectProjectManager::getProject(1);

		return $project->getTemplateData();
	}



	/**
	 * Create empty project
	 *
	 * @return	Integer
	 */
	private function createEmptyProject() {
		$data	= array(
			'title'			=> 'My First Project',
			'id_company'	=> 1
		);

		return TodoyuProjectProjectManager::addProject($data);
	}



	/**
	 * Check whether the database already contains a project
	 *
	 * @return	Boolean
	 */
	private function hasProjects() {
		return Todoyu::db()->queryHasResult('SELECT id FROM ext_project_project LIMIT 1');
	}



	/**
	 * Update first project
	 *
	 * @param	Array	$submittedData
	 * @return	Integer
	 */
	private function saveProject(array $submittedData) {
		$data	= array(
			'status'		=> STATUS_PLANNING,
			'title'			=> $submittedData['title'],
			'description'	=> $submittedData['description'],
			'id_company'	=> intval($submittedData['id_company']),
			'date_start'	=> $submittedData['date_start'],
			'date_end'		=> $submittedData['date_end'],
			'date_deadline'	=> $submittedData['date_end'],
		);

		$idProject	= TodoyuProjectProjectManager::updateProject(1, $data);

		$idPerson	= intval($submittedData['id_person']);
		$idRole		= intval($submittedData['id_role']);

		if( $idPerson > 0 && $idRole > 0 ) {
			TodoyuProjectProjectManager::addPerson(1, $idPerson, $idRole);
		}

		return $idProject;
	}

}

?>