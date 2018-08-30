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
 * Profile manager
 *
 * @package		Todoyu
 * @subpackage	Profile
 */
class TodoyuProfileManager {

	/**
	 * Add a module to profile extension
	 *
	 * @param	String		$name
	 * @param	Array		$config
	 */
	public static function addModule($name, array $config) {
		Todoyu::$CONFIG['EXT']['profile']['module'][$name] = $config;
		Todoyu::$CONFIG['EXT']['profile']['module'][$name]['name'] = $name;
	}



	/**
	 * Get module configuration
	 *
	 * @param	String		$name
	 * @return	Array
	 */
	public static function getModuleConfig($name) {
		return TodoyuArray::assure(Todoyu::$CONFIG['EXT']['profile']['module'][$name]);
	}



	/**
	 * Get all registered modules
	 *
	 * @return	Array
	 */
	public static function getModules() {
		$modules	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['profile']['module']);
		$modules	= TodoyuArray::sortByLabel($modules, 'position');

		return $modules;
	}

}

?>