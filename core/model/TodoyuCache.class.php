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
 * Cache for various data
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCache {

	private static $enabled = true;

	/**
	 * Cache
	 *
	 * @var	Array
	 */
	private static $cache = array();



	/**
	 * Get element from cache
	 *
	 * @param	String		$key		Unique key
	 * @return	Mixed		Whatever is stored in the cache under the key
	 */
	public static function get($key) {
		return self::$enabled ? self::$cache[$key] : null;
	}



	/**
	 * Store data in cache identified by key
	 *
	 * @param	String		$key
	 * @param	Mixed		$data
	 */
	public static function set($key, $data) {
		if( self::$enabled !== false ) {
			self::$cache[$key] = $data;
		}
	}



	/**
	 * Remove element from cache
	 *
	 * @param	String		$key
	 */
	public static function remove($key) {
		unset(self::$cache[$key]);
	}



	/**
	 * Check if something is stored under $key
	 *
	 * @param	String		$key
	 * @return	Boolean
	 */
	public static function isIn($key) {
		return self::$enabled ? array_key_exists($key, self::$cache) : false;
	}



	/**
	 * Disable caching
	 */
	public static function disable() {
		self::$enabled = false;
	}



	/**
	 * Enable caching
	 */
	public static function enable() {
		self::$enabled = true;
	}



	/**
	 * Flush the cache
	 */
	public static function flush() {
		self::$cache = array();
	}

}

?>