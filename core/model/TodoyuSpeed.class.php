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
 * Todoyu speed test class
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuSpeed {

	/**
	 * Way point of a time tracking
	 *
	 * @var	Array
	 */
	private static $track = array();

	/**
	 * Currently active trackings
	 *
	 * @var	Array
	 */
	private static $active = array();


	
	/**
	 * Get first tick
	 *
	 * @param	String		$key
	 * @return	Float
	 */
	private static function first($key) {
		return floatval(self::$track[$key][0]);
	}



	/**
	 * Get last tick
	 *
	 * @param	String		$key
	 * @return	Float
	 */
	private static function last($key) {
		return floatval(self::$track[$key][sizeof(self::$track[$key])-1]);
	}



	/**
	 * Start tracking
	 *
	 * @param	String		$key
	 */
	public static function start($key = 'default') {
		self::$active[$key] = true;
		self::$track[$key] = array();
		self::tick($key);
	}



	/**
	 * Stop tracking
	 *
	 * @param	String		$key
	 */
	public static function stop($key = 'default') {
		self::$active[$key] = false;
		self::tick($key);
	}



	/**
	 * Add a tick between start and stop
	 *
	 * @param	String		$key
	 */
	public static function tick($key = 'default') {
		self::$track[$key][] = microtime(true);
	}



	/**
	 * Get total tracking time. Difference between start and stop
	 *
	 * @param	String		$key
	 * @param	Boolean		$format		Format as milliseconds instead of microseconds
	 * @return	String
	 */
	public static function total($key = 'default', $format = false) {
		if( self::isActive($key) ) {
			self::stop($key);
		}

		$t = self::last($key) - self::first($key);

		if( $format ) {
			$t = round($t * 1000, 4) . 'ms';
		}

		return $t;
	}



	/**
	 * Print total time in firebug
	 *
	 * @param	String		$key
	 */
	public static function totalInFirebug($key = 'default') {
		$total	= self::total($key, true);

		TodoyuDebug::printInFireBug($total, $key);
	}



	/**
	 * Stop measuring and print all point in firebug
	 *
	 * @param	String		$key
	 */
	public static function allInFirebug($key = 'default') {
		if( self::isActive($key) ) {
			self::stop($key);
		}
		
		TodoyuDebug::printInFireBug(self::all($key), $key);
	}



	/**
	 * Get all ticks
	 *
	 * @param	String		$key
	 * @return	Array
	 */
	public static function all($key = 'default') {
		return (array)self::$track[$key];
	}



	/**
	 * Check whether test is active for a key
	 *
	 * @param	String	$key
	 * @return	Boolean
	 */
	public static function isActive($key) {
		return self::$active[$key] === true;
	}

}

?>