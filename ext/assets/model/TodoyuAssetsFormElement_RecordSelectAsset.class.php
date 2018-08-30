<?php
/****************************************************************************
 * todoyu is published under the BSD License:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * Copyright (c) 2013, snowflake productions GmbH, Switzerland
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



class TodoyuAssetsFormElement_RecordSelectAsset extends TodoyuAssetsFormElement_RecordsAsset {


	/**
	 * Initialize
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		TodoyuFormElement_Records::__construct('selectAsset', $name, $fieldset, $config);
	}



	/**
	 * Init the object with special person config
	 *
	 */
	protected function initRecords($type) {
		parent::initRecords($type);

		$this->config['options'] = array();
	}



	/**
	 * Get template data
	 *
	 * @return	Array
	 */
	protected function getData() {
		$options = TodoyuFunction::callUserFunction($this->config['source']['optionFunction'], $this);

		if( is_array($options) > 0 ) {
			foreach($options as $option) {
				$this->addOption($option['group'], $option['value'], $option['label'], $option['disabled'], $option['class']);
			}
		}

		return parent::getData();
	}



	/**
	 * Add option
	 *
	 * @param	String		$group
	 * @param	String		$value
	 * @param	String		$label
	 * @param	Boolean		$disabled
	 * @param	String		$className
	 */
	public function addOption($group, $value, $label, $disabled = false, $className = '') {
		$this->config['options'][$group][] = array(
			'value'		=> $value,
			'label'		=> $label,
			'disabled'	=> ( $disabled !== true ) ? false : 'disabled',
			'class'		=> $className
		);
	}



	/**
	 * Get records
	 * Nothing to do because not using normal records listing
	 *
	 * @return	Array
	 */
	public function getRecords() {
		return array();
//		return TodoyuFunction::callUserFunction($this->config['source']['recordFunction'], $this);
	}
}

?>