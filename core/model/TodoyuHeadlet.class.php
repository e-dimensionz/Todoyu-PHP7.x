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
 * Abstract headlet base class
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
abstract class TodoyuHeadlet implements TodoyuHeadletInterface {

	/**
	 * Headlet template file
	 *
	 * @var	String
	 */
	protected $template	= 'core/view/headlet.tmpl';

	/**
	 * Headlet render data
	 *
	 * @var	Array
	 */
	protected $data		= array();

	/**
	 * Request parameters
	 *
	 * @var	Array
	 */
	protected $params	= array();

	protected $buttonAttributes	= array();

	protected $jsHeadlet	= null;

	protected $type;

	protected $class	= '';




	/**
	 * Headlet constructor which calls the init function
	 *
	 * @final
	 */
	public final function __construct() {
		$this->addButtonClass('button');

		$this->initType();
		$this->init();
		$this->setOpenStatus();
	}



	/**
	 * Set reference to JavaScript headlet object
	 *
	 * @param	String		$jsHeadlet
	 */
	protected final function setJsHeadlet($jsHeadlet) {
		$this->jsHeadlet = $jsHeadlet;
	}



	/**
	 * Init function for type
	 */
	protected function initType() {
		// Dummy, override in headlet type class
	}



	/**
	 * Init function for panel widget, alternative for constructor
	 */
	protected function init() {
		// Dummy, override in headlet class
	}



	/**
	 * Get headlet name
	 *
	 * @return	String
	 */
	public function getName() {
		return strtolower(get_class($this));
	}



	/**
	 * Get headlet label
	 */
	abstract public function getLabel();



	/**
	 * Get headlet ID (for HTML)
	 *
	 * @return	String
	 */
	public function getID() {
		return strtolower($this->getName());
	}



	/**
	 * Get headlet type
	 *
	 * @return	unknown
	 */
	public function getType() {
		return $this->type;
	}



	/**
	 * Set headlet template
	 *
	 * @param	String		$template
	 */
	protected function setTemplate($template) {
		$this->template = $template;
	}



	/**
	 * Set headlet render data
	 *
	 * @param	Array		$data
	 */
	protected function setData(array $data) {
		$this->data = $data;
	}



	/**
	 * Set visible flag (visible on page load)
	 */
	protected function setOpenStatus() {
		$open	= TodoyuHeadletManager::isOpen($this->getName());

		if( $open ) {
			$this->data['open']	= true;
			$this->addClass('active');
		}
	}



	/**
	 * Get current area ID
	 *
	 * @return	Integer
	 */
	protected function getAreaID() {
		return Todoyu::getArea();
	}



	/**
	 * Get current area key
	 *
	 * @return	String
	 */
	protected function getAreaKey() {
		return Todoyu::getAreaKey();
	}



	/**
	 * Add attribute for the button
	 *
	 * @param	String		$name
	 * @param	String		$value
	 */
	protected function addButtonAttribute($name, $value) {
		$this->buttonAttributes[$name][] = $value;
	}



	/**
	 * Add classname (CSS) attribute to button config
	 *
	 * @param	String	$class
	 */
	protected function addButtonClass($class) {
		$this->addButtonAttribute('class', trim($class) . ' ');
	}



	/**
	 * Get button attributes
	 *
	 * @return	Array
	 */
	protected function getButtonAttributes() {
		$attributes	= array();

		foreach($this->buttonAttributes as $name => $values) {
			$attributes[$name] = implode('', $values);
		}

		return $attributes;
	}



	/**
	 * Add headlet class
	 *
	 * @param	String		$class
	 */
	public function addClass($class) {
		$this->class = trim($this->class . ' ' . $class);
	}



	/**
	 * Get headlet classes
	 *
	 * @return	String
	 */
	public function getClass() {
		return trim($this->class);
	}



	/**
	 * Render headlet
	 *
	 * @return	String
	 */
	public function render() {
		$this->data['id'] = $this->getName();


		$this->data['buttonAttributes']	= $this->getButtonAttributes();

		if( ! is_null($this->jsHeadlet) ) {
			TodoyuPage::addJsInit('Todoyu.Headlets.add(\'' . $this->getName() . '\', ' . $this->jsHeadlet . ')', 150);
		}

		return Todoyu::render($this->template, $this->data);
	}



	/**
	 * Check whether headlet is empty and should not be displayed
	 * A headlet can override this function and prevent to be rendered
	 * Empty means, the headlet has no reason to be displayed
	 *
	 * @return	Boolean
	 */
	public function isEmpty() {
		return false;
	}



	/**
	 * Check whether current headlet is open
	 *
	 * @return	Boolean
	 */
	public function isOpen() {
		return TodoyuHeadletManager::isOpen(get_class($this));
	}

}
?>