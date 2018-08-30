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
 * Tab Manager
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuTabManager {

	/**
	 * Get tabs from array. Check if they have a required flag, and check it
	 *
	 * @param	Array		$tabs
	 * @return	Array
	 */
	public static function getAllowedTabs(array $tabs) {
		foreach($tabs as $index => $tab) {
			if( isset($tab['require']) ) {
				$parts	= explode('.', $tab['require'], 2);
				if( ! Todoyu::allowed($parts[0], $parts[1]) ) {
					unset($tabs[$index]);
				}
			}
		}

		return $tabs;
	}

}

?>