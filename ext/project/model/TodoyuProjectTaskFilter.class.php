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
 * Task filter
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskFilter extends TodoyuSearchFilterBase implements TodoyuFilterInterface {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_project_task';

	/**
	 * Container mode (ignore container status)
	 *
	 * @var	Boolean
	 */
	private $containerMode = false;



	/**
	 * Init filter object
	 *
	 * @param	Array	$activeFilters		Active filters for request
	 * @param	String	$conjunction
	 * @param	Array	$sorting
	 */
	public function __construct(array $activeFilters = array(), $conjunction = 'AND', array $sorting = array()) {
		parent::__construct('TASK', self::TABLE, $activeFilters, $conjunction, $sorting);
	}



	/**
	 * Add rights clause to limit view to the persons rights
	 */
	private function addRightsClauseFilter() {
			// Limit to current person
		if( ! Todoyu::allowed('project', 'seetask:seeAll') ) {
			$this->addRightsFilter('assignedPerson', Todoyu::personid());
		}

			// Limit to selected status
		if( ! TodoyuAuth::isAdmin() ) {
			$statusIDs = TodoyuProjectTaskStatusManager::getStatusIDs();
			if( sizeof($statusIDs) > 0 ) {
				$statusList	= implode(',', $statusIDs);
				$this->addRightsFilter('status', $statusList);
			} else {
				$this->addRightsFilter('Not', 0);
			}

				// Limit to tasks which are in available projects
			if( ! Todoyu::allowed('project', 'project:seeAll') ) {
				$this->addRightsFilter('availableprojects', 0);
			}

				// Add public filter for all externals (not internal)
			if( ! Todoyu::person()->isInternal() ) {
				$this->addRightsFilter('isPublic', 1);
			}
		}
	}



	/**
	 * Get task IDs which match to all filters
	 *
	 * @param	String		$sortingFallback		Force sorting column
	 * @param	String		$limit				Limit result items
	 * @return	Array
	 */
	public function getTaskIDs($sortingFallback = 'sorting', $limit = '') {
		$this->addRightsClauseFilter();

		return parent::getItemIDs($sortingFallback, $limit, false);
	}



	/**
	 * General access to the result items
	 *
	 * @param	String		$sortingFallback
	 * @param	String		$limit
	 * @return	Array
	 */
	public function getItemIDs($sortingFallback = 'sorting', $limit = '', $showDeleted = false) {
		return $this->getTaskIDs($sortingFallback, $limit);
	}



	/**
	 * Enable container mode
	 * If enabled, container will be found, even if status doesn't match
	 */
	public function enableContainerMode() {
		$this->containerMode = true;
	}



	/**
	 * Filter condition: tasks of given project
	 *
	 * @param	Integer			$value		Project ID
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts / false if no project ID given
	 */
	public function Filter_project($value, $negate = false) {
		$value		= intval($value);
		$queryParts	= false;

		if( $value > 0 ) {
				// Set up query parts array
			$tables	= array(self::TABLE);
			$compare= $negate ? '!= ' : '= ';
			$where	= 'ext_project_task.id_project ' . $compare . $value;

			$queryParts	= array(
				'tables'	=> $tables,
				'where'		=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: project title matches?
	 *
	 * @param	String			$value		Space-separated search-words
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts / false if no search-words given
	 */
	public function Filter_projecttitle($value, $negate = false) {
		$words		= TodoyuString::trimExplode(' ', $value, true);
		$queryParts	= false;

		if( sizeof($words) > 0 ) {
			$tables	= array('ext_project_project');
			$fields	= array('ext_project_project.title');
			$where	= TodoyuSql::buildLikeQueryPart($words, $fields, $negate);
			$join	= array('ext_project_task.id_project = ext_project_project.id');

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Get query parts for available projects filter
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array					Query parts
	 */
	public function Filter_availableprojects($value, $negate = false) {
		$availableProjects	= TodoyuProjectProjectManager::getAvailableProjectsForPerson();

		if( sizeof($availableProjects) > 0 ) {
			$queryParts	= array(
				'tables'	=> array('ext_project_project'),
				'where'		=> 'ext_project_task.id_project IN(' . implode(',', $availableProjects) . ')'
			);
		} else {
				// Add negative WHERE. Will definitely cause an empty result
			$queryParts	= array('where'	=> '0');
		}

		return $queryParts;
	}



	/**
	 * Shortcut to company filter
	 *
	 * @param	Integer		$value			Company ID
	 * @param	Boolean		$negate
	 * @return	Array|Boolean				Query parts / false if no company ID given
	 */
	public function Filter_customer($value, $negate = false) {
		return $this->Filter_company($value, $negate);
	}



	/**
	 * Filter condition: tasks of projects of given customer
	 *
	 * @param	Integer			$value		Company ID
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts / false if no company ID given
	 */
	public function Filter_company($value, $negate = false) {
		$idCompany	= intval($value);
		$queryParts	= false;

		if( $idCompany > 0 ) {
			$tables	= array('ext_project_project');

			$compare	= $negate ? '!=' : '=' ;
			$where	= 'ext_project_project.id_company ' . $compare . ' ' . $idCompany;

			$join	= array('ext_project_task.id_project = ext_project_project.id');

			return array(
				'tables'	=> $tables,
				'where'		=> $where,
				'join'		=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: tasks where person is owner
	 *
	 * @param	Integer			$value		Owner person ID
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no person ID given
	 */
	public function Filter_ownerPerson($value, $negate = false) {
		$idOwner	= intval($value);
		$queryArray	= false;

		if( $idOwner !== 0 ) {
				// Set up query parts array
			$tables	= array(self::TABLE);
			$compare= $negate ? '!= ' : '= ';
			$where	= 'ext_project_task.id_person_owner ' . $compare . $idOwner;

			$queryArray	= array(
				'tables'	=> $tables,
				'where'		=> $where
			);
		}

		return $queryArray;
	}



	/**
	 * Filter condition: tasks where current person is owner
	 *
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no person ID given
	 */
	public function Filter_currentPersonOwner($negate = false) {
		$idPerson	= Todoyu::personid();

		return $this->Filter_ownerPerson($idPerson, $negate);
	}



	/**
	 * Filter condition: tasks of given owner
	 *
	 * @param	Array			$value		Comma-separated IDs of selected roles
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no role IDs given
	 */
	public function Filter_ownerRoles($value, $negate = false) {
		$roleIDs	= TodoyuArray::intExplode(',', $value, true, true);
		$queryParts	= false;

		if( sizeof($roleIDs) > 0 ) {
			$tables	= array(
				self::TABLE,
				'ext_contact_mm_person_role'
			);

			$where	= 'ext_contact_mm_person_role.id_role IN(' . implode(',', $roleIDs) . ')';

			$join	= array('ext_project_task.id_person_owner = ext_contact_mm_person_role.id_person');

			$queryParts	= array(
				'tables'	=> $tables,
				'where'		=> $where,
				'join'		=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter for task number
	 *
	 * @param	String			$value		Task number
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no task number given
	 */
	public function Filter_tasknumber($value, $negate = false) {
		$taskNumber	= intval($value);
		$queryParts	= false;

		if( $taskNumber > 0 ) {
			$tables	= array(self::TABLE);
			$where	= 'ext_project_task.tasknumber = ' . $taskNumber;

			$queryParts	= array(
				'tables'	=> $tables,
				'where'		=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: task title like given string?
	 *
	 * @param	String			$value		(String part out of) Task title
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no title given
	 */
	public function Filter_title($value, $negate = false) {
		$queryParts	= false;
		$titleWords	= TodoyuString::trimExplode(' ', $value, true);

		if( sizeof($titleWords) ) {
			$tables	= array(self::TABLE);
			$fields	= array('ext_project_task.title');
			$where	= TodoyuSql::buildLikeQueryPart($titleWords, $fields, $negate);

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Task full-text filter. Searches in task number, title, description
	 *
	 * @param	String			$value		Text
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no text given
	 */
	public function Filter_fulltext($value, $negate = false) {
		$value		= trim($value);
		$queryParts	= false;

		if( $value !== '' ) {
			$logic		= $negate ? ' NOT LIKE ':' LIKE ';
			$conjunction= $negate ? ' AND ':' OR ';

			$tables	= array(self::TABLE);
			$keyword= TodoyuSql::escape($value);
			$where	= '((							' . self::TABLE . '.description	' . $logic . ' \'%' . $keyword . '%\'
							' . $conjunction . '	' . self::TABLE . '.title		' . $logic . ' \'%' . $keyword . '%\'
						)';

			if( strpos($value, '.') !== false ) {
				list($project, $task) = TodoyuArray::intExplode('.', $value);
				$where	.= ' OR (	  ' . self::TABLE . '.id_project = ' . $project .
								' AND ' . self::TABLE . '.tasknumber = ' . $task .
							')';
			}

			$where	 .= ')';

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Search tasks which match the value in the title or the task number
	 *
	 * @param	String			$value		Task number and/or task title
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if value is not empty title or task number
	 */
	public function Filter_tasknumberortitle($value, $negate = false) {
		$value		= trim($value);
		$queryParts	= false;

		if( $value !== '' ) {
			$whereParts	= array();

				// Task number
			if( TodoyuProjectTaskManager::isTaskNumberFormat($value) ) {
				list($idProject, $taskNumber) = TodoyuArray::intExplode('.', $value, true, true, 2);
				$compare	= $negate ? '!=' : '=';

				$whereParts[] = '(		ext_project_task.id_project ' . $compare . $idProject .
								' AND	ext_project_task.tasknumber ' . $compare . $taskNumber . ')';
			}

				// Title
			$searchWords	= TodoyuArray::trimExplode(' ', $value, true);
			$searchFields	= array('ext_project_task.title');
			$whereParts[]	= TodoyuSql::buildLikeQueryPart($searchWords, $searchFields, $negate);

			$queryParts	= array(
				'where'	=> '(' . implode(' OR ', $whereParts) . ')'
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: tasks created by person
	 *
	 * @param	Integer			$value		Person ID
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no person ID given
	 */
	public function Filter_creatorPerson($value, $negate = false) {
		$idPerson	= intval($value);
		$queryParts	= false;

		if( $idPerson !== 0 ) {
			$logic	= $negate ? '!=':'=';

			$tables	= array(self::TABLE);
			$where	= self::TABLE . '.id_person_create ' . $logic . ' ' . $idPerson;

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: Task created by a person which is member of one of the selected roles
	 *
	 * @param	Array		$value		Comma-separated role IDs
	 * @param	Boolean		$negate
	 * @return	Array|Boolean			Query parts array / false if no role IDs given
	 */
	public function Filter_creatorRoles($value, $negate = false) {
		$roleIDs	= TodoyuArray::intExplode(',', $value, true, true);
		$queryParts	= false;

		if( sizeof($roleIDs) > 0 ) {
			$tables	= array(
				self::TABLE,
				'ext_contact_mm_person_role'
			);
			$compare= $negate ? 'NOT IN' : 'IN';
			$where	= 'ext_contact_mm_person_role.id_role ' . $compare . '(' . implode(',', $roleIDs) . ')';
			$join	= array(
				self::TABLE . '.id_person_create = ext_contact_mm_person_role.id_person'
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
	 * Filter condition: tasks assigned to person
	 *
	 * @param	Integer			$value		Person ID
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts / false if no person ID given
	 */
	public function Filter_assignedPerson($value, $negate = false) {
		$idPerson	= intval($value);
		$queryParts	= false;

		if( $idPerson !== 0 ) {
				// Set up query parts array
			$compare= $negate ? '!=' : '=';
			$where	= self::TABLE . '.id_person_assigned ' . $compare . ' ' . intval($idPerson);

			if( !$negate ) {
				$where	.= ' AND ' . self::TABLE . '.type	= ' . TASK_TYPE_TASK;
			}

			$queryParts	= array(
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: task assigned to person of a role?
	 *
	 * @param	Array			$value		Role IDs, comma-separated
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts / false if no role IDs given
	 */
	public function Filter_assignedRoles($value, $negate = false) {
		$roleIDs	= TodoyuArray::intExplode(',', $value, true, true);
		$queryParts	= false;

		if( sizeof($roleIDs) > 0 ) {
			$tables	= array('ext_contact_mm_person_role');

			$where	= TodoyuSql::buildInListQueryPart($roleIDs, 'ext_contact_mm_person_role.id_role', true, $negate);

			if( !$negate ) {
				$where .= ' AND	' . self::TABLE . '.type	= ' . TASK_TYPE_TASK;
			}

			$join	= array(self::TABLE . '.id_person_assigned = ext_contact_mm_person_role.id_person');

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: tasks assigned to current person
	 *
	 * @param	String			$value		Not used
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts / false if no person ID given
	 */
	public function Filter_currentPersonAssigned($value = '', $negate = false) {
		$idPerson	= Todoyu::personid();

		$queryParts	= $this->Filter_assignedPerson($idPerson, $negate);

		return $queryParts;
	}



	/**
	 * Filter condition: Project description like given filter value?
	 *
	 * @param	String			$value		Project description substring
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts / false if given string is empty
	 */
	public function Filter_projectDescription($value = '', $negate = false) {
		$queryParts	= false;
		$string		= trim($value);

		if( strlen($string) ) {
			$string	= TodoyuSql::escape($string);

			$tables	= array('ext_project_project');
			$where	=  'ext_project_project.description LIKE \'%' . $string . '%\'';
			$join	= array(self::TABLE . '.id_project = ext_project_project.id');

			$queryParts	= array(
				'where'	=> $where,
				'tables'=> $tables,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: tasks of projects with given status
	 *
	 * @param	String			$value			Comma-separated statuses
	 * @param	Boolean			$negate
	 * @return	Array|Boolean					Query parts / false if no statuses given
	 */
	public function Filter_projectstatus($value, $negate = false) {
		$statuses	= TodoyuArray::intExplode(',', $value, true, true);
		$queryParts	= false;

		if( sizeof($statuses) > 0 ) {
			$queryParts	= array(
				'where'	=> TodoyuSql::buildInListQueryPart($statuses, 'ext_project_project.status', true, $negate),
				'tables'=> array('ext_project_project'),
				'join'	=> array(self::TABLE . '.id_project = ext_project_project.id')
			);

		}

		return $queryParts;
	}



	/**
	 * Filters for tasks being publicly visible
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array					Query parts
	 */
	public function Filter_isPublic($value, $negate = false) {
		$tables	= array(self::TABLE);

		$isPublic	= $negate ? 0 : 1;
		$where		= self::TABLE . '.is_public = ' . $isPublic;

		$queryParts	= array(
			'tables'	=> $tables,
			'where'		=> $where
		);

		return $queryParts;
	}



	/**
	 * Filter condition: Task status in given status list?
	 *
	 * @param	String			$value		Comma-separated statuses
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no statuses given
	 */
	public function Filter_status($value, $negate = false) {
		$statusList	= TodoyuArray::intExplode(',', $value, true, true);
		$queryParts	= false;

		if( sizeof($statusList) > 0 ) {
			$tables	= array(self::TABLE);
			$compare= $negate ? 'NOT IN' : 'IN';

			$where	= self::TABLE . '.status ' . $compare . '(' . implode(',', $statusList) . ')';

			if( $this->containerMode ) {
				$where = '(' . $where . ' OR ' . self::TABLE . '.type = ' . TASK_TYPE_CONTAINER . ')';
			}

			$queryParts	= array(
				'tables'	=> $tables,
				'where'		=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: Task acknowledged by given person?
	 *
	 * @param	Integer			$value		Person ID
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts / false if no person ID given
	 */
	public function Filter_acknowledged($value, $negate = false) {
		$idPerson	= intval($value);
		$queryParts	= false;

		if( $idPerson !== 0 ) {
			$check	= $negate ? 0 : 1;
			$where	= '		' . self::TABLE . '.id_person_assigned	= ' . $idPerson .
					  ' AND	' . self::TABLE . '.is_acknowledged	= ' . $check;

			$queryParts	= array(
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: task acknowledged by current person
	 *
	 * @param	String		$value		Person ID
	 * @param	Boolean		$negate
	 * @return	Array					Query parts
	 */
	public function Filter_currentPersonHasAcknowledged($value, $negate) {
		$idPerson	= Todoyu::personid();
		$queryParts	= $this->Filter_acknowledged($idPerson, $negate);

		return $queryParts;
	}



	/**
	 * Get sub-tasks of the given task ID
	 *
	 * @param	Integer		$value		Task ID
	 * @param	Boolean		$negate
	 * @return	Array					Query parts
	 */
	public function Filter_parentTask($value, $negate = false) {
		$queryParts	= false;

		if( is_numeric($value) ) {
			$idTask	= intval($value);

			$where	= self::TABLE . '.id_parenttask ' . ( $negate ? '!=' : '=' ) . ' ' . $idTask;

			$queryParts	= array(
				'where'		=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: date_deadline
	 *
	 * @param	String		$date		Formatted (according to current locale) date string
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_deadlinedate($date, $negate = false) {
		return $this->makeFilter_date('date_deadline', $date, $negate);
	}



	/**
	 * Get the dynamic deadline
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_deadlinedateDyn($value, $negate) {
		$timeStamps = TodoyuSearchFilterHelper::getDynamicDateTimestamp($value, $negate);

		return $this->Filter_dateDyn($timeStamps, self::TABLE . '.date_deadline', $negate);
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
	 * Get the dynamic startdate
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_startdateDyn($value, $negate) {
		$timeStamps = TodoyuSearchFilterHelper::getDynamicDateTimestamp($value, $negate);

		return $this->Filter_dateDyn($timeStamps, self::TABLE . '.date_start', $negate);
	}



	/**
	 * Filter condition: date_end
	 * Contains fallback to date deadline
	 *
	 * @param	String		$date
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_enddate($date, $negate = false) {
		$queryParts	= false;
		$time		= TodoyuTime::parseDate($date);

		if( $time !== 0 ) {
			$info	= TodoyuSearchFilterHelper::getTimeAndLogicForDate($time, $negate);
			$fields	= 'IF(ext_project_task.date_end, ext_project_task.date_end, ext_project_task.date_deadline)';

			$queryParts = array(
				'where'	=> $fields . ' ' . $info['logic'] . ' ' . $info['timestamp']
			);
		}

		return $queryParts;
	}



	/**
	 * get the dynamic enddate
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_enddateDyn($value, $negate = false) {
		$date = TodoyuSearchFilterHelper::getDynamicDateTimestamp($value, $negate);
		$field = 'IF(ext_project_task.date_end, ext_project_task.date_end, ext_project_task.date_deadline)';

		return $this->Filter_dateDyn($date, $field, $negate);
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
		$refDate = TodoyuSearchFilterHelper::getDynamicDateTimestamp($value, $negate);

		return $this->Filter_dateDyn($refDate, self::TABLE . '.date_update', $negate);
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
	 * Get the dynamic creation date (date_create)
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_createdateDyn($value, $negate) {
		$timeStamps = TodoyuSearchFilterHelper::getDynamicDateTimestamp($value, $negate);

		return $this->Filter_dateDyn($timeStamps, self::TABLE . '.date_create', $negate);
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
		$date		= intval($date);
		$compare	= $negation ? '>=' : '<=';
		$fieldName	= $field;

		$where	= '(' .		$fieldName . ' ' . $compare . ' ' . $date
				. ' AND ' . $fieldName . ' > 0)';

		return array(
			'where' => $where
		);
	}



	/**
	 * Filter task by not being given ID (get all but given)
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_nottask($value, $negate = false) {
		$idTask	= intval($value);

		$where = self::TABLE . '.id != ' . $idTask;

		return array(
			'where'	=> $where
		);
	}



	/**
	 * Filter by type (task / container)
	 *
	 * @param	Integer			$value		Task type - 0: both, 1: TASK_TYPE_TASK / 2: TASK_TYPE_CONTAINER
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if both types wanted (no limitation needed than)
	 */
	public function Filter_type($value, $negate = false) {
		$type		= intval($value);
		$queryParts	= false;

		if( $type > 0 ) {
			$compare	= $negate ? '!=' : '=';
			$queryParts = array(
				'where'		=> self::TABLE . '.type ' . $compare . $type
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: Task has activity
	 *
	 * @param	Array		$value		Comma-separated activity IDs
	 * @param	Boolean		$negate
	 * @return	Array|Boolean			Query parts array / false if no activity IDs given
	 */
	public function Filter_activity($value, $negate = false) {
		$activityIDs= TodoyuArray::intExplode(',', $value);
		$queryParts	= false;

		if( sizeof($activityIDs) !== 0 ) {
			$queryParts	= array(
				'where'	=> TodoyuSql::buildInListQueryPart($activityIDs, self::TABLE . '.id_activity', true, $negate)
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition for project role
	 * The value is a combination between the project role and the person
	 *
	 * @param	String			$value		Format: PERSON:ROLE,ROLE,ROLE
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if not both person AND role(s) given
	 */
	public function Filter_projectrole($value, $negate = false) {
		$parts		= explode(':', $value);
		$idPerson	= intval($parts[0]);
		$roles		= TodoyuArray::intExplode(',', $parts[1]);

		$queryParts	= false;

		if( $idPerson !== 0 && sizeof($roles) > 0 ) {
			$tables	= array('ext_project_mm_project_person');

			$where	= '		ext_project_mm_project_person.id_person	= ' . $idPerson .
					  ' AND ' . TodoyuSql::buildInListQueryPart($roles, 'ext_project_mm_project_person.id_role', true, $negate);

			$join	= array(self::TABLE . '.id_project = ext_project_mm_project_person.id_project');

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition for all subTasks (recursive)
	 *
	 * @param	Integer		$value		Task ID
	 * @param	Boolean		$negate
	 * @return	Array|Boolean			Query parts / false if no task ID given
	 */
	public function Filter_subtask($value, $negate = false) {
		$idTask		= intval($value);
		$queryParts	= false;

		if( $idTask !== 0 ) {
			$subTasks	= TodoyuProjectTaskManager::getAllSubTaskIDs($idTask);

			if( sizeof($subTasks) > 0 ) {
				$compare= $negate ? 'NOT IN' : 'IN';
				$where	= self::TABLE . '.id ' . $compare . '(' . implode(',', $subTasks) . ')';

				$queryParts	= array(
					'where'	=> $where
				);
			}
		}

		return $queryParts;
	}



	/**
	 * Setup query parts for task date_... fields (create, update, start, end, deadline) filter
	 *
	 * @param	String			$field
	 * @param	Integer			$date		Formatted (according to current locale) date string
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no date timestamp given (or 1.1.1970 00:00)
	 */
	public static function makeFilter_date($field, $date, $negate = false) {
		return TodoyuSearchFilterHelper::makeFilter_date(self::TABLE, $field, $date, $negate);
	}



	/**
	 * Alias Method for TodoyuSearchFiltersetManager::Filter_filterObject for TaskFilter
	 *
	 * @param	Array		$value
	 * @param	Boolean		$negate
	 * @return	Boolean
	 */
	public static function Filter_filterObject(array $value, $negate = false) {
		return TodoyuSearchFiltersetManager::Filter_filterObject($value, $negate);
	}



	/**
	 * Order by date create
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	private function Sorting_Attribute($attribute, $desc = false) {
		return array(
			'order'	=> array(
				self::TABLE . '.' . $attribute . ' ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * Order by date create
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public function Sorting_DateCreate($desc = false) {
		return $this->Sorting_Attribute('date_create', $desc);
	}



	/**
	 * @param	Boolean $desc
	 * @return	Array
	 */
	public function Sorting_DateUpdate($desc = false) {
		return $this->Sorting_Attribute('date_update', $desc);
	}



	/**
	 * @param	Boolean $desc
	 * @return	Array
	 */
	public function Sorting_dateStart($desc = false) {
		return $this->Sorting_Attribute('date_start', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_dateEnd($desc = false) {
		return array(
			'order' => array(
				'IF( ' . self::TABLE .  '.date_end, ' . self::TABLE . '.date_end, ' . self::TABLE . '.date_deadline) ' . self::getSortDir($desc)
			)
		);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_dateDeadline($desc = false) {
		return $this->Sorting_Attribute('date_deadline', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_projectID($desc = false) {
		return $this->Sorting_Attribute('id_project', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_title($desc = false) {
		return $this->Sorting_Attribute('title', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_personAssigned($desc = false) {
		return $this->Sorting_Attribute('id_person_assigned', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_personOwner($desc = false) {
		return $this->Sorting_Attribute('id_person_owner', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_taskNumber($desc = false) {
		return $this->Sorting_Attribute('tasknumber', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_status($desc = false) {
		return $this->Sorting_Attribute('status', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_activity($desc = false) {
		return $this->Sorting_Attribute('id_activity', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_estimatedWorkload($desc = false) {
		return $this->Sorting_Attribute('estimated_workload', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_acknowledged($desc = false) {
		return $this->Sorting_Attribute('is_acknowledged', $desc);
	}



	/**
	 * @param	Boolean	$desc
	 * @return	Array
	 */
	public function Sorting_public($desc = false) {
		return $this->Sorting_Attribute('is_public', $desc);
	}

}

?>