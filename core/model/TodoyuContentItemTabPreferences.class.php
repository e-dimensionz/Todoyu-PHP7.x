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
 * Manager preferences of content item tabs
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuContentItemTabPreferences {

	/**
	 * Get the key of the currently active tab inside the details of the given project (default if none is selected)
	 *
	 * @param	Integer		$idItem
	 * @return	String
	 */
	public static function getActiveTab($extKey, $itemKey, $idItem) {
		$idItem	= intval($idItem);

			// Override selected tab
		$forceTab	= self::getForcedTab($extKey, $itemKey);

		if( $forceTab ) {
			$prefTab = $forceTab;
		} else {
			$extID		= TodoyuExtensions::getExtID($extKey);
			$preference	= $itemKey . '-tab';
			$prefTab	= TodoyuPreferenceManager::getPreference($extID, $preference, $idItem);

			if( !$prefTab || $prefTab === '' ) {
				$prefTab = TodoyuContentItemTabManager::getDefaultTab($extKey, $itemKey, $idItem);
			}
		}

		return $prefTab;
	}



	/**
	 * Save active tab in project
	 *
	 * @param	String		$extKey
	 * @param	String		$itemKey
	 * @param	Integer		$idItem
	 * @param	String		$tabKey
	 */
	public static function saveActiveTab($extKey, $itemKey, $idItem, $tabKey) {
		$extID		= TodoyuExtensions::getExtID($extKey);
		$idItem		= intval($idItem);
		$preference	= $itemKey . '-tab';

		TodoyuPreferenceManager::savePreference($extID, $preference, $tabKey, $idItem, true);
	}



	/**
	 * Set forced tab for current rendering
	 *
	 * @param	String		$extKey
	 * @param	String		$itemKey
	 * @param	String		$tab
	 */
	public static function setForcedTab($extKey, $itemKey, $tab) {
		Todoyu::$CONFIG['EXT'][$extKey][$itemKey]['forceTab'] = $tab;
	}



	/**
	 * Get currently forced tab (or false) inside project details
	 *
	 * @param	String		$extKey
	 * @param	String		$itemKey
	 * @return	String		Or FALSE
	 */
	public static function getForcedTab($extKey, $itemKey) {
		return Todoyu::$CONFIG['EXT'][$extKey][$itemKey]['forceTab'];
	}

}

?>