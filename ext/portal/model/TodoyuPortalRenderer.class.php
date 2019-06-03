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
 * Portal renderer
 *
 * @name		Portal renderer
 * @package		Todoyu
 * @subpackage	Portal
 */
class TodoyuPortalRenderer {

	/**
	 * Extension key
	 *
	 * @var	String
	 */
	const EXTKEY = 'portal';




	/**
	 * Render tab headers for portal
	 *
	 * @param	String	$activeTab
	 * @return	String
	 */
	public static function renderTabHeads($activeTab = '') {
		$name		= 'portal';
		$jsHandler	= 'Todoyu.Ext.portal.Tab.onSelect.bind(Todoyu.Ext.portal.Tab)';
		$tabs		= TodoyuPortalManager::getTabs();

		if( empty($activeTab) ) {
			$activeTab	= TodoyuPortalPreferences::getActiveTab();
		}

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}



	/**
	 * Render content of a portal tab (call registered render function)
	 *
	 * @param	String		$tabKey
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderTabContent($tabKey, array $params = array()) {
		$tab	= TodoyuPortalManager::getTabConfig($tabKey);

		if( TodoyuFunction::isFunctionReference($tab['contentFunc']) ) {
			return TodoyuFunction::callUserFunction($tab['contentFunc'], $params);
		} else {
			TodoyuLogger::logError('Missing render function for tab "' . $tabKey . '"');
			return 'Found no render function for this tab';
		}
	}



	/**
	 * Get label of selection tab in portal
	 *
	 * @param	Boolean		$count
	 * @return	String
	 */
	public static function getSelectionTabLabel($count = true) {
		$label	= Todoyu::Label('portal.ext.tab.selection');

		if( $count ) {
			$numResults	= TodoyuPortalManager::getSelectionCount();
			$label		= $label . ' (' . $numResults . ')';
		}

		return $label;
	}



	/**
	 * Get content of selection tab in portal
	 * Task-list based on the selected filters in filterPresetList panelWidget
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function renderSelectionTabContent(array $params = array()) {
			// Check whether filterSets are available as parameters
		if( isset($params['filtersets']) ) {
			$filtersetIDs	= TodoyuArray::intval($params['filtersets'], true, true);
			TodoyuPortalPreferences::saveSelectionTabFiltersetIDs($filtersetIDs);
		} else {
			$filtersetIDs	= TodoyuPortalPreferences::getSelectionTabFiltersetIDs();
		}

			// Check if type is available as parameter
		if( ! isset($params['type']) ) {
			if( !empty($filtersetIDs)  ) {
				$type	= TodoyuSearchFiltersetManager::getFiltersetType($filtersetIDs[0]);
			} else {
				$type	= 'task';
			}
		} else {
			$type	= trim($params['type']);
		}

			// Send items amount header to update filter-tab, render items listing
		if( empty($filtersetIDs) ) {
				// No filterset selected
			TodoyuHeader::sendTodoyuHeader('items', 0);

			return self::renderNoSelectionMessage();
		} else {
				// Get items, send amount header, render listing
			$resultItemIDs	= TodoyuSearchFiltersetManager::getFiltersetsResultItemIDs($filtersetIDs, 200);

				// If only one filterset, get real count
			if( !empty($filtersetIDs) && sizeof($filtersetIDs) === 1 ) {
				$totalCount	= TodoyuSearchFiltersetManager::getFiltersetCount($filtersetIDs[0]);
			} else {
				$totalCount	= !empty($resultItemIDs) ? sizeof($resultItemIDs) : 0;
			}

			TodoyuHeader::sendTodoyuHeader('items', $totalCount);

			return TodoyuSearchRenderer::renderResultsListing($type, $resultItemIDs);
		}
	}



	/**
	 * Render message if no filterset is selected
	 *
	 * @return	String
	 */
	private static function renderNoSelectionMessage() {
		$tmpl	= 'ext/portal/view/selection-nofilterset.tmpl';

		return Todoyu::render($tmpl);
	}



	/**
	 * Render panel widgets
	 *
	 * @return	String
	 */
	public static function renderPanelWidgets() {
		return TodoyuPanelWidgetRenderer::renderPanelWidgets(self::EXTKEY);
	}

}

?>