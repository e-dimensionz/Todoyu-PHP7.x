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
 * Base class for panel widgets
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
abstract class TodoyuPanelWidget {

	/**
	 * Data of the widget
	 *
	 * @var	Array
	 */
	protected $data	= array();

	/**
	 * Widget config
	 *
	 * @var	Array
	 */
	protected $config;

	/**
	 * Widget parameters
	 *
	 * @var	Array
	 */
	protected $params;

	/**
	 * Area ID where widget is rendered (used to load specific configuration)
	 *
	 * @var	Integer
	 */
	protected $idArea = 0;

	/**
	 * Collapsed status
	 *
	 * @var	Boolean
	 */
	protected $collapsed = false;



	/**
	 * Initialize basic panel widget configuration
	 *
	 * @param	String		$ext		Extension key where the widget is located
	 * @param	String		$id			Panel widget ID (class name without TodoyuPanelWidget)
	 * @param	String		$title		Title of the panel widget
	 * @param	Array		$config		Configuration array for the widget
	 * @param	Array		$params		Custom parameters for current page request
	 */
	public function __construct($ext, $id, $title, array $config = array(), array $params = array()) {
		$this->set('ext', $ext);
		$this->set('id', $id);

		$this->setTitle($title);
		$this->setClass($id);

		$this->config	= $config;
		$this->params	= $params;

		$this->setCollapsedStatus();
	}



	/**
	 * Set data
	 *
	 * @param	String						$key
	 * @param	Array|Boolean|Int|String	$value
	 */
	public function set($key, $value) {
		$this->data[$key] = $value;
	}



	/**
	 * Set widget title
	 *
	 * @param	String		$title
	 */
	public function setTitle($title) {
		$this->set('title', Todoyu::Label($title));
	}



	/**
	 * Set widget class
	 *
	 * @param	String		$class
	 */
	public function setClass($class) {
		$this->set('class', $class);
	}



	/**
	 * Add a CSS class
	 *
	 * @param	String		$class
	 */
	public function addClass($class) {
		$classes	= explode(' ', $this->getClass());
		$classes[]	= $class;
		$classes	= implode(' ', array_unique($classes));

		$this->setClass($classes);
	}



	/**
	 * Add a hasIcon class
	 */
	public function addHasIconClass() {
		$this->addClass('hasIcon');
	}



	/**
	 * Set collapsed status
	 */
	public function setCollapsedStatus() {
		$this->set('collapsed', TodoyuPanelWidgetManager::isCollapsed($this->get('id')));
	}



	/**
	 * Get data
	 *
	 * @param	String						$key
	 * @return	Array|Boolean|Int|String
	 */
	public function get($key) {
		return $this->data[$key];
	}



	/**
	 * Get ID
	 *
	 * @return	String
	 */
	public function getID() {
		return $this->get('id');
	}



	/**
	 * Get box classes
	 *
	 * @return	String
	 */
	public function getClass() {
		return $this->get('class');
	}



	/**
	 * Get title
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->get('title');
	}



	/**
	 * Get area ID
	 *
	 * @return	Integer
	 */
	public function getArea() {
		return $this->idArea;
	}



	/**
	 * Check if widget is configured as collapsed
	 *
	 * @return	Boolean
	 */
	public function isCollapsed() {
		return $this->collapsed;
	}



	/**
	 * Set box content
	 *
	 * @param	String		$content
	 */
	protected function setContent($content) {
		$this->set('content', $content);
	}



	/**
	 * Get content
	 *
	 * @return	String
	 */
	public function getContent() {
		return $this->get('content');
	}



	/**
	 * Render panel widget
	 *
	 * @return	String
	 */
	public function render() {
		$tmpl	= 'core/view/panelwidget.tmpl';

		$this->setContent($this->renderContent());

		return Todoyu::render($tmpl, $this->data);
	}



	/**
	 * Checks if the panel widget is allowed
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return true;
	}



	/**
	 * @abstract
	 * @return	String
	 */
	public abstract function renderContent();

}

?>