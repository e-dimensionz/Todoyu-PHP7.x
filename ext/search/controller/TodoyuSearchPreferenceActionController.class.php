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
class TodoyuSearchPreferenceActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('search', 'general:use');
	}



	/**
	 * Save current selected filterset
	 *
	 * @param	Array	$params
	 */
	public function saveCurrentFilterSetAction(array $params) {
		$idFilterset	= intval($params['value']);

		TodoyuSearchPreferences::saveCurrentFilter($idFilterset);
	}



	/**
	 * Save preference of active search tab
	 *
	 * @param	Array	$params
	 */
	public function saveActiveTabAction(array $params) {
		$tab	= $params['value'];

		TodoyuSearchPreferences::saveActiveTab($tab);
	}



	/**
	 * Save active filterset preference
	 *
	 * @param	Array	$params
	 */
	public function activeFiltersetAction(array $params) {
		$idFilterset= intval($params['item']);
		$tab		= $params['value'];

		TodoyuSearchPreferences::saveActiveFilterset($tab, $idFilterset);
	}



	/**
	 * Rename a filterset
	 *
	 * @param	Array	$params
	 */
	public function renameFiltersetAction(array $params) {
		Todoyu::restrict('search', 'general:area');

		$idFilterset= intval($params['item']);
		$title		= trim($params['value']);

		TodoyuSearchFiltersetManager::renameFilterset($idFilterset, $title);
	}



	/**
	 * Update the visibility of a filterset
	 *
	 * @param	Array	$params
	 */
	public function toggleFiltersetVisibilityAction(array $params) {
		Todoyu::restrict('search', 'general:area');

		$idFilterset= intval($params['item']);
		$isVisible	= intval($params['value']) === 1;

		TodoyuSearchFiltersetManager::updateFiltersetVisibility($idFilterset, $isVisible);
	}



	/**
	 * Delete a filterset with its condition and all delete it from preferences possibly using it
	 *
	 * @param	Array	$params
	 */
	public function deleteFiltersetAction(array $params) {
		Todoyu::restrict('search', 'general:area');

		$idFilterset	= intval($params['item']);

		TodoyuSearchFiltersetManager::deleteFilterset($idFilterset, true);
	}



	/**
	 * Update order of the filtersets
	 *
	 * @param	Array	$params
	 */
	public function filtersetOrderAction(array $params) {
		$orderData	= json_decode($params['value'], true);

		TodoyuSearchFiltersetManager::updateOrder($orderData['items']);
	}



	/**
	 * Save headlet open status for quicksearch headlet
	 *
	 * @param	Array		$params
	 */
	public function headletOpenAction(array $params) {
		$open	= intval($params['open']) === 1 ? 1 : 0;

		TodoyuSearchPreferences::savePref('headletOpen', $open, 0, true);
	}
}

?>