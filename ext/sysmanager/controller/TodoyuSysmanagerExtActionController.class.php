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
 * Sysmanager Ext action controller
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerExtActionController extends TodoyuActionController {

	/**
	 * Restrict access to admins
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('sysmanager', 'general:use');
	}



	/**
	 * Sysmanager extension default action controller method
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		TodoyuPage::init('ext/sysmanager/view/ext.tmpl');
		TodoyuPage::setTitle('sysmanager.ext.page.title');

		TodoyuFrontend::setActiveTab('none');

			// Load config
		TodoyuExtensions::loadAllSysmanager();

			// Get current sysmanager module
		$module	= trim($params['mod']);
		if( !$module || !TodoyuSysmanagerManager::isModule($module) ) {
			$module = TodoyuSysmanagerManager::getActiveModule();
		} else {
				// Save current module
			TodoyuSysmanagerPreferences::saveActiveModule($module);
		}

		TodoyuPage::addBodyClass('module' . ucfirst($module));

			// Add colors stylesheet to page
		TodoyuColors::generate();

		$panelWidgets	= TodoyuSysmanagerRenderer::renderPanelWidgets();
		$fullContent	= TodoyuSysmanagerRenderer::renderModule($module);

		TodoyuPage::setPanelWidgets($panelWidgets);
		TodoyuPage::setFullContent($fullContent);

			// Render output
		return TodoyuPage::render();
	}



	/**
	 * Load and display module
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function moduleAction(array $params) {
		$module	= trim($params['module']);

			// Save current module
		TodoyuSysmanagerPreferences::saveActiveModule($module);

		return TodoyuSysmanagerRenderer::renderModule($module, $params);
	}

}

?>