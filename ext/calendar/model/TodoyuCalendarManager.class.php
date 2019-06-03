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
 * Calendar Manager
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarManager {

	/**
	 * Get name of calendar mode from mode constant
	 *
	 * @param	Integer	$mode
	 * @return	String
	 */
	public static function getModeName($mode = CALENDAR_MODE_DAY) {
		if( is_string($mode) ) {
			return $mode;
		}

		$modes	= array(
			CALENDAR_MODE_DAY	=> 'day',
			CALENDAR_MODE_WEEK	=> 'week',
			CALENDAR_MODE_MONTH	=> 'month'
		);

		return $modes[$mode];
	}



	/**
	 * Get current selected range
	 *
	 * @param	Integer			$idArea
	 * @return	TodoyuDayRange
	 */
	public static function getCurrentRange($idArea = 0) {
		$tab	= TodoyuCalendarPreferences::getActiveTab();
		$date	= TodoyuCalendarPreferences::getDate($idArea);
		$date	= TodoyuTime::time($date);

		switch($tab) {
			case 'week':
				return new TodoyuCalendarRangeWeek($date);
			case 'month':
				return new TodoyuCalendarRangeMonth($date);
			case 'day':
			default:
				return new TodoyuCalendarRangeDay($date);
		}
	}



	/**
	 * Get holidays in a timespan for the current holiday sets
	 *
	 * @param	TodoyuDayRange	$range
	 * @return	Array
	 */
	public static function getHolidays(TodoyuDayRange $range) {
		$holidaySets	= self::getSelectedHolidaySets();

		if( !empty($holidaySets)  ) {
			$holidays	= TodoyuCalendarHolidayManager::getHolidaysInRange($range, $holidaySets);
			$grouped	= TodoyuCalendarHolidayManager::groupHolidaysByDays($holidays);
		} else {
			$grouped	= array();
		}

		return $grouped;
	}



	/**
	 * Get holidays for a day
	 *
	 * @param	Integer		$date
	 * @return	Array
	 */
	public static function getHolidaysForDay($date) {
		$range		= new TodoyuCalendarRangeDay($date);
		$holidays	= self::getHolidays($range);

		$today		= $holidays[date('Ymd', $date)];

		return is_array($today) ? $today : array();
	}



	/**
	 * Get amount of days between two week-day numbers (0-6)
	 *
	 * @param	Integer 	$startDay			Timestamp of the starting day
	 * @param	Integer 	$endDay				Timestamp of the ending day
	 * @param	Boolean		$insideTheSameWeek	If true, the two days are inside the same week
	 * @return	Integer
	 */
	public static function getAmountOfDaysInbetweenWeekdayNums($startDay, $endDay, $insideTheSameWeek = true) {
		if( $insideTheSameWeek ) {
				// Both days are within the same week
			$amount	= ($endDay == 0 ? 7 : $endDay) - ($startDay == 0 ? 7 : $startDay) + 1;
		} else {
				// Days are not within the same week (spanning over tow or more weeks)
			if( $endDay != '' ) {
				$amount	= $endDay == 0 ? 7 : $endDay;
			} else {
				$amount	= $startDay != '' ? ($startDay == 0 ? 1 : 8 - $startDay) : false;
			}
		}

		return $amount;
	}



	/**
	 * Get amount of weeks visible in calendar depending on given amount of displayed days
	 *
	 * @param	Integer		$amountDays
	 * @return	Integer
	 */
	public static function getVisibleWeeksAmount($amountDays = 35) {
		if( $amountDays === 28 ) {
			$amount	= 4;
		} elseif( $amountDays === 35 ) {
			$amount	= 5;
		} else {
			$amount	= 6;
		}

		return $amount;
	}



	/**
	 * Get date range for month of the timestamp
	 * (include days of the previous and next month because of the calendar layout)
	 *
	 * @param	Integer		$timestamp
	 * @return	Array
	 */
	public static function getMonthDisplayRange($timestamp) {
		$timestamp	= intval($timestamp);
		$monthRange	= TodoyuTime::getMonthRange($timestamp);

		return array(
			'start'	=> TodoyuTime::getWeekStart($monthRange['start']),
			'end'	=> TodoyuTime::getWeekEnd($monthRange['end'])
		);
	}



	/**
	 * Get timestamps shown in calendar month view (days of month before selected, of the selected and of the month after the selected month)
	 *
	 * Explanation:
	 * As the month view of the calendar displays 5 weeks from monday to sunday, there are always some days
	 * out of the months before and after the selected month being displayed, this function calculates their timestamps.
	 *
	 * @param	Integer	$dateStart
	 * @param	Integer	$dateEnd
	 * @return	Array				Timestamps of days to be shown in month view of calendar
	 */
	public static function getDayTimestampsForMonth($dateStart, $dateEnd) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);

		$weekDayStart	= date('w', $dateStart);
		$weekDayEnd		= date('w', $dateEnd);

		$daysLastMonth	= ($weekDayStart + 6) % 7;
		$daysNextMonth	= (7 - $weekDayEnd) % 7;

			// Adjust day-columns to start with sunday / monday (system config)
		$firstDayOfWeek	= TodoyuSysmanagerSystemConfigManager::getFirstDayOfWeek();
		$adjustDaysLastMonth	= $firstDayOfWeek === 0 ? 6 : 0;
		$adjustDaysNextMonth	= $firstDayOfWeek === 0 ? -7 : 0;

		$viewStart	= TodoyuTime::addDays($dateStart, - $daysLastMonth + $adjustDaysLastMonth);
		$viewEnd	= TodoyuTime::addDays($dateEnd, $daysNextMonth + $adjustDaysLastMonth + $adjustDaysNextMonth);

			// Fill array of timestamps of days in range
		$timestamps		= array();
		$currentDate	= $viewStart;
		while($currentDate <= $viewEnd) {
			$timestamps[]	= $currentDate;
			$currentDate	= TodoyuTime::addDays($currentDate, 1);
		}

		return $timestamps;
	}



	/**
	 * Check whether overbooking (more than one event assigned to one person at the same time) is allowed
	 *
	 * @return	Boolean
	 */
	public static function isOverbookingAllowed() {
		$extConf	= TodoyuSysmanagerExtConfManager::getExtConf('calendar');

		return intval($extConf['allowoverbooking']) === 1;
	}



	/**
	 * Get context menu items
	 *
	 * @param	Integer	$timestamp
	 * @param	Array	$items
	 * @return	Array
	 */
	public static function getContextMenuItems($timestamp, array $items) {
		$allowed= array();
		$own	= Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['Area'];

		if( Todoyu::allowed('calendar', 'event:add') ) {
			$allowed[]	= $own['add'];
		}

		return array_merge_recursive($items, $allowed);
	}



	/**
	 * Get calendar tabs configuration array
	 *
	 * @return	Array
	 */
	public static function getCalendarTabsConfig() {
		return TodoyuArray::assure(Todoyu::$CONFIG['EXT']['calendar']['tabs']);
	}



	/**
	 * Get currently selected persons (defined by the panel widget)
	 * If no person is selected, the current person will automatically be selected
	 *
	 * @return	Array
	 */
	public static function getSelectedPersons() {
		$widget	= TodoyuPanelWidgetManager::getPanelWidget('contact', 'StaffSelector');
		/**
		 * @var	TodoyuContactPanelWidgetStaffSelector	$widget
		 */
		return $widget->getPersonIDsOfSelection();
	}



	/**
	 * Get currently selected event types
	 *
	 * @return	Array
	 */
	public static function getSelectedEventTypes() {
		return TodoyuCalendarPanelWidgetEventTypeSelector::getSelectedEventTypes();
	}



	/**
	 * Get currently selected holiday sets
	 *
	 * @return	Array
	 */
	public static function getSelectedHolidaySets() {
		$personIDs		= self::getSelectedPersons();

		if( sizeof($personIDs) === 0 ) {
			return TodoyuArray::getColumn(TodoyuCalendarHolidaySetManager::getAllHolidaySets(), 'id');
		}

		return TodoyuCalendarHolidaySetManager::getPersonHolidaySets($personIDs);
	}



	/**
	 * Extend company address form (hooked into contact's form building)
	 *
	 * @param	TodoyuForm		$form			Address form object
	 * @param	Integer			$index
	 * @param	Array			$params
	 * @return	TodoyuForm
	 */
	public static function hookAddHolidaysetToCompanyAddress(TodoyuForm $form, $index, array $params) {
		if( $params['field'] instanceof TodoyuFormElement ) {
			$parentForm	= $params['field']->getForm()->getName();

			if( $parentForm == 'company' ) {
					// Extend company record form with holiday set selector
				$xmlPath	= 'ext/calendar/config/form/addressholidayset.xml';
				$form->getFieldset('main')->addElementsFromXML($xmlPath);
			}
		}

		return $form;
	}



	/**
	 * Get birthday persons in time range, grouped by day
	 * Subgroups are date keys in format Ymd
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	public static function getBirthdaysByDay($dateStart, $dateEnd) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$range		= new TodoyuDayRange($dateStart, $dateEnd);

		$birthdaysByDay	= array();

		$birthdayPersons= TodoyuContactPersonManager::getBirthdayPersons($range);

		foreach($birthdayPersons as $birthdayPerson) {
			$dateKey	= date('Ymd', $birthdayPerson['date']);

			$birthdaysByDay[$dateKey][]	= $birthdayPerson;
		}

		return $birthdaysByDay;
	}



	/**
	 * Get day keys (format Ymd, e.g. 20111224) for every day in given date range
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	public static function getDayKeys($dateStart, $dateEnd) {
		$keys	= array();
		$start	= TodoyuTime::getDayStart($dateStart);
		$end	= TodoyuTime::getDayEnd($dateEnd);

		for($date = $start; $date <= $end; $date += TodoyuTime::SECONDS_DAY) {
			$keys[]	= date('Ymd', $date);
		}

		return $keys;
	}



	/**
	 * Get height of  starting hour
	 *
	 * @param	Integer	$dateTime	UNIX Timestamp of the starttime or endtime
	 * @return	Integer				Top-Y of starting hour
	 */
	public static function getOffsetByDayTime($dateTime) {
		$dateTime		= intval($dateTime);
		$heightHour		= date('G', $dateTime) * CALENDAR_HEIGHT_HOUR;
		$heightMinute	= date('i', $dateTime) * CALENDAR_HEIGHT_HOUR/60;

		return round($heightHour + $heightMinute, 0);
	}


	/**
	 * Add selected persons to filter
	 *
	 * @param	Array		$filters
	 * @return	Array
	 */
	public static function hookEventFilterPersons(array $filters) {
		$filters['persons']	= self::getSelectedPersons();

		return $filters;
	}



	/**
	 * Add selected event types to filter
	 *
	 * @param	Array		$filters
	 * @return	Array
	 */
	public static function hookEventFilterEventTypes(array $filters) {
		$filters['eventtypes']	= self::getSelectedEventTypes();

		return $filters;
	}



	/**
	 * Add selected holiday sets to filter
	 *
	 * @param	Array		$filters
	 * @return	Array
	 */
	public static function hookEventFilterHolidaySets(array $filters) {
		$filters['holidaysets']	= self::getSelectedHolidaySets();

		return $filters;
	}



	/**
	 * Collect event filters with hooks
	 *
	 * @return	Array
	 */
	public static function getAllEventFilters() {
		return TodoyuHookManager::callHookDataModifier('calendar', 'event.filter', array());
	}



	/**
	 * Get label for weekday by daykey (mo,tu,...)
	 *
	 * @param	String		$dayKey
	 * @param	Boolean		$short
	 * @return	String
	 */
	public static function getWeekDayLabel($dayKey, $short = false) {
		$dateKey	= Todoyu::$CONFIG['EXT']['calendar']['weekDays'][$short?'short':'long'][strtolower($dayKey)];

		return Todoyu::Label('core.date.weekday.' . $dateKey);
	}



	/**
	 * Get key for weekday of a date
	 *
	 * @param	Integer		$date
	 * @return	String		mo,tu,we,etc
	 */
	public static function getWeekDayKey($date) {
		$map 	= array_flip(Todoyu::$CONFIG['EXT']['calendar']['weekDays']['short']);
		$day	= strtolower(date('D', $date));

		return $map[$day];
	}



	/**
	 * Get role IDs which are configured for auto mail notification
	 *
	 * @return	Integer[]
	 */
	public static function getAutoMailRoleIDs() {
		$roleConfig	= TodoyuSysmanagerExtConfManager::getExtConfValue('calendar', 'autosendeventmail');

		return TodoyuArray::intExplode(',', $roleConfig, true, true);
	}
}

?>