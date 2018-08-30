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
 * Portal preferences
 *
 * @name		Portal preferences
 * @package		Todoyu
 * @subpackage	Portal
 */
class TodoyuPortalPreferences {

	/**
	 * Save a preference for portal
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Boolean		$unique
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function savePref($preference, $value, $idItem = 0, $unique = false, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::savePreference(EXTID_PORTAL, $preference, $value, $idItem, $unique, $idArea, $idPerson);
	}



	/**
	 * Get a preference
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Boolean		$unserialize
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getPref($preference, $idItem = 0, $idArea = 0, $unserialize = false, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreference(EXTID_PORTAL, $preference, $idItem, $idArea, $unserialize, $idPerson);
	}



	/**
	 * Get  project preference
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPrefs($preference, $idItem = 0, $idArea = 0, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreferences(EXTID_PORTAL, $preference, $idItem, $idArea, $idPerson);
	}



	/**
	 * Delete portal preference
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function deletePref($preference, $value = null, $idItem = 0, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::deletePreference(EXTID_PORTAL, $preference, $value, $idItem, $idArea, $idPerson);
	}



	/**
	 * Get currently active tab of current person
	 *
	 * @return	Integer
	 */
	public static function getActiveTab() {
		$tab = self::getPref('tab');

		if( !$tab ) {
			$tab = 'selection';
		}

		return $tab;
	}



	/**
	 * Save active tab of current person
	 *
	 * @param	String	$tabKey
	 */
	public static function saveActiveTab($tabKey) {
		self::savePref('tab', $tabKey, 0, true);
	}



	/**
	 * Get currently selected filtersets for selection tab
	 *
	 * @return	Array
	 */
	public static function getSelectionTabFiltersetIDs() {
		$filtersetIDs	= self::getPref('filtersets');

		if( $filtersetIDs ) {
			$filtersetIDs = explode(',', $filtersetIDs);

				// Remove references of non-existent filtersets
			$allFiltersetIDs	= TodoyuSearchFiltersetManager::getFiltersetIDs(Todoyu::personid());
			$allFiltersetIDs	= TodoyuArray::flatten($allFiltersetIDs);

			$filtersetIDs	= array_intersect($allFiltersetIDs, $filtersetIDs);
		} else {
			$filtersetIDs	= array();
		}

		$filtersetIDs	= array_values($filtersetIDs);

		return $filtersetIDs;
	}



	/**
	 * Save currently selected filtersets for selection tab
	 *
	 * @param	Array		$filtersetIDs
	 */
	public static function saveSelectionTabFiltersetIDs(array $filtersetIDs) {
		$filtersetIDs	= TodoyuArray::intval($filtersetIDs, true);
		$prefValue		= implode(',', $filtersetIDs);

		self::savePref('filtersets', $prefValue, 0, true);
	}



	/**
	 * Save filtersets of tab
	 *
	 * @param	String	$filtersetIDs
	 * @param	Integer	$idTab
	 */
	public static function saveTabFiltersets($filtersetIDs, $idTab = 0) {
		$idTab			= intval($idTab);
		$filtersetIDs	= TodoyuArray::intExplode(',', $filtersetIDs, true, true);

		if( $idTab == 0 ) {
				// 'Selection' tab
			TodoyuPreferenceManager::deletePreference(EXTID_PORTAL, 'filterset', null, $idTab);

			foreach($filtersetIDs as $idFilterset) {
				TodoyuPreferenceManager::savePreference(EXTID_PORTAL, 'filterset', $idFilterset, $idTab, false);
			}
		} else {
			// Regular tab (filtersets stored in 'ext_portal_mm_tab_filterset')

		}
	}



	/**
	 * Get IDs of the expanded tasks
	 *
	 * @return	Array
	 */
	public static function getExpandedTasks() {
		$taskIDs = self::getPrefs('task-exp');

		if( !$taskIDs ) {
			$taskIDs = array();
		}

		return $taskIDs;
	}



	/**
	 * Save expanded task status
	 *
	 * @param	Integer		$idTask			Task ID
	 * @param	Boolean		$expanded		Is task now expanded?
	 */
	public static function saveTaskExpandedStatus($idTask, $expanded = true) {
		$idTask	= intval($idTask);

		if( $expanded ) {
			self::savePref('task-exp', $idTask);
		} else {
			self::deletePref('task-exp', $idTask);
		}
	}

}

?>