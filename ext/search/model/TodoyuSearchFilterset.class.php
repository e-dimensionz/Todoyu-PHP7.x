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
 * Filterset object
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterset extends TodoyuBaseObject {

	/**
	 * Initialize filterset
	 *
	 * @param	Integer		$idFilterset
	 */
	public function __construct($idFilterset) {
		parent::__construct($idFilterset, 'ext_search_filterset');
	}



	/**
	 * Get owner of the filterset
	 *
	 * @return	TodoyuContactPerson
	 */
	public function getPerson($type) {
		return $this->getPerson('create');
	}



	/**
	 * Get filterset conditions
	 *
	 * @return	Array
	 */
	public function getConditions() {
		return TodoyuSearchFiltersetManager::getFiltersetConditions($this->getID());
	}



	/**
	 * Get filterset conjunction
	 *
	 * @return	String
	 */
	public function getConjunction() {
		return $this->get('conjunction');
	}



	/**
	 * Get result sorting flags
	 *
	 * @return	Array
	 */
	public function getResultSorting() {
		$sorting	= trim($this->get('resultsorting'));

		if( $sorting === '' ) {
			$sorting = array();
		} else {
			$sorting = json_decode($sorting, true);
		}

		return $sorting;
	}



	/**
	 * Get result sorting flags with labels
	 *
	 * @return	Array[]
	 */
	public function getResultSortingWithLabels() {
		$resultSortings = $this->getResultSorting();

		TodoyuExtensions::loadAllFilters();

		foreach($resultSortings as $index => $resultSorting) {
			$resultSortings[$index]['label'] = TodoyuSearchSortingManager::getLabel($this->getType(), $resultSorting['name']);
		}

		return $resultSortings;
	}



	/**
	 * Get javascript code to initialize all sorting flags of the filterset
	 *
	 * @return	String
	 */
	public function getResultSortingJsInitCode() {
		$resultSortings	= $this->getResultSortingWithLabels();

		$jsCode	= 'Todoyu.Ext.search.Filter.Sorting.addAll(' . json_encode($resultSortings) . ')';

		return TodoyuString::wrapScript($jsCode);
	}



	/**
	 * Get filterset title
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->get('title');
	}



	/**
	 * Get filterset type
	 *
	 * @return	String
	 */
	public function getType() {
		return $this->get('type');
	}



	/**
	 * Get matching item IDs
	 *
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public function getItemIDs($limit = 1000) {
		$limit			= intval($limit);
		$filterObject	= $this->getFilterObject();
		$itemIDs		= array();

		if( $filterObject !== false ) {
			$sorting= TodoyuSearchFilterManager::getFilterDefaultSorting($this->getType());

			return $filterObject->getItemIDs($sorting, $limit);
		}

		return $itemIDs;
	}



	/**
	 * Get type class
	 *
	 * @return		String
	 */
	public function getClass() {
		return TodoyuSearchFiltersetManager::getFiltersetTypeClass($this->getType());
	}



	/**
	 * Get filter object from filterset
	 *
	 * @return	TodoyuSearchFilterBase|Boolean
	 */
	public function getFilterObject() {
		$class	= $this->getClass();

		if( $class !== false ) {
			$conditions	= $this->getConditions();
			$conjunction= $this->getConjunction();
			$sorting	= $this->getResultSorting();

			return new $class($conditions, $conjunction, $sorting);
		} else {
			return false;
		}
	}

}

?>