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
 * Timetracking filters for persons.
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingPersonFilter extends TodoyuSearchFilterBase implements TodoyuFilterInterface {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_person';



	/**
	 * Init filter object
	 *
	 * @param	Array		$activeFilters
	 * @param	String		$conjunction
	 * @param	Array		$sorting
	 */
	public function __construct(array $activeFilters = array(), $conjunction = 'AND', array $sorting = array()) {
		parent::__construct('PERSON', self::TABLE, $activeFilters, $conjunction, $sorting);
	}



	/**
	 * Person filter: tracked time in given project
	 *
	 * @param	Integer		$idProject
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_trackedinproject($idProject, $negate = false) {
		$idProject	= intval($idProject);
		$queryParts	= false;

		if( $idProject > 0 ) {
			$tables = array(
				'ext_contact_person',
				'ext_project_task',
				'ext_timetracking_track'
			);

			$where  = ' 		ext_project_task.id_project		= ' . $idProject
					. ' AND		ext_project_task.deleted		= 0 '
					. ' AND		ext_timetracking_track.id_task	= ext_project_task.id '
					. ' AND		ext_contact_person.id			= ext_timetracking_track.id_person_create';

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Person filter: tracked time in project of given customer company
	 *
	 * @param	Integer		$idCompany
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_trackedforcustomer($idCompany, $negate = false) {
		$idCompany	= intval($idCompany);
		$queryParts	= false;

		if( $idCompany > 0 ) {
			$tables = array(
				'ext_contact_person',
				'ext_project_project',
				'ext_project_task',
				'ext_timetracking_track'
			);

			$where  = ' 		ext_project_project.id_company	= ' . $idCompany
					. ' AND		ext_project_project.deleted		= 0 '
					. ' AND		ext_project_task.id_project		= ext_project_project.id '
					. ' AND		ext_project_task.deleted		= 0 '
					. ' AND		ext_timetracking_track.id_task	= ext_project_task.id '
					. ' AND		ext_contact_person.id			= ext_timetracking_track.id_person_create';

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}

}

?>