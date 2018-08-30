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
 * Wizard step: user roles
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepUserroles extends TodoyuFirstStepsWizardStep {

	/**
	 * Initialize steps
	 */
	protected function init() {
		$this->table = 'system_role';
	}



	/**
	 * Save user role
	 *
	 * @param	Array	$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		$roles	= TodoyuArray::assure($data['userrole']);
		$roles	= TodoyuArray::trim($roles, true);

		$this->saveRoles($roles);

		$this->data	= $roles;

		return true;
	}



	/**
	 * Render content (auto extending list)
	 *
	 * @return	String
	 */
	public function getContent() {
		if( $this->data === null ) {
			$this->data = $this->getRoles();
		}

		return TodoyuFirstStepsRenderer::renderItemList($this->data, 'userrole', 'userroles');
	}



	/**
	 * Get existing roles
	 *
	 * @return	Array
	 */
	private function getRoles() {
		$roles	= TodoyuRoleManager::getAllRoles();

		return TodoyuArray::getColumn($roles, 'title');
	}



	/**
	 * Save the roles
	 *
	 * @param	Array		$submittedRoles
	 */
	private function saveRoles(array $submittedRoles) {
		$dbRoles	= $this->getRoles();
		$extraFields= array(
			'is_active'	=> 1
		);

		TodoyuFirstStepsManager::saveLabelRecords($submittedRoles, $dbRoles, $this->table, 'title', $extraFields);
	}

}

?>