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
 * Preference action controller for portal extension
 *
 * @package		Todoyu
 * @subpackage	Portal
 */
class TodoyuPortalPreferenceActionController extends TodoyuActionController {

	/**
	 * Initialize: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('portal', 'general:use');
	}



	/**
	 * Save active tab ID preference
	 *
	 * @param	Array	$params
	 */
	public function tabAction(array $params) {
		$idTab	= intval($params['value']);

		TodoyuPortalPreferences::saveActiveTab($idTab);
	}



	/**
	 * Save preference: currently selected filtersets for selection tab
	 *
	 * @param	Array	$params
	 */
	public function filtersetsAction(array $params) {
		$filtersetIDs	= explode(',', $params['value']);
		$filtersetIDs	= TodoyuArray::intval($filtersetIDs, true, true);

		TodoyuPortalPreferences::saveSelectionTabFiltersetIDs($filtersetIDs);
	}



	/**
	 * Save task being expanded / collapsed.
	 * $params contains task ID and expand-state.
	 *		task ID:	$params['item'],
	 *		expanded:	$params['value'] === 1
	 *
	 * @param	Array	$params
	 */
	public function taskOpenAction(array $params) {
		$idTask		= intval($params['item']);
		$isExpanded	= intval($params['value']) === 1;

		TodoyuPortalPreferences::saveTaskExpandedStatus($idTask, $isExpanded);
	}



	/**
	 * General panelWidget action, saves collapse status
	 *
	 * @param	Array	$params
	 */
	public function pwidgetAction(array $params) {
		$idWidget	= $params['item'];
		$value		= $params['value'];

		TodoyuPanelWidgetManager::saveCollapsedStatus(EXTID_PORTAL, $idWidget, $value);
	}

}

?>