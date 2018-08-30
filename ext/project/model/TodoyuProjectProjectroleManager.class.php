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
 * Projectrole manager
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProjectroleManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_project_role';



	/**
	 * Get projectrole
	 *
	 * @param	Integer		$idProjectrole
	 * @return	TodoyuProjectProjectrole
	 */
	public static function getProjectrole($idProjectrole) {
		return TodoyuRecordManager::getRecord('TodoyuProjectProjectrole', $idProjectrole);
	}



	/**
	 * Save projectrole
	 *
	 * @param	Array		$data
	 * @return	Integer		$idProjectrole
	 */
	public static function saveProjectrole(array $data) {
		$idProjectrole	= intval($data['id']);
		$xmlPath		= 'ext/project/config/form/admin/projectrole.xml';

		if( $idProjectrole === 0 ) {
			$idProjectrole = self::addProjectrole();
		}

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idProjectrole);

		self::updateProjectrole($idProjectrole, $data);

		return $idProjectrole;
	}



	/**
	 * Add new projectrole
	 *
	 * @param	Array		$data
	 * @return	Integer		New projectrole ID
	 */
	public static function addProjectrole(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update project role record with given data
	 *
	 * @param	Integer		$idProjectrole
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateProjectrole($idProjectrole, array $data) {
		$idProjectrole	= intval($idProjectrole);

		return TodoyuRecordManager::updateRecord(self::TABLE, $idProjectrole, $data);
	}



	/**
	 * Sets deleted flag for project role
	 *
	 * @param	Integer		$idProjectrole
	 * @return	Boolean
	 */
	public static function deleteProjectrole($idProjectrole) {
		return TodoyuRecordManager::deleteRecord(self::TABLE, $idProjectrole);
	}



	/**
	 * Get label of projectrole
	 *
	 * @param	Integer		$idProjectrole
	 * @return	String
	 */
	public static function getLabel($idProjectrole) {
		$idProjectrole	= intval($idProjectrole);
		$label			= '';

		if( $idProjectrole !== 0 ) {
			$projectrole= self::getProjectrole($idProjectrole);
			$label		= $projectrole->getTitle();
		}

		return $label;
	}



	/**
	 * Get all active project roles, optionally parse to render title labels
	 *
	 * @param	Boolean	$parse
	 * @return	Array
	 */
	public static function getProjectroles($parse = true) {
		$where	= 'deleted = 0';
		$order	= 'title';

		$projectroles	= TodoyuRecordManager::getAllRecords(self::TABLE, $where, $order);

		if( $parse ) {
			foreach($projectroles as $index => $projectrole) {
				$projectroles[$index]['title'] = Todoyu::Label($projectrole['title']);
			}
		}

		return $projectroles;
	}



	/**
	 * Get role IDs of given person in given project
	 *
	 * @param	Integer		$idProject
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPersonRoleIDs($idProject, $idPerson = 0) {
		$idProject	= intval($idProject);
		$idPerson	= Todoyu::personid($idPerson);

		$field	= 'id_role';
		$table	= '	ext_project_mm_project_person';
		$where	= '		id_project	= ' . $idProject
				. ' AND id_person	= ' . $idPerson;

		return Todoyu::db()->getColumn($field, $table, $where);
	}



	/**
	 * Get list of existing projectrole records
	 *
	 * @return	Array
	 */
	public static function getRecords() {
		$projectroles	= self::getProjectroles();
		$reformConfig		= array(
			'id'	=> 'id',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($projectroles, $reformConfig);
	}



	/**
	 * Check whether the project role is in use and therefor not deletable
	 *
	 * @param	Integer		$idProjectrole
	 * @return	Boolean
	 */
	public static function isDeletable($idProjectrole) {
		$idProjectrole	= intval($idProjectrole);

		$field	= 'id';
		$table	= 'ext_project_mm_project_person';
		$where	= 'id_role = ' . $idProjectrole;

		return Todoyu::db()->hasResult($field, $table, $where, '', 1) === false;
	}

}
?>