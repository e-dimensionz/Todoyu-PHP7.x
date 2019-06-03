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
 * Manager class for search extension
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchManager {

	/**
	 * Get ID of filterset storing the current condition of the given search type tab
	 *
	 * @param	String		$type		'task' / 'project' etc.
	 * @return	Integer
	 */
	public static function getIDCurrentTabFilterset($type) {
		$field	= 'id';
		$table	= TodoyuSearchFiltersetManager::TABLE;
		$where	= ' deleted					= 0'
				. ' AND current				= 1'
				. ' AND id_person_create	= ' . Todoyu::personid()
				. ' AND `type`				= ' . TodoyuSql::quote($type, true);

		return intval(Todoyu::db()->getFieldValue($field, $table, $where));
	}



	/**
	 * Get current tab filterset
	 *
	 * @param	String	$type
	 * @return	Array|TodoyuSearchFilterset
	 */
	public static function getCurrentTabFilterset($type) {
		$idFilterset	= self::getIDCurrentTabFilterset($type);

		if( $idFilterset === 0 ) {
			return array();
		}

		return TodoyuSearchFiltersetManager::getFilterset($idFilterset);
	}



	/**
	 * Get filter type configs
	 *
	 * @return	Array
	 */
	public static function getFilters() {
		TodoyuExtensions::loadAllFilters();

		return TodoyuArray::assure(Todoyu::$CONFIG['FILTERS']);
	}



	/**
	 * Get filter types (keys)
	 *
	 * @return	Array
	 */
	public static function getFilterTypes() {
		return array_keys(self::getFilters());
	}



	/**
	 * Get config array for all filter types
	 *
	 * @return	Array
	 */
	public static function getFilterConfigs() {
		$filters= self::getFilters();
		$config	= array();

			// Check all filter types
		foreach($filters as $type => $data) {
			if( isset($data['config']['require']) ) {
				list($extKey, $rightKey) = explode('.', $data['config']['require'], 2);

				if( !Todoyu::allowed($extKey, $rightKey) ) {
					continue; // Don't add not allowed filter configs
				}
			}

			$config[$type] = $data['config'];
		}

		return $config;
	}


	
	/**
	 * Convert a simple filter array (from url) to a search filter array
	 *
	 * @param	Array		$simpleFilterConditions
	 * @return	Array
	 */
	public static function convertSimpleToFilterConditionArray(array $simpleFilterConditions) {
		$conditions = array();

		foreach($simpleFilterConditions as $filterName => $filterValue) {
			$conditions[] = array(
				'type'		=> $filterName,
				'negate'	=> false,
				'value'		=> $filterValue
			);
		}

		return $conditions;
	}



	/**
	 * Add a new search engine and register needed functions
	 *
	 * @param	String		$type
	 * @param	String		$methodSuggest
	 * @param	String		$labelSuggest
	 * @param	String		$labelMode
	 * @param	Integer		$position
	 */
	public static function addEngine($type, $methodSuggest, $labelSuggest, $labelMode = '', $position = 100) {
		$type		= strtolower(trim($type));
		$position	= intval($position);

		if( $labelMode === '' ) {
			$labelMode = $labelSuggest;
		}

		Todoyu::$CONFIG['EXT']['search']['engines'][$type] = array(
			'type'			=> $type,
			'suggestion'	=> $methodSuggest,
			'labelSuggest'	=> $labelSuggest,
			'labelMode'		=> $labelMode,
			'position'		=> $position
		);
	}



	/**
	 * Get all registered search engine in correct order
	 *
	 * @return	Array
	 */
	public static function getEngines() {
		TodoyuExtensions::loadAllSearch();

		$searchEngines	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['search']['engines']);

		if( !empty($searchEngines) ) {
			$searchEngines = TodoyuArray::sortByLabel($searchEngines, 'position');
		}

		return $searchEngines;
	}

}

?>