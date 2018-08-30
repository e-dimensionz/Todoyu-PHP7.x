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
 * Manage create wizard configs
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCreateWizardManager {

	/**
	 * Wizard configs
	 *
	 * @var	Array
	 */
	private static $wizards = array();


	/**
	 * Add wizard config
	 *
	 * @param	String		$name		Name of the wizard
	 * @param	Array		$config
	 */
	public static function addWizard($name, array $config) {
		$config['name']	= $name;

		self::$wizards[$name] = $config;
	}


	/**
	 * Get wizard config
	 *
	 * @param	String		$name
	 * @return	Array
	 */
	public static function getWizard($name) {
		return TodoyuArray::assure(self::$wizards[$name]);
	}

}

?>