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
 * Calendar week view
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarViewWeek extends TodoyuCalendarView {

	/**
	 * Initialize week view
	 *
	 * @param	Integer		$date
	 * @param	Array		$filters
	 */
	public function __construct($date, array $filters = array()) {
		$showWeekend= TodoyuCalendarPreferences::isWeekendDisplayed();
		$range		= new TodoyuCalendarRangeWeek($date, $showWeekend);

		parent::__construct($range, $filters);
	}



	/**
	 * Render week view
	 *
	 * @return	String
	 */
	public function render() {
		$tmpl	= 'ext/calendar/view/views/week.tmpl';
		$data	= array(
			'title'			=> $this->getTitle(),
			'dayColumns'	=> $this->getDayColumns(),
			'events'		=> $this->renderEventsPerDay(),
			'dayEvents'		=> $this->getMappedDayEvents(),
			'showFullDay'	=> $this->isFullDayView(),
			'rangeStart'	=> TodoyuCalendarPreferences::getCompactViewRangeStart(),
			'rangeEnd'		=> TodoyuCalendarPreferences::getCompactViewRangeEnd(),
			'displayWeekend'=> $this->isWeekendDisplayed()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Check whether weekend is displayed
	 *
	 * @return	Boolean
	 */
	public function isWeekendDisplayed() {
		return TodoyuCalendarPreferences::isWeekendDisplayed();
	}



	/**
	 * Get week headers template data
	 *
	 * @return	Array[]
	 */
	private function getDayColumns() {
		$timeToday	= TodoyuTime::getDayStart();
		$dayDates	= $this->getRange()->getDayTimestamps();
		$columns	= array();

		foreach($dayDates as $dayDate) {
			$dayKey		= date('Ymd', $dayDate);

			$columns[$dayKey]	=  array(
				'key'	=> $dayKey,
				'date'	=> date('Y-m-d', $dayDate),
				'today'	=> $dayDate === $timeToday,
				'title'	=> TodoyuTime::format($dayDate, 'DlongD2MlongY4'),
				'label'	=> TodoyuTime::format($dayDate, 'DshortDMshort')
			);
		}

		return $columns;
	}



	/**
	 * Render events per day
	 * Rendered events html is grouped by date key
	 *
	 * @return	String[][]
	 */
	private function renderEventsPerDay() {
		$eventElements	= $this->getEventElementsWithOverlapping();
		$eventDayMap	= $this->getDayMapForWeek();

		foreach($eventElements as $eventElement) {
			$dayKeys	= $eventElement->getEvent()->getRange()->getDayTimestamps('Ymd');

				// Render event for all days it occurs
			foreach($dayKeys as $dayKey) {
				if( isset($eventDayMap[$dayKey]) ) {
					$dayDate		= strtotime($dayKey);
					$eventDayMap[$dayKey][]	= $eventElement->render($dayDate);
				}
			}
		}

		return $eventDayMap;
	}



	/**
	 * Get event elements with overlapping information for rendering
	 *
	 * @return	TodoyuCalendarEventElementWeek[]
	 */
	private function getEventElementsWithOverlapping() {
		$eventElements	= $this->getEventElements(array(), false);

		return TodoyuCalendarEventElementManager::addOverlapInformationToEvents($eventElements);
	}



	/**
	 * Group the events in sub array. The key for each sub array is a date-key (YYYYMMDD)
	 * An event appears in each sub array, the event is running on
	 *
	 * @param	Array		$events			Array of event records
	 * @param	Integer		$dateStart		Date of first day group
	 * @param	Integer		$dateEnd		Date of last day group
	 * @return	Array		Events grouped by date-key
	 */
	public static function groupEventsByDay(array $events, $dateStart, $dateEnd) {
		$dateStart		= TodoyuTime::getDayStart($dateStart);
		$dateEnd		= intval($dateEnd);
		$groupedEvents	= array();

		for($date = $dateStart; $date <= $dateEnd; $date += TodoyuTime::SECONDS_DAY ) {
			$dayKey		= date('Ymd', $date);
			$dayRange	= TodoyuTime::getDayRange($date);

			$groupedEvents[$dayKey]	= array();

			foreach($events as $event) {
				if( TodoyuTime::rangeOverlaps($dayRange['start'], $dayRange['end'], $event['date_start'], $event['date_end']) ) {
					$groupedEvents[$dayKey][]	= $event;
				}
			}
		}

		return $groupedEvents;
	}



	/**
	 * Get day map for week
	 * Map keys are the date key
	 *
	 * @return	Array[]
	 */
	private function getDayMapForWeek() {
		return $this->getRange()->getDayTimestampsMap('Ymd', array());
	}



	/**
	 * Get pre rendered and mapped (columns per day on collisions) event elements
	 *
	 * @return	Array[]
	 */
	private function getMappedDayEvents() {
		$dayEventWeekMap	= array();
		$mapPattern			= null;
		$dayEventElements	= $this->getDayEventsElements();

		if( !empty($dayEventElements)) {
			$mapPattern			= $this->getRange()->getDayMap('Ymd', false);
			$dayEventWeekMap[]	= $mapPattern;
		}

			// Find a position for all events
		foreach($dayEventElements as $dayEventElement) {
			$found				= false;
			$overlappingRange	= $dayEventElement->getEvent()->getRange()->getOverlappingRange($this->getRange(), true);
			$eventDayKeys		= $overlappingRange->getDayTimestamps('Ymd');


				// Check every row for available space
			foreach($dayEventWeekMap as $index => $displayRow) {
				foreach($eventDayKeys as $dayKey) {
					if( $displayRow[$dayKey] !== false ) {
						continue 2; // Not the whole space is available, check next row
					}
				}

					// No collision, insert event and block rest of used cells
				$found			= true;
				$firstDayKey	= array_shift($eventDayKeys);
				$dayEventWeekMap[$index][$firstDayKey]	= array(
					'html'	=> $dayEventElement->render($this->getRange()),
					'length'=> sizeof($eventDayKeys)+1
				);

				foreach($eventDayKeys as $dayKey) {
					$dayEventWeekMap[$index][$dayKey]	= true;
				}

				break; // Done, event positioned
			}

				// No free spot found, add new column
			if( !$found ) {
				$dayEventWeekMap[]	= $mapPattern;
				$index				= sizeof($dayEventWeekMap)-1;
				$firstDayKey		= array_shift($eventDayKeys);
				$dayEventWeekMap[$index][$firstDayKey]	= array(
					'html'	=> $dayEventElement->render($this->getRange()),
					'length'=> !empty($eventDayKeys) ? sizeof($eventDayKeys)+1 : 1
				);

				foreach($eventDayKeys as $dayKey) {
					$dayEventWeekMap[$index][$dayKey]	= true;
				}
			}
		}

		return $dayEventWeekMap;
	}




	/**
	 * Get day event elements to render in calendar
	 *
	 * @return TodoyuCalendarEventElementDayeventWeek[]
	 */
	private function getDayEventsElements() {
		$eventElements		= $this->getEventElements(array(), true);
		$dayEventElements	= array();

		foreach($eventElements as $eventElement) {
			$dayEventElements[]	= new TodoyuCalendarEventElementDayeventWeek($eventElement);
		}

		return $dayEventElements;
	}



	/**
	 * Get week event elements
	 *
	 * @param	Array	$extraFilters
	 * @param	Boolean	$dayEvents
	 * @return	TodoyuCalendarEventElementWeek[]
	 */
	private function getEventElements(array $extraFilters = array(), $dayEvents = null) {
		$events				= $this->getEvents($extraFilters, $dayEvents);
		$dayEventElements	= array();

		foreach($events as $event) {
			$dayEventElements[]	= new TodoyuCalendarEventElementWeek($event, $this);
		}

		return $dayEventElements;
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
	 * Get title for week view
	 *
	 * @return	String
	 */
	protected function getTitle() {
		$dateStart	= $this->getRange()->getStart();
		$dateEnd	= $this->getRange()->getEnd();

			// Vary title depending on start and end of week being within same/different months
		$monthStart	= date('n', $dateStart);
		$monthEnd	= date('n', $dateEnd);
		$rangeType	= $monthStart !== $monthEnd ? 'spanstwomonths' : 'samemonth';

		$labelStart	= TodoyuCalendarTime::format($dateStart, 'calendar.ext.calendartitle.dateformat.week.' . $rangeType . '.part1');
		$labelEnd	= TodoyuCalendarTime::format($dateEnd, 'calendar.ext.calendartitle.dateformat.week.' . $rangeType . '.part2');

		return $labelStart . $labelEnd;
	}

}

?>