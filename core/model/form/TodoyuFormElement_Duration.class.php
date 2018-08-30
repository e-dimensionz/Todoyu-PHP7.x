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
 * FormElement: Duration selector
 *
 * Input field to select a duration 00:00 - 99:59 (hour:minute)
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_Duration extends TodoyuFormElement_Time {

	/**
	 * Initialize duration field
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset		$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		TodoyuFormElement::__construct('duration', $name, $fieldset, $config);
	}



	/**
	 * Initialize js config for time picker
	 *
	 */
	protected function initJsConfig() {
		$this->setJsConfig(array(
			'rangeHour' => array(0,99)
		));
	}



	/**
	 * Set field value (seconds)
	 *
	 * @param	Mixed		$value
	 */
	public function setValue($value, $updateForm = true) {
		if( is_numeric($value) ) {
			$value	= (int) $value;
		} else {
			$value	= TodoyuTime::parseDuration($value);
		}

		parent::setValue($value);
	}

}

?>