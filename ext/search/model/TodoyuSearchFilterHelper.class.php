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
 * Various filter helper functions
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterHelper {

	/**
	 * Get query parts for date field filtering
	 *
	 * @param	String		$tables
	 * @param	String		$field
	 * @param	Integer		$timestamp
	 * @param	Boolean		$negate
	 * @return	Array|Boolean			Query parts array / false if no date timestamp given (or 1.1.1970 00:00)
	 */
	public static function getDateFilterQueryparts($tables, $field, $timestamp, $negate = false) {
		$queryParts	= false;

		if( $timestamp !== 0 ) {
			$info	= self::getTimeAndLogicForDate($timestamp, $negate);

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $field . ' ' . $info['logic'] . ' ' . $info['timestamp']
			);
		}

		return $queryParts;
	}



	/**
	 * Return timestamp and conjunction logic for date-input queries
	 *
	 * @param	Integer		$timestamp
	 * @param	Boolean		$negate
	 * @return	Array		[timestamp,logic]
	 */
	public static function getTimeAndLogicForDate($timestamp, $negate = false) {
		$timestamp	= intval($timestamp);

		if( $negate ) {
			$info	= array(
				'timestamp'	=> TodoyuTime::getDayStart($timestamp),
				'logic'		=> '>='
			);
		} else {
			$info	= array(
				'timestamp'	=> TodoyuTime::getDayEnd($timestamp),
				'logic'		=> '<='
			);
		}

		return $info;
	}



	/**
	 * Get config for dynamic date options
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getDynamicDateOptions() {
		return array(
			array(
				'label' => Todoyu::Label('core.date.dyndate.today'),
				'value'	=> 'today'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.tomorrow'),
				'value'	=> 'tomorrow'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.dayaftertomorrow'),
				'value'	=> 'dayaftertomorrow'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.yesterday'),
				'value'	=> 'yesterday'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.daybeforeyesterday'),
				'value'	=> 'daybeforeyesterday'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.currentweek'),
				'value'	=> 'currentweek'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.nextweek'),
				'value'	=> 'nextweek'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.lastweek'),
				'value'	=> 'lastweek'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.currentmonth'),
				'value'	=> 'currentmonth'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.nextyear'),
				'value'	=> 'nextyear'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.currentyear'),
				'value'	=> 'currentyear'
			),
			array(
				'label' => Todoyu::Label('core.date.dyndate.lastyear'),
				'value'	=> 'lastyear'
			)

		);
	}



	/**
	 * Calculate timestamp from dynamic date key ("today", "tomorrow", ...). Optionally suiting negated comparisom.
	 *
	 * @param	String		$dateRangeKey
	 * @param	Boolean		$negate
	 * @return	Integer
	 */
	public static function getDynamicDateTimestamp($dateRangeKey, $negate = false) {
		$todayStart	= TodoyuTime::getDayStart();
		$todayEnd	= TodoyuTime::getDayEnd();
		$date		= $negate ? $todayStart : $todayEnd;

		switch( $dateRangeKey ) {
			case 'tomorrow':
				$date += TodoyuTime::SECONDS_DAY;
				break;

			case 'dayaftertomorrow':
				$date += TodoyuTime::SECONDS_DAY * 2;
				break;

			case 'yesterday':
				$date -= TodoyuTime::SECONDS_DAY;
				break;

			case 'daybeforeyesterday':
				$date -= TodoyuTime::SECONDS_DAY * 2;
				break;

			case 'currentweek':
				$weekRange	= TodoyuTime::getWeekRange(NOW);
				$date		= $negate ? $weekRange['start'] : $weekRange['end'] ;
				break;

			case 'nextweek':
				$weekRange	= TodoyuTime::getWeekRange(NOW + TodoyuTime::SECONDS_WEEK);
				$date		= $negate ? $weekRange['start'] : $weekRange['end'] ;
				break;

			case 'lastweek':
				$weekRange	= TodoyuTime::getWeekRange(NOW - TodoyuTime::SECONDS_WEEK);
				$date		= $negate ? $weekRange['start'] : $weekRange['end'] ;
				break;

			case 'currentmonth':
				$monthRange	= TodoyuTime::getMonthRange(NOW);
				$date		= $negate ? $monthRange['start'] : $monthRange['end'] ;
				break;

			case 'currentyear':
				$date		= $negate ? TodoyuTime::getYearStart(NOW) : TodoyuTime::getYearEnd(NOW) ;
				break;

			case 'nextyear':
				$nextYear	= NOW + TodoyuTime::SECONDS_WEEK * 52;
				$date		= $negate ? TodoyuTime::getYearStart($nextYear) : TodoyuTime::getYearEnd($nextYear) ;
				break;

			case 'lastyear':
				$oneYearAgo	= NOW - TodoyuTime::SECONDS_WEEK * 52;
				$date		= $negate ? TodoyuTime::getYearStart($oneYearAgo) : TodoyuTime::getYearEnd($oneYearAgo) ;
				break;

			case 'todoay':
			default:
				break;
		}

		return $date;
	}



	/**
	 * Prepare query parts for date based filter widget
	 *
	 * @param	String			$table
	 * @param	String			$field
	 * @param	String			$date		Formatted (according to current locale) date string
	 * @param	Boolean			$negate
	 * @return	Array|Boolean
	 */
	public static function makeFilter_date($table, $field, $date, $negate = false) {
		$tables	= array($table);
		$field	= $table . '.' . $field;

		$timestamp	= TodoyuTime::parseDate($date);

		return TodoyuSearchFilterHelper::getDateFilterQueryparts($tables, $field, $timestamp, $negate);
	}

}

?>