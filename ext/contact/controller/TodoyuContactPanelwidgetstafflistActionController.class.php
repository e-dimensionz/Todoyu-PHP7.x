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
 * Controller for project autocomplete
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPanelwidgetStafflistActionController extends TodoyuActionController {

	/**
	 * Check whether use of project area is allowed
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('contact', 'panelwidgets:staffSelector');
	}



	/**
	 * Get person list for current filter
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		$search	= trim($params['search']);
		/**
		 * @var	TodoyuContactPanelWidgetStaffList	$widget
		 */
		$widget	= TodoyuPanelWidgetManager::getPanelWidget('contact', 'StaffList');

		$widget->saveSearchText($search);

		return $widget->renderList();
	}

}

?>