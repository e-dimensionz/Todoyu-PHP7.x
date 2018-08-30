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
 * Project filters for persons.
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectPersonFilter extends TodoyuSearchFilterBase implements TodoyuFilterInterface {

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
	 * Person filter: assigned (has projectrole) in given project
	 *
	 * @param	Integer		$idProject
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_assignedinproject($idProject, $negate = false) {
		$idProject	= intval($idProject);
		$queryParts	= false;

		if( $idProject > 0 ) {
			$tables = array(
				'ext_contact_person',
				'ext_project_project',
				'ext_project_mm_project_person'
			);

			$where	= '		ext_project_mm_project_person.id_project	= ' . $idProject
					. ' AND ext_project_project.deleted					= 0'
					. ' AND ext_contact_person.id						= ext_project_mm_project_person.id_person';

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}

}

?>