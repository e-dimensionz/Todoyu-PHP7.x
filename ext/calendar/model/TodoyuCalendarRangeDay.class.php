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
 * Date range for a single day
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarRangeDay extends TodoyuDayRange {

	/**
	 * Initialize with the date of the day
	 *
	 * @param	Integer		$date
	 */
	public function __construct($date = 0) {
		$date	= TodoyuTime::time($date);

		parent::__construct($date, $date);
	}

}

?>