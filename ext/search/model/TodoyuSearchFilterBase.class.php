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
 * Filter base. Implements the basic filter logic and fetches
 *
 * @package		Todoyu
 * @subpackage	Filter
 * @abstract
 */
abstract class TodoyuSearchFilterBase {

	/**
	 * All active filters
	 *
	 * @var	Array
	 */
	protected $activeFilters;

	/**
	 * Filter type. Function references are stored in the config array under the specific type
	 *
	 * @var	String
	 */
	protected $type;

	/**
	 * The table the filter gets the IDs from. This table needs to be in the request, so it's added by default
	 *
	 * @var	String
	 */
	protected $defaultTable;

	/**
	 * Extra tables to be used by the filter
	 *
	 * @var	Array
	 */
	protected $extraTables = array();

	/**
	 * Extra WHERE clauses for the filter
	 *
	 * @var	Array
	 */
	protected $extraWhere	= array();

	/**
	 * @var	Array
	 */
	protected $rightsFilters	= array();

	/**
	 * Logical conjunction
	 *
	 * @var String
	 */
	protected $conjunction = 'AND';

	/**
	 * Sorting flags
	 *
	 * @var	Array
	 */
	protected $sorting = array();

	/**
	 * Cache for result IDs
	 *
	 * @var	Array
	 */
	protected $resultIDs	= array();

	/**
	 * Total found rows of last query
	 *
	 * @var	Integer
	 */
	private $totalFoundRows	= 0;




	/**
	 * Initialize filter object
	 *
	 * @param	String		$type				Type of the filter (funcRefs are stored in the config unter this type)
	 * @param	String		$defaultTable		Table to get the IDs from
	 * @param	Array		$activeFilters		Active filters of the current request
	 * @param	String		$conjunction		AND or OR
	 * @param	Array		$sorting			Sorting flags
	 */
	protected function __construct($type, $defaultTable, array $activeFilters = array(), $conjunction = 'AND', array $sorting = array()) {
		$this->type					= strtoupper($type);
		$this->defaultTable			= $defaultTable;
		$this->activeFilters		= $activeFilters;
		$this->conjunction			= $conjunction;
		$this->sorting				= $sorting;

		TodoyuExtensions::loadAllFilters();
	}



	/**
	 * Get conjunction of the filterset
	 *
	 * @return	String
	 */
	public function getConjunction() {
		return $this->conjunction;
	}



	/**
	 * Add an extra table for the request query
	 *
	 * @param	String		$table
	 */
	public function addExtraTable($table) {
		$this->extraTables[] = $table;
	}



	/**
	 * Add an extra WHERE clause for the request query
	 *
	 * @param	String		$where		WHERE clause
	 */
	public function addExtraWhere($where) {
		$this->extraWhere[] = $where;
	}



	/**
	 * Add an extra filter
	 *
	 * @param	String		$name		Filter name
	 * @param	String		$value
	 * @param	Boolean		$negate
	 */
	public function addFilter($name, $value, $negate = false) {
		$this->activeFilters[] = array(
			'filter'	=> $name,
			'value'		=> $value,
			'negate'	=> $negate
		);
	}



	/**
	 * Add a rights filter where is always added with AND
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public function addRightsFilter($name, $value) {
		$this->rightsFilters[] = array(
			 'filter'	=> $name,
			 'value'	=> $value,
			 'negate'	=> false
		);
	}



	/**
	 * Check whether filter exists in config
	 *
	 * @param	String		$filter
	 * @return	Boolean
	 */
	protected function isFilter($filter) {
		$filterMethod = $this->getFilterMethod($filter);

		return method_exists($filterMethod[0], $filterMethod[1]);
	}



	/**
	 * Check whether name is a valid sorting flag
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	protected function isSorting($name) {
		$sortingMethod	= $this->getSortingMethod($name);

		return method_exists($sortingMethod[0], $sortingMethod[1]);
	}



	/**
	 * Check first if its a filterWidget. then return class Todoyu and method
	 *
	 * else build it from current type and filter
	 *
	 * @param	String		$filter
	 * @return	Array		[0]=> classname [1]=> methodname
	 */
	protected function getFilterMethod($filter) {
		$method	= 'Filter_' . $filter;

			// Check if filter is a local function
		if( method_exists($this, $method) ) {
			return array($this, $method);
		} else {
			$config	= TodoyuSearchFilterManager::getFilterConfig($this->type, $filter);

			if( $config !== false ) {
				$funcRef	= $config['funcRef'];

				if( TodoyuFunction::isFunctionReference($funcRef) ) {
					return explode('::', $funcRef);
				}
			}
		}

			// If no function reference found, log error
		TodoyuLogger::logError('Filter method "' . $filter . '" (table: ext_search_filtercondition) not found for type ' . $this->type);

		return false;
	}



