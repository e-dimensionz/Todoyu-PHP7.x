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
 * FormElement: Button
 *
 * Button element, <button type="[button,cancel,submit]">
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_Button extends TodoyuFormElement {

	/**
	 * Create button element
	 *
	 * @param	String		$name		Button name
	 * @param	TodoyuFormFieldset	$fieldset	Parent fieldset
	 * @param	Array		$config		Button config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('button', $name, $fieldset, $config);
	}



	/**
	 * Initialize default button config
	 */
	protected function init() {
		if( ! $this->hasAttribute('type') ) {
			$this->setType('button');
		}
		if( $this->hasAttribute('disable') ) {
			$this->setAttribute('disable', true);
		}

		$this->setAttribute('noIcon', $this->hasAttribute('noIcon'));
	}



	/**
	 * Get button type
	 *
	 * @return	String
	 */
	public function getType() {
		return $this->getAttribute('type');
	}



	/**
	 * Set button text
	 *
	 * @param	String		$label
	 */
	public function setText($label) {
		$this->setAttribute('text', $label);
	}



	/**
	 * Set class
	 *
	 * @param	String		$class
	 */
	public function setClass($class) {
		$this->setAttribute('class', $class);
	}



	/**
	 * Set onClick javascript action
	 *
	 * @param	$onClickAction
	 */
	public function setOnClick($onClickAction) {
		$this->setAttribute('onclick', $onClickAction);
	}



	/**
	 * Set button type
	 *
	 * @param	String		$type		A valid button type (button,cancel,submit)
	 */
	public function setType($type) {
		$this->setAttribute('type', $type);
	}



	/**
	 * Get data for rendering
	 *
	 * @return	Array
	 */
	protected function getData() {
		if( $this->hasAttribute('onclick') ) {
			$this->setAttribute('onclick', $this->getForm()->parseWithFormData($this->getAttribute('onclick')));
		}

		if( $this->hasAttribute('title') ) {
			$title	= Todoyu::Label($this->getAttribute('title'));
			$this->setAttribute('title', $this->getForm()->parseWithFormData($title));
		}

		return parent::getData();
	}



	/**
	 * Never save buttons
	 *
	 * @return	Boolean
	 */
	public function isNoStorageField() {
		return true;
	}



	/**
	 * Render button to HTML
	 *
	 * @param	Boolean	$odd
	 * @return	String
	 */
	public function render($odd = false) {
		$tmpl	= $this->getTemplate();
		$data	= $this->getData();
		$data['odd'] = $odd;

		return Todoyu::render($tmpl, $data);
	}

}

?>