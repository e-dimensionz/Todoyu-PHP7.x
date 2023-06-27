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
class TodoyuFormElement_Select extends TodoyuFormElement {

	/**
	 * Initialize
	 *
	 * @param	String			$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array			$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('select', $name, $fieldset, $config);

		$this->setValue(array());

		if( ! $this->isLazyInit() ) {
			$this->initSource();
		}

			// Fix noPleaseSelect setting
		if( isset($this->config['noPleaseSelect']) ) {
			$this->config['noPleaseSelect'] = true;
		}
	}



	/**
	 * Init
	 */
	protected function init() {

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
	 * Check whether multiple attribute is set
	 *
	 * @return	Boolean
	 */
	public function isMultiple() {
		return $this->hasAttribute('multiple');
	}



	/**
	 * Initalize config with dynamic data
	 */
	protected function initSource() {
			// Load options (type defined how)
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


				case 'sql':
					$this->initSourceSql($source);
					break;


				default:
					die('Unknown source tpye');
					break;
			}
		}

		if( $this->hasAttribute('multiple') ) {
			$this->setAttribute('multiple', 1);
		}
	}



	/**
	 * Load select options from database
	 *
	 * @param	Array		$source
	 * @deprecated
	 * @todo	Remove
	 */
	protected function initSourceSql(array $source) {
		$data	= Todoyu::db()->getArray(
			$source['fields'],
			$source['tables'],
			$source['where'],
			$source['group'],
			$source['order'],
			$source['limit']
		);

			// Key for label and value
		$valueKey	= $source['value'];
		$labelKey	= $source['label'];

			// Set flag
		$useValueFunc = false;
		$useLabelFunc = false;

		if( strstr($valueKey, '::') !== false ) {
			$valueFunc = explode('::', $valueKey);
			if( method_exists($valueFunc[0], $valueFunc[1]) ) {
				$useValueFunc = true;
			}
		}

		if( strstr($labelKey, '::') !== false ) {
			$labelFunc = explode('::', $labelKey);
			if( method_exists($labelFunc[0], $labelFunc[1]) ) {
				$useLabelFunc = true;
			}
		}

		foreach( $data as $option ) {
			$value	= $useValueFunc ? call_user_func($valueFunc, $this, $option) : $option[$valueKey];
			$label	= $useLabelFunc ? call_user_func($labelFunc, $this, $option) : $option[$labelKey];

			$this->addOption($value, $label);
		}
	}



	/**
	 * Init options from an XML list
	 *
	 * @param	Array		$source
	 */
	protected function initSourceList(array $source) {
		if( is_array($source['option']) ) {
			foreach($source['option'] as $option) {
				$this->addOption($option['value'], Todoyu::Label($option['label']), $option['disabled'] ?? false);
			}
		}
	}



	/**
	 * Init source function (evoke options gathering per user_func)
	 *
	 * @param	Array	$source
	 */
	protected function initSourceFunction( array $source ) {
		$funcRef	= explode('::', $source['function']);

		switch( sizeof($funcRef) ) {

				// funcRef is built like class::function
			case 2:
				$options = TodoyuFunction::callUserFunction($source['function'], $this);
				foreach($options as $option) {
					$this->addOption($option['value'], $option['label'] ?? '', $option['disabled'] ?? false, $option['class'] ?? '');
				}
				break;

				// funcRef is built like class::function::param, param is e.g the field ID
			case 3:
				TodoyuLogger::logNotice('Non standard 3 parts select source function: ' . $source['function']);
				$funcParam	= $funcRef[2];
				array_pop($funcRef);
				$options	= call_user_func($funcRef, $this, $funcParam);
				foreach($options as $option) {
					$this->addOption($option['value'], $option['label'], $option['disabled']);
				}
				break;
		}
	}



	/**
	 * Get the index of the option by its value
	 *
	 * @param	String		$value
	 * @return	Integer		Or false if not found
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
	 * @param	String		$label
	 * @param	Boolean		$disabled
	 * @param	String		$className
	 */
	public function addOption($value, $label, $disabled = false, $className = '', $selected = false, $group = null) {
		$this->config['options'][] = array(
			'value'		=> $value,
			'label'		=> $label,
			'disabled'	=> ( $disabled !== true ) ? false : 'disabled',
			'class'		=> $className
		);
	}



	/**
	 * Set an option. The (first) option with the same value will be replaced.
	 * If no option with this value exists, a new options will be added
	 *
	 * @param	String		$value
	 * @param	String		$label
	 * @param	Boolean		$selected
	 * @param	Boolean		$disabled
	 * @param	String		$className
	 */
	public function setOption($value, $label, $selected = false, $disabled = false, $className = '') {
		$index = $this->getOptionIndexByValue($value);

		if( !$index ) {
			$this->addOption($value, $label);
		} else {
			$this->config['options'][$index] =  array(
				'value'		=> $value,
				'label'		=> $label,
				'selected'	=> $selected,
				'disabled'	=> $disabled,
				'class'		=> $className
			);
		}
	}



	/**
	 * Set selected values
	 * Should be an array, but can also be a single value
	 *
	 * @param	Mixed		$value
	 */
	public function setValue($value, $updateForm = true) {
		if( ! is_array($value) ) {
			if( is_string($value) && strstr($value, ',') !== false ) {
				$value	= explode(',', $value);
			} else {
				$value = array($value);
			}
		}

		parent::setValue($value);
	}



	/**
	 * Get selected option values as array
	 *
	 * @return	Array
	 */
	public function getValue() {
		$value	= parent::getValue();

		if( !is_array($value) ) {
			$value = array($value);
		}

		return $value;
	}



	/**
	 * Get selected value of select is not multiple
	 *
	 * @param	Boolean		$asInt		Convert value to integer
	 * @return	String|Integer
	 */
	public function getSelectedValue($asInt = false) {
		$values	= $this->getValue();
		$value	= reset($values);

		if( $asInt ) {
			$value = intval($value);
		}

		return $value;
	}



	/**
	 * Add value to selected-values list
	 *
	 * @param	String		$value
	 */
	public function addSelectedValue($value) {
		$selected	= $this->getValue();

		$selected	= array_unique(array_push($selected, $value));

		$this->setValue($selected);
	}



	/**
	 * Get data for template rendering
	 *
	 * @return	Array
	 */
	protected function getData() {
		if( $this->isLazyInit() ) {
			$this->initSource();
		}

			// If size is not set, try to find a good size
		if( ! $this->hasAttribute('size') ) {
			$size	= 1;

				// If multiple, use number of items, but maximal 5
			if( $this->isMultiple() ) {
				$size	= min(5, sizeof($this->config['options']));
			}

			$this->setAttribute('size', $size);
		}

		return parent::getData();
	}



	/**
	 * Get storage data as comma separated list (if multiple values are selected)
	 *
	 * @return	String
	 */
	protected function getStorageDataInternal() {
		return implode(',', $this->getValue());
	}



	/**
	 * Validate required status
	 * The first value shall not be 0 (means please select)
	 *
	 * @return	Boolean
	 */
	public function validateRequired() {
		$firstValue	= array_pop($this->getValue());

		return ! empty($firstValue);
	}



	/**
	 * Set please select label which replaces the default text 'please select'
	 *
	 * @param	String		$label
	 */
	public function setPleaseSelectLabel($label) {
		$this->setAttribute('pleaseSelectLabel', $label);
	}



	/**
	 * Set/unset noPleaseSelect attribute
	 *
	 * @param	Boolean		$active
	 */
	public function setNoPleaseSelect($active = true) {
		if( $active ) {
			$this->setAttribute('noPleaseSelect', 1);
		} else {
			$this->removeAttribute('noPleaseSelect');
		}
	}



	/**
	 * Set an alternative source function
	 *
	 * @param	String		$sourceFunction
	 */
	public function setSourceFunction($sourceFunction) {
		$this->config['source']['@attributes']['type'] = 'function';
		$this->config['source']['function'] = $sourceFunction;
	}

}

?>