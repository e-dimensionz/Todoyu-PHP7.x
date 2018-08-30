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
 * ActionController for project preferences
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectPreferenceActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access (project extension must be allowed)
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('project', 'general:use');
	}



	/**
	 * Save preference: project details expanded?
	 *
	 * @param	Array		$params
	 */
	public function detailsexpandedAction(array $params) {
		$idProject	= intval($params['item']);
		$expanded	= intval($params['value']) === 1;

		TodoyuProjectPreferences::saveExpandedDetails($idProject, $expanded);
	}



	/**
	 * Save task open/closed status
	 *
	 * @param	Array		$params
	 */
	public function taskopenAction(array $params) {
		$idTask		= intval($params['item']);
		$expanded	= intval($params['value']) === 1;

		TodoyuProjectPreferences::saveTaskExpandedStatus($idTask, $expanded);
	}



	/**
	 * Save opened sub tasks pref
	 *
	 * @param	Array		$params
	 */
	public function subtasksAction(array $params) {
		$idTask	= intval($params['item']);
		$isOpen	= intval($params['value']) === 1;

		TodoyuProjectPreferences::saveSubTasksVisibility($idTask, $isOpen, AREA);
	}



	/**
	 * Save preference of selected task status filter widget: selected status
	 *
	 * @param	Array		$params
	 */
	public function panelwidgettaskstatusfilterAction(array $params) {
		$selectedStatuses	= TodoyuArray::intExplode(',', $params['value'], true, true);

		$widget	= TodoyuPanelWidgetManager::getPanelWidget('project', 'StatusFilterTask');

		$widget->saveSelectedStatuses($selectedStatuses);
	}



	/**
	 * Project status filter panelwidget action: save selected status preference
	 *
	 * @param	Array		$params
	 */
	public function panelwidgetprojectstatusfilterAction(array $params) {
		$selectedStatuses	= TodoyuArray::intExplode(',', $params['value'], true, true);

		$widget	= TodoyuPanelWidgetManager::getPanelWidget('project', 'StatusFilterProject');

		$widget->saveSelectedStatuses($selectedStatuses);
	}



	/**
	 * General panelWidget action, saves collapse status
	 *
	 * @param	Array	$params
	 */
	public function pwidgetAction(array $params) {
		$idWidget	= $params['item'];
		$value		= $params['value'];

		TodoyuPanelWidgetManager::saveCollapsedStatus(EXTID_PROJECT, $idWidget, $value);
	}
}

?>