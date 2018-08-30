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
 * Manage admin module
 *
 * @package		Todoyu
 * @subpackage	Admin
 */
class TodoyuAdminManager {

	/**
	 * Add a module to the admin panel
	 *
	 * @param	String		$module				Module key
	 * @param	String		$label				Module label
	 * @param	String		$renderFunction		Function ref to the content render function
	 * @param	Integer		$position			Position in menu
	 */
	public static function addModule($module, $label, $renderFunction, $position = 100) {
		$position	= intval($position);

		TodoyuExtensions::loadAllAdmin();

		Todoyu::$CONFIG['EXT']['admin']['modules'][$module] = array(
			'key'			=> $module,
			'label'			=> $label,
			'render'		=> $renderFunction,
			'position'		=> $position
		);
	}



	/**
	 * Get name of currently active admin module
	 *
	 * @return	String
	 */
	public static function getActiveModule() {
		$module	= TodoyuAdminPreferences::getActiveModule();

		if( $module === false ) {
			$module = Todoyu::$CONFIG['EXT']['admin']['defaultModule'];
		}

		return $module;
	}



	/**
	 * Get installed admin modules
	 *
	 * @return	Array
	 */
	public static function getModules() {
		TodoyuExtensions::loadAllAdmin();

		if( is_array( Todoyu::$CONFIG['EXT']['admin']['modules'] ) ) {
			return TodoyuArray::sortByLabel(Todoyu::$CONFIG['EXT']['admin']['modules']);
		} else {
			return array();
		}
	}



	/**
	 * Get the render function of a module
	 *
	 * @param	String		$module		Module key
	 * @return	String					'class::method'
	 */
	public static function getModuleRenderFunction($module) {
		TodoyuExtensions::loadAllAdmin();

		return Todoyu::$CONFIG['EXT']['admin']['modules'][$module]['render'];
	}



	/**
	 * Check whether the key belongs to a registered module
	 *
	 * @param	String		$module
	 * @return	Boolean
	 */
	public static function isModule($module) {
		TodoyuExtensions::loadAllAdmin();

		return is_string($module) && is_array(Todoyu::$CONFIG['EXT']['admin']['modules'][$module]);
	}
}

?>