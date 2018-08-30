<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
 * Admin preferences
 *
 * @name 		Admin preferences
 * @package		Todoyu
 * @subpackage	Admin
 */
class TodoyuAdminPreferences {

	/**
	 * Save admin preference
	 *
	 * @param	String		$preference
	 * @param	Mixed		$value
	 * @param	Integer		$idItem
	 * @param	Boolean		$unique
	 * @return	Integer
	 */
	private static function save($preference, $value, $idItem = 0, $unique = false) {
		$idItem	= intval($idItem);

		return TodoyuPreferenceManager::savePreference(EXTID_ADMIN, $preference, $value, $idItem, $unique, EXTID_ADMIN);
	}



	/**
	 * Get admin preference
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Boolean		$unserialize
	 * @return	Integer
	 */
	private static function getPref($preference, $idItem = 0, $unserialize = false) {
		$idItem	= intval($idItem);

		return TodoyuPreferenceManager::getPreference(EXTID_ADMIN, $preference, $idItem, EXTID_ADMIN, $unserialize);
	}



	/**
	 * Get admin preferences
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @return	Array
	 */
	private static function getPrefs($preference, $idItem = 0) {
		$idItem	= intval($idItem);

		return TodoyuPreferenceManager::getPreferences(EXTID_ADMIN, $preference, $idItem, EXTID_ADMIN);
	}



	/**
	 * Save currently active admin area module
	 *
	 * @param	String	$module
	 */
	public static function saveActiveModule($module) {
		self::save('module', $module, 0, true);
	}



	/**
	 * Get previously active admin area module
	 *
	 * @return	String
	 */
	public static function getActiveModule() {
		return self::getPref('module');
	}

}

?>