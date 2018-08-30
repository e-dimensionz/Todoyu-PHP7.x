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
 * Manager for tabs of content items (project, task, container, ...)
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuContentItemTabManager {

	/**
	 * @var	Array		Registered tabs
	 */
	protected static $tabs = array();



	/**
	 * Register an items tab
	 *
	 * @param	String		$extKey					Extension that originally implements the item
	 * @param	String		$itemKey				e.g. 'project' / 'task' / 'container' ...
	 * @param	String		$tabKey					Tab identifier
	 * @param	String		$labelFunction			Function which renders the label or just a label string
	 * @param	String		$contentFunction		Function which renders the content
	 * @param	Integer		$position
	 */
	public static function registerTab($extKey, $itemKey, $tabKey, $labelFunction, $contentFunction, $position = 100) {
		self::$tabs[$extKey][$itemKey][$tabKey] = array(
			'id'		=> $tabKey,
			'label'		=> $labelFunction,
			'position'	=> intval($position),
			'content'	=> $contentFunction
		);
	}



	/**
	 * Get project detail tabs config array
	 *
	 * @param	String		$extKey			Extension that originally implements the item
	 * @param	String		$itemKey		'project' / 'task' / ...
	 * @param	Integer		$idItem
	 * @return	Array[]
	 */
	public static function getTabs($extKey, $itemKey, $idItem) {
		$tabs = self::getTabConfigs($extKey, $itemKey, true);

		foreach($tabs as $index => $tab) {
				// Is the label a method? A method can also return false and remove the tab
			if( TodoyuFunction::isFunctionReference($tab['label']) ) {
				$tabLabel	= TodoyuFunction::callUserFunction($tab['label'], $idItem);
			} else {
				$tabLabel	= Todoyu::Label($tab['label']);
			}

			if( $tabLabel === false ) {
				unset($tabs[$index]);
			} else {
				$tabs[$index]['label']	= $tabLabel;
			}
		}

		return $tabs;
	}



	/**
	 * Get a project detail tab configuration
	 *
	 * @param	String		$extKey		Extension that originally implements the item
	 * @param	String		$itemKey
	 * @param	String		$tabKey
	 * @return	Array
	 */
	public static function getTabConfig($extKey, $itemKey, $tabKey) {
		return TodoyuArray::assure(self::$tabs[$extKey][$itemKey][$tabKey]);
	}



	/**
	 * Get all tab configs for an item type
	 *
	 * @param	String		$extKey
	 * @param	String		$itemKey
	 * @param	Boolean		$sort		Sort by position
	 * @return	Array[]
	 */
	public static function getTabConfigs($extKey, $itemKey, $sort = true) {
		$tabs	= TodoyuArray::assure(self::$tabs[$extKey][$itemKey]);

		if( $sort ) {
			$tabs = TodoyuArray::sortByLabel($tabs, 'position');
		}

		return $tabs;
	}



	/**
	 * Get the tab which is active by default (if no preference is stored)
	 *
	 * @param	String		$extKey		Extension that originally implements the item
	 * @param	String		$itemKey
	 * @param	Integer		$idItem
	 * @return	String
	 */
	public static function getDefaultTab($extKey, $itemKey, $idItem) {
		$tabs	= self::getTabs($extKey, $itemKey, $idItem);
		$first	= array_shift($tabs);

		return $first['id'];
	}



	/**
	 * Check whether tabs are registered for type
	 *
	 * @param	String		$extKey
	 * @param	String		$itemKey
	 * @return	Boolean
	 */
	public static function hasTabs($extKey, $itemKey) {
		$tabs = self::getTabConfigs($extKey, $itemKey, false);

		return sizeof($tabs) > 0;
	}

}

?>