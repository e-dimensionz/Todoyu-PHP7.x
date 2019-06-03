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
 * Task search
 * Delivers search results for task to the search engine
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskSearch implements TodoyuSearchEngineIf {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_project_task';



	/**
	 * Search task which match the keywords
	 *
	 * @param	Array		$find		Keywords which must be in the task
	 * @param	Array		$ignore		Keywords which must not be in the task
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function searchTasks(array $find, array $ignore = array(), $limit = 200) {
		$limit	= intval($limit);

			// If keyword is a task number, directly get the task
		if( sizeof($find) === 1 ) {
			if( TodoyuProjectTaskManager::isTaskNumber($find[0], true) ) {
				$idTask	= TodoyuProjectTaskManager::getTaskIDByTaskNumber($find[0]);

				return array($idTask);
			}
		}

			// Task full-text search
		$table	= self::TABLE;
		$fields	= array('id_project', 'tasknumber', 'description', 'title');

		$addToWhere	= self::getAddToWhereRightsClause();

		return TodoyuSearch::searchTable($table, $fields, $find, $ignore, $limit, $addToWhere);
	}



	/**
	 * Get render arrays for select options of task autocompleter suggestion
	 *
	 * @param	Array	$find		Array of words to search for
	 * @param	Array	$ignore		Array of words to be ignored
	 * @param	Integer	$limit
	 * @return	Array
	 */
	public static function getSuggestions(array $find, array $ignore = array(), $limit = 5) {
		$limit		= intval($limit);
		$suggestions= array();

			// Search matching projects
		$taskIDs	= self::searchTasks($find, $ignore, $limit);

		if( !empty($taskIDs) ) {
			$fields	= '	t.id,
						t.id_project,
						t.tasknumber,
						t.title,
						p.title as project,
						c.shortname as company';
			$table	= self::TABLE . ' t,
						ext_project_project p,
						ext_contact_company c';
			$where	= '		t.id_project = p.id
						AND	p.id_company= c.id
						AND t.deleted = 0
						AND p.deleted = 0
						AND	t.id IN(' . implode(',', $taskIDs) . ')';
			$order	= '	t.date_create DESC';

			$tasks	= Todoyu::db()->getArray($fields, $table, $where, '', $order);

				// Assemble found task suggestions
			foreach($tasks as $task) {
				if( TodoyuProjectTaskRights::isSeeAllowed($task['id']) ) {
					$labelTitle = TodoyuString::wrap($task['title'], '<span class="keyword">|</span>') . ' | ' . $task['id_project'] . '.' . $task['tasknumber'];

					$suggestions[] = array(
						'labelTitle'=> $labelTitle,
						'labelInfo'	=> $task['company'] . ' | ' . $task['project'],
						'title'		=> strip_tags($labelTitle),
						'onclick'	=> 'Todoyu.goToHashURL(\'index.php?ext=project&amp;project=' . $task['id_project'] . '&amp;task=' . $task['id'] . '\', \'task-' . $task['id'] . '\')'
					);
				}
			}
		}

		return $suggestions;
	}



	/**
	 * Search task by title, description, project and task number
	 *
	 * if there is a . in the sword explode it and search by id_project (1st parameter of explode) and task number (2nd parameter of explode)
	 *
	 * else create a normal like search
	 *
	 * @param	String	$sword
	 * @return	Array
	 */
	public static function searchTask($sword) {
		$fields	= array('id', 'title', 'description', 'id_project', 'tasknumber');
		$table	= self::TABLE;

		if( strstr($sword, '.') ) {
			list($project, $taskNumber) = explode('.', $sword);
			$where = 'id_project = '.intval($project) . ' AND tasknumber = ' . intval($taskNumber);
		} else {
			$searchWords = TodoyuArray::trimExplode(' ', $sword, true);

			$where = TodoyuSql::buildLikeQueryPart($searchWords, $fields);
		}

		if( $where ) {
			$where.= ' AND deleted = 0';
		}

		$tasks = Todoyu::db()->getArray(implode(',', $fields), $table, $where);

		return $tasks;
	}



	/**
	 * Returns the rights clause query for task
	 *
	 * @return	String
	 */
	protected static function getAddToWhereRightsClause() {
		$addToWhere = ' AND deleted = 0';

			// Add limitations for non-admin persons
		if( ! TodoyuAuth::isAdmin() ) {
			if( ! Todoyu::allowed('project', 'seetask:seeAll') ) {
				$addToWhere .= ' AND ext_project_task.id_person_assigned = ' . Todoyu::personid();
			} else {
					// Limit to selected status
				$statusesSee= array_keys(TodoyuProjectTaskStatusManager::getStatuses('see'));
				if( count($statusesSee) > 0 ) {
					$addToWhere .= ' AND ' . TodoyuSql::buildInListQueryPart($statusesSee, 'ext_project_task.status');
				} else {
						// Rights do not permit user to see tasks in any status!
					return ' AND 0';
				}
			}

				// Limit to tasks which are in available projects
			if( ! Todoyu::allowed('project', 'project:seeAll') ) {
				$availableProjects = TodoyuProjectProjectManager::getAvailableProjectsForPerson();
				if( !empty($availableProjects) ) {
					$addToWhere	.= ' AND ext_project_task.id_project IN(' . implode(',', $availableProjects) . ')';
				}
			}

				// Add public filter for all externals (not internal)
			if( ! Todoyu::person()->isInternal() ) {
				$addToWhere	.= ' AND ext_project_task.is_public = 1';
			}
		}

		return $addToWhere;
	}
}

?>