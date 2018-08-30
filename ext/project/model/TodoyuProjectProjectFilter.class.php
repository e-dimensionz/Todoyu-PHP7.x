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
 * Project filter. Compile and execute active filters.
 * All project filters are defined in this class
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProjectFilter extends TodoyuSearchFilterBase implements TodoyuFilterInterface {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_project_project';



	/**
	 * Initialize project filter with active filters
	 *
	 * @param	Array		$activeFilters
	 * @param	String		$conjunction
	 * @param	Array		$sorting
	 */
	public function __construct(array $activeFilters = array(), $conjunction = 'AND', array $sorting = array()) {
		parent::__construct('PROJECT', self::TABLE, $activeFilters, $conjunction, $sorting);

		$this->addRightsClauseFilter();
	}



	/**
	 * Add rights clause for projects
	 */
	private function addRightsClauseFilter() {
			// Add rights clause
		$this->addRightsFilter('rights', '');

			// Add status filter
		if( ! TodoyuAuth::isAdmin() ) {
			$statusIDs	= TodoyuProjectProjectStatusManager::getStatusIDs();
			if( sizeof($statusIDs) > 0 ) {
				$statusList = implode(',', $statusIDs);
				$this->addRightsFilter('status', $statusList);
			} else {
				$this->addRightsFilter('Not', 0);
			}
		}
	}



	/**
	 * Get IDs of the projects matching to all filters
	 *
	 * @param	String		$sortingFallback
	 * @param	String		$limit
	 * @return	Array
	 */
	public function getProjectIDs($sortingFallback = '', $limit = '') {
		return parent::getItemIDs($sortingFallback, $limit);
	}



	/**
	 * General items function for anonymous access
	 *
	 * @param	String		$sortingFallback
	 * @param	String		$limit
	 * @return	Array
	 */
	public function getItemIDs($sortingFallback = '', $limit = '', $showDeleted = false) {
		return $this->getProjectIDs($sortingFallback, $limit);
	}



	/**
	 * Project rights clause. Limit output by person rights
	 * If person is not admin or can see all projects, limit projects to assigned ones
	 *
	 * @param	String		$value			IGNORED
	 * @param	Boolean		$negate			IGNORED
	 * @return	Array
	 */
	public function Filter_rights($value, $negate = false) {
		$queryParts	= false;

		if( ! TodoyuAuth::isAdmin() && ! Todoyu::allowed('project', 'project:seeAll') ) {
			$tables	= array(
				'ext_project_mm_project_person'
			);
			$where	= 'ext_project_mm_project_person.id_person	= ' . TodoyuAuth::getPersonID();
			$join	= array(
				self::TABLE . '.id	= ext_project_mm_project_person.id_project'
			);

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter search words full-text, optionally negated
	 *
	 * @param	String	$searchWords
	 * @param	Boolean	$negate
	 * @return	Array
	 */
	public function Filter_fulltext($searchWords, $negate = false) {
		$searchWords= trim($searchWords);
		$searchWords= TodoyuArray::trimExplode(' ', $searchWords);
		$queryParts	= false;

		if( sizeof($searchWords) > 0 ) {
			$searchInFields	= array(
				self::TABLE . '.id',
				self::TABLE . '.title',
				self::TABLE . '.description',
				'ext_contact_company.title',
				'ext_contact_company.shortname'
			);

			$tables	= array(
				'ext_contact_company'
			);
			$where	= TodoyuSql::buildLikeQueryPart($searchWords, $searchInFields);
			$join	= array(
				self::TABLE . '.id_company	= ext_contact_company.id'
			);

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter project ID, optionally negated
	 *
	 * @param	String	$projectID
	 * @param	Boolean	$negate
	 * @return	Array
	 */
	public function Filter_projectID($projectID, $negate = false) {
		$idProject	= TodoyuNumeric::intPositive($projectID);
		$queryParts	= false;

		if( $projectID > 0 ) {
			$compare	= $negate ? '!=' : '=';
			$queryParts = array('where'	=> self::TABLE . '.id ' . $compare . ' ' . $idProject);
		}

		return $queryParts;
	}



//	/**
//	 * Filter for locked projects, optionally negated
//	 *
//	 * @todo	implement negation
//	 *
//	 * @param	Boolean	$locked
//	 * @param	Boolean	$negate
//	 * @return	Array
//	 */
//	public function Filter_locked($locked, $negate = false) {
//		$tables	= array('system_lock');
//
//		if( $negate === false ) {
//			$where	= 'system_lock.table = \'ext_project_project\'';
//			$join	= array('ext_project_project.id	= system_lock.id_record');
//		} else {
//			$where	= ' ext_project_project.id NOT IN(
//							SELECT system_lock.id_record FROM system_lock WHERE system_lock.table = \'ext_project_project\'
//						)';
//			$join	= array();
//		}
//
//		$queryParts = array(
//			'tables'=> $tables,
//			'where'	=> $where,
//			'join'	=> $join
//		);
//
//		return $queryParts;
//	}



	/**
	 * Filter projects by status
	 *
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_status($value, $negate = false) {
		$status		= is_array($value) ? TodoyuArray::intval($value, true, true) : TodoyuArray::intExplode(',', $value, true, true);
		$queryParts	= false;

		if( sizeof($status) > 0 ) {
			$compare	= $negate ? 'NOT IN' : 'IN' ;

			$queryParts	= array(
				'where'	=> self::TABLE . '.status ' . $compare . '(' . implode(',', $status) . ')'
			);
		}

		return $queryParts;
	}



	/**
	 * Filter projects by title
	 *
	 * @param	String	$title
	 * @param	Boolean	$negate
	 * @return	Array
	 */
	public function Filter_title($title, $negate = false) {
		$title		= trim($title);
		$queryParts	= false;

		if( $title !== '' ) {
			$titleParts	= explode(' ', $title);

			$where	= TodoyuSql::buildLikeQueryPart($titleParts, array(self::TABLE . '.title'), $negate);

			$queryParts = array(
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter projects by (customer) company
	 *
	 * @param	Integer		$idCompany
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_company($idCompany, $negate = false) {
		$idCompany	= intval($idCompany);
		$queryParts	= false;

		if( $idCompany > 0 ) {
			$compare	= $negate ? '!=' : '=' ;

			$where	= self::TABLE . '.id_company ' . $compare . ' ' . $idCompany;

			$queryParts = array(
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition for projectrole
	 * The value is a combination between the projectroles and the selected person
	 *
	 * @param	String		$value		Format: PERSON:ROLE,ROLE,ROLE
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_projectrole($value, $negate = false) {
		$parts		= explode(':', $value);
		$idPerson	= intval($parts[0]);
		$roles		= TodoyuArray::intExplode(',', $parts[1]);

		$queryParts	= false;

		if( $idPerson !== 0 && sizeof($roles) > 0 ) {
				// This double sub query is here for performance reasons (don't optimize it!)
			$subQuery	= '	SELECT id_project
							FROM (
								SELECT	id_project
								FROM	ext_project_mm_project_person
								WHERE
										id_person	= ' . $idPerson .
								' AND ' . TodoyuSql::buildInListQueryPart($roles, 'id_role')
								. ' GROUP BY id_project
							) as x';
			$compare	= $negate ? 'NOT IN' : 'IN';
			$where		= ' ' . self::TABLE . '.id ' . $compare . ' (' . $subQuery . ')';

			$queryParts	= array(
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Prepare projectrole filter widget: get available projectroles for selector
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public function prepareDataForProjectroleWidget(array $definitions) {
		$projectroles	= TodoyuProjectProjectroleManager::getProjectroles(true);
		$reformConfig	= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);
		$definitions['options']	= TodoyuArray::reform($projectroles, $reformConfig);

			// Prepare separate values
		$values	= explode(':', $definitions['value']);
		$definitions['valuePerson']			= intval($values[0]);
		$definitions['valuePersonLabel']	= TodoyuContactPersonManager::getLabel($values[0]);
		$definitions['valueProjectroles']	= TodoyuArray::intExplode(',', $values[1], true, true);

			// Add JS config
		$definitions['specialConfig'] = json_encode(array(
			'acOptions' => array(
				'afterUpdateElement' => 'Todoyu.Ext.project.Filter.onProjectrolePersonAcSelect'
			)
		));

		return $definitions;
	}



	/**
	 * Filter condition: date_start
	 *
	 * @param	String		$date		Formatted (according to current locale) date string
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_startdate($date, $negate = false) {
		return $this->makeFilter_date('date_start', $date, $negate);
	}



	/**
	 * Filter condition: date_end
	 *
	 * @param	String		$date		Formatted (according to current locale) date string
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_enddate($date, $negate = false) {
		return $this->makeFilter_date('date_end', $date, $negate);
	}



	/**
	 * Filter condition: deadline
	 *
	 * @param	String		$date		Formatted (according to current locale) date string
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_deadline($date, $negate = false) {
		return $this->makeFilter_date('date_deadline', $date, $negate);
	}



	/**
	 * Filter condition: date_create
	 *
	 * @param	String		$date		Formatted (according to current locale) date string
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_createdate($date, $negate = false) {
		return $this->makeFilter_date('date_create', $date, $negate);
	}



	/**
	 * Get the dynamic create date
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_createdateDyn($value, $negate) {
		$timeStamps = TodoyuSearchFilterHelper::getDynamicDateTimestamp($value, $negate);

		return $this->Filter_dateDyn($timeStamps, 'date_create', $negate);
	}



	/**
	 * Filter condition: date_update
	 *
	 * @param	String		$date		Formatted (according to current locale) date string
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_editdate($date, $negate = false) {
		return $this->makeFilter_date('date_update', $date, $negate);
	}



	/**
	 * get the dynamic edit date
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_editdateDyn($value, $negate) {
		$timeStamps = TodoyuSearchFilterHelper::getDynamicDateTimestamp($value, $negate);

		return $this->Filter_dateDyn($timeStamps, 'date_update', $negate);
	}



	/**
	 * Get the dynamic date
	 *
	 * @param	Integer		$date
	 * @param	String		$field
	 * @param	Boolean		$negation
	 * @return	Array
	 */
	protected static function Filter_dateDyn($date, $field, $negation = false) {
		$date	= intval($date);
		$compare= $negation ? '>=' : '<=';

		$where	= self::TABLE . '.' . $field . ' ' . $compare . ' ' . $date;

		return array(
			'where'	=> $where
		);
	}



	/**
	 * Prepare date based filter widget for given field
	 *
	 * @param	String		$field
	 * @param	Integer		$date		Formatted (according to current locale) date string
	 * @param	Boolean		$negate
	 * @return	Boolean
	 */
	public function makeFilter_date($field, $date, $negate = false) {
		return TodoyuSearchFilterHelper::makeFilter_date(self::TABLE, $field, $date, $negate);
	}



	/**
	 * Set filtering WHERE clause falsified (e.g. if no project status is allowed) to avoid MySQL errors
	 *
	 * @return	Array
	 */
	public function Filter_Not() {
		return array(
			'where' => '0'
		);
	}



	/**
	 * Filter by tasks from the project which match a Filter condition from task-filter
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_taskFilter($value, $negate = false) {
		$filterSets	= TodoyuArray::intExplode(',', $value, true, true);
		$queryParts	= false;

		if( sizeof($filterSets) === 0 ) {
			return false;
		}

		$taskFilter = new TodoyuProjectTaskFilter(array(array('filter' => 'filterSet', 'value' => $filterSets)));

		$queryArray = $taskFilter->getQueryArray();
		$queryArray['group']	= '';
		$queryArray['fields']	= str_ireplace('sql_calc_found_rows', '', $queryArray['fields']);
		$subQuery = TodoyuSql::buildSELECTquery($queryArray['fields'], $queryArray['tables'], $queryArray['where'], 'ext_project_task.id_project');

		// This double sub query is here for performance reasons (don't optimize it!)
		$subQuery = ' SELECT id FROM ( ' . $subQuery . ') as x';

		$compare	= $negate ? ' NOT IN ' : ' IN ';

		$queryParts['tables']	= array('ext_project_project', 'ext_project_task');
		$queryParts['where']	= 'ext_project_task.id ' . $compare . ' (' . $subQuery . ')';
		$queryParts['join']		= array('ext_project_project.id = ext_project_task.id_project');

		return $queryParts;
	}



	/**
	 * Order by date create
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_DateCreate($desc = false) {
		return array(
			'order'	=> array(
				'ext_project_project.date_create ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * Order by date update
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_DateUpdate($desc = false) {
		return array(
			'order'	=> array(
				'ext_project_project.date_update ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * Order by date start
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_dateStart($desc = false) {
		return array(
			'order'	=> array(
				'ext_project_project.date_start ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * Order by date end
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_dateEnd($desc = false) {
		return array(
			'order'	=> array(
				'ext_project_project.date_end ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * Order by project id
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_projectID($desc = false) {
		return array(
			'order'	=> array(
				'ext_project_project.id ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * Order by title
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_title($desc = false) {
		return array(
			'order'	=> array(
				'ext_project_project.title ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * Order by status
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_status($desc = false) {
		return array(
			'order'	=> array(
				'ext_project_project.status ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * Order by company (id)
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_company($desc = false) {
		return array(
			'order'	=> array(
				'ext_project_project.id_company ' . self::getSortDir($desc)
			)
		);
	}

}

?>