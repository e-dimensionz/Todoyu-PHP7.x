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
 * Search Ext action controller
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchExtActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('search', 'general:area');
	}



	/**
	 * Render search area view
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		TodoyuFrontend::setActiveTab('search');

		TodoyuPage::init('ext/search/view/ext.tmpl');
		TodoyuPage::setTitle('search.ext.page.title');

			// Get given tab parameter or load preference
		$activeTab	= ( ! empty($params['tab']) ) ? $params['tab'] : TodoyuSearchPreferences::getActiveTab();

		$idFilterset	= TodoyuSearchManager::getIDCurrentTabFilterset($activeTab);

		$panelWidgets	= TodoyuSearchRenderer::renderPanelWidgets();
		$tabs			= TodoyuSearchFilterAreaRenderer::renderTypeTabs($activeTab);
		$filterArea		= TodoyuSearchFilterAreaRenderer::renderFilterArea($activeTab, $idFilterset);

		TodoyuPage::setPanelWidgets($panelWidgets);
		TodoyuPage::setTabs($tabs);
		TodoyuPage::set('filterArea', $filterArea);

		return TodoyuPage::render();
	}



	/**
	 * Loads whole filter area on tab change
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function tabAction(array $params) {
		$tab		= $params['tab'];

		$idFilterset	= TodoyuSearchManager::getIDCurrentTabFilterset($tab);

		TodoyuSearchPreferences::saveActiveTab($tab);

			// Save preferences
		if( $idFilterset === 0 ) {
			$data = array(
				'type'			=> $tab,
				'current'		=> '1',
				'conditions'	=> array(),
				'conjunction'	=> strtoupper($params['conjunction']) === 'AND' ? 'AND' : 'OR',
				'resultsorting'	=> trim($params['sorting'])
			);

			TodoyuSearchFiltersetManager::saveFilterset($data);
		}

		return TodoyuSearchFilterAreaRenderer::renderFilterArea($tab, $idFilterset);
	}

}

?>