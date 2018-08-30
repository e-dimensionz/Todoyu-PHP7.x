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
 * General renderer for tabs inside content items (e.g. project, task, container, ...)
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuContentItemTabRenderer {

	/**
	 * Render all item tabs (only content of the active tab)
	 *
	 * @param	String		$extKey			Extension that implements the item containing the tabs
	 * @param	String		$itemKey		Item containing the tabs, e.g. project / task / container / ...
	 * @param	Integer		$idItem
	 * @param	String		$activeTab
	 * @return	String
	 */
	public static function renderTabs($extKey, $itemKey, $idItem, $activeTab = '') {
		$idItem		= intval($idItem);
		$activeTab	= trim($activeTab) === '' ? TodoyuContentItemTabPreferences::getActiveTab($extKey, $itemKey, $idItem) : $activeTab;
		$tmpl		= 'core/view/contentitem-tabcontainer.tmpl';

		$data = array(
			'itemName'		=> $itemKey,
			'idItem'		=> $idItem,
			'tabHeads'		=> self::renderTabHeads($extKey, $itemKey, $idItem, $activeTab),
			'tabContents'	=> self::renderTabsContent($extKey, $itemKey, $idItem, $activeTab),
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render the heads of all project detail tabs
	 *
	 * @param	String		$extKey
	 * @param	String		$itemKey
	 * @param	Integer		$idItem
	 * @param	String		$activeTab
	 * @return	String
	 */
	public static function renderTabHeads($extKey, $itemKey, $idItem, $activeTab = '') {
		$idItem		= intval($idItem);
		$activeTab	= $activeTab === '' ? TodoyuContentItemTabPreferences::getActiveTab($extKey, $itemKey, $idItem) : $activeTab;

		$name		= $itemKey . '-' . $idItem;
		$jsHandler	= 'Todoyu.ContentItemTab.onSelect.bind(Todoyu.ContentItemTab, \'' . $extKey . '\')';

		$tabs		= TodoyuContentItemTabManager::getTabs($extKey, $itemKey, $idItem);

			// Add special fields for item tabs
		foreach($tabs as $index => $tab) {
			$tabs[$index]['htmlId']		= $itemKey . '-' . $idItem . '-tab-' . $tab['id'];
			$tabs[$index]['classKey']	= $tab['id'] . '-' . $idItem;
		}

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}



	/**
	 * Render item tab container with content tab of the active item
	 *
	 * @param	String		$extKey
	 * @param	String		$itemKey
	 * @param	Integer		$idItem
	 * @param	String		$activeTab
	 * @return	String
	 */
	public static function renderTabsContent($extKey, $itemKey, $idItem, $activeTab = '') {
		$idItem		= intval($idItem);

		$tabsConfig	= TodoyuContentItemTabManager::getTabs($extKey, $itemKey, $idItem);
		$tabContent	= '';
		$activeTab	= empty($activeTab) ? TodoyuContentItemTabPreferences::getActiveTab($extKey, $itemKey, $idItem) : $activeTab;

			// Only render active tab
		foreach($tabsConfig as $tabConfig) {
			if( $tabConfig['id'] == $activeTab ) {
				$tabContent	= TodoyuFunction::callUserFunction($tabConfig['content'], $idItem);
				break;
			}
		}

		$data	= array(
			'itemName'	=> $itemKey,
			'tabHtml'	=> $tabContent,
			'tabKey'	=> $activeTab,
			'idItem'	=> $idItem
		);
		$tmpl	= 'core/view/contentitem-tabs.tmpl';

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render content of an item tab
	 *
	 * @param	String		$extKey
	 * @param	String		$itemKey
	 * @param	Integer		$idItem
	 * @param	String		$tab
	 * @return	String
	 */
	public static function renderTabContent($extKey, $itemKey, $idItem, $tab) {
		$idItem		= intval($idItem);
		$tabConfig	= TodoyuContentItemTabManager::getTabConfig($extKey, $itemKey, $tab);

		return TodoyuFunction::callUserFunction($tabConfig['content'], $idItem);
	}

}