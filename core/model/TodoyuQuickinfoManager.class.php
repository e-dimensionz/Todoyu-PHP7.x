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
 * Todoyu Quickinfo Manager
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuQuickinfoManager {

	/**
	 * Register a function which will add items to this type of quickinfo
	 *
	 * @param	String		$type
	 * @param	String		$function
	 * @param	Integer		$position
	 */
	public static function addFunction($type, $function, $position = 100) {
		$type	= strtoupper(trim($type));

		Todoyu::$CONFIG['Quickinfo'][$type][] = array(
			'function'	=> $function,
			'position'	=> (int) $position
		);
	}



	/**
	 * Get all registered functions for a type. Ordered by position key
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getTypeFunctions($type) {
		$type		= strtoupper(trim($type));
		$funcRefs	= TodoyuArray::assure(Todoyu::$CONFIG['Quickinfo'][$type]);

			// Sort registered functions by position flag
		return TodoyuArray::sortByLabel($funcRefs, 'position');
	}

}

?>