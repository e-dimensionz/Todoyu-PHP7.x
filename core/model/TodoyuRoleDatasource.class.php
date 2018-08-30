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
 * Handles the Datasource for filter-widgets which belong to roles
 *
 * @package Todoyu
 */
class TodoyuRoleDatasource {

	/**
	 * @var	String		Default Database table
	 */
	const TABLE = 'system_role';



	/**
	 * Prepares roles options for rendering in the widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getRoleOptions(array $definitions) {
		$options	= array();

		$roleIDs	= array();
		$roles		= TodoyuRoleManager::getRoles($roleIDs);
//		$selected	= TodoyuArray::intExplode(',', $definitions['value'], true, true);

		foreach($roles as $role) {
			$options[] = array(
				'label'		=> $role['title'],
				'value'		=> $role['id'],
//				'selected'	=> in_array($status['index'], $selected)
			);
		}

		$definitions['options'] = $options;

		return $definitions;
	}

}
?>