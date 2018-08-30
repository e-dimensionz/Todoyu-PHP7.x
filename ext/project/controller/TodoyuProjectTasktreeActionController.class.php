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
 * Tasktree action controller
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTasktreeActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access (project extension must be allowed)
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('project', 'general:area');
	}



	/**
	 * Update task tree in project view
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		$idProject	= intval($params['project']);
		$filter		= $params['filter'];

			// If a filter is submitted
		if( ! is_null($filter) ) {
			TodoyuProjectProjectManager::updateProjectTreeFilters($filter['name'], $filter['value']);
		}

		return TodoyuProjectProjectRenderer::renderProjectTaskTree($idProject);
	}

}

?>