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
 * Base data source
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
abstract class TodoyuCalendarDataSource {

	/**
	 * Data source configuration
	 * @var	Array
	 */
	protected $config;

	/**
	 * Range
	 * @var	TodoyuDayRange
	 */
	protected $range;

	/**
	 * Active filters
	 * @var	Array
	 */
	protected $filters;



	/**
	 * Initialize data source with config, range and filter
	 *
	 * @param	Array			$config
	 * @param	TodoyuDayRange	$range
	 * @param	Array			$filters
	 */
	public final function __construct(array $config, TodoyuDayRange $range, array $filters = array()) {
		$this->config	= $config;
		$this->range	= $range;
		$this->filters	= $filters;
	}



	/**
	 * Get range
	 *
	 * @return	TodoyuDayRange
	 */
	public function getRange() {
		return $this->range;
	}



	/**
	 * Get filters
	 *
	 * @return	Array
	 */
	public function getFilters() {
		return $this->filters;
	}



	/**
	 * Get filter value
	 *
	 * @param	String		$name
	 * @param	Boolean		$asArray
	 * @return	Array|Mixed
	 */
	public function getFilter($name, $asArray = false) {
		$filter	= $this->filters[$name];

		if( $asArray ) {
			$filter	= TodoyuArray::assure($filter);
		}

		return $filter;
	}



	/**
	 * Get events from data source
	 *
	 * @return	TodoyuCalendarEvent[]
	 */
	abstract public function getEvents();



	/**
	 * Get event count
	 *
	 * @return	Integer
	 */
	abstract public function getEventCount();



	/**
	 * Search events
	 *
	 * @param	String		$searchWord
	 */
	abstract public function searchEvents($searchWord);



	/**
	 * Get event object from data source
	 *
	 * @param	Integer		$idEvent
	 * @return	TodoyuCalendarEvent
	 */
	abstract public static function getEvent($idEvent);

}

?>