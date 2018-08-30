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
 * Date range element
 *
 * The two constructor parameters are start and end of the range
 * start	end		result
 * 0		0		full range
 * 0		x		everything before x
 * x		0		everything from x
 * x		y		everything between x and y
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuDateRange {

	/**
	 * Start date
	 *
	 * @var	Integer
	 */
	protected $dateStart;

	/**
	 * End date
	 *
	 * @var	Integer
	 */
	protected $dateEnd;

	/**
	 * Minimal date for maximal range
	 *
	 * @var	Integer
	 */
	protected $dateMin = -2000000000;

	/**
	 * Maximal date for maximal range
	 *
	 * @var	Integer
	 */
	protected $dateMax = 2000000000;
	


	/**
	 * Initialize with range
	 * 
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 */
	public function __construct($dateStart = 0, $dateEnd = 0) {
		$dateStart	= (int) $dateStart;
		$dateEnd	= (int) $dateEnd;

		if( $dateStart === 0 ) {
			$dateStart = $this->dateMin;
		}

		if( $dateEnd === 0 ) {
			$dateEnd = $this->dateMax;
		}

		$this->setRange($dateStart, $dateEnd);
	}



	/**
	 * Get range ID, based on start and end of the range
	 *
	 * @return	String
	 */
	public function getID() {
		return date('YmdHis', $this->getStart()) . date('YmdHis', $this->getEnd());
	}



	/**
	 * Set to maximum ranges
	 * 1910-2037 should be enough
	 *
	 */
	public function setMaxRanges() {
		$this->setStart(PHP_INT_MIN);
		$this->setEnd(PHP_INT_MAX);
	}



	/**
	 * Get start date
	 *
	 * @return	Integer
	 */
	public function getStart() {
		return $this->dateStart;
	}



	/**
	 * Get end date
	 *
	 * @return	Integer
	 */
	public function getEnd() {
		return $this->dateEnd;
	}



	/**
	 * Set start date
	 *
	 * @param	Integer		$date
	 */
	public function setStart($date) {
		$this->dateStart = (int) $date;
	}



	/**
	 * Set end date
	 *
	 * @param	Integer		$date
	 */
	public function setEnd($date) {
		$this->dateEnd = (int) $date;
	}



	/**
	 * Set range start by date
	 *
	 * @param	Integer		$year
	 * @param	Integer		$month
	 * @param	Integer		$day
	 * @param	Integer		$hour
	 * @param	Integer		$minute
	 * @param	Integer		$second
	 */
	public function setDateStart($year, $month, $day, $hour = 0, $minute = 0, $second = 0) {
		$this->setStart(mktime($hour, $minute, $second, $month, $day, $year));
	}

	

	/**
	 * Set range end by date
	 *
	 * @param	Integer		$year
	 * @param	Integer		$month
	 * @param	Integer		$day
	 * @param	Integer		$hour
	 * @param	Integer		$minute
	 * @param	Integer		$second
	 */
	public function setDateEnd($year, $month, $day, $hour = 0, $minute = 0, $second = 0) {
		$this->setEnd(mktime($hour, $minute, $second, $month, $day, $year));
	}



	/**
	 * Set range dates (start/end)
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 */
	public function setRange($dateStart, $dateEnd) {
		$this->setStart($dateStart);
		$this->setEnd($dateEnd);
	}



	/**
	 * Force minimal length of range in seconds
	 *
	 * @param	Integer		$seconds
	 */
	public function setMinLength($seconds) {
		$seconds	= intval($seconds);

		if( $this->getDuration() < $seconds ) {
			$this->setEnd($this->getStart()+$seconds);
		}
	}



	/**
	 * Check whether this range ends before the given date
	 *
	 * @param	Integer		$date
	 * @return	Boolean
	 */
	public function endsBefore($date) {
		$date	= (int) $date;

		return $this->dateEnd < $date;
	}



	/**
	 * Check whether this range starts before the given date
	 *
	 * @param	Integer		$date
	 * @param	Boolean		$allowSame
	 * @return	Boolean
	 */
	public function startsBefore($date, $allowSame = false) {
		$date	= (int) $date;

		if( $allowSame ) {
			return $this->dateStart <= $date;
		} else {
			return $this->dateStart < $date;
		}
	}



	/**
	 * Check whether this range ends after the given date
	 *
	 * @param	Integer		$date
	 * @param	Boolean		$allowSame
	 * @return	Boolean
	 */
	public function endsAfter($date, $allowSame = false) {
		$date	= (int) $date;

		if( $allowSame ) {
			return $this->dateEnd >= $date;
		} else {
			return $this->dateEnd > $date;
		}
	}



	/**
	 * Check whether this range starts after the given date
	 *
	 * @param	Integer		$date
	 * @return	Boolean
	 */
	public function startsAfter($date) {
		$date	= (int) $date;

		return $this->dateStart > $date;
	}



	/**
	 * Check whether the range is active at the given date
	 * If no date given, use current date
	 *
	 * @param	Integer		$date
	 * @return	Boolean
	 */
	public function isActive($date = 0) {
		$date	= TodoyuTime::time($date);

		return $this->isInRange($date);
	}



	/**
	 * Check whether this range is (partly) in the period between the given start and end date
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @param	Boolean		$partly			It's enough when the range and the period just overlap at some date
	 * @param	Boolean		$allowLimits	Allow the period to start or end exactly at the start or end date
	 * @return	Boolean
	 */
	public function isPeriodInRange($dateStart, $dateEnd, $partly = false, $allowLimits = false) {
		$dateStart	= (int) $dateStart;
		$dateEnd	= (int) $dateEnd;

		if( $partly ) {
			return $this->isInRange($dateStart, $allowLimits) || $this->isInRange($dateEnd, $allowLimits);
		} else {
			return $this->startsBefore($dateStart, $allowLimits) && $this->endsAfter($dateEnd, $allowLimits);
		}
	}



	/**
	 * Check whether the given date is inside of this range
	 *
	 * @param	Integer		$date
	 * @param	Boolean		$allowLimits		Allow the date to be at the start or end date
	 * @return	Boolean
	 */
	public function isInRange($date, $allowLimits = true) {
		$date = (int) $date;
		
		return $this->startsBefore($date, $allowLimits) && $this->endsAfter($date, $allowLimits);
	}



	/**
	 * Check whether the range containes another range completely
	 *
	 * @param	TodoyuDateRange		$range
	 * @return	Boolean
	 */
	public function contains(TodoyuDateRange $range) {
		return $this->startsBefore($range->getStart(), true) && $this->endsAfter($range->getEnd(), true);
	}



	/**
	 * Check whether a range overlaps another comment
	 *
	 * @param	TodoyuDateRange		$range
	 * @param	Boolean				$allowBorderTouching		See TodoyuTime::rangeOverlaps() for details
	 * @return	Boolean
	 */
	public function isOverlapping(TodoyuDateRange $range, $allowBorderTouching = false) {
		return TodoyuTime::rangeOverlaps($this->getStart(), $this->getEnd(), $range->getStart(), $range->getEnd(), $allowBorderTouching);
	}



	/**
	 * Get duration of this range in seconds
	 *
	 * @return	Integer
	 */
	public function getDuration() {
		return intval($this->dateEnd - $this->dateStart);
	}



	/**
	 * Set limit for end date
	 * If end date exceeds the limit, it will be adjusted
	 * The limit does not affect later operations
	 *
	 * @param	Integer		$dateEnd
	 */
	public function setEndLimit($dateEnd) {
		$dateEnd	= (int) $dateEnd;

		if( $this->getEnd() > $dateEnd ) {
			$this->setEnd($dateEnd);
		}
	}



	/**
	 * Set limit for start date
	 * If start date exceeds the limit, it will be adjusted
	 * The limit does not affect later operations
	 *
	 * @param	Integer		$dateStart
	 */
	public function setStartLimit($dateStart) {
		$dateStart	= (int) $dateStart;

		if( $this->dateStart < $dateStart ) {
			$this->setStart($dateStart);
		}
	}



	/**
	 * Set limits based on given range
	 *
	 * @param	TodoyuDateRange		$range
	 */
	public function setRangeLimits(TodoyuDateRange $range) {
		$this->setStartLimit($range->getStart());
		$this->setEndLimit($range->getEnd());
	}



	/**
	 * Check whether dateRange spans one full year (01.01. to 12.31.)
	 *
	 * @return	Boolean
	 */
	public function isFullYearRange() {
		return $this->isInOneYear() && date('m-d', $this->getStart()) === '01-01' && date('m-d', $this->getEnd()) === '12-31';
	}



	/**
	 * Check whether dateRange spans one full month
	 *
	 * @return	Boolean
	 */
	public function isFullMonthRange() {
		return $this->isInOneMonth() && $this->isStartStartOfMonth() && $this->isEndEndOfMonth();
	}



	/**
	 * Check whether dateRange span lays within one (start/end the same) year
	 *
	 * @return	Boolean
	 */
	public function isInOneYear() {
		return date('Y', $this->getStart()) === date('Y', $this->getEnd());
	}



	/**
	 * Check whether dateRange span lays within one (start/ end the same) month
	 *
	 * @return	Boolean
	 */
	public function isInOneMonth() {
		return date('Y-m', $this->getStart()) === date('Y-m', $this->getEnd());
	}



	/**
	 * Check whether the range is inside of a day
	 *
	 * @return	Boolean
	 */
	public function isInOneDay() {
		return date('Y-m-d', $this->getStart()) === date('Y-m-d', $this->getEnd());
	}



	/**
	 * Check whether dateRange starts at 1st day of month
	 *
	 * @return	Boolean
	 */
	public function isStartStartOfMonth() {
		return date('d', $this->getStart()) === '01';
	}



	/**
	 * Check whether dateRange ends on last day of month
	 *
	 * @return	Boolean
	 */
	public function isEndEndOfMonth() {
		$lastDay	= date('t', $this->getEnd());

		return date('d', $this->getEnd()) === $lastDay;
	}



	/**
	 * Get dates as array
	 * 
	 * @return	Array	[start,end]
	 */
	public function getDates() {
		return array(
			'start'	=> $this->getStart(),
			'end'	=> $this->getEnd()
		);
	}



	/**
	 * Get label for range
	 * Format depends on start, end times
	 * - Full year:
	 *
	 * @return	String
	 */
	public function getLabel() {
			// Full year range: 2011
		if( $this->isFullYearRange() ) {
			return date('Y', $this->getStart());
		}

			// Full month range: January 2011
		if( $this->isFullMonthRange() ) {
			return TodoyuTime::format($this->getStart(), 'MlongY4');
		}

			// Starts on first of the month: January 2011 / January 13 2011
		if( $this->isStartStartOfMonth() ) {
			$start	= TodoyuTime::format($this->getStart(), 'MlongY4');
		} else {
			$start	= TodoyuTime::format($this->getStart(), 'D2MlongY4');
		}

			// Ends on last of the month. March / March 13
		if( $this->isEndEndOfMonth() ) {
			$end	= TodoyuTime::format($this->getEnd(), 'MlongY4');
		} else {
			$end	= TodoyuTime::format($this->getEnd(), 'D2MlongY4');
		}

		return $start . ' - ' . $end;
	}



	/**
	 * Get an exact label which includes the time
	 * One day: 15. August 2012, 15:00 - 16:00
	 * Multi day: 15. August 2012 15:00 - 16. August 2012 18:00
	 *
	 * @return	String
	 */
	public function getLabelWithTime() {
		if( $this->isInOneDay() ) {
			$label	= TodoyuTime::format($this->getStart(), 'D2MlongY4');
			$label .= ', ' . date('H:i', $this->getStart()) . ' - ' . date('H:i', $this->getEnd());
		} else {
			$label	= TodoyuTime::format($this->getStart(), 'D2MlongY4');
			$label .= ' ' . date('H:i', $this->getStart());
			$label .= ' - ';
			$label .= TodoyuTime::format($this->getEnd(), 'D2MlongY4');
			$label .= ' ' . date('H:i', $this->getEnd());
		}

		return $label;
	}



	/**
	 * Get timestamps for days in range
	 * The timestamps always have the time 00:00:00 for all days inside the range
	 *
	 * @param	String|Boolean	$format		Format timestamp with date and given format (false = integer)
	 * @return	Array
	 */
	public function getDayTimestamps($format = false) {
		$dateStart	= $this->getStart();
		$dateEnd	= $this->getEnd();
		$day		= date('j', $dateStart);
		$month		= date('n', $dateStart);
		$year		= date('Y', $dateStart);
		$count		= 0;
		$date		= $dateStart;
		$days		= array();

			// Loop while end date not reached
		while( $date <= $dateEnd ) {
			$date	= mktime(0, 0, 0, $month, $day + $count, $year);
			$days[]	= $date;
			$count++;
		}

			// Remove last date. It is after the end date
		if( sizeof($days) > 1 ) {
			array_pop($days);
		}

			// Format?
		if( $format ) {
			foreach($days as $index => $timestamp) {
				$days[$index] = date($format, $timestamp);
			}
		}

		return $days;
	}



	/**
	 * Get array with a key for every day in the range
	 * By default, it's the timestamp, but when format is a string, it will be formatted with date()
	 * Value is the value which will be set for every item
	 *
	 * @param	Boolean		$format
	 * @param	Mixed		$value
	 * @return	Array
	 * @deprecated
	 * @see	getDayMap
	 */
	public function getDayTimestampsMap($format = false, $value = 0) {
		return $this->getDayMap($format, $value);
	}



	/**
	 * Get array with a key for every day in the range
	 * By default, it's the timestamp, but when format is a string, it will be formatted with date()
	 * Value is the value which will be set for every item
	 *
	 * @param	Boolean|String	$keyFormat
	 * @param	Mixed			$defaultValue
	 * @return	Array
	 */
	public function getDayMap($keyFormat = false, $defaultValue = 0) {
		$dayTimestamps	= $this->getDayTimestamps($keyFormat);

		return TodoyuArray::createMap($dayTimestamps, $defaultValue);
	}



	/**
	 * Get a new date range for overlapping with the $range
	 *
	 * @param	TodoyuDateRange		$range
	 * @param	Boolean				$allowBorderTouching
	 * @return	TodoyuDateRange|Boolean
	 */
	public function getOverlappingRange(TodoyuDateRange $range, $allowBorderTouching = false) {
		if( !$this->isOverlapping($range, $allowBorderTouching) ) {
			return false;
		}

		$start	= max($this->getStart(), $range->getStart());
		$end	= min($this->getEnd(), $range->getEnd());

		return new TodoyuDateRange($start, $end);
	}



	/**
	 * Get amount of intersected days
	 *
	 * @return	Integer
	 */
	public function getAmountOfDays() {
		$dayTimestamps	= $this->getDayTimestamps();
		$amount			= sizeof($dayTimestamps);

		return $amount > 0 ? $amount : 1;
	}

	

	/**
	 * Get debug string of range
	 *
	 * @return	String
	 */
	public function __toString() {
		return date('r', $this->getStart()) . ' - ' . date('r', $this->getEnd());
	}

}

?>