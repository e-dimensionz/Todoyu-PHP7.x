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
 * Search actionpanel action controller
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchActionpanelActionController extends TodoyuActionController	{

	/**
	 * Initialize controller: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('search', 'general:area');
	}



	/**
	 * Controller to catch export from the search area
	 *
	 * @param	Array	$params
	 */
	public function exportAction($params) {
		$exportName	= $params['exportname'];
		$type		= $params['tab'];

		$conditions	= trim($params['conditions']);
		$conditions	= $conditions === '' ? array() : TodoyuArray::assure(json_decode($conditions, true));

		$conjunction= $params['conjunction'];

		TodoyuSearchActionPanelManager::dispatchExport($exportName, $type, $conditions, $conjunction);
	}

}

?>