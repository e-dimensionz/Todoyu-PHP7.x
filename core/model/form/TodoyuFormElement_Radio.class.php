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
 * class for radio boxes
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_Radio extends TodoyuFormElement {

	/**
	 * Constructor of the class
	 *
	 * @param	String		$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array		$config
	 */
	function __construct($name, TodoyuFormFieldset $fieldset, array $config  = array()) {
		parent::__construct('radio', $name, $fieldset, $config);
	}



	/**
	 * Init
	 */
	protected function init() {
		if( is_array($this->config['source']) ) {
			$type	= $this->config['source']['@attributes']['type'];
			$source	= $this->config['source'];

			switch( $type ) {
				case 'list':
					$this->initSourceList($source);
					break;

				case 'function':
					$this->initSourceFunction($source);
					break;
			}
		}
	}



	/**
	 * Init options from a XML list
	 *
	 * @param	Array		$source
	 */
	protected function initSourceList(array $source) {
		if( is_array($source['option']) ) {
			foreach($source['option'] as $option) {
				$this->addOption($option['value'], $option['label']);
			}
		}
	}



	/**
	 * Initialize source function
	 *
	 * @param	Array	$source
	 */
	protected function initSourceFunction(array $source) {
		if( TodoyuFunction::isFunctionReference($source['function']) ) {
			$options	= TodoyuFunction::callUserFunction($source['function'], $this->getForm());

			foreach($options as $option) {
				$this->addOption($option['value'], $option['label']);
			}
		}
	}



	/**
	 * Detect if lazy init is defined (grab data when form is rendered)
	 *
	 * @return	Boolean
	 */
	protected function isLazyInit() {
		return isset($this->config['source']['lazyInit']);
	}



	/**
	 * Get all options
	 *
	 * @return	Array
	 */
	public function getOptions() {
		return TodoyuArray::assure($this->config['options']);
	}



	/**
	 * Add a new option at the end of the list
	 *
	 * @param	String		$value
	 * @param	String		$label
	 * @param	Boolean		$disabled
	 */
	public function addOption($value, $label, $disabled = false) {
		$this->config['options'][] = array(
			'value'		=> defined($value) ? constant($value) : $value,
			'label'		=> $label,
			'disabled'	=> $disabled
		);
	}



	/**
	 * Set an option. The (first) option with the same value will be replaced.
	 * If no option with this value exists, a new options will be added
	 *
	 * @param	String		$value
	 * @param	String		$label
	 * @param	Boolean		$disabled
	 * @deprecated
	 */
	public function setOption($value, $label, $disabled = false) {
		$index = $this->getOptionIndexByValue($value);

		if( !$index ) {
			$this->addOption($value, $label, $disabled);
		} else {
			$this->config['options'][$index] =  array(
				'value'		=> $value,
				'label'		=> $label,
				'disabled'	=> $disabled
			);
		}
	}



	/**
	 * Get the index of the option by its value
	 *
	 * @param	String		$value
	 * @return	Integer		Or false if not found
	 * @deprecated
	 */
	protected function getOptionIndexByValue($value) {
		$optionIndex = false;

		foreach( $this->config['options'] as $index => $option ) {
			if( $option['value'] == $value ) {
				$optionIndex = $index;
				break;
			}
		}

		return $optionIndex;
	}



	/**

	/**
	 * Add value to selected-values list
	 *
	 * @param	String		$value
	 */
	public function addCheckedValue($value) {
		$this->setValue(TodoyuString::addToList($this->getValue(), $value, ',', true));
	}



	/**
	 * Get data for template rendering
	 *
	 * @return	Array
	 */
	protected function getData() {
		if( $this->isLazyInit() ) {
			$this->init();
		}
		
		return parent::getData();
	}


}

?>