	/**
	 * Get sorting flag method reference
	 *
	 * @param	String		$name
	 * @return array|bool
	 */
	protected function getSortingMethod($name) {
		$method	= 'Sorting_' . $name;

			// Check if filter is a local function
		if( method_exists($this, $method) ) {
			return array($this, $method);
		} else {
			$config	= TodoyuSearchSortingManager::getSortingConfig($this->type, $name);

			if( isset($config['funcRef']) ) {
				$funcRef= $config['funcRef'];

				if( TodoyuFunction::isFunctionReference($funcRef) ) {
					return explode('::', $funcRef);
				}
			}
		}

			// If no function reference found, log error
		TodoyuLogger::logError('Sorting method "' . $name . '" not found for type ' . $this->type);

		return false;
	}



	/**
	 * returns the function to render the searchresults
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getFilterRenderFunction($type = 'task') {
		return TodoyuSearchFilterManager::getFilterTypeResultsRenderer($type);
	}



	/**
	 * Get query parts provided by all active filters
	 *
	 * @return	Array|Boolean		Array with sub arrays named 'tables' and 'where' OR false of no query is active
	 */
	protected function fetchFilterQueryParts() {
		$queryParts	= array(
			'fields'		=> array(
				$this->defaultTable . '.id'
			),
			'tables'		=> array(
				$this->defaultTable
			),
			'removeTables'	=> array(),
			'where'			=> array(),
			'join'			=> array(),
			'order'			=> array()
		);

			// Add extra tables and WHERE parts
		$queryParts['tables']	= TodoyuArray::merge($queryParts['tables'], $this->extraTables);
		$queryParts['where']	= TodoyuArray::merge($queryParts['where'], $this->extraWhere);

			// Fetch all query parts from the filters
		foreach($this->activeFilters as $filter) {
			if( $this->isFilter($filter['filter']) ) {
					// Get array which references the filter function
				$funcRef	= $this->getFilterMethod($filter['filter']);

					// Filter function parameters
				$params		= array(
					$filter['value'],
					$filter['is_negated'] ?? 0 == 1
				);

					// Call filter function to get query parts for filter
				$filterQueryParts = call_user_func_array($funcRef, $params);

					// Check if return value is an array
				if( ! is_array($filterQueryParts) ) {
					continue;
				}

					#### Add queryParts from filter ####
					// Add tables
				if( !empty($filterQueryParts['tables']) && is_array($filterQueryParts['tables']) ) {
					$queryParts['tables'] = TodoyuArray::merge($queryParts['tables'], $filterQueryParts['tables']);
				}
					// Add WHERE
				if( is_string($filterQueryParts['where']) ) {
					$queryParts['where'][] = $filterQueryParts['where'];
				}
					// Add JOIN WHERE
				if(  !empty($filterQueryParts['join']) && is_array($filterQueryParts['join']) ) {
					$queryParts['join'] = TodoyuArray::merge($queryParts['join'], $filterQueryParts['join']);
				}
					// Add remove tables
				if( !empty($filterQueryParts['removeTables']) && is_array($filterQueryParts['removeTables']) ) {
					$queryParts['removeTables'] = TodoyuArray::merge($queryParts['removeTables'], $filterQueryParts['removeTables']);
				}
			} else {
				TodoyuLogger::logError('Unknown filter: ' . $filter['filter']);
			}
		}

			// Add sorting flag query parts
		foreach($this->sorting as $sorting) {
			if( $this->isSorting($sorting['name']) ) {
					// Get array which references the filter function
				$funcRef	= $this->getSortingMethod($sorting['name']);

					// Filter function parameters
				$params		= array(
					$sorting['dir'] === 'desc'
				);

					// Call filter function to get query parts for filter
				$sortingQueryParts = call_user_func_array($funcRef, $params);

					// Check if return value is an array
				if( ! is_array($sortingQueryParts) ) {
					continue;
				}

					#### Add queryParts from sorting ####
				if( is_array($sortingQueryParts['fields']) ) {
					$queryParts['fields'] = TodoyuArray::merge($queryParts['fields'], $sortingQueryParts['fields']);
				}
					// Add tables
				if( is_array($sortingQueryParts['tables']) ) {
					$queryParts['tables'] = TodoyuArray::merge($queryParts['tables'], $sortingQueryParts['tables']);
				}
					// Add WHERE
				if( is_string($sortingQueryParts['where']) ) {
					$queryParts['where'][] = $sortingQueryParts['where'];
				}
					// Add JOIN WHERE
				if( is_array($sortingQueryParts['join']) ) {
					$queryParts['join'] = TodoyuArray::merge($queryParts['join'], $sortingQueryParts['join']);
				}
					// Add ORDER
				if( is_array($sortingQueryParts['order']) ) {
					$queryParts['order'] = TodoyuArray::merge($queryParts['order'], $sortingQueryParts['order']);
				}
					// Add remove tables
				if( is_array($sortingQueryParts['removeTables']) ) {
					$queryParts['removeTables'] = TodoyuArray::merge($queryParts['removeTables'], $sortingQueryParts['removeTables']);
				}
			} else {
				TodoyuLogger::logError('Unknown sorting: ' . $sorting['name']);
			}
		}

			// Remove double entries
		foreach($queryParts as $partName => $partValues) {
			$queryParts[$partName] = array_unique($partValues);
		}

		return $queryParts;
	}



