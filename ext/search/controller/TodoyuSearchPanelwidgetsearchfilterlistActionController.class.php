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
 * Search panelwidget filterlist action controller
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchPanelwidgetSearchfilterlistActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('search', 'general:use');
	}



	/**
	 * Update search widget filter list
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		/**
		 * @var	TodoyuSearchPanelWidgetFilterList $panelWidget
		 */
		$panelWidget = TodoyuPanelWidgetManager::getPanelWidget('search', 'FilterList');

		return $panelWidget->renderContent();
	}



	/**
	 * @param	Array	$params
	 * @return	Integer				Separator's (filterset) ID
	 */
	public function saveNewSeparatorAction(array $params) {
		$type	= $params['type'];

		$data = array(
			'filterset'		=> 0,
			'type'			=> $type,
			'title'			=> TodoyuSearchFiltersetManager::validateTitle($type, trim($params['title'])),
			'is_separator'	=> '1'
		);

		$idFilterset = TodoyuSearchFiltersetManager::saveFiltersetSeparator($data);

		return $idFilterset;
	}

}

?>