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
 * Default portal action controller
 *
 * @package		Todoyu
 * @subpackage	Portal
 */
class TodoyuPortalExtActionController extends TodoyuActionController {


	/**
	 * Initialize: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('portal', 'general:use');
	}



	/**
	 * Portal default action: render portal view
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
			// Activate FE tab
		TodoyuFrontend::setActiveTab('portal');

			// Setup page to be rendered
		TodoyuPage::init('ext/portal/view/ext.tmpl');
		TodoyuPage::setTitle('portal.ext.page.title');

			// Get active tab
		$activeTab	= isset( $params['tab'] ) ? $params['tab'] : NULL;
		if( ! empty($activeTab) ) {
			TodoyuPortalPreferences::saveActiveTab($activeTab);
		} else {
			$activeTab = TodoyuPortalPreferences::getActiveTab();
		}

			// Panel widgets
		$panelWidgets	= TodoyuPortalRenderer::renderPanelWidgets();
			// Tab-heads
		$tabHeads		= TodoyuPortalRenderer::renderTabHeads($activeTab);
			// Render active tab, tab content
		$activeTabContent	= TodoyuPortalRenderer::renderTabContent($activeTab);

		TodoyuPage::set('panelWidgets', $panelWidgets);
		TodoyuPage::set('tabHeads', $tabHeads);
		TodoyuPage::set('activeTabContent', $activeTabContent);

			// Add JS onLoad functions
//		TodoyuPage::addJsInit('Todoyu.Ext.portal.init()', 100, true);

			// Display output
		return TodoyuPage::render();
	}

}

?>