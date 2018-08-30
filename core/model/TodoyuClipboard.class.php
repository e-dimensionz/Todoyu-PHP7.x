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
 * Basic clipboard
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuClipboard {

	/**
	 * @var string		Key in todoyu session
	 */
	const KEY = 'CLIPBOARD';



	/**
	 * Set type data in clipboard
	 *
	 * @param	String		$type
	 * @param	Array		$data
	 */
	public static function set($type, array $data) {
		$type	= strtoupper(trim($type));
		$path	= self::KEY . '/' . $type;

		TodoyuSession::set($path, $data);
	}



	/**
	 * Get data of type
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	public static function get($type) {
		$type	= strtoupper(trim($type));
		$path	= self::KEY . '/' . $type;

		$data	= TodoyuSession::get($path);

		return TodoyuArray::assure($data);
	}



	/**
	 * Remove type data
	 *
	 * @param	String		$type
	 */
	public static function remove($type) {
		$type	= strtoupper(trim($type));
		$path	= self::KEY . '/' . $type;

		TodoyuSession::remove($path);
	}



	/**
	 * Check if type has data (something of this type is on the clipboard)
	 *
	 * @param	String		$type
	 * @return	Boolean
	 */
	public static function has($type) {
		$type	= strtoupper(trim($type));
		$path	= self::KEY . '/' . $type;

		return TodoyuSession::isIn($path);
	}

}

?>