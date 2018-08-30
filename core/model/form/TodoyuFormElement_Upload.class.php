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
 * FormElement: Upload
 *
 * Upload field
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_Upload extends TodoyuFormElement {

	/**
	 * Constructor
	 *
	 * @param	String		$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array		$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('upload', $name, $fieldset, $config);
	}



	/**
	 * Init
	 */
	protected function init() {
		$this->getForm()->setAttribute('enctype', 'multipart/form-data');
		$this->getForm()->setAttribute('method', 'post');
	}



	/**
	 * Set type
	 *
	 * @param	String	$type
	 */
	public function setType($type) {
		$this->setAttribute('type', $type);
	}



	/**
	 * Get data
	 *
	 * @return	Array
	 */
	protected function getData() {
		if( $this->hasAttribute('onchange') ) {
			$this->config['extraAttributes'] .= 'onchange="' . $this->getForm()->parseWithFormData($this->getAttribute('onchange')) . '"';
		}

		return parent::getData();
	}

}

?>