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
 * Base class for panelwidget search boxes
 *
 * @package		Todoyu
 * @subpackage	Core
 */
abstract class TodoyuPanelWidgetSearchBox extends TodoyuPanelWidget {

	/**
	 * @var	String
	 */
	protected $jsObject;



	/**
	 * Initialize
	 *
	 * @param	String		$extKey
	 * @param	String		$panelWidgetKey
	 * @param	String		$title
	 * @param	Array		$config
	 * @param	Array		$params
	 */
	public function __construct($extKey, $panelWidgetKey, $title, array $config, array $params = array()) {
		parent::__construct(
			$extKey,
			$panelWidgetKey,
			$title,
			$config,
			$params
		);

		$this->addClass('panelwidgetSearchBox');
	}



	/**
	 * Set js object
	 *
	 * @param	String		$jsObject
	 */
	protected function setJsObject($jsObject) {
		$this->jsObject = $jsObject;
	}



	/**
	 * Get template
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return 'core/view/panelwidget-searchbox.tmpl';
	}



	/**
	 * Get data to render content
	 *
	 * @return	Array
	 */
	protected function getContentData() {
		return array(
			'id'		=> $this->getID(),
			'searchWord'=> $this->getSearchWord(),
			'jsInit'	=> $this->getJsObjectInitCode()
		);
	}



	/**
	 * Build js ini code for panelwidget
	 *
	 * @return	String
	 */
	protected function getJsObjectInitCode() {
		return '(function(){Todoyu.R[\'panelwidgetsearchbox-' . $this->getID() . '\'] = new ' . $this->jsObject . '();})()';
	}



	/**
	 * Render content of contact search panel widget
	 *
	 * @return String
	 */
	public function renderContent() {
		return Todoyu::render($this->getTemplate(), $this->getContentData());
	}



	/**
	 * Check whether using the search widget is allowed to current logged in person
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return true;
	}



	/**
	 * Get search word
	 *
	 * @return	String
	 */
	abstract protected function getSearchWord();


}

?>