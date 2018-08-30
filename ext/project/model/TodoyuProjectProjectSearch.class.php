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
 * Project search
 * Delivers search results for project to the search engine
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProjectSearch implements TodoyuSearchEngineIf {

	/**
	 * Default table for database requests
	 */
	const TABLE = 'ext_project_project';



	/**
	 * Search project in full-text mode. Return the ID of the matching projects
	 *
	 * @todo rewrite whole method with filters if possible
	 *
	 * @param	Array		$searchWords		Keywords which have to be in the projects
	 * @param	Array		$ignoreWords		Keywords which must not be in the project
	 * @param	Integer		$limit
	 * @return	Array		Project IDs
	 */
	public static function searchProjects(array $searchWords, array $ignoreWords = array(), $limit = 200) {
		return TodoyuProjectProjectManager::searchProjects($searchWords, array(), $ignoreWords, $limit);
	}



	/**
	 * Get project suggestions (label and onclick)
	 *
	 * @param	Array		$find		Keywords which have to be in the projects
	 * @param	Array		$ignore		Keywords which must not be in the project
	 * @param	Integer		$limit
	 * @return	Array		Project IDs
	 */
	public static function getSuggestions(array $find, array $ignore = array(), $limit = 5) {
		$limit			= intval($limit);
		$suggestions	= array();

			// Search matching projects
		$projectIDs		= self::searchProjects($find, $ignore, $limit);

			// Get project details
		if( sizeof($projectIDs) > 0 ) {
			$fields	= '	p.id,
						p.title,
						p.status,
						c.title company';
			$table	= self::TABLE . ' p,
						ext_contact_company c';
			$where	= '		p.id IN(' . implode(',', $projectIDs) . ')
						AND	p.id_company	= c.id';
			$order	= '	p.date_create DESC';

			$projects	= Todoyu::db()->getArray($fields, $table, $where, '', $order);
//			$status		= TodoyuProjectProjectStatusManager::getStatusLabels();

				// Assemble project suggestions array
			foreach($projects as $project) {
				if( TodoyuProjectProjectRights::isSeeAllowed($project['id']) ) {
					$labelTitle = TodoyuString::wrap($project['title'], '<span class="keyword">|</span>') . ' | ' . $project['id'];
					$suggestions[] = array(
						'labelTitle'=> $labelTitle,
						'labelInfo'	=> $project['company'],
						'title'		=> strip_tags($labelTitle),
						'onclick'	=> 'location.href=\'index.php?ext=project&amp;project=' . $project['id'] . '\''
					);
				}
			}
		}

		return $suggestions;
	}

}

?>