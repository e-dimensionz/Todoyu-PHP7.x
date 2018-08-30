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
 * Wizard step: Add employees
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepEmployees extends TodoyuFirstStepsWizardStep {

	/**
	 * Initialize with table and form
	 */
	protected function init() {
		$this->table	= 'ext_contact_person';
		$this->formXml	= 'ext/firststeps/config/form/employee.xml';
	}



	/**
	 * Save employee (add new record)
	 *
	 * @param	Array	$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		$form	= $this->getForm($data);

		if( $form->isValid() ) {
			$employeeData	= $form->getStorageData();

			$form->clear();
			$this->data	= array();

			$this->addEmployee($employeeData);

			TodoyuNotification::notifySuccess('firststeps.ext.wizard.employees.added');

			return true;
		} else {
			$this->data	= $data;

			 return false;
		}
	}



	/**
	 * Render content
	 *
	 * @return	String
	 */
	public function getContent() {
		$tmpl	= 'ext/firststeps/view/form-with-list.tmpl';
		$data	= array(
			'items'	=> $this->getListItems(),
			'form'	=> $this->getForm($this->data)->renderContent()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get all employees as list items
	 *
	 * @return	Array
	 */
	private function getListItems() {
		$items		= array();
		$employees	= $this->getEmployees();

		foreach($employees as $employee) {
			$items[]	= array(
				'label'	=> $employee['lastname'] . ' ' . $employee['firstname'] . ($employee['email']?' [' . $employee['email'] . ']':''),
				'id'	=> $employee['id']
			);
		}

		return $items;
	}



	/**
	 * Get all employee records
	 *
	 * @return	Array
	 */
	private function getEmployees() {
		return TodoyuContactPersonManager::getInternalPersons();
	}



	/**
	 * Create new employee (person) record and add it the the internal company
	 *
	 * @param	Array	$submittedData
	 * @return	Integer
	 */
	private function addEmployee(array $submittedData) {
		$data	= array(
			'salutation'	=> $submittedData['salutation'],
			'firstname'		=> $submittedData['firstname'],
			'lastname'		=> $submittedData['lastname'],
			'email'			=> $submittedData['email'],
			'username'		=> $submittedData['username'],
			'password'		=> md5($submittedData['password']),
			'is_admin'		=> 0,
			'is_active'		=> 1,
			'shortname'		=> strtoupper(substr($submittedData['firstname'], 0, 2) . substr($submittedData['lastname'], 0, 2)),
			'birthday'		=> '0000-00-00'
		);

		$idPerson	= TodoyuRecordManager::addRecord($this->table, $data);

		$companyIDs	= TodoyuContactCompanyManager::getInternalCompanyIDs();
		$idCompany	= intval($companyIDs[0]);
		$idJobtype	= intval($submittedData['id_jobtype']);

		TodoyuContactCompanyManager::addPerson($idCompany, $idPerson, 0, $idJobtype);

		return $idPerson;
	}

}

?>