	/**
	 * Fetch extra rights parts for query
	 * They are always added with AND
	 *
	 * @return	Array|Boolean
	 */
	protected function fetchRightsQueryParts() {
		$whereParts	= array();
		$tables		= array();
		$join		= array();

		if( TodoyuAuth::isAdmin() || empty($this->rightsFilters) ) {
			return false;
		}

		foreach($this->rightsFilters as $filter) {
			$funcRef= $this->getFilterMethod($filter['filter']);

			$params	= array(
				$filter['value'],
				false
			);

				// Call filter function to get query parts for filter
			$filterQueryParts = call_user_func_array($funcRef, $params);

			if( is_array($filterQueryParts['tables']) ) {
				$tables = TodoyuArray::merge($tables, $filterQueryParts['tables']);
			}
			if( $filterQueryParts !== false && isset($filterQueryParts['where']) ) {
				$whereParts[] = $filterQueryParts['where'];
			}
			if( is_array($filterQueryParts['join']) ) {
				$join = TodoyuArray::merge($join, $filterQueryParts['join']);
			}
		}

			// Only add WHERE clause
		if( !empty($whereParts)) {
			$where = '(' . implode(' AND ', $whereParts) . ')';
		} else {
			$where = '';
		}

		return array(
			'tables'	=> array_unique($tables),
			'where'		=> $where,
			'join'		=> array_unique($join)
		);
	}



	/**
	 * Gets the query array which is merged from all filters
	 * Array contains the strings for the following parts:
	 * fields, tables, where, group, order, limit
	 * Extra fields for internal use: whereNoJoin, join
	 *
	 * @param	String		$sortingFallback					Optional ORDER BY for query
	 * @param	String		$limit						Optional LIMIT for query
	 * @param	Boolean		$showDeleted				Show deleted records?
	 * @param	Boolean		$noResultOnEmptyConditions	Return false if no condition is active
	 * @return	Array|Boolean
	 */
	public function getQueryArray($sortingFallback = '', $limit = '', $showDeleted = false, $noResultOnEmptyConditions = false) {
			// Get normal query parts
		$queryParts	= $this->fetchFilterQueryParts();

			// If no conditions in WHERE clause and $noResultOnEmptyConditions flag set, return flag (no SQL query performed)
		if( $noResultOnEmptyConditions && empty($queryParts['where']) ) {
			return false;
		}

			// Get rights query parts
		$rightsParts= $this->fetchRightsQueryParts();

			// Combine join from filter and rights
		$join	= array_unique(TodoyuArray::merge($queryParts['join'], $rightsParts ? $rightsParts['join'] : []));
		$tables	= array_unique(TodoyuArray::merge($queryParts['tables'], $rightsParts ? $rightsParts['tables'] : []));
			// Remove tables
		$tables	= array_diff($tables, $queryParts['removeTables']);

		$connection	= $this->conjunction ? $this->conjunction : 'AND';
		$queryArray	= array();

		$queryArray['fields']	= 'SQL_CALC_FOUND_ROWS ' . implode(', ', $queryParts['fields']);
		$queryArray['tables']	= implode(', ', $tables);
		$queryArray['where']	= ''; // WHERE clause is added later
		$queryArray['group']	= $this->defaultTable . '.id';
		$queryArray['limit']	= $limit;

			// Has custom sorting or use fallback?
		if( empty($queryParts['order']) ) {
			$queryArray['order'] = $sortingFallback;
		} else {
			$queryArray['order'] = implode(', ', $queryParts['order']);
		}


		$whereParts	= array();

			// Deleted
		if( !$showDeleted ) {
			$whereParts[] = $this->defaultTable . '.deleted = 0';
		}

			// Rights
		if( $rightsParts !== false && !empty($rightsParts['where']) ) {
			$whereParts[] = $rightsParts['where'];
		}

			// Make a backup of the WHERE parts which are required to be AND
		$queryArray['whereAND']	= $whereParts;

			// Filter
		if( !empty($queryParts['where']) ) {
			$basicFilterWhere			= implode(' ' . $connection . ' ', $queryParts['where']);
			$whereParts[]				= $basicFilterWhere;
				// Make a backup of the basic filters which are combined by the conjunction of the filterset
			$queryArray['whereBasic']	= $basicFilterWhere;
		}

			// Join
		if( !empty($join)) {
			$whereParts[] = implode(' AND ', $join);
				// Save joins for further use
			$queryArray['join'] = $join;
		}

		if( !empty($whereParts) ) {
			$queryArray['where'] = '(' . implode(') AND (', $whereParts) . ')';
		}

		return $queryArray;
	}



	/**
	 * Get the full query array. This is just for debugging
	 *
	 * @param	String		$orderBy	Optional order by for query
	 * @param	String		$limit		Optional limit for query
	 * @param	Boolean		$showDeleted
	 * @return	String
	 */
	public function getQuery($orderBy = '', $limit = '', $showDeleted = false) {
		$queryArray = $this->getQueryArray($orderBy, $limit, $showDeleted);

		$query	= TodoyuSql::buildSELECTquery(
			$queryArray['fields'],
			$queryArray['tables'],
			$queryArray['where'],
			$queryArray['group'],
			$queryArray['order'],
			$queryArray['limit']
		);

		return $query;
	}



	/**
	 * Check if filter has conditions
	 *
	 * @return	Boolean
	 */
	public function hasActiveFilters() {
		return $this->getQueryArray('', '', false, true) !== false;
	}



	/**
	 * Get item IDs from default table which match to all active filters
	 *
	 * @param	String		$sortingFallback			Optional order by for query
	 * @param	String		$limit						Optional limit for query
	 * @param	Boolean		$showDeleted				Show deleted records
	 * @return	Array		List of IDs of matching records
	 */
	public function getItemIDs($sortingFallback = '', $limit = '', $showDeleted = false) {
		$cacheID	= md5(serialize(func_get_args()));

			// Check if results are already cached
		if( !isset($this->resultIDs[$cacheID]) ) {
			$queryArray = $this->getQueryArray($sortingFallback, $limit, $showDeleted, false);

			$this->resultIDs[$cacheID] = Todoyu::db()->getColumn(
				$queryArray['fields'],
				$queryArray['tables'],
				$queryArray['where'],
				$queryArray['group'],
				$queryArray['order'],
				$queryArray['limit'],
				'id'
			);

//			TodoyuDebug::printLastQueryInFirebug();

			$this->totalFoundRows	= Todoyu::db()->getTotalFoundRows();
		}

		return $this->resultIDs[$cacheID];
	}



	/**
	 * Get total found rows. Same as when the filter would have been called without a limit
	 *
	 * @return	Integer
	 */
	public final function getTotalItems() {
		return $this->totalFoundRows;
	}



	/**
	 * Filter after filter sets
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 * @todo	Implement negation?
	 */
	public function Filter_filterSet($value, $negate = false) {
		$filtersetIDs	= TodoyuArray::intExplode(',', $value, true, true);

			// Prepare return values
		$filterObjects	= array();

			// Process all filtersets
		foreach($filtersetIDs as $idFilterset) {
			$filterSet		= TodoyuSearchFiltersetManager::getFilterset($idFilterset);
			$filterObject	= $filterSet->getFilterObject();

			if( $filterObject !== false ) {
				$filterObjects[] = $filterObject;
			}
		}

		if( !empty($filterObjects) ) {
			return TodoyuSearchFiltersetManager::Filter_filterObject($filterObjects, $negate);
		} else {
			return false;
		}
	}



	/**
	 * Get sorting direction string
	 *
	 * @param	Boolean		$desc
	 * @return	String		DESC or ASC
	 */
	protected static function getSortDir($desc = false) {
		return $desc ? ' DESC' : ' ASC';
	}

}

?>