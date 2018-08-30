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
 * Manage sorting options for filters
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchSortingManager {

	/**
	 * Get all sorting options for a type
	 *
	 * @param string $type
	 * @return array
	 */
	public static function getGroupedSortingOptions($type = 'TASK') {
		$type	= strtoupper(trim($type));
		$options= array();

		TodoyuExtensions::loadAllFilters();

		$sortings	= TodoyuArray::assure(Todoyu::$CONFIG['FILTERS'][$type]['sorting']);

		foreach($sortings as $name => $config) {
			$allowed	= true;
			if( isset($config['require']) ) {
				list($extKey, $right) = explode('.', $config['require']);
				$allowed = TodoyuRightsManager::isAllowed($extKey, $right);
			} elseif( isset($config['restrictInternal']) && TodoyuAuth::isExternal() ) {
				$allowed = false;
			}

			if( $allowed ) {
				$options[$name] = array(
					'value'	=> $name,
					'label'	=> $config['label'],
					'group'	=> $config['optgroup']
				);
			}
		}

		return TodoyuArray::groupByField($options, 'group', 'project.task.task');
	}



	/**
	 *
	 * @param	String		$type
	 * @param	String		$name
	 * @return	Array
	 */
	public static function getSortingConfig($type, $name) {
		TodoyuExtensions::loadAllFilters();

		return TodoyuArray::assure(Todoyu::$CONFIG['FILTERS'][$type]['sorting'][$name]);
	}



	/**
	 * Get label of sorting
	 *
	 * @param	String		$type		Filter type (TASK, PROJECT, INVOICE, HOSTING, etc)
	 * @param	String		$name		Name of the sorting
	 * @return	String
	 */
	public static function getLabel($type, $name) {
		$type	= strtoupper($type);

		TodoyuExtensions::loadAllFilters();

		return Todoyu::Label(trim(Todoyu::$CONFIG['FILTERS'][$type]['sorting'][$name]['label']));
	}

}

?>