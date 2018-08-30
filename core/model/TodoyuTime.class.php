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
 * General time functions
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuTime {

	/**
	 * Seconds per minute
	 *
	 * @var	Integer
	 */
	const SECONDS_MIN	= 60;

	/**
	 * Seconds per hour
	 *
	 * @var	Integer
	 */
	const SECONDS_HOUR	= 3600;

	/**
	 * Seconds per day
	 *
	 * @var	Integer
	 */
	const SECONDS_DAY	= 86400;

	/**
	 * Seconds per week (7 days)
	 *
	 * @var	Integer
	 */
	const SECONDS_WEEK	= 604800;

	/**
	 * Max and min value for dates
	 */
	const MAX = 2000000000;



	/**
	 * Get current time if time is not a number
	 *
	 * @param	Integer		$time
	 * @return	Integer
	 */
	public static function time($time = 0) {
		$time	= (int) $time;
		
		return $time === 0 || $time >= self::MAX || $time <= -self::MAX ? NOW : $time;
	}



	/**
	 *Get timestamp of start of day (00:00:00)
	 *
	 * @param	Integer		$time
	 * @return	Integer
	 * @deprecated
	 * @see		getDayStart
	 */
	public static function getStartOfDay($time = 0) {
		return self::getDayStart($time);
	}



	/**
	 * Get timestamp of start of day (00:00:00)
	 *
	 * @param	Integer		$time
	 * @return	Integer
	 */
	public static function getDayStart($time = 0) {
		$time	= self::time($time);

		$month	= date('n', $time);
		$day	= date('j', $time);
		$year	= date('Y', $time);

		return mktime(0, 0, 0, $month, $day, $year);
	}



	/**
	 * Make timestamp for end (23:59:59) of day
	 *
	 * @param	Integer		$time
	 * @return	Integer
	 * @deprecated
	 * @see		getDayEnd
	 */
	public static function getEndOfDay($time = 0) {
		return self::getDayEnd($time);
	}



	/**
	 * Make timestamp for end (23:59:59) of day
	 *
	 * @param	Integer		$time
	 * @return	Integer
	 */
	public static function getDayEnd($time = 0) {
		$time = self::time($time);

		$month	= date('n', $time);
		$day	= date('j', $time);
		$year	= date('Y', $time);

		return mktime(23, 59, 59, $month, $day, $year);
	}



	/**
	 * Make timestamp for given date's time (at 1.1.1970)
	 *
	 * @param	Integer|Boolean		$time
	 * @return	Integer
	 */
	public static function getTimeOfDay($time = false) {
		$time	= self::time($time);

		$hour	= date('G', $time);
		$minute	= date('i', $time) + 0;
		$second	= date('s', $time) + 0;

		return self::SECONDS_HOUR * $hour + self::SECONDS_MIN * $minute  + $second;
	}



	/**
	 * Get timestamps for the first and the last second of a day
	 *
	 * @param	Integer|Boolean		$timestamp
	 * @return	Array				[start,end]
	 */
	public static function getDayRange($timestamp = false) {
		return array(
			'start'	=> self::getStartOfDay($timestamp),
			'end'	=> self::getEndOfDay($timestamp)
		);
	}



	/**
	 * Get timestamps of start and of week that contains the given timestamp
	 *
	 * @param	Integer	$timestamp
	 * @return	Array
	 */
	public static function getWeekRange($timestamp) {
		$timestamp	= (int) $timestamp;
		$start	= self::getWeekstart($timestamp);

		return array(
			'start'	=> $start,
			'end'	=> $start + self::SECONDS_WEEK - 1
		);
	}



	/**
	 * Get range (start and end timestamp) of month
	 *
	 * @param	Integer		$timestamp
	 * @return	Array
	 */
	public static function getMonthRange($timestamp) {
		return array(
			'start'	=> self::getMonthStart($timestamp),
			'end'	=> self::getMonthEnd($timestamp)
		);
	}



	/**
	 * Get start and end timestamp of every day in the week of the timestamp
	 * 00:00:00
	 *
	 * @param		Integer		$timestamp					Timestamp
	 * @param		Boolean		$forceStartWithMonday
	 * @return		Integer		Timestamp of beginning of week (sunday or monday by system config) the given timestamp belongs to
	 */
	public static function getWeekStart($timestamp = 0, $forceStartWithMonday = false) {
		$timestamp		= self::time($timestamp);

		$year	= date('Y', $timestamp);
		$month	= date('n', $timestamp);
		$day	= date('j', $timestamp);
		$weekDay= date('w', $timestamp);

		if( $forceStartWithMonday || self::isMondayFirstDayOfWeek() ) {
			$dayShift	= ($weekDay + 6) % 7; // Monday
		} else {
			$dayShift	= $weekDay; // Sunday
		}

		return mktime(0, 0, 0, $month, $day-$dayShift, $year);
	}



	/**
	 * Get timestamp for the end of the week (last second in the week) 23:59:59
	 *
	 * @param	Integer		$timestamp
	 * @param	Boolean		$forceStartWeekWithMonday
	 * @return	Integer
	 */
	public static function getWeekEnd($timestamp = 0, $forceStartWeekWithMonday = false) {
		$weekStart	= self::getWeekStart($timestamp, $forceStartWeekWithMonday);

		return self::addDays($weekStart, 7) - 1;
	}



	/**
	 * Get timestamp of first day (at 00:00:00) of month
	 *
	 * @param	Integer	$timestamp
	 * @return	Integer
	 */
	public static function getMonthStart($timestamp = 0) {
		$timestamp	= self::time($timestamp);

		return mktime(0, 0, 0, date('n', $timestamp), 1, date('Y', $timestamp));
	}



	/**
	 * Get timestamp for end of month (last second in the month, 23:59:59)
	 *
	 * @param	Integer		$timestamp
	 * @return	Integer
	 */
	public static function getMonthEnd($timestamp = 0) {
		$timestamp	= self::time($timestamp);
		
		return mktime(0, 0, 0, date('n', $timestamp) + 1, 1, date('Y', $timestamp)) - 1;
	}



	/**
	 * Get timestamp for start of year
	 *
	 * @param	Integer		$timestamp
	 * @return	Integer
	 */
	public static function getYearStart($timestamp = 0) {
		$timestamp	= self::time($timestamp);

		return mktime(0, 0, 0, 1, 1, date('Y', $timestamp));
	}



	/**
	 * Get timestamp for end of year
	 *
	 * @param	Integer		$timestamp
	 * @return	Integer
	 */
	public static function getYearEnd($timestamp = 0) {
		$timestamp	= self::time($timestamp);

		return mktime(0, 0, 0, 1, 1, date('Y', $timestamp) + 1) - 1;
	}



	/**
	 * Get day-number of last day of month of given timestamp
	 *
	 * @param	Integer		$timestamp
	 * @return	Integer
	 */
	public static function getLastDayNumberInMonth($timestamp = 0) {
		$timestamp	= self::time($timestamp);

		$timeLastDay= self::getMonthEnd($timestamp);

		return date('j', $timeLastDay);
	}



	/**
	 * Get weekday of a timestamp. Like date('w'), but starts with monday
	 * With $mondayFirst monday will be 0 and sunday 6
	 *
	 * @param	Integer		$timestamp
	 * @param	Boolean		$mondayFirst
	 * @return	Integer		0 = monday, 6 = sunday
	 * @deprecated
	 * @todo	Should be removed, this is dangerous. Sunday should always be 0
	 */
	public static function getWeekday($timestamp = 0, $mondayFirst = true) {
		$timestamp	= self::time($timestamp);

		$weekday	= date('w', $timestamp);

		return $mondayFirst ? ($weekday + 6) % 7 : $weekday;
	}



	/**
	 * Get time parts (hours, minutes, seconds) from an integer which represents seconds
	 *
	 * @param	Integer		$seconds		Number of seconds
	 * @return	Array		[hours,minutes,seconds]
	 */
	public static function getTimeParts($seconds) {
		$seconds	= TodoyuNumeric::intPositive($seconds);

		$hours		= floor($seconds / self::SECONDS_HOUR);
		$seconds	= $seconds - $hours * self::SECONDS_HOUR;
		$minutes	= floor($seconds / self::SECONDS_MIN);
		$seconds	= $seconds - $minutes * self::SECONDS_MIN;

		return array(
			'hours'		=> $hours,
			'minutes'	=> $minutes,
			'seconds'	=> $seconds
		);
	}



	/**
	 * Get time parts (days, hours, minutes, seconds) from an integer which represents seconds
	 *
	 * @param	Integer		$seconds		Number of seconds
	 * @return	Array		[days,hours,minutes,seconds]
	 */
	public static function getTimePartsDHMS($seconds) {
		$parts	= self::getTimeParts($seconds);

		$parts['days']	= floor($parts['hours'] / self::SECONDS_DAY);
		$parts['hours']	= $parts['hours'] - $parts['days'] * self::SECONDS_DAY;

		return $parts;
	}



	/**
	 * Convert seconds (integer) to a readable format with hours and minutes (03:10 = 3 hours and 10 minutes)
	 *
	 * @param	Integer		$seconds		Seconds
	 * @param	Boolean		$leadingZero	Assure leading zero: 2:30 = 02:30
	 * @return	String		Formatted
	 * @deprecated
	 * @see		formatHours
	 */
	public static function sec2hour($seconds, $leadingZero = true) {
		return self::formatHours($seconds, $leadingZero);
	}



	/**
	 * Convert seconds (integer) to a readable format with hours and minutes (03:10 = 3 hours and 10 minutes)
	 *
	 * @param	Integer		$seconds		Seconds
	 * @param	Boolean		$leadingZero	Assure leading zero: 2:30 = 02:30
	 * @return	String		Formatted
	 */
	public static function formatHours($seconds, $leadingZero = true) {
		$timeParts	= self::getTimeParts($seconds);
		$format		= $leadingZero ? '%02d:%02d' : '%d:%02d';

			// Round up minute, if more than 30 seconds
		if( $timeParts['seconds'] >= 30 ) {
			$timeParts['minutes'] += 1;

				// If the minute round up caused 60 minutes, increment hour
			if( $timeParts['minutes'] == 60 ) {
				$timeParts['minutes'] = 0;
				$timeParts['hours'] += 1;
			}
		}

		return sprintf($format, $timeParts['hours'], $timeParts['minutes']);
	}



	/**
	 * Format time values 23:59 or 23:59:59
	 *
	 * @param	Integer		$seconds
	 * @param	Boolean		$withSeconds
	 * @param	Boolean		$round			Round or cut seconds
	 * @return	String
	 */
	public static function formatTime($seconds, $withSeconds = false, $round = true) {
		$seconds	= (int) $seconds;
		$timeParts	= self::getTimeParts($seconds);

		if( $withSeconds ) {
			$formatted	= sprintf('%02d:%02d:%02d', $timeParts['hours'], $timeParts['minutes'], $timeParts['seconds']);
		} else {
			if( $round && $timeParts['seconds'] >= 30 ) {
				$timeParts['minutes'] += 1;

				if( $timeParts['minutes'] == 60 ) {
					$timeParts['hours'] += 1;
					$timeParts['minutes'] = 0;
				}
			}
			$formatted	= sprintf('%02d:%02d', $timeParts['hours'], $timeParts['minutes']);
		}

		return $formatted;
	}



	/**
	 * Format a timestamp with one of todoyu's default date formats
	 *
	 * @see		core/config/dateformat.xml
	 * @param	Integer			$timestamp
	 * @param	String|Null		$formatName
	 * @param	String|Boolean	$format				Ignore formatName and use directly this format
	 * @return	String		Formatted date
	 */
	public static function format($timestamp, $formatName = 'datetime', $format = false) {
		$timestamp	= (int) $timestamp;

		if( $timestamp === 0 ) {
			return '-';
		}

			// Format timestamp with pattern
		if( $format ) {
			$format	= self::cleanFormatForWindows($format);
		} else {
			$format	= self::getFormat($formatName);
		}
		$formattedDate	= strftime($format, $timestamp);

			// Convert to utf-8 if not already
		return TodoyuString::getAsUtf8($formattedDate);
	}



	/**
	 * Get given duration formatted in most suiting format
	 *
	 * @param	Integer		$duration
	 * @return	String
	 */
	public static function formatDuration($duration) {
		$duration	= intval($duration);

		if( $duration === 0 ) {
			return '-';
		}

		if( $duration > self::SECONDS_DAY-self::SECONDS_HOUR ) { // days
			$value	= round($duration / self::SECONDS_DAY, 0);
			$unit	= $value == 1 ? 'day' : 'days';
		} elseif( $duration > self::SECONDS_HOUR ) { // hours
			if( $duration % self::SECONDS_HOUR === 0 ) {
				$value = $duration / self::SECONDS_HOUR;
			} else {
				$value = self::formatHours($duration, false);
			}
			$unit	= 'time.hours';
		} elseif( $duration === self::SECONDS_HOUR ) { // 1 hour
			$value	= 1;
			$unit	= 'time.hour';
		} elseif( $duration > self::SECONDS_MIN ) { // minutes
			$value	= round($duration / TodoyuTime::SECONDS_MIN, 0);
			$unit	= 'time.minutes';
		} elseif( $duration === self::SECONDS_MIN ) { // 1 minute
			$value	= 1;
			$unit	= 'time.minute';
		} elseif( $duration === 1 ) { // 1 second
			$value	= $duration;
			$unit	= 'time.second';
		} else { // seconds
			$value	= $duration;
			$unit	= 'time.seconds';
		}

		return $value . ' ' . Todoyu::Label('core.date.' . $unit);
	}



	/**
	 * Have given timespan formatted in most suiting format
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @param	Boolean		$withMultidayTime
	 * @return	String
	 */
	public static function formatRange($dateStart, $dateEnd, $withMultidayTime = false) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);

		if( $dateStart === $dateEnd ) { // Start and end identical
			$formatted = self::format($dateStart, 'DshortD2MshortY2') . ', ' . self::format($dateStart, 'time');
		} elseif( self::getDayStart($dateStart) === self::getDayStart($dateEnd) ) { // Start and end at same day
			$formatted = self::format($dateStart, 'DshortD2MshortY2') . ', ' . self::format($dateStart, 'time') . ' - ' . self::format($dateEnd, 'time');
		} elseif( $withMultidayTime ) {
			$formatted = self::format($dateStart, 'DshortD2MshortY2') . ', ' . self::format($dateStart, 'time') . ' - ' . self::format($dateEnd, 'DshortD2MshortY2'). ', ' . self::format($dateEnd, 'time');
		} else { // Different days
			$formatted = self::format($dateStart, 'DshortD2MshortY2') . ' - ' . self::format($dateEnd, 'DshortD2MshortY2');
		}

		return $formatted;
	}



	/**
	 * Format an SQL datetime string with one of todoyu's default date formats
	 *
	 * @param	String	$sqlDate
	 * @param	String	$format
	 * @return	String
	 */
	public static function formatSqlDate($sqlDate, $format = 'date') {
		$timestamp	= self::parseSqlDate($sqlDate);

		return self::format($timestamp, $format);
	}



	/**
	 * Parse SQL date to timestamp
	 *
	 * @param	String		$sqlDate
	 * @return	Integer
	 */
	public static function parseSqlDate($sqlDate) {
		$pattern	= '%Y-%m-%d';

		return self::parseDateTime($sqlDate, $pattern);
	}



	/**
	 * Get format config string
	 *
	 * @see		core/config/dateformat.xml
	 * @param	String		$formatName
	 * @return	String
	 */
	public static function getFormat($formatName) {
		$localeKey	= 'core.dateformat.' . $formatName;
		$format		= Todoyu::Label($localeKey);

			// Replace %e with %d on windows
		return self::cleanFormatForWindows($format);
	}



	/**
	 * Clean date format for windows
	 * Replace %e with %d
	 *
	 * @param	String		$format
	 * @return	String
	 */
	public static function cleanFormatForWindows($format) {
		if( strpos($format, '%e') !== false ) {
			if( TodoyuServer::isWindows() ) {
				$format = str_replace('%e', '%#d', $format);
				$format = str_replace('%V', '%W', $format);
			}
		}

		return $format;
	}



	/**
	 * Parse date string with check if its a dateString or a dateTimeString
	 *
	 * @param	String	$dateString
	 * @return	Integer
	 */
	public static function parseDateString($dateString) {
		$time = self::parseDateTime($dateString);

			// If parseDateTime did not work, try parseDate
		if( $time === 0 ) {
			$time = self::parseDate($dateString);
		}

		if( $time === 0 ) {
			$time	= strtotime($dateString);
		}

		return $time;
	}



	/**
	 * Parse date string (formatted according to current locale) to UNIX timestamp
	 *
	 * @param	String		$dateString
	 * @return	Integer		UNIX timestamp
	 */
	public static function parseDate($dateString) {
		$dateString	= trim($dateString);
		$time		= 0;

			// Standard date from MySQL date type
		if( self::isStandardDate($dateString) ) {
			$format	= '%Y-%m-%d';
		} else {
			$format	= self::getFormat('date');
		}

		$dateParts	= strptime($dateString, $format);

		if( $dateParts !== false ) {
				// Fix for built in function (windows function works correctly)
			if( PHP_OS !== 'WINNT' && $dateParts !== false ) {
				$dateParts['tm_year']	= $dateParts['tm_year'] + 1900;
				$dateParts['tm_mon']	= $dateParts['tm_mon'] + 1;
			}

			$time = mktime(0, 0, 0, $dateParts['tm_mon'], $dateParts['tm_mday'], $dateParts['tm_year']);
		}

		return $time;
	}



	/**
	 * Parse date time string (get UNIX timestamp)
	 *
	 * @param	String		$dateTimeString
	 * @param	String		$format
	 * @return	Integer
	 */
	public static function parseDateTime($dateTimeString, $format = '') {
		$format		= $format == '' ? self::getFormat('datetime') : $format;
		$dateParts	= strptime($dateTimeString, $format);

		if( !$dateParts ) {
			return 0;
		}

		if( isset($dateParts['timestamp']) ) {
			return $dateParts['timestamp'];
		} else {
			$dateParts['tm_year']	+= 1900;
			$dateParts['tm_mon']	+= 1;

			return mktime(
				$dateParts['tm_hour'],
				$dateParts['tm_min'],
				$dateParts['tm_sec'],
				$dateParts['tm_mon'],
				$dateParts['tm_mday'],
				$dateParts['tm_year']
			);
		}
	}



	/**
	 * Parse time string to UNIX timestamp (time format is based on the format time or timesec)
	 *
	 * @param	String		$timeString		Time string: 23:59 or 23:59:59 (function auto-detects seconds part)
	 * @return	Integer		Seconds
	 */
	public static function parseTime($timeString) {
		$colons		= substr_count($timeString, ':');
		$format		= $colons === 2 ? self::getFormat('timesec') : self::getFormat('time');
		$timeParts	= strptime($timeString, $format);

		$hours	= (int) $timeParts['tm_hour'];
		$minutes= (int) $timeParts['tm_min'];
		$seconds= (int) $timeParts['tm_sec'];

		return $hours * self::SECONDS_HOUR + $minutes * self::SECONDS_MIN + $seconds;
	}



	/**
	 * Parse duration to seconds (format: 32:50)
	 *
	 * @param	String		$timeString
	 * @return	Integer
	 */
	public static function parseDuration($timeString) {
		$parts	= explode(':', $timeString);

		return ((int) $parts[0]) * self::SECONDS_HOUR + ((int) $parts[1]) * self::SECONDS_MIN;
	}



	/**
	 * Check whether date string has standard date format 2011-08-05 (year-month-day)
	 *
	 * @param	String		$dateString
	 * @return	Boolean
	 */
	public static function isStandardDate($dateString) {
		return preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($dateString)) === 1;
	}



	/**
	 * Round minutes by given steps
	 *
	 * @param	Integer		$timestamp
	 * @param	Integer		$steps
	 * @return	Integer		Rounded time
	 */
	public static function getRoundedTime($timestamp = 0, $steps = 5) {
		$timestamp	= (int) $timestamp;
		$factor		= (int) (self::SECONDS_MIN / $steps);

		if( $timestamp === 0 ) {
			$timestamp = NOW;
		}

		$currentMinutes	= (int) date('i', $timestamp);
		$roundedMinutes	= (int) (round(($currentMinutes * $factor) / self::SECONDS_MIN, 0) * $steps);
		$currentSeconds	= (int) date('s', $timestamp);
		$newTime		= $timestamp + ($roundedMinutes - $currentMinutes) * self::SECONDS_MIN - $currentSeconds;

		return $newTime;
	}



	/**
	 * Check whether two time ranges overlap.
	 *
	 * Border touching example:
	 * A (14:00-15:00), B (15:00-16:00)
	 * Only when border touching is true, this ranges are recognized as overlapping
	 *
	 * @param	Integer		$dateStart1
	 * @param	Integer		$dateEnd1
	 * @param	Integer		$dateStart2
	 * @param	Integer		$dateEnd2
	 * @param	Boolean		$allowBorderTouching		False: ranges have to overlap, True: Count border touching as overlapping
	 * @return	Boolean
	 */
	public static function rangeOverlaps($dateStart1, $dateEnd1, $dateStart2, $dateEnd2, $allowBorderTouching = false) {
		$dateStart1	= (int) $dateStart1;
		$dateEnd1	= (int) $dateEnd1;
		$dateStart2	= (int) $dateStart2;
		$dateEnd2	= (int) $dateEnd2;

		if( $allowBorderTouching ) {
			if( $dateEnd2 < $dateStart1 || $dateStart2 > $dateEnd1 ) {
				return false;
			}
		} else {
			if( $dateEnd2 <= $dateStart1 || $dateStart2 >= $dateEnd1 ) {
				return false;
			}
		}


		return true;
	}



	/**
	 * Round-UP given time in seconds to given rounding minute
	 *
	 * @param	Integer		$timestamp
	 * @param	Integer		$roundingMinute		Round to number of minutes (10min, 15min, etc)
	 * @return	Integer		Rounded duration in seconds
	 */
	public static function roundUpTime($timestamp, $roundingMinute = 1) {
		$timestamp			= intval($timestamp);
		$roundingMinute		= max(1, $roundingMinute);
		$roundingSeconds	= $roundingMinute * self::SECONDS_MIN;

		return (int) (ceil($timestamp / $roundingSeconds) * $roundingSeconds);
	}



	/**
	 * Add given amount of days to given date
	 *
	 * @param	Integer		$timestamp
	 * @param	Integer		$amountDays
	 * @return	Integer
	 */
	public static function addDays($timestamp, $amountDays) {
		$timestamp	= (int) $timestamp;
		$amountDays	= (int) $amountDays;

		$date	= getdate($timestamp);

		return mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'] + $amountDays, $date['year']);
	}



	/**
	 * Check whether monday is the first day of the week
	 *
	 * @return	Boolean
	 */
	public static function isMondayFirstDayOfWeek() {
		return TodoyuSysmanagerSystemConfigManager::getFirstDayOfWeek() === 1;
	}



	/**
	 * Get indexes of weekend days (compare to date('w')
	 * Fixed to 6=Saturday and 0=Sunday
	 *
	 * @return	Array
	 */
	public static function getWeekEndDayIndexes() {
		return  array(6, 0);
	}



	/**
	 * Check whether a date is during the weekend
	 *
	 * @param	Integer		$date
	 * @return	Boolean
	 */
	public static function isWeekendDate($date) {
		$weekDay		= date('w', $date);
		$weekendDays	= self::getWeekEndDayIndexes();

		return in_array($weekDay, $weekendDays);
	}

}

?>