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
 * Holiday manager
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarHolidayManager {

	/**
	 * @var	String		Default database table
	 */
	const TABLE		= 'ext_calendar_holiday';


	/**
	 * Get holiday
	 *
	 * @param	Integer			$idHoliday
	 * @return	TodoyuCalendarHoliday
	 */
	public static function getHoliday($idHoliday) {
		$idHoliday	= intval($idHoliday);

		return TodoyuRecordManager::getRecord('TodoyuCalendarHoliday', $idHoliday);
	}



	/**
	 * All all holidays
	 *
	 * @return	Array
	 */
	public static function getAllHolidays() {
		$where	= 'deleted = 0';
		$order	= 'date DESC';

		return TodoyuRecordManager::getAllRecords(self::TABLE, $where, $order);
	}



	/**
	 * Save holiday
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function saveHoliday(array $data) {
		$idHoliday	= intval($data['id']);
		$xmlPath	= 'ext/contact/config/form/holiday.xml';

		if( $idHoliday === 0 ) {
			$idHoliday	= self::addHoliday();
		}

		$data	= self::saveHolidayForeignRecords($data, $idHoliday);
			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idHoliday);

		return self::updateHoliday($idHoliday, $data);
	}



	/**
	 * Add a new holiday record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addHoliday(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE , $data);
	}



	/**
	 * Update a holiday record
	 *
	 * @param	Integer		$idHoliday
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateHoliday($idHoliday, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE , $idHoliday, $data);
	}



	/**
	 * Get holiday sets where the holiday is linked in
	 *
	 * @param	Integer		$idHoliday
	 * @return	Array
	 */
	public static function getHolidaySets($idHoliday) {
		$idHoliday	= intval($idHoliday);

		$fields	= '	s.*';
		$table	= '	ext_calendar_holidayset s,
					ext_calendar_mm_holiday_holidayset mm';
		$where	= '		mm.id_holiday	= ' . $idHoliday .
				  ' AND	mm.id_holidayset= s.id
					AND	s.deleted		= 0';
		$order	= '	s.title';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Save foreign records in the holiday record
	 * - Save linked holiday sets
	 *
	 * @param	Array		$data
	 * @param	Integer		$idHoliday
	 * @return	Array
	 */
	protected static function saveHolidayForeignRecords(array $data, $idHoliday) {
		$idHoliday	= intval($idHoliday);

		self::removeHolidaySets($idHoliday);

		if( is_array($data['holidayset']) ) {
			$holidaySetIDs	= TodoyuArray::getColumn($data['holidayset'], 'id');
			foreach($holidaySetIDs as $idHolidaySet) {
				self::addHolidaySet($idHoliday, $idHolidaySet);
			}
		}
		unset($data['holidayset']);

		return $data;
	}



	/**
	 * Remove all linked holiday sets to the holiday
	 *
	 * @param	Integer		$idHoliday
	 */
	public static function removeHolidaySets($idHoliday) {
		$idHoliday	= intval($idHoliday);

		TodoyuDbHelper::removeMMrelations('ext_calendar_mm_holiday_holidayset', 'id_holiday', $idHoliday);
	}



	/**
	 * Add/link a holiday set to the holiday
	 *
	 * @param	Integer		$idHoliday
	 * @param	Integer		$idHolidaySet
	 */
	public static function addHolidaySet($idHoliday, $idHolidaySet) {
		$idHoliday		= intval($idHoliday);
		$idHolidaySet	= intval($idHolidaySet);

		TodoyuDbHelper::addMMLink('ext_calendar_mm_holiday_holidayset', 'id_holiday', 'id_holidayset', $idHoliday, $idHolidaySet);
	}



	/**
	 * Delete a holiday
	 *
	 * @param	Integer		$idHoliday
	 * @return	Boolean
	 */
	public static function deleteHoliday($idHoliday) {
		$idHoliday	= intval($idHoliday);

		return TodoyuRecordManager::deleteRecord(self::TABLE, $idHoliday);
	}



	/**
	 * Get holiday records for admin
	 *
	 * @return	Array
	 */
	public static function getRecords() {
		$holidays	= self::getAllHolidays();
		$records	= array();

		foreach($holidays as $holiday) {
			$records[]	= array(
				'id'					=> $holiday['id'],
				'label'					=> self::getHolidayLabel($holiday['id'], true, true),
				'additionalInformations'=> TodoyuTime::format($holiday['date'], 'date')
			);
		}

		return $records;
	}



	/**
	 * Get IDs of holiday sets of given addresses (IDs)
	 *
	 * @todo	Refactor this method. Don't support multiple addresses. Why grouping?
	 * @param	Array		$addressIDs
	 * @param	Boolean		$groupByHolidayset
	 * @return	Array
	 */
	public static function getHolidaysetIDsOfAddresses(array $addressIDs, $groupByHolidayset = false) {
		$fields	= '	id,
					id_holidayset';
		$table	= '	ext_contact_address';
		$where	= ' deleted	= 0';

		if( !empty($addressIDs) ) {
			$where .= ' AND ' . TodoyuSql::buildInListQueryPart($addressIDs, 'id');
		}

		$addresses		= Todoyu::db()->getArray($fields, $table, $where);
		$holidaySetIDs	= array();

		if( $groupByHolidayset ) {
				// Get an array of the address IDs with IDs of their assigned holidaySets
			foreach($addresses as $address) {
				$holidaySetIDs[ $address['id'] ][]	= $address['id_holidayset'];
			}
		} else {
				// Just get the holiday set IDs
			foreach($addresses as $address) {
				$holidaySetIDs[]	= $address['id_holidayset'];
			}
			$holidaySetIDs	= array_unique($holidaySetIDs);
		}

		return $holidaySetIDs;
	}



	/**
	 * Get holidays of given sets in given time span.
	 *
	 * @param	TodoyuDayRange	$range
	 * @param	Integer[]		$holidaySetIDs
	 * @return	Array[]
	 */
	public static function getHolidaysInRange(TodoyuDayRange $range, array $holidaySetIDs) {
		$holidaySetIDs	= TodoyuArray::intval($holidaySetIDs, true, false);

		if( sizeof($holidaySetIDs) === 0 ) {
			return array();
		}

		$fields	= '	h.*,
					hhmm.id_holidayset';
		$table	=	self::TABLE . ' h,
					ext_calendar_mm_holiday_holidayset hhmm';
		$where	= '		h.id		= hhmm.id_holiday'
				. ' AND	h.deleted	= 0'
				. ' AND	hhmm.id_holidayset IN(' . implode(',', $holidaySetIDs) . ')'
				. ' AND	h.date BETWEEN ' . $range->getStart() . ' AND ' . $range->getEnd();
		$group	= '	h.id';
		$order	= 'h.date';

		return Todoyu::db()->getArray($fields, $table, $where, $group, $order);
	}



	/**
	 * Get holidays of given persons in given timespan
	 *
	 * @param	Array		$personIDs
	 * @param	Integer		$dateStart		UNIX timestamp of day at beginning of timespan
	 * @param	Integer		$dateEnd		UNIX timestamp of day at ending of timespan
	 * @return	Array
	 */
	public static function getPersonHolidaysInTimespan(array $personIDs, $dateStart = 0, $dateEnd = 0) {
		$personIDs		= TodoyuArray::intval($personIDs, true, true);
		$dateStart		= intval($dateStart);
		$dateEnd		= intval($dateEnd);

			// Get working locations (company addresses) of given persons, affected holidaySets of given address IDs
		$addressIDs		= TodoyuContactPersonManager::getWorkaddressIDsOfPersons($personIDs);
		$holidaySetIDs	= self::getHolidaysetIDsOfAddresses($addressIDs);

			// Get all holidays affected holidaySets in given timespan
		$range		= new TodoyuDayRange($dateStart, $dateEnd);
		$holidays	= self::getHolidaysInRange($range, $holidaySetIDs);

		return $holidays;
	}



	/**
	 * AutoComplete holidays
	 *
	 * @param	String	$sword
	 * @return	Array
	 */
	public static function autocompleteHolidays($sword) {
		$swordArray	= TodoyuArray::trimExplode(' ', $sword, true);
		$results	= array();

		if( !empty($swordArray)  ) {
			$where		= TodoyuSql::buildLikeQueryPart($swordArray, array('title', 'description'));
			$holidays	= Todoyu::db()->getArray('id, title, date', self::TABLE, $where, '', 'date DESC');

			foreach($holidays as $holiday) {
				$results[$holiday['id']]	= $holiday['title'] . ' - ' . TodoyuTime::format($holiday['date'], 'date');
			}
		}

		return $results;
	}



	/**
	 * Get label of given holiday. Includes the holiday title plus optionally it's date and holidaysets
	 *
	 * @param	Integer		$idHoliday
	 * @param	Boolean		$showDate
	 * @return	String
	 */
	public static function getHolidayLabel($idHoliday, $showDate = false) {
		$idHoliday	= intval($idHoliday);
		$holiday	= self::getHoliday($idHoliday);

		$label	= $holiday->getLabel();

			// Include date?
		if( $showDate ) {
			$date	= $holiday->get('date');
			$label	= TodoyuTime::format($date, 'date') . ' - ' . $label;
		}

		return $label;
	}



	/**
	 * Compile array of holidays into an array of days,
	 * (key is date of resp. day) with holidays happening that day in a sub array
	 *
	 * @param	Array	$holidays
	 * @return	Array
	 */
	public static function groupHolidaysByDays(array $holidays) {
		$holidaysGrouped	= array();

		foreach($holidays as $holiday) {
			$dateKey	= date('Ymd', $holiday['date']);
			$holidaysGrouped[$dateKey][]	= $holiday;
		}

		return $holidaysGrouped;
	}

}

?>