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
 * Sysmanager preferences
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerPreferences {

	/**
	 * Save a preference for sysmanager
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Boolean		$unique
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function savePref($preference, $value, $idItem = 0, $unique = false, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::savePreference(EXTID_SYSMANAGER, $preference, $value, $idItem, $unique, $idArea, $idPerson);
	}



	/**
	 * Get a sysmanager preference
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Boolean		$unserialize
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getPref($preference, $idItem = 0, $idArea = 0, $unserialize = false, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreference(EXTID_SYSMANAGER, $preference, $idItem, $idArea, $unserialize, $idPerson);
	}



	/**
	 * Get sysmanager preferences
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPrefs($preference, $idItem = 0, $idArea = 0, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreferences(EXTID_SYSMANAGER, $preference, $idItem, $idArea, $idPerson);
	}



	/**
	 * Delete sysmanager preference
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function deletePref($preference, $value = null, $idItem = 0, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::deletePreference(EXTID_SYSMANAGER, $preference, $value, $idItem, $idArea, $idPerson);
	}



	/**
	 * Save currently active sysmanager area module
	 *
	 * @param	String	$module
	 */
	public static function saveActiveModule($module) {
		self::savePref('module', $module, 0, true);
	}



	/**
	 * Get previously active sysmanager area module
	 *
	 * @return	String
	 */
	public static function getActiveModule() {
		return self::getPref('module');
	}



	/**
	 * Get currently active sysmanager tab
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getActiveTab($type) {
		return self::getPref($type . '-tab');
	}



	/**
	 * Save active tab preference
	 *
	 * @param	String		$type
	 * @param	String		$tab
	 */
	public static function saveActiveTab($type, $tab) {
		self::savePref($type . '-tab', $tab, 0, true);
	}



	/**
	 * Save given extension's rights
	 *
	 * @param	String	$ext
	 */
	public static function saveRightsExt($ext) {
		self::savePref('rights-ext', $ext, 0, true);
	}



	/**
	 * Get sysmanager rights settings
	 *
	 * @return	String
	 */
	public static function getRightsExt() {
		$ext	= self::getPref('rights-ext');

		if( !$ext ) {
//			$extKeys= TodoyuExtensions::getInstalledExtKeys();
			$ext	= $ext[0];
		}

		return $ext;
	}



	/**
	 * Save rights and roles to prefs
	 *
	 * @param	Array	$roles
	 */
	public static function saveRightsRoles(array $roles) {
		$roles		= TodoyuArray::intval($roles, true, true);
		$roleList	= implode(',', $roles);

		TodoyuRightsManager::saveChangeTime();
		self::savePref('rights-roles', $roleList, 0, true);
	}



	/**
	 * Get rights and roles from prefs
	 *
	 * @return	Integer[]
	 */
	public static function getRightsRoles() {
		$roleList	= self::getPref('rights-roles');

		return TodoyuArray::intExplode(',', $roleList);
	}

}

?>