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
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFiltersetActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('search', 'general:area');
	}



	/**
	 * Save current conditions as "current" filterset (being restored when reloading the tab)
	 *
	 * @param	Array	$params
	 */
	public function saveAsCurrentAction(array $params) {
		$type		= $params['type'];
		$idFilterset	= TodoyuSearchManager::getIDCurrentTabFilterset($type);

		$data = array(
			'filterset'		=> $idFilterset,
			'current'		=> '1',
			'type'			=> $type,
			'conjunction'	=> $params['conjunction'],
			'resultsorting'	=> trim($params['sorting']),
			'conditions'	=> TodoyuArray::assureFromJSON($params['conditions'])
		);

			// Save filterset and have conditions updated (store newly or update existing)
		TodoyuSearchFiltersetManager::saveFilterset($data);
	}



	/**
	 * Save current conditions with their settings as new filterset
	 *
	 * @param	Array	$params
	 * @return	Integer
	 */
	public function saveAsNewAction(array $params) {
		$type		= $params['type'];
		$conditions = empty($params['conditions']) ? array() : json_decode($params['conditions'], true);

		$data = array(
			'filterset'		=> 0,
			'type'			=> $type,
			'title'			=> TodoyuSearchFiltersetManager::validateTitle($type, trim($params['title'])),
			'conjunction'	=> $params['conjunction'],
			'conditions'	=> $conditions,
			'resultsorting'	=> trim($params['sorting'])
		);

		$idFilterset = TodoyuSearchFiltersetManager::saveFilterset($data);

		TodoyuSearchPreferences::saveActiveFilterset($type, $idFilterset);

		return $idFilterset;
	}



	/**
	 * Save conditions as filterset
	 *
	 * @param	Array	$params
	 * @return	Integer
	 */
	public function saveAction(array $params) {
		$idFilterset= intval($params['filterset']);
		$conditions = empty($params['conditions']) ? array() : json_decode($params['conditions'], true);
		$conjunction= $params['conjunction'];
		$tab		= $params['tab'];
		$sorting	= trim($params['sorting']);

		$data = array(
			'conjunction'	=> $conjunction,
			'resultsorting'	=> $sorting
		);
		// @todo	use TodoyuSearchFiltersetManager::saveFilterset instead - it updates the set and conditions
		TodoyuSearchFiltersetManager::updateFilterset($idFilterset, $data);
		TodoyuSearchFilterConditionManager::saveFilterConditions($idFilterset, $conditions);

		TodoyuSearchPreferences::saveActiveFilterset($tab, $idFilterset);

		return $idFilterset;
	}

}

?>