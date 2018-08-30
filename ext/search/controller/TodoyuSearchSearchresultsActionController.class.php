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
 * Searchresults action controller
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchSearchresultsActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('search', 'general:use');
	}



	/**
	 * Update search result for the submitted conditions
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		$tab			= $params['tab'];
		$idFilterset	= intval($params['filterset']);
		$conditions		= trim($params['conditions']);
		$conditions		= $conditions === '' ? array() : TodoyuArray::assure(json_decode($conditions, true));
		$conjunction	= trim($params['conjunction']);
		$sorting		= empty($params['sorting']) ? array() : TodoyuArray::assure(json_decode($params['sorting'], true));

		return TodoyuSearchFilterAreaRenderer::renderResults($tab, $idFilterset, $conditions, $conjunction, $sorting);
	}

}

?>