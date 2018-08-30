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
 * Manage callbacks for track request update
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingCallbackManager {

	/**
	 * Callback functions for track request data update
	 *
	 * @var	Array
	 */
	private static $callback = array();

	/**
	 * Add a callback
	 *
	 * @param	String		$key			Identifier. Same as in javascript
	 * @param	String		$function		Function reference
	 */
	public static function add($key, $function) {
		self::$callback[$key] = $function;
	}



	/**
	 * Get a callback function
	 *
	 * @param	String		$key
	 * @return	String
	 */
	public static function get($key) {
		return self::$callback[$key];
	}



	/**
	 * Remove a callback function
	 *
	 * @param	String		$key
	 */
	public static function remove($key) {
		unset(self::$callback[$key]);
	}



	/**
	 * Call a callback function
	 * Parameters for the callback are: $idTask, $info
	 *
	 * @param	String		$key
	 * @param	Integer		$idTask
	 * @param	Mixed		$info
	 * @return	Mixed
	 */
	public static function call($key, $idTask, $info) {
		return TodoyuFunction::callUserFunction(self::get($key), $idTask, $info);
	}



	/**
	 * Call all registered callbacks
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$data		All request data
	 * @return	Array		Results of all callbacks
	 */
	public static function callAll($idTask, array $data) {
		$result	= array();

		foreach(self::$callback as $key => $function) {
			if( isset($data[$key]) ) {
				$result[$key] = self::call($key, $idTask, $data[$key]);
			}
		}

		return $result;
	}

}

?>