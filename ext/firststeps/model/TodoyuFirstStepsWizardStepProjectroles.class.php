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
 * Wizard step: project roles
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepProjectroles extends TodoyuFirstStepsWizardStep {

	/**
	 * Initialize
	 */
	protected function init() {
		$this->table = 'ext_project_role';
	}



	/**
	 * Save project roles
	 *
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		$projectRoles	= TodoyuArray::assure($data['projectrole']);
		$projectRoles	= TodoyuArray::trim($projectRoles, true);

		$this->saveProjectRoles($projectRoles);

		$this->data	= $projectRoles;

		return true;
	}



	/**
	 * Render content (project role items list)
	 *
	 * @return	String
	 */
	public function getContent() {
		if( $this->data === null ) {
			$this->data = $this->getProjectRoles();
		}

		return TodoyuFirstStepsRenderer::renderItemList($this->data, 'projectrole', 'projectroles');
	}



	/**
	 * Get available project roles
	 *
	 * @return	Array
	 */
	private function getProjectRoles() {
		$projectRoles	= TodoyuProjectProjectroleManager::getProjectroles(true);

		return TodoyuArray::getColumn($projectRoles, 'title');
	}



	/**
	 * Save project roles
	 *
	 * @param	Array		$submittedProjectRoles
	 */
	private function saveProjectRoles(array $submittedProjectRoles) {
		$dbProjectRoles	= $this->getProjectRoles();

		TodoyuFirstStepsManager::saveLabelRecords($submittedProjectRoles, $dbProjectRoles, $this->table);
	}

}

?>