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
 * Filterset Manager
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFiltersetManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_search_filterset';

	/**
	 * Cache for results of filterObjects
	 *
	 * @var	Array
	 */
	private static $filterObjectCache = array();

	/**
	 * Flags to prevent nested processing of the same filterset
	 * Prevents recursive loops
	 *
	 * @var	Array
	 */
	private static $filterObjectProcessing = array();

	/**
	 * List of already checked filterset usages (prevents recursions)
	 *
	 * @var	Array
	 */
	private static $filtersetChecked = array();



	/**
	 * Get filterset
	 *
	 * @param	Integer						$idFilterset
	 * @return	TodoyuSearchFilterset		Filterset record
	 */
	public static function getFilterset($idFilterset) {
		$idFilterset	= intval($idFilterset);

		return TodoyuRecordManager::getRecord('TodoyuSearchFilterset', $idFilterset);
	}



	/**
	 * Get filterset database record
	 *
	 * @param	Integer		$idFilterset
	 * @return	Array
	 */
	public static function getFiltersetRecord($idFilterset) {
		$idFilterset	= intval($idFilterset);

		return Todoyu::db()->getRecord(self::TABLE, $idFilterset);
	}



	/**
	 * Add a new filterset
	 *
	 * @param	Array		$data
	 * @return	Integer		Filterset ID
	 */
	public static function addFilterset(array $data) {
		$data['sorting']= self::getNextFiltersetSortingPosition($data['type']);

		$idFilterset = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('search', 'filterset.add', array($idFilterset));

		return $idFilterset;
	}



	/**
	 * Get next sorting position for filterset
	 *
	 * @param	String		$type
	 * @return	Integer
	 */
	private static function getNextFiltersetSortingPosition($type) {
		$field	= 'sorting';
		$table	= self::TABLE;
		$where	= '		id_person_create= ' . Todoyu::personid()
				. ' AND	deleted			= 0'
				. ' AND `type`			= ' . TodoyuSql::quote($type, true);
		$order	= 'sorting DESC';
		$limit	= 1;

		$value	= Todoyu::db()->getFieldValue($field, $table, $where, '', $order, $limit);

		if( $value === false ) {
			$value = 0;
		} else {
			$value = intval($value + 1);
		}

		return $value;
	}



	/**
	 * Update filterset
	 *
	 * @param	Integer		$idFilterset
	 * @param	Array		$data
	 */
	public static function updateFilterset($idFilterset, array $data) {
		$idFilterset	= intval($idFilterset);

		TodoyuRecordManager::updateRecord(self::TABLE, $idFilterset, $data);

		TodoyuHookManager::callHook('search', 'filterset.update', array($idFilterset, $data));
	}



	/**
	 * Delete filterset
	 *
	 * @param	Integer		$idFilterset			ID of the filterset
	 * @param	Boolean		$deleteConditions		Delete linked conditions too
	 */
	public static function deleteFilterset($idFilterset, $deleteConditions = true) {
		$idFilterset	= intval($idFilterset);

		TodoyuRecordManager::deleteRecord(self::TABLE, $idFilterset);

		if( $deleteConditions ) {
			TodoyuSearchFilterConditionManager::deleteFiltersetConditions($idFilterset);
		}

		TodoyuHookManager::callHook('search', 'filterset.delete', array($idFilterset));
	}



	/**
	 * Get conditions of filterset
	 *
	 * @param	Integer		$idFilterset
	 * @return	Array
	 */
	public static function getFiltersetConditions($idFilterset) {
		$idFilterset= intval($idFilterset);

		return TodoyuSearchFilterConditionManager::getFiltersetConditions($idFilterset);
	}



	/**
	 * Get the type of the filterset
	 *
	 * @param	Integer		$idFilterset
	 * @return	String
	 */
	public static function getFiltersetType($idFilterset) {
		$idFilterset= intval($idFilterset);
		$filterset	= self::getFiltersetRecord($idFilterset);

		return $filterset['type'];
	}



	/**
	 * Get result items to given set of filter conditions
	 *
	 * @param	Integer		$idFilterset
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getFiltersetResultItemIDs($idFilterset, $limit = 1000) {
		$idFilterset	= intval($idFilterset);
		$limit			= intval($limit);
		$filterset		= TodoyuSearchFiltersetManager::getFilterset($idFilterset);

		return $filterset->getItemIDs($limit);
	}



	/**
	 * Get items IDs for all filtersets
	 * Combination: OR
	 *
	 * @note	The limit per filter is set to 500, because everything else is useless
	 * @param	Array		$filtersetIDs
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getFiltersetsResultItemIDs(array $filtersetIDs, $limit = 1000) {
		$filtersetIDs	= TodoyuArray::intval($filtersetIDs, true, true);
		$allResultItems	= array();

		foreach($filtersetIDs as $idFilterset) {
			$allResultItems[] = self::getFiltersetResultItemIDs($idFilterset, $limit);
		}

		$resultItems	= array_unique(TodoyuArray::mergeSubArrays($allResultItems));

		return array_slice($resultItems, 0, $limit);
	}



	/**
	 * Get number of result items for a filterset
	 *
	 * @param	Integer		$idFilterset
	 * @return	Integer
	 */
	public static function getFiltersetCount($idFilterset) {
		$idFilterset	= intval($idFilterset);
		$filterset		= TodoyuSearchFiltersetManager::getFilterset($idFilterset);
		$filterObject	= $filterset->getFilterObject();

			// Execute dummy query to count the result rows
		TodoyuCache::disable(); // Disable cache to make sure result count is correct
		$filterObject->getItemIDs('', 1);
		TodoyuCache::enable();

		return $filterObject->getTotalItems();
	}



	/**
	 * Get amount of result items for the combination of all filtersets
	 *
	 * @param	Array	$filtersetIDs
	 * @return	Integer
	 */
	public static function getFiltersetsCount(array $filtersetIDs) {
		$resultItems	= self::getFiltersetsResultItemIDs($filtersetIDs);

		return sizeof($resultItems);
	}



	/**
	 * Update filterset title
	 *
	 * @param	Integer		$idFilterset
	 * @param	String		$title
	 */
	public static function renameFilterset($idFilterset, $title) {
		$idFilterset	= intval($idFilterset);
		$data = array(
			'title'	=> $title
		);

		self::updateFilterset($idFilterset, $data);
	}



	/**
	 * Update filterset visibility: Set hidden attribute of the filterset
	 *
	 * @param	Integer		$idFilterset
	 * @param	Boolean		$isVisible
	 */
	public static function updateFiltersetVisibility($idFilterset, $isVisible = true) {
		$data = array(
			'is_hidden'	=> $isVisible ? 0 : 1
		);

		self::updateFilterset($idFilterset, $data);
	}



	/**
	 * Get all filterset types
	 *
	 * @return	Array
	 */
	public static function getFiltersetTypes() {
		TodoyuExtensions::loadAllFilters();

		if( is_array(Todoyu::$CONFIG['FILTERS']) ) {
			$keys	= array_keys(Todoyu::$CONFIG['FILTERS']);
		} else {
			$keys	= array();
		}

		return array_map('strtolower', $keys);
	}



	/**
	 * Get filtersets of a type for the (current) person
	 *
	 * @param	String		$type
	 * @param	Integer		$idPerson
	 * @param	Boolean		$showHidden
	 * @return	Array
	 */
	public static function getTypeFiltersets($type = 'TASK', $idPerson = 0, $showHidden = false, $includeSeparators = false) {
		$type		= empty($type) ? 'TASK' : strtolower(trim($type));
		$idPerson	= Todoyu::personid($idPerson);

		return self::getFiltersets($idPerson, $type, $showHidden, $includeSeparators);
	}



	/**
	 * Get filtersets (of a person and a type)
	 * If no person defined, it gets filtersets for the current person
	 * If no type defined, it gets filtersets of all types (of installed extensions)
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$type
	 * @param	Boolean		$showHidden
	 * @param	Boolean		$includeSeparators
	 * @return	Array
	 */
	public static function getFiltersets($idPerson = 0, $type = null, $showHidden = false, $includeSeparators = true) {
		$idPerson		= Todoyu::personid($idPerson);
		$filtersetTypes	= TodoyuSearchFiltersetManager::getFiltersetTypes();
		$typeList		= TodoyuArray::implodeQuoted($filtersetTypes);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		id_person_create	= ' . $idPerson
				. ' AND	deleted				= 0'
				. '	AND	`type`				IN(' . $typeList . ')'
				. ' AND current				= 0';

		if( $includeSeparators === false ) {
			$where	.= ' AND is_separator	= 0';
		}

		if( $showHidden === false ) {
			$where	.= ' AND is_hidden		= 0';
		}

		$order	= 'sorting, date_create';

		if( ! is_null($type) ) {
			$where .= ' AND `type` = ' . TodoyuSql::quote($type, true);
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get IDs of filtersets of given person. if no type given: all types
	 *
	 * @param	Integer	$idPerson
	 * @param	String	$type
	 * @return	Array
	 */
	public static function getFiltersetIDs($idPerson = 0, $type = null) {
		$idPerson	= Todoyu::personid($idPerson);

		$fields	= 'id';
		$table	= self::TABLE;
		$where	= '			id_person_create	= ' . $idPerson
				  . ' AND	deleted				= 0';
		$order	= 'title';

		if( ! is_null($type) ) {
			$where .= ' AND `type` = ' . TodoyuSql::quote($type, true);
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get filterset titles (of a person and of a type)
	 * If no person defined, it gets filtersets for the current person
	 * If no type defined, it gets filtersets of all types
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getFiltersetTitles($idPerson = 0, $type = null) {
		$idPerson	= Todoyu::personid($idPerson);

		$fields	= 'title';
		$table	= self::TABLE;
		$where	= '		id_person_create	= ' . $idPerson
				. ' AND	deleted				= 0';
		$order	= 'title';

		if( ! is_null($type) ) {
			$where .= ' AND `type` = ' . TodoyuSql::quote($type, true);
		}

		return Todoyu::db()->getColumn($fields, $table, $where, '', $order);
	}



	/**
	 * Updates given order of the filterset in the database
	 *
	 * @param	Array	$items
	 */
	public static function updateOrder(array $items) {
		$sorting	= 0;

		foreach($items as $idItem) {
			$update	= array(
				'sorting'	=> $sorting++
			);

			TodoyuRecordManager::updateRecord(self::TABLE, $idItem, $update);
		}
	}



	/**
	 * Create new / update existing filterset and (re-)create the conditions in the database
	 *
	 * @param	Array		$filterData
	 * @return	Integer						Filterset ID
	 */
	public static function saveFilterset(array $filterData) {
		$idFilterset= intval($filterData['filterset']);

		$filtersetData	= array(
			'type'			=> $filterData['type'],
			'title'			=> $filterData['title'],
			'conjunction'	=> $filterData['conjunction'],
			'resultsorting'	=> $filterData['resultsorting'],
			'current'		=> intval($filterData['current']) === 1 ? '1' : '0'
		);

			// Add or update filterset
		if( $idFilterset === 0 ) {
			$idFilterset = self::addFilterset($filtersetData);
		} else {
			self::updateFilterset($idFilterset, $filtersetData);
		}

			// Save conditions
		TodoyuSearchFilterConditionManager::saveFilterConditions($idFilterset, $filterData['conditions']);

		return $idFilterset;
	}



	/**
	 * Save (update or create new) separator
	 *
	 * @param	Array	$data
	 * @return	Integer			Separator's filterset ID
	 */
	public static function saveFiltersetSeparator(array $data) {
		$idFilterset= intval($data['filterset']);

		$filtersetData	= array(
			'type'			=> $data['type'],
			'title'			=> $data['title'],
			'is_separator'	=> '1'
		);

			// Add or update filterset
		if( $idFilterset === 0 ) {
			$idFilterset = self::addFilterset($filtersetData);
		} else {
			self::updateFilterset($idFilterset, $filtersetData);
		}

		return $idFilterset;
	}



	/**
	 * Validate filterset title (ensure uniqueness)
	 *
	 * @param	String		$type
	 * @param	String		$title
	 * @return	String
	 */
	public static function validateTitle($type, $title) {
		$typeFiltersets	= self::getFiltersetTitles(0, $type);

		if( in_array($title, $typeFiltersets) ) {
			$title = self::validateTitle($type, $title . '-2');
		}

		return $title;
	}



	/**
	 * Merges FilterObjects as one query
	 *
	 * @param	Array		$filterObjects
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_filterObject(array $filterObjects, $negate = false) {
		$cacheID	= md5(serialize(func_get_args()));

			// Prevent processing the same filterset in nested conditions
		if( self::$filterObjectProcessing[$cacheID] ) {
			TodoyuLogger::logFatal('Filterset was nested recursively. Skipped processing. Will cause empty result');
			return false;
		} else {
			self::$filterObjectProcessing[$cacheID] = true;
		}

			// Only calculate result if not cached already
		if( ! array_key_exists($cacheID, self::$filterObjectCache) ) {
			$queryParts		= false;

			$tables		= array();
			$wheres		= array();
			$joins		= array();

			$whereAND	= false;

			foreach($filterObjects as $filterSet) {
				/**
				 * @var	TodoyuSearchFilterBase	$filterSet
				 */
				$queryArray = $filterSet->getQueryArray('', '', false, true);

					// If filterset is active
				if( $queryArray !== false ) {
					if( is_array($queryArray['whereAND']) ) {
						$whereAND	= '(' . implode(') AND (', $queryArray['whereAND']) . ')';
					}

						// If both are set, concatenate with AND
					if( $queryArray['whereBasic'] && $whereAND ) {
						$where	= $queryArray['whereBasic'] . ' AND ' . $whereAND;
					} else {
							// If not both are set, combine to one string
						$where = trim($queryArray['whereBasic'] . $whereAND);
					}

						// If no WHERE statement available, go to next filterset
					if( $where === '' ) {
						continue;
					}

						// Add WHERE statement to list
					$wheres[] = $where;
						// Add tables (they are already concatenated as string, so explode)
					$tables	= array_merge($tables, TodoyuArray::trimExplode(',', $queryArray['tables'], true));
						// Add joins
					if( is_array($queryArray['join']) ) {
						$joins	= array_merge($joins, $queryArray['join']);
					}
				}
			}

				// If conditions found, build query parts
			if( sizeof($wheres) > 0 ) {
					// Remove double tables
				$tables	= array_unique($tables);
				$where	= '(' . implode(' AND ', $wheres) . ')';
				$joins	= array_unique($joins);

				$queryParts	= array(
					'tables'	=> $tables,
					'where'		=> $where,
					'join'		=> $joins
				);
			}

			self::$filterObjectCache[$cacheID] = $queryParts;
		}

			// Reset lock flag for processed filterset
		self::$filterObjectProcessing[$cacheID] = false;

		return self::$filterObjectCache[$cacheID];
	}







		### NOT YET CLEANED UP FUNCTIONS ###



	/**
	 * Get filterset options for task
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getTaskFilterSetSelectionOptions(array $definitions) {
		$allFiltersets	= self::getTypeFiltersets('TASK', Todoyu::personid(), true);
		$activeFilterset= TodoyuSearchPreferences::getActiveFilterset('task');

		$definitions['options']	= self::buildFiltersetOptions($allFiltersets, $activeFilterset);

		return $definitions;
	}



	/**
	 * Get filterset options for project
	 *
	 * @param	Array		$definitions
	 * @return	Array
	 */
	public static function getProjectFilterSetSelectionOptions(array $definitions) {
		$allFiltersets	= self::getTypeFiltersets('PROJECT', Todoyu::personid(), true);
		$activeFilterset= TodoyuSearchPreferences::getActiveFilterset('project');

		$definitions['options']	= self::buildFiltersetOptions($allFiltersets, $activeFilterset);

		return $definitions;
	}



	/**
	 * Build options from filtersets. Exclude active if in search area
	 *
	 * @param	Array	$allFiltersets
	 * @param	Integer	$activeFilterset
	 * @return	Array
	 */
	private static function buildFiltersetOptions(array $allFiltersets, $activeFilterset) {
		$options	= array();

		foreach($allFiltersets as $filterset) {
				// Prevent adding the filterset to itself
			if( AREA !== EXTID_SEARCH || $filterset['id'] != $activeFilterset ) {
				if( ! self::isFiltersetUsed($filterset['id'], $activeFilterset) ) {
					$options[] = array(
						'value'		=> $filterset['id'],
						'label'		=> $filterset['title']
					);
				}
			}
		}

		return $options;
	}



	/**
	 * Check to avoid from endless loop.
	 *
	 * @param	Integer		$idFilterset
	 * @param	Integer		$idFiltersetToCheck
	 * @return	Boolean
	 */
	protected static function isFiltersetUsed($idFilterset, $idFiltersetToCheck) {
		$conditions = TodoyuSearchFilterConditionManager::getFilterSetConditions($idFilterset);

		foreach($conditions as $condition) {
			if( $condition['filter'] === 'filterSet' ) {
				$subFiltersetIDs	= explode(',', $condition['value']);

					// Make sure the cache array for the filterset exists
				if( ! array_key_exists($idFiltersetToCheck, self::$filtersetChecked) ) {
					self::$filtersetChecked[$idFiltersetToCheck] = array();
				}

					// Check whether filterset is directly used
				if( in_array($idFiltersetToCheck, $subFiltersetIDs) ) {
					return true;
				} else {
						// Check sub filter sets
					foreach($subFiltersetIDs as $subFiltersetID) {
							// If already checked, return result of check
						if( array_key_exists($subFiltersetID, self::$filtersetChecked[$idFiltersetToCheck]) ) {
							return self::$filtersetChecked[$idFiltersetToCheck][$subFiltersetID];
						} else {
								// Simulate the be already checked to prevent loops
							self::$filtersetChecked[$idFiltersetToCheck][$subFiltersetID] = true;
						}

							// Check usage recursively
						$isUsed	= self::isFiltersetUsed($subFiltersetID, $idFiltersetToCheck);

							// Save check to cache
						self::$filtersetChecked[$idFiltersetToCheck][$subFiltersetID] = $isUsed;

						if( $isUsed ) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}



	/**
	 * Get filter class for a type
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getFiltersetTypeClass($type) {
		TodoyuExtensions::loadAllFilters();

		$class	= Todoyu::$CONFIG['FILTERS'][strtoupper($type)]['config']['class'];

		return is_null($class) ? false : $class;
	}

}

?>