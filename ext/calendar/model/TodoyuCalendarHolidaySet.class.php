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
 * HolidaySet object
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarHolidaySet extends TodoyuBaseObject {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE	= 'ext_calendar_holidayset';



	/**
	 * Constructor
	 *
	 * @param	Integer	$idHolidaySet
	 */
	function __construct($idHolidaySet) {
		parent::__construct($idHolidaySet, self::TABLE);
	}



	/**
	 * Get title
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->get('title');
	}




	/**
	 * Get description
	 *
	 * @return	String
	 */
	public function getDescription() {
		return $this->get('description');
	}



	/**
	 * Get holidays in range
	 *
	 * @param	TodoyuDayRange		$range
	 * @return	TodoyuCalendarHoliday
	 */
	public function getHolidays(TodoyuDayRange $range = null) {
		$fields	= '	h.id';
		$table	= '	ext_calendar_holiday h,
					ext_calendar_mm_holiday_holidayset mm';
		$where	= '		mm.id_holidayset= ' . $this->getID()
				. ' AND	mm.id_holiday	= h.id'
				. '	AND	h.deleted		= 0';
		$order	= ' h.date';

		if( !is_null($range) ) {
			$where .= ' AND h.date BETWEEN ' . $range->getStart() . ' AND ' . $range->getEnd();
		}

		$holidayIDs	= Todoyu::db()->getColumn($fields, $table, $where, '', $order, '', 'id');

		return TodoyuRecordManager::getRecordList('TodoyuCalendarHoliday', $holidayIDs);
	}



	/**
	 * Load foreign data (holidays)
	 */
	public function loadForeignData() {
		$this->data['holidays']	= TodoyuCalendarHolidaySetManager::getHolidaysData($this->getID());
	}



	/**
	 * Get template data for holiday set
	 *
	 * @param	Boolean	$loadForeignData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false) {
		if( $loadForeignData ) {
			$this->loadForeignData();
		}

		return parent::getTemplateData();
	}

}
?>