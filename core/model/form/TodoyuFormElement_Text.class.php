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
 * FormElement: Text
 *
 * Single line text, <input type="text">
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_Text extends TodoyuFormElement {

	/**
	 * TodoyuFormElement text constructor
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset		$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('text', $name, $fieldset, $config);
	}



	/**
	 * Initialize form element
	 */
	protected function init() {
		if( ! $this->hasAttribute('type') ) {
			$this->setInputType('text');
		}

			// Add password info
		if( $this->getInputType() === 'password' ) {
			if( isset($this->config['validate']['goodPassword']) ) {
				$validator	= new TodoyuPasswordValidator();
				$validator->validate('');
				$text		= implode('<br />', $validator->getErrors());

				$this->addAfterFieldText($text);
			}
		}
	}



	/**
	 * Set type attribute
	 *
	 * @param	String		$type
	 */
	public function setInputType($type) {
		$this->setAttribute('type', $type);
	}



	/**
	 * Get "type"
	 *
	 * @return	Mixed
	 */
	public function getInputType() {
		return $this->getAttribute('type');
	}



	/**
	 * Validate if field is required
	 * Text = not empty if spaces are removed
	 *
	 * @return	Boolean
	 */
	public function validateRequired() {
		return trim($this->getValue()) !== '';
	}



	/**
	 * Get value for template (hide password)
	 *
	 * @return	String
	 */
	public function getValueForTemplate() {
		if( $this->getAttribute('type') === 'password' ) {
			return '';
		}

		return parent::getValueForTemplate();
	}



	/**
	 * Get data of field to store in the database
	 *
	 * @return	String
	 */
	protected function getStorageDataInternal() {
		return trim($this->getValue());
	}

}

?>