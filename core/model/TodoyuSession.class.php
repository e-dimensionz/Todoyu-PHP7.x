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
 * Access the todoyu session
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuSession {

	/**
	 * Get key where session is stored
	 *
	 * @return	String
	 */
	private static function key() {
		return PATH_WEB;
	}



	/**
	 * Get session value
	 *
	 * @param	String		$path			Slash separated path
	 * @return	Mixed
	 */
	public static function get($path) {
		$parts	= explode('/', $path);
		$key	= array_shift($parts);
		$value	= $_SESSION[self::key()][$key];

		foreach($parts as $part) {
			$value = $value[$part];
		}

		return $value;
	}



	/**
	 * Set a session value
	 * Format for sub areas: this/is/a/nested/path
	 *
	 * @param	String		$path		Slash separated path to value
	 * @param	Mixed		$data
	 */
	public static function set($path, $data) {
		$parts	= explode('/', $path);
		$key	= array_shift($parts);
		$pointer= &$_SESSION[self::key()][$key];

		foreach($parts as $part) {
			$pointer = &$pointer[$part];
		}

		$pointer = $data;
	}



	/**
	 * Check if a value is stored under the path
	 *
	 * @param	String		$path		Slash separated path to value
	 * @return	Boolean
	 */
	public static function isIn($path) {
		return self::get($path) !== null;
	}



	/**
	 * Remove data from the session	 *
	* @param	String		$name
	 */

	/**
	 * Delete session entry (set null)
	 *
	 * @param	String		$path		Slash separated path to value
	 */
	public static function remove($path) {
		self::set($path, null);
	}



	/**
	 * Clear all todoyu session data
	 */
	public static function clear() {
		$_SESSION[self::key()] = null;
	}



	/**
	 * Get all session data
	 *
	 * @return	Array
	 */
	public static function getAll() {
		return $_SESSION[self::key()];
	}

}

?>