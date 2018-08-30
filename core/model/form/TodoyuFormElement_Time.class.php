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
 * FormElement: Time
 *
 * Single line text, <input type="text"> for time values
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_Time extends TodoyuFormElement_Text {

	/**
	 * Initialize time-input field
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset		$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		TodoyuFormElement::__construct('time', $name, $fieldset, $config);
	}



	/**
	 * Init basic values
	 */
	protected function init() {
		$this->setInputType('text');
		$this->initJsConfig();
	}



	/**
	 * Initialize js config
	 *
	 */
	protected function initJsConfig() {
		$this->setJsConfig(array(
			'rangeHour' => array(0,23)
		));
	}


	/**
	 * Set js config for time picker
	 *
	 * @param	Array	$jsConfig
	 */
	protected function setJsConfig(array $jsConfig) {
		$this->config['jsConfig'] = $jsConfig;
	}


	/**
	 * Get js config for timepicker
	 *
	 * @return	Array
	 */
	public function getJsConfig() {
		return $this->config['jsConfig'];
	}



	/**
	 * Get element data
	 * Add js config
	 *
	 * @return	Array
	 */
	public function getData() {
		$data	= parent::getData();

		$data['jsConfig'] = json_encode($this->getJsConfig());

		return $data;
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
			$value	= TodoyuTime::parseTime($value);
		}

		parent::setValue($value);
	}



	/**
	 * Get value of the timeinput field (as numeric seconds value)
	 * @return	Integer		Seconds
	 */
	public function getValue() {
		return (int) parent::getValue();
	}



	/**
	 * Get formatted value for template
	 *
	 * @return	String
	 */
	public function getValueForTemplate() {
		$value = $this->getValue();

		return $value === 0 ? '00:00' : TodoyuTime::formatTime($value);
	}

}

?>