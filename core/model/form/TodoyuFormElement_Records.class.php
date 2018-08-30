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
 * Select form element
 *
 * @package		Todoyu
 * @subpackage	Form
 */
abstract class TodoyuFormElement_Records extends TodoyuFormElement {

	/**
	 * Initialize
	 *
	 * @param	String				$type
	 * @param	String				$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array				$config
	 */
	public function __construct($type, $name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('records', $name, $fieldset, $config);

		if( !is_array($this->config['options']) ) {
			$this->config['options'] = array();
		}
		$this->config['multiple'] = true;

		$this->initRecords($type);
	}



	/**
	 * Init records config
	 *
	 * @param	String		$type
	 */
	protected function initRecords($type) {
		$this->type				= 'records' . ucfirst($type);
		$this->config['type']	= $type;
		$this->config['class'] .= ' typeRecords records' . ucfirst($type);

		$this->config['options']['params']	= array();
	}



	/**
	 * Add params to options config value
	 *
	 * @param	Array	$params
	 */
	protected function addOptionParams(array $params) {
		$this->config['options']['params'] = array_merge($this->config['options']['params'], $params);
	}



	/**
	 * Get config options
	 *
	 * @return	Array
	 */
	protected function getOptions() {
		return TodoyuArray::assure($this->config['options']);
	}



	/**
	 * Set value
	 * Fix data if value contains whole records instead of IDs
	 *
	 * @param	Array		$value
	 */
	public function setValue($value, $updateForm = true) {
		$value	= TodoyuArray::assure($value);
		$first	= reset($value);

		if( is_array($first) ) {
			$value	= TodoyuArray::getColumn($value, 'id');
		}

		parent::setValue($value);
	}



	/**
	 * Get selected option values as array
	 *
	 * @return Array
	 */
	public function getValue() {
		return TodoyuArray::assure(parent::getValue());
	}



	/**
	 * Get data for template rendering
	 *
	 * @return	Array
	 */
	protected function getData() {
		$this->beforeGetData();

		$this->config['jsonOptions']= json_encode($this->getOptions());
		$this->config['records']	= $this->getRecords();

		return parent::getData();
	}



	/**
	 * Overwrite this method to set additional config before element is rendered
	 * Ex: Set option params
	 */
	protected function beforeGetData() {

	}



	/**
	 * Get display data for selected records
	 *
	 * @abstract
	 * @return	Array[]
	 */
	abstract protected function getRecords();



	/**
	 * Validate required status
	 * The first value shall not be 0 (means please select)
	 *
	 * @return	Boolean
	 */
	public function validateRequired() {
		$firstValue	= reset($this->getValue());

		return !empty($firstValue);
	}



	/**
	 * For live validation observe the storage field (select)
	 *
	 * @return	String
	 */
	protected function getLiveValidationFieldId() {
		return $this->getHtmlID() . '-storage';
	}

}

?>