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
 * FormElement: Dateselector
 *
 * Input field with calendar popup to select date and time
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_DateTime extends TodoyuFormElement_Date {

	/**
	 * Constructs a date time input form field
	 *
	 * @param	String			$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array			$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
			// Initialize directly with the base class, not the parent
		TodoyuFormElement::__construct('datetime', $name, $fieldset, $config);
		TodoyuFormElement::setValue(false);

		$this->config['calendar']['showsTime'] = true;
	}



	/**
	 * Get date format key for date
	 *
	 * @return	String
	 */
	protected function getFormatKey() {
		return 'datetime';
	}



	/**
	 * Parse date-time string to timestamp
	 * Format should be datetime, but if this fails, date and strtotime is tried too
	 *
	 * @param	String		$dateString
	 * @return	Integer
	 */
	protected function parseDate($dateString) {
		$time	= TodoyuTime::parseDateTime($dateString);

		if( $time === 0 && $dateString !== '' ) {
			$time = TodoyuTime::parseDateString($dateString);
		}

		return $time;
	}

}

?>