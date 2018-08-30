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
 * Filter condition manager
 * Add, remove and get filter conditions of filtersets
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterConditionManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_search_filtercondition';



	/**
	 * Get filter condition
	 *
	 * @param	Integer			$idFilterCondition
	 * @return	TodoyuSearchFilterCondition
	 */
	public static function getFilterCondition($idFilterCondition) {
		$idFilterCondition	= intval($idFilterCondition);

		return TodoyuRecordManager::getRecord('TodoyuSearchFilterCondition', $idFilterCondition);
	}



	/**
	 * Get filter condition database record
	 *
	 * @param	Integer		$idFilterCondition
	 * @return	Array
	 */
	public static function getFilterConditionRecord($idFilterCondition) {
		$idFilterCondition	= intval($idFilterCondition);

		return Todoyu::db()->getRecord(self::TABLE, $idFilterCondition);
	}



	/**
	 * Get filter conditions to given filter set
	 *
	 * @param	Integer		$idFilterset
	 * @return	Array
	 */
	public static function getFiltersetConditions($idFilterset) {
		$idFilterset	= intval($idFilterset);

			// Get all conditions of filter set
		$where	= '		id_set	= ' . $idFilterset
				. ' AND deleted	= 0';
		$order	= 'id';

		$conditions = TodoyuRecordManager::getAllRecords(self::TABLE, $where, $order);

			// Get conditions to type of filter set
		$config = self::getTypeFilterConditions(TodoyuSearchFiltersetManager::getFiltersetType($idFilterset));
			// Remove conditions without configuration
		foreach($conditions as $key => $condition) {
			if( ! $config[$condition['filter']] ) {
				unset($conditions[$key]);
			}
		}

		return array_values($conditions);
	}



	/**
	 * Get type filters (conditions)
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getTypeFilterConditions($type = 'TASK') {
		$type	= strtoupper(trim($type));

		TodoyuExtensions::loadAllFilters();

		$filters= TodoyuArray::assure(Todoyu::$CONFIG['FILTERS'][$type]['widgets']);

		if( isset(Todoyu::$CONFIG['FILTERS'][$type]['config']['require']) ) {
			list($extKey, $rightKey) = explode('.', Todoyu::$CONFIG['FILTERS'][$type]['config']['require'], 2);

			if( !Todoyu::allowed($extKey, $rightKey) ) {
				$filters = array();
			}
		}

		return self::removeNotAllowedFilters($filters);
	}



	/**
	 * Get conditions of a type, grouped by their optgroup attribute
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getGroupedTypeFilterConditions($type = 'TASK') {
		$filters	= self::getTypeFilterConditions($type);

		return TodoyuArray::groupByField($filters, 'optgroup', 'project.task.search.label');
	}



	/**
	 * Save all filterset conditions
	 *
	 * @param	Integer		$idFilterset
	 * @param	Array		$filterConditions
	 * @return	Array		Condition IDs
	 * @todo	Do not delete every filtercondition, update them
	 */
	public static function saveFilterConditions($idFilterset, array $filterConditions) {
		$idFilterset	= intval($idFilterset);
		$conditionIDs	= array();

			// Delete all conditions of the filterset
		self::deleteFiltersetConditions($idFilterset);

			// Save all conditions
		foreach($filterConditions as $condition) {
			$conditionIDs[] = self::addFilterCondition($idFilterset, $condition['condition'], $condition['value'], $condition['negate']);
		}

		return $conditionIDs;
	}



	/**
	 * Add a new filterset condition
	 *
	 * @param	Integer		$idFilterset		Parent filterset
	 * @param	String		$filterName			Name of the filter (-function)
	 * @param	Mixed		$value				String or Array value data
	 * @param	Boolean		$negate				Filter is negated
	 * @return	Integer		Condition ID
	 */
	public static function addFilterCondition($idFilterset, $filterName, $value, $negate = false) {
		$idFilterset= intval($idFilterset);

		$data = array(
			'id_set'		=> $idFilterset,
			'filter'		=> $filterName,
			'value'			=> is_array($value) ? implode(',', $value) : $value,
			'is_negated'	=> $negate ? 1 : 0
		);

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Delete all conditions of a filterset
	 *
	 * @param	Integer		$idFilterset
	 */
	public static function deleteFiltersetConditions($idFilterset) {
		$idFilterset = intval($idFilterset);

		Todoyu::db()->doDelete(self::TABLE, 'id_set = ' . $idFilterset);
	}



	/**
	 * Transform the filter conditions to a valid filter condition array
	 *
	 * @param	Array		$filterConditions
	 * @return	Array
	 */
	public static function buildFilterConditionArray(array $filterConditions = array()) {
		$conditions	= array();

		foreach($filterConditions as $condition) {
			$conditions[] = array(
				'name'		=> $condition['name'],
				'filter'	=> $condition['condition'],
				'is_negated'=> $condition['negate'],
				'value'		=> is_array($condition['value']) ? implode(',', $condition['value']) : $condition['value']
			);
		}

		return $conditions;
	}



	/**
	 * Remove restricted filter conditions
	 *
	 * @param	Array		$filterConditions
	 * @return	Array
	 */
	protected static function removeNotAllowedFilters(array $filterConditions) {
		foreach($filterConditions as $index => $condition) {
			$remove = false;

				// Missing right
			if( isset( $condition['require'] ) ) {
				$requireArray = explode('.', $condition['require']);
				if( !Todoyu::allowed($requireArray[0], $requireArray[1]) ) {
					$remove = true;
				}
			}

				// Marked as 'internal only'
			if( isset( $condition['internal'] ) && !TodoyuAuth::isInternal() ) {
				$remove = true;
			}

				// Remove condition
			if( $remove ) {
				unset($filterConditions[$index]);
			}
		}

		return $filterConditions;
	}

}

?>