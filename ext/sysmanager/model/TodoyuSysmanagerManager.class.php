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
 * Manage sysmanager module
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerManager {

	/**
	 * Add a module to the sysmanager panel
	 *
	 * @param	String		$module				Module key
	 * @param	String		$label				Module label
	 * @param	String		$renderFunction		Function ref to the content render function
	 * @param	Integer		$position			Position in menu
	 */
	public static function addModule($module, $label, $renderFunction, $position = 100) {
		$position	= intval($position);

		TodoyuExtensions::loadAllSysmanager();

		Todoyu::$CONFIG['EXT']['sysmanager']['modules'][$module] = array(
			'key'			=> $module,
			'label'			=> $label,
			'render'		=> $renderFunction,
			'position'		=> $position
		);
	}



	/**
	 * Get name of currently active sysmanager module
	 *
	 * @return	String
	 */
	public static function getActiveModule() {
		$module	= TodoyuSysmanagerPreferences::getActiveModule();

		if( !$module ) {
			$module = self::getDefaultModule();
		}

		return $module;
	}


	public static function getDefaultModule() {
		return Todoyu::$CONFIG['EXT']['sysmanager']['defaultModule'];
	}



	/**
	 * Get installed sysmanager modules
	 *
	 * @return	Array
	 */
	public static function getModules() {
		TodoyuExtensions::loadAllSysmanager();

		if( is_array( Todoyu::$CONFIG['EXT']['sysmanager']['modules'] ) ) {
			return TodoyuArray::sortByLabel(Todoyu::$CONFIG['EXT']['sysmanager']['modules'], 'position');
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
		TodoyuExtensions::loadAllSysmanager();

		return Todoyu::$CONFIG['EXT']['sysmanager']['modules'][$module]['render'];
	}



	/**
	 * Check whether the key belongs to a registered module
	 *
	 * @param	String		$module
	 * @return	Boolean
	 */
	public static function isModule($module) {
		TodoyuExtensions::loadAllSysmanager();

		return isset(Todoyu::$CONFIG['EXT']['sysmanager']['modules'][$module]);
	}


	public static function getActiveModuleKey($moduleKey) {


	}

}

?>