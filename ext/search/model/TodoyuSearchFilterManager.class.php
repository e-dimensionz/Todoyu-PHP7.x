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
 * Manage filters
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterManager {

	/**
	 * Get configuration of a filter (or a widget)
	 *
	 * @param	String		$type
	 * @param	String		$name
	 * @return	Array		Or FALSE
	 */
	public static function getFilterConfig($type, $name) {
		$base	=& Todoyu::$CONFIG['FILTERS'][$type];
		$config	= false;

		if( isset($base['filters'][$name]) && is_array($base['filters'][$name]) ) {
			$config	= $base['filters'][$name];
		} elseif( isset($base['widgets'][$name]) && is_array($base['widgets'][$name]) ) {
			$config	= $base['widgets'][$name];
		}

		return $config;
	}



	/**
	 * Get configuration of a filtertype (like task or project)
	 *
	 * @param	String		$type
	 * @param	String		$key
	 * @return	Mixed
	 */
	public static function getFilterTypeConfig($type, $key = null) {
		TodoyuExtensions::loadAllFilters();

		$base =& Todoyu::$CONFIG['FILTERS'][strtoupper($type)]['config'];

		return is_null($key) ? $base : $base[$key];
	}



	/**
	 * Get available filter types (project,task,etc)
	 *
	 * @param	Boolean		$sort		Sort types by position flag
	 * @return	Array
	 */
	public static function getFilterTypes($sort = false) {
		TodoyuExtensions::loadAllFilters();

		$types	= array_keys(Todoyu::$CONFIG['FILTERS']);

		if( $sort ) {
			$sorting = array();

			foreach($types as $type) {
				$sorting[] = array(
					'position'	=> intval(Todoyu::$CONFIG['FILTERS'][$type]['config']['position']),
					'type'		=> $type
				);
			}

			$sorted	= TodoyuArray::sortByLabel($sorting, 'position');

			$types	= TodoyuArray::getColumn($sorted, 'type');
		}

		return $types;
	}



	/**
	 * Get filter sorting for type
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getFilterDefaultSorting($type) {
		TodoyuExtensions::loadAllFilters();

		$sorting	= Todoyu::$CONFIG['FILTERS'][strtoupper($type)]['config']['defaultSorting'];

		return is_null($sorting) ? '' : $sorting;
	}



	/**
	 * Get label of a filter type (Ex: Task)
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getFilterTypeLabel($type) {
		TodoyuExtensions::loadAllFilters();

		return Todoyu::Label(Todoyu::$CONFIG['FILTERS'][strtoupper($type)]['config']['label']);
	}



	/**
	 * Get results renderer function for a filter type
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getFilterTypeResultsRenderer($type) {
		TodoyuExtensions::loadAllFilters();

		$type	= strtoupper($type);

		return Todoyu::$CONFIG['FILTERS'][$type]['config']['resultsRenderer'];
	}



	/**
	 * Get conjunction options
	 *
	 * @return	Array
	 */
	public static function getConjunctionOptions() {
		return array(
			array(
				'value'	=> 'AND',
				'label'	=> 'search.ext.and'
			),
			array(
				'value'	=> 'OR',
				'label'	=> 'search.ext.or'
			)
		);
	}

}

?>