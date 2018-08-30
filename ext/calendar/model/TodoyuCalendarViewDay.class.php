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
 * Day view for calendar
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarViewDay extends TodoyuCalendarView {

	/**
	 * Initialize with date and filters
	 *
	 * @param	Integer		$date
	 * @param	Array		$filters
	 */
	public function __construct($date, array $filters = array()) {
		$range	= new TodoyuCalendarRangeDay($date);

		parent::__construct($range, $filters);
	}



	/**
	 * Render day view
	 *
	 * @return String
	 */
	public function render() {
		$tmpl		= 'ext/calendar/view/views/day.tmpl';
		$data		= array(
			'timestamp'		=> $this->getRange()->getStart(),
			'showFullDay'	=> $this->isFullDayView(),
			'events'		=> $this->renderEvents(), // self::preRenderEventsForDay($dateStart, $eventTypeIDs, $personIDs, $personColors),
			'dayEvents'		=> $this->renderDayEvents(),
			'title'			=> $this->getTitle(),
			'rangeStart'	=> TodoyuCalendarPreferences::getCompactViewRangeStart(),
			'rangeEnd'		=> TodoyuCalendarPreferences::getCompactViewRangeEnd(),
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Check whether full day view is enabled
	 *
	 * @return	Boolean
	 */
	private function isFullDayView() {
		return TodoyuCalendarPreferences::getFullDayView();
	}



	/**
	 * Get event elements to render in calendar
	 *
	 * @param	Array			$extraFilters
	 * @param	Boolean|Null	$dayEvents
	 * @return	TodoyuCalendarEventElementDay[]
	 */
	private function getEventElements(array $extraFilters = array(), $dayEvents = null) {
		$events			= $this->getEvents($extraFilters, $dayEvents);
		$eventElements	= array();

		foreach($events as $event) {
			$eventElements[]	= new TodoyuCalendarEventElementDay($event, $this);
		}

		return $eventElements;
	}



	/**
	 * Get day event elements to render in calendar
	 *
	 * @return TodoyuCalendarEventElementDayeventDay[]
	 */
	private function getDayEventsElements() {
		$eventElements		= $this->getEventElements(array(), true);
		$dayEventElements	= array();

		foreach($eventElements as $eventElement) {
			$dayEventElements[]	= new TodoyuCalendarEventElementDayeventDay($eventElement);
		}

		return $dayEventElements;
	}



	/**
	 * Pre render event elements
	 *
	 * @return	String[]
	 */
	private function renderEvents() {
		$renderedElements	= array();
		$eventElements		= $this->getEventElementsWithOverlapping();

		foreach($eventElements as $eventElement) {
			$renderedElements[]	= $eventElement->render();
		}

		return $renderedElements;
	}



	/**
	 * Get event elements with overlapping information for rendering
	 *
	 * @return	TodoyuCalendarEventElementDay[]
	 */
	private function getEventElementsWithOverlapping() {
		$eventElements	= $this->getEventElements(array(), false);

		return TodoyuCalendarEventElementManager::addOverlapInformationToEvents($eventElements);
	}



	/**
	 * Pre render day event elements
	 *
	 * @return	String[]
	 */
	private function renderDayEvents() {
		$dayEventElements	= $this->getDayEventsElements();
		$renderedElements	= array();

		foreach($dayEventElements as $dayEventElement) {
			$renderedElements[]	= $dayEventElement->render();
		}

		return $renderedElements;
	}



	/**
	 * Get view title
	 *
	 * @return	String
	 */
	protected function getTitle() {
		return TodoyuCalendarTime::format($this->getRange()->getStart(), 'calendar.ext.calendartitle.dateformat.day');
	}

}

?>