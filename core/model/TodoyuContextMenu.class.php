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
 * Dynamic context menu loaded by AJAX request
 * Extensions can register menu items for menu types
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuContextMenu {

	/**
	 * Type of the context menu
	 *
	 * @var	String
	 */
	private $type = '';

	/**
	 * Id of the element, the context menu is generated for (ex: Task-ID)
	 *
	 * @var	Integer
	 */
	private $idElement = 0;

	/**
	 * Items in the context menu
	 *
	 * @var	Array
	 */
	private $elements = array();



	/**
	 * Initialize context menu with ID of the processed element
	 *
	 * @param	String		$type
	 * @param	Integer		$idElement
	 */
	public function __construct($type, $idElement) {
		$this->type			= $type;
		$this->idElement	= (int) $idElement;

		$this->init();
	}



	/**
	 * Initialize context menu with elements
	 */
	private function init() {
		$this->elements	= TodoyuContextMenuManager::getTypeContextMenuItems($this->type, $this->idElement);

			// Parse labels and jsActions
		$this->elements	= $this->parseElements($this->elements);
	}




	/**
	 * Parse elements (label and jsAction)
	 *
	 * @param	Array	$elements
	 * @return	Array
	 */
	private function parseElements(array $elements) {
		foreach($elements as $index => $element) {
				// Parse jsAction and label
			$elements[$index]['jsAction']	= $this->renderJsAction($element['jsAction']);
			$elements[$index]['label']		= $this->renderLabel($element['label']);

				// Parse recursive for sub menus
			if( is_array($element['submenu']) ) {
				$elements[$index]['submenu'] = $this->parseElements($element['submenu']);
			}
		}

		return $elements;
	}



	/**
	 * Get context menu elements array
	 *
	 * @return	Array
	 */
	public function getElements() {
		return $this->elements;
	}



	/**
	 * Get context menu elements as JSON encoded string
	 *
	 * @return	String
	 */
	public function getJSON() {
		return json_encode($this->getElements());
	}



	/**
	 * Print json encoded context menu struct
	 */
	public function printJSON() {
		TodoyuHeader::sendTypeJSON();

		echo $this->getJSON();
	}



	/**
	 * Replace the #ID# placeholder with the current element ID
	 *
	 * @param	String		$jsAction		JavaScript link
	 * @return	String
	 */
	private function renderJsAction($jsAction) {
		$jsAction	= str_replace('#ID#', $this->idElement,	$jsAction);

		return $jsAction;
	}



	/**
	 * Render label if there is a function reference
	 *
	 * @param	String		$label
	 * @return	String
	 */
	private function renderLabel($label) {
			// Check if there is a function reference in the label
		if( strstr($label, '::') ) {
			$label = TodoyuFunction::callUserFunction($label, $this->idElement);
		} else {
			$label	= Todoyu::Label($label);
		}

		return $label;
	}

}

?>