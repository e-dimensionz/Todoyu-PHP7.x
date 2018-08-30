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
 * Manager for Portal
 *
 * @package		Todoyu
 * @subpackage	Portal
 */
class TodoyuPortalManager {

	/**
	 * Add a tab to portal view
	 *
	 * @param	String		$key			Key of the tab
	 * @param	String		$labelFunc		Function which renders the label
	 * @param	String		$contentFunc	Function which renders the content
	 * @param	Integer		$position		Tab position (left to right)
	 */
	public static function addTab($key, $labelFunc, $contentFunc, $position = 100) {
		Todoyu::$CONFIG['EXT']['portal']['tabs'][$key] = array(
			'key'			=> $key,
			'labelFunc'		=> $labelFunc,
			'contentFunc'	=> $contentFunc,
			'position'		=> intval($position)
		);
	}



	/**
	 * Get config of all added tabs sorted by position
	 *
	 * @return	Array
	 */
	public static function getTabsConfig() {
		TodoyuExtensions::loadAllPage();

		$tabs	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['portal']['tabs']);

		return TodoyuArray::sortByLabel($tabs, 'position');
	}



	/**
	 * Get config of a tab
	 *
	 * @param	String		$tabKey
	 * @return	Array
	 */
	public static function getTabConfig($tabKey) {
		TodoyuExtensions::loadAllPage();

		return TodoyuArray::assure(Todoyu::$CONFIG['EXT']['portal']['tabs'][$tabKey]);
	}



	/**
	 * Get tabs config
	 *
	 * @return	Array
	 */
	public static function getTabs() {
		$tabs	= self::getTabsConfig();

			// Get label, content list counter, 'active' or not-state
		foreach($tabs as $index => $tab) {
			$tabs[$index]['id']		= $tab['key'];
			$tabs[$index]['label']	= TodoyuFunction::callUserFunction($tab['labelFunc'], true);
		}

		return $tabs;
	}



	/**
	 * Add items to task context menu
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$items
	 * @return	Array
	 */
	public static function getTaskContextMenuItems($idTask, array $items) {
		$idTask	= intval($idTask);

			// Only show it in portal and if the task is not deleted
		if( AREA === EXTID_PORTAL && ! TodoyuProjectTaskManager::isDeleted($idTask) ) {
				// Add special portal items
			$ownItems	= Todoyu::$CONFIG['EXT']['portal']['ContextMenu']['Task'];
			$items		= array_merge_recursive($items, $ownItems);

				// Remove clone function
			unset($items['actions']['submenu']['clone']);

				// Remove add function for task and container
			unset($items['add']['submenu']['task']);
			unset($items['add']['submenu']['container']);
		}

		return $items;
	}



	/**
	 * Get number of result items for selection tab with currently selected filtersets
	 *
	 * @return	Integer
	 */
	public static function getSelectionCount() {
		$filtersetIDs	= TodoyuPortalPreferences::getSelectionTabFiltersetIDs();
		$numResults		= TodoyuSearchFiltersetManager::getFiltersetsCount($filtersetIDs);

		return $numResults;
	}



	/**
	 * Hook called before page is rendered completely
	 * - Add portal sub menu entries
	 */
	public static function hookRenderPage() {
		$tabsConfig	= self::getTabsConfig();
		$pos		= 0;

			// Add all registered tabs
		foreach($tabsConfig as $tabConfig) {
			$label	= TodoyuFunction::callUserFunction($tabConfig['labelFunc'], false);

			TodoyuFrontend::addSubmenuEntry('portal', $tabConfig['key'], $label, 'index.php?ext=portal&amp;tab=' . $tabConfig['key'], $pos++);
		}
	}

}

?>