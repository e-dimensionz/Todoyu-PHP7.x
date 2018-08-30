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
 * Basic view for calendar
 * Renders a full content view
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
abstract class TodoyuCalendarView {

	/**
	 * Range of the view
	 *
	 * @var	TodoyuDayRange
	 */
	protected $range;

	/**
	 * Active filters
	 *
	 * @var	Array
	 */
	protected $filters;



	/**
	 * Initialize view
	 *
	 * @param	TodoyuDayRange	$range
	 * @param	Array			$filters		Filters provided by arbitrary elements
	 */
	public function __construct(TodoyuDayRange $range, array $filters) {
		$this->range	= $range;
		$this->filters	= $filters;
	}



	/**
	 * Get view name
	 *
	 * @return	String
	 */
	public function getName() {
		return strtolower(str_replace('TodoyuCalendarView', '', get_class($this)));
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
	 * Set range
	 *
	 * @param	TodoyuDayRange		$range
	 */
	public function setRange(TodoyuDayRange $range) {
		$this->range	= $range;
	}



	/**
	 * Get filters
	 *
	 * @return	Array
	 */
	protected function getFilters() {
		return $this->filters;
	}



	/**
	 * Get a filter
	 *
	 * @param	String		$name
	 * @return	Mixed
	 */
	protected function getFilter($name) {
		return $this->filters[$name];
	}



	/**
	 * Add an additional filter
	 *
	 * @param	String	$name
	 * @param	Mixed	$value
	 */
	public function addFilter($name, $value) {
		$this->filters[$name]	= $value;
	}



	/**
	 * Remove/delete a filter
	 *
	 * @param	String		$name
	 */
	public function removeFilter($name) {
		unset($this->filters[$name]);
	}



	/**
	 * Get events from all data sources which match the given filters and the range
	 * The dayEvents parameter is just for convenience and is passed as normal filter value
	 *
	 * @param	Array			$extraFilters
	 * @param	Boolean|Null	$dayEvents
	 * @return	TodoyuCalendarEvent[]
	 */
	protected function getEvents(array $extraFilters = array(), $dayEvents = null) {
		$filters	= $this->getFilters();

		if( !is_null($dayEvents) ) {
			$extraFilters['dayevents']	= $dayEvents;
		}

		if( sizeof($extraFilters) > 0 ) {
			$filters	= array_merge($filters, $extraFilters);
		}

		return TodoyuCalendarDataSourceManager::getEvents($this->getRange(), $filters);
	}



	/**
	 * Render the view
	 *
	 * @return	String
	 */
	abstract public function render();



	/**
	 * Get title of the view
	 *
	 * @return	String
	 */
	abstract protected function getTitle();

}

?>