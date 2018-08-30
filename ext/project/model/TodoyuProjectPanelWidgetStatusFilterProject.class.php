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
 * Panel widget: status filter base
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectPanelWidgetStatusFilterProject extends TodoyuProjectPanelWidgetStatusFilter {

	/**
	 * Initialize panel widget status filter
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 * @param	Integer		$idArea
	 */
	public function __construct(array $config, array $params = array(), $idArea = 0) {
		parent::__construct(
			'project',											// ext key
			'projectstatusfilter',								// panel widget ID
			'project.panelwidget-statusfilter.title.project',	// widget title text
			$config,											// widget config array
			$params,											// widget parameters
			$idArea												// area ID
		);

			// Define preference
		$this->pref	= 'panelwidget-projectstatusfilter';

			// Get selected status IDs
		$filterJSON			= json_encode($this->getSelectedStatuses());

			// Initialize JavaScript
		if( $this->isAllowed() ) {
			TodoyuPage::addJsInit('Todoyu.R.ProjectStatusFilter = new Todoyu.Ext.project.PanelWidget.ProjectStatusFilter(' . $filterJSON . ')');
		}
	}



	/**
	 * Get panelWidget statuses infos
	 *
	 * @return	Array
	 */
	protected function getStatusesInfos() {
		return TodoyuProjectProjectStatusManager::getStatusInfos();
	}



	/**
	 * Get selected status IDs
	 *
	 * @return	Array
	 */
	public function getSelectedStatuses() {
		$statusIDs	= TodoyuProjectPreferences::getPref($this->pref, 0, AREA);

		if( $statusIDs ) {
			$statusIDs = TodoyuArray::intExplode(',', $statusIDs);
		} else {
			$statusIDs = TodoyuProjectProjectStatusManager::getStatusIDs();
		}

		return $statusIDs;
	}



	/**
	 * Check panelWidget access permission
	 * Allowed if project area allowed and more than one status visible
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		$statuses	= TodoyuProjectProjectStatusManager::getStatuses();

		return Todoyu::allowed('project', 'general:use') && sizeof($statuses) > 1;
	}

}

?>