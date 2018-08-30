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
 * [Add class description]
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuDayRange extends TodoyuDateRange {

	/**
	 * Get range ID
	 *
	 * @return	String
	 */
	public function getID() {
		return date('Ymd', $this->getStart()) . date('Ymd', $this->getEnd());
	}



	/**
	 * Set start date
	 *
	 * @param	Integer		$date
	 */
	public function setStart($date) {
		$date	= TodoyuTime::getDayStart($date);

		parent::setStart($date);
	}



	/**
	 * Set end date
	 *
	 * @param	Integer		$date
	 */
	public function setEnd($date) {
		$date	= TodoyuTime::getDayEnd($date);

		parent::setEnd($date);
	}



	/**
	 * Set range start by date
	 *
	 * @param	Integer		$year
	 * @param	Integer		$month
	 * @param	Integer		$day
	 */
	public function setDateStart($year, $month, $day, $hour = 0, $minute = 0, $second = 0) {
		parent::setDateStart($year, $month, $day, 0, 0, 0);
	}



	/**
	 * Set range end by date
	 *
	 * @param	Integer		$year
	 * @param	Integer		$month
	 * @param	Integer		$day
	 */
	public function setDateEnd($year, $month, $day, $hour = 0, $minute = 0, $second = 0) {
		parent::setDateEnd($year, $month, $day, 23, 59, 59);
	}



	/**
	 * Set same date for start and end
	 *
	 * @param	Integer		$date
	 */
	public function setDate($date) {
		$this->setStart($date);
		$this->setEnd($date);
	}



	/**
	 * Get duration of this range in days
	 *
	 * @return	Integer
	 */
	public function getDurationInDays() {
		return round($this->getDuration() / TodoyuTime::SECONDS_DAY, 0);
	}

}

?>