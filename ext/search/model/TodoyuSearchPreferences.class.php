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
 * Search preference manager
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchPreferences {

	/**
	 * Save search extension preference
	 *
	 * @param	Integer		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Boolean		$unique
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function savePref($preference, $value, $idItem = 0, $unique = false, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::savePreference(EXTID_SEARCH, $preference, $value, $idItem, $unique, $idArea, $idPerson);
	}



	/**
	 * Get search extension preference
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Boolean		$unserialize
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getPref($preference, $idItem = 0, $idArea = 0, $unserialize = false, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreference(EXTID_SEARCH, $preference, $idItem, $idArea, $unserialize, $idPerson);
	}



	/**
	 * Get search extension preferences
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPrefs($preference, $idItem = 0, $idArea = 0, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreferences(EXTID_SEARCH, $preference, $idItem, $idArea, $idPerson);
	}



	/**
	 * Save current tab
	 *
	 * @param	String	$currentTab
	 */
	public static function saveActiveTab($currentTab) {
		self::savePref('tab', $currentTab, 0, true);
	}



	/**
	 * Get active tab. If non stored in preferences, use default
	 *
	 * @return	String
	 */
	public static function getActiveTab() {
		$tab = self::getPref('tab');

		if( empty($tab) ) {
			$tab = Todoyu::$CONFIG['EXT']['search']['defaultTab'];
		}

		return $tab;
	}



	/**
	 * Get active filterset ID
	 *
	 * @param	String		$tab
	 * @return	Integer
	 */
	public static function getActiveFilterset($tab) {
		$pref			= 'filterset-' . $tab;
		$idFilterset	= self::getPref($pref);

		return intval($idFilterset);
	}



	/**
	 * Save active filterset
	 *
	 * @param	String		$tab		Tab name
	 * @param	Integer		$idFilterset
	 */
	public static function saveActiveFilterset($tab, $idFilterset) {
		$idFilterset= intval($idFilterset);
		$pref		= 'filterset-' . $tab;

		self::savePref($pref, $idFilterset, 0, true);
	}



	/**
	 * Save filterset list toggling status
	 *
	 * @param	String		$type
	 * @param	Boolean		$expanded
	 */
	public static function saveFiltersetListToggle($type, $expanded = true) {
		$preference	= self::getFiltersetListToggle();

		if( $expanded ) {
			unset($preference[$type]);
		} else {
			$preference[$type] = 1;
		}

		$value	= serialize($preference);

		self::savePref('filtersetListToggle', $value, 0, true);
	}



	/**
	 * Get filterset log toggling status
	 *
	 * @return	Array
	 */
	public static function getFiltersetListToggle() {
		$pref	= self::getPref('filtersetListToggle', 0, 0, true);

		return TodoyuArray::assure($pref);
	}



	/**
	 * Save currently active filter
	 *
	 * @param	String	$currentFilter
	 */
	public static function saveCurrentFilter($currentFilter) {
		$extID		= EXTID_SEARCH;
		$preference	= 'searchcurrentfilter';
		$value		= $currentFilter;
		$unique		= true;

		TodoyuPreferenceManager::savePreference($extID, $preference, $value, null, $unique, 0, Todoyu::personid());
	}

}