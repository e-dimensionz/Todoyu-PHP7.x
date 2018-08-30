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
 * Project object
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProject extends TodoyuBaseObject {

	/**
	 * Initialize project
	 *
	 * @param	Integer		$idProject
	 */
	public function __construct($idProject) {
		parent::__construct($idProject, 'ext_project_project');
	}



	/**
	 * Get full project title with company short name
	 *
	 * @param	Boolean	$companyShort
	 * @return	String
	 */
	public function getFullTitle($companyShort = false) {
		$company	= $companyShort ? $this->getCompany()->getShortLabel() : $this->getCompany()->getTitle();

		return $company . ' - ' . $this->getTitle();
	}



	/**
	 * Get short label for tabs (includes company)
	 *
	 * @param	Boolean		$withCompany		Prepend company
	 * @return	String
	 */
	public function getShortLabel($withCompany = true) {
		$projectLabel	= $this->getTitle();

		if( $withCompany ) {
			$companyLabel	= $this->getCompany()->getShortLabel();
			return $companyLabel . ': ' . $projectLabel;
		} else {
			return $projectLabel;
		}
	}



	/**
	 * Get project title
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->get('title');
	}



	/**
	 * Get project label
	 *
	 * @param	Boolean		$companyShort
	 * @return	String
	 */
	public function getLabel($companyShort = true) {
		return $this->getFullTitle($companyShort);
	}



	/**
	 * Get project description
	 *
	 * @return	String
	 */
	public function getDescription() {
		return $this->get('description');
	}



	/**
	 * Get company ID
	 *
	 * @return	Integer
	 */
	public function getCompanyID() {
		return $this->getInt('id_company');
	}



	/**
	 * Get company object
	 *
	 * @return	 TodoyuContactCompany
	 */
	public function getCompany() {
		return TodoyuContactCompanyManager::getCompany($this->getCompanyID());
	}



	/**
	 * Get project status ID
	 *
	 * @return	Integer
	 */
	public function getStatus() {
		return $this->getInt('status');
	}



	/**
	 * Get status key of the project
	 *
	 * @return	String
	 */
	public function getStatusKey() {
		return TodoyuProjectProjectStatusManager::getStatusKey($this->getStatus());
	}



	/**
	 * Get status label of the project
	 *
	 * @return	String
	 */
	public function getStatusLabel() {
		return TodoyuProjectProjectStatusManager::getStatusLabel($this->getStatus());
	}



	/**
	 * Get project start date
	 *
	 * @deprecated
	 * @see		getDateStart
	 * @return	Integer
	 */
	public function getStartDate() {
		return $this->getDateStart();
	}



	/**
	 * Get project start date
	 *
	 * @return	Integer
	 */
	public function getDateStart() {
		return $this->getInt('date_start');
	}



	/**
	 * Get project end date
	 *
	 * @deprecated
	 * @see		getDateEnd
	 * @return	Integer
	 */
	public function getEndDate() {
		return $this->getDateEnd();
	}



	/**
	 * Get project end date
	 *
	 * @return	Integer
	 */
	public function getDateEnd() {
		return $this->getInt('date_end');
	}



	/**
	 * Get project deadline date
	 *
	 * @deprecated
	 * @see		getDateDeadline
	 * @return	Integer
	 */
	public function getDeadlineDate() {
		return $this->getDateDeadline();
	}



	/**
	 * Get project deadline date
	 *
	 * @return	Integer
	 */
	public function getDateDeadline() {
		return $this->getInt('date_deadline');
	}



	/**
	 * Get project range
	 *
	 * @return	Array
	 */
	public function getRangeDates() {
		return array(
			'start'	=> $this->getDateStart() === 0 ? $this->getDateCreate() : $this->getDateStart(),
			'end'	=> $this->getDateDeadline() === 0 ? $this->getDateEnd() === 0 ? NOW : $this->getDateEnd() : $this->getDateDeadline()
		);
	}



	/**
	 * Check whether current person is assigned to this project
	 *
	 * @return	Boolean
	 */
	public function isCurrentPersonAssigned() {
		return $this->isPersonAssigned();
	}



	/**
	 * Check whether person is assigned to the project
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public function isPersonAssigned($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		return TodoyuProjectProjectManager::isPersonAssigned($this->getID(), $idPerson);
	}



	/**
	 * Check whether there are tabs configured
	 *
	 * @return	Boolean
	 */
	public function hasTabs() {
		$hasTabs	= TodoyuContentItemTabManager::hasTabs('project', 'project');

			// Call hooks
		return TodoyuHookManager::callHookDataModifier('project', 'projectHasTabs', $hasTabs, array($this->getID()));
	}



	/**
	 * Load foreign data of a project
	 */
	public function loadForeignData() {
		$this->data['persons'] = $this->getPersons();
		$this->data['company'] = $this->getCompany()->getTemplateData(false);
	}



	/**
	 * Get project persons
	 *
	 * @return	Array
	 */
	public function getPersons() {
		return TodoyuProjectProjectManager::getProjectPersons($this->getID());
	}



	/**
	 * Get IDs of project persons
	 *
	 * @return	Array
	 */
	public function getPersonsIDs() {
		$persons		= $this->getPersons();
		$reformConfig	= array(
			'id'	=> 'id_person',
		);

		return TodoyuArray::flatten(TodoyuArray::reform($persons, $reformConfig));
	}



	/**
	 * Get all IDs of persons with the ID of their todoyu role
	 *
	 * @param	Boolean		$indexWithPersonIDs
	 * @return	Array
	 */
	public function getPersonsRolesIDs($indexWithPersonIDs = false) {
		$persons		= $this->getPersons();

		$reformConfig	= array(
			'id_person'	=> 'id_person',
			'id_role'	=> 'id_role'
		);

		if( $indexWithPersonIDs ) {
			$personRoles = TodoyuArray::reformWithFieldAsIndex($persons, $reformConfig, false, 'id_person');
		} else {
			$personRoles = TodoyuArray::reform($persons, $reformConfig);	
		}

		return $personRoles;
	}



	/**
	 * Get all IDs of persons with the label of their assigned projectrole
	 *
	 * @param	Boolean		$indexWithPersonIDs
	 * @return	Array
	 */
	public function getPersonsProjectrolesLabels($indexWithPersonIDs = false) {
		$persons		= $this->getPersons();
		$reformConfig	= array(
			'id_person'	=> 'id_person',
			'rolelabel'	=> 'rolelabel'
		);

		if( $indexWithPersonIDs ) {
			$personRoles = TodoyuArray::reformWithFieldAsIndex($persons, $reformConfig, false, 'id_person');
		} else {
			$personRoles = TodoyuArray::reform($persons, $reformConfig);
		}

		return $personRoles;
	}



	/**
	 * Get ID of role of given (or currently logged-in) person in project
	 *
	 * @param	Integer				$idPerson
	 * @return	Integer				0 if no role defined for person
	 */
	public function getPersonRoleID($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);
		$idRole		= 0;

		$persons	= $this->getPersons();
		foreach($persons as $person) {
			if( $person['id'] == $idPerson ) {
				$idRole	= $person['id_role'];
				break;
			}
		}

		return $idRole;
	}



	/**
	 * Get role of given (or currently logged-in) person in project
	 *
	 * @param	Integer				$idPerson
	 * @return	TodoyuProjectProjectrole
	 */
	public function getPersonRole($idPerson = 0) {
		$idRole		= $this->getPersonRoleID($idPerson);

		return TodoyuProjectProjectroleManager::getProjectrole($idRole);
	}



	/**
	 * Get person IDs with role in project
	 *
	 * @param	Integer		$idRole
	 * @return	Array
	 */
	public function getRolePersonIDs($idRole) {
		$idRole	= intval($idRole);

		$field	= 'id_person';
		$table	= 'ext_project_mm_project_person';
		$where	= '		id_project	= ' . $this->getID()
				. '	AND id_role		= ' . $idRole;
		$order	= '	is_public,
					id';

		return Todoyu::db()->getColumn($field, $table, $where, '', $order);
	}



	/**
	 * Get ID of assigned taskpreset of project
	 *
	 * @return	Integer
	 */
	public function getTaskPresetID() {
		return $this->getInt('id_taskpreset');
	}



	/**
	 * Check whether project has a task preset
	 *
	 * @return	Boolean
	 */
	public function hasTaskPreset() {
		return $this->getTaskPresetID() !== 0;
	}



	/**
	 * Get task preset
	 *
	 * @return	TodoyuProjectTaskPreset
	 */
	public function getTaskPreset() {
		return TodoyuProjectTaskPresetManager::getTaskPreset($this->getTaskPresetID());
	}



	/**
	 * Check whether project is locked
	 *
	 * @return	Boolean
	 */
	public function isLocked() {
		return TodoyuLockManager::isLocked('ext_project_project', $this->getID());
	}



	/**
	 * Check whether a project has locked tasks
	 *
	 * @return	Boolean
	 */
	public function hasLockedTasks() {
		$field	= '	t.id';
		$tables	= '	system_lock sl,
					ext_project_task t';
		$where	= '		t.id_project= ' . $this->getID()
				. ' AND	t.id		= sl.id_record'
				. ' AND sl.table	= \'ext_project_task\'';

		return Todoyu::db()->hasResult($field, $tables, $where);
	}



	/**
	 * Check whether currency person can add tasks to this project
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public function canPersonAddTasks($idPerson = 0) {
		return TodoyuProjectProjectManager::canPersonAddTasks($this->getID(), $idPerson);
	}



	/**
	 * Check whether this project is editable
	 *
	 * @return	Boolean
	 */
	public function isEditable() {
		return TodoyuProjectProjectRights::isEditAllowed() && !$this->isLocked();
	}



	/**
	 * Get template data
	 *
	 * @param	Boolean		$loadForeignData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false) {
		if( $loadForeignData ) {
			$this->loadForeignData();
		}

		$this->data['fulltitle'] = $this->getFullTitle();
		$this->data['statusKey'] = $this->getStatusKey();

		return parent::getTemplateData();
	}

}

?>