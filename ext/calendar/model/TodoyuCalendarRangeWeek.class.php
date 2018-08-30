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
 * Date range for a week
 * Support exclusion of the weekend (definition of weekend: last two days in the week Sa,So or Fr,Sa)
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarRangeWeek extends TodoyuDayRange {

	/**
	 * Initialize
	 *
	 * @param	Integer		$date		Date of a day in this week
	 * @param	Boolean		$displayWeekend	Include weekend in the range
	 */
	public function __construct($date, $displayWeekend = true) {
		$date	= TodoyuTime::time($date);

		$this->setStart($date, $displayWeekend);
		$this->setEnd($date, $displayWeekend);
	}



	/**
	 * Set start date
	 * Will get adjusted to the week start
	 *
	 * @param	Integer		$date
	 * @param	Boolean		$includeWeekend
	 */
	public function setStart($date, $includeWeekend = true) {
		if( $includeWeekend ) {
				// Get 1st day  of week (sunday or monday depending on system config of 1st day of week)
			$date	= TodoyuTime::getWeekStart($date);
		} else {
				// Displayed range is MON-FRI
			$date	= TodoyuTime::getWeekStart($date, true);
		}

		parent::setStart($date);
	}



	/**
	 * Set end date
	 * Will get adjusted to the week end or the end of the working week (without weekend)
	 *
	 * @param	Integer		$date
	 * @param	Boolean		$includeWeekend
	 */
	public function setEnd($date, $includeWeekend = true) {
		if( $includeWeekend ) {
				// Get end of of week (saturday or sunday depending on system config of 1st day of week)
			$date	= TodoyuTime::getWeekEnd($date);
		} else {
				// Get friay of week
			$date	= TodoyuTime::getWeekEnd($date, true);
			$date	= TodoyuTime::addDays($date, -2);
		}

		parent::setEnd($date);
	}

}

?>