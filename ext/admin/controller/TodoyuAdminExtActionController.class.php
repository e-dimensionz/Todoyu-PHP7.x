<?php
/****************************************************************************
 * todoyu is published under the BSD License:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
 * Admin Ext action controller
 *
 * @package		Todoyu
 * @subpackage	Admin
 */
class TodoyuAdminExtActionController extends TodoyuActionController {

	/**
	 * Restrict access to admins
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		TodoyuRightsManager::restrictAdmin();
	}



	/**
	 * Admin extension default action controller method
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		TodoyuPage::init('ext/admin/view/ext.tmpl');
		TodoyuPage::setTitle('admin.ext.page.title');

		TodoyuFrontend::setActiveTab('none');

		// Load config
		TodoyuExtensions::loadAllConfig('admin');

		// Get current admin module
		$module	= $params['mod'];
		if( ! TodoyuAdminManager::isModule($module) ) {
			$module = TodoyuAdminManager::getActiveModule();
		} else {
			// Save current module
			TodoyuAdminPreferences::saveActiveModule($module);
		}

		TodoyuPage::addBodyClass('module' . ucfirst($module));

		// Add colors stylesheet to page
		TodoyuColors::generate();

		$panelWidgets	= TodoyuAdminRenderer::renderPanelWidgets();
		$fullContent	= TodoyuAdminRenderer::renderModule($module);

		TodoyuPage::setPanelWidgets($panelWidgets);
		TodoyuPage::setFullContent($fullContent);

		// Render output
		return TodoyuPage::render();
	}



	/**
	 * Handle unknown actions (call default action)
	 *
	 * @param	String	$actionName
	 * @param	Array	$params
	 * @return	String
	 */
	public function _unknownAction($actionName, array $params) {
		return $this->defaultAction($params);
	}

}

?>