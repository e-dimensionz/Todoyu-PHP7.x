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
 * Todoyu Quickinfo
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuQuickinfo {

	/**
	 * Element key. Mostly a record ID
	 *
	 * @var int|String
	 */
	private $element = 0;

	/**
	 * Quickinfo type
	 *
	 * @var	String
	 */
	private $type;

	/**
	 * Infos
	 *
	 * @var	Array
	 */
	private $elements = array();



	/**
	 * Create a new quickinfo object
	 * Call all registered functions to let them add items
	 *
	 * @param	String	$recordType		element type, e.g. 'person'
	 * @param	String	$element		ID of element item
	 */
	public function __construct($recordType, $element) {
		$this->type		= $recordType;
		$this->element	= $element;

		$this->init();
	}



	/**
	 * Initialize quickinfo
	 * Load data from registered callback functions
	 */
	private function init() {
		$funcRefs	= TodoyuQuickinfoManager::getTypeFunctions($this->type);

			// Get items from all functions
		foreach($funcRefs as $funcRef) {
			TodoyuFunction::callUserFunction($funcRef['function'], $this, $this->element);
		}

			// Sort items
		$this->elements = TodoyuArray::sortByLabel($this->elements, 'position');
	}



	/**
	 * Add new info
	 *
	 * @param	String		$key		Identifier and classname
	 * @param	String		$label		Label text
	 * @param	Integer		$position
	 * @param	Boolean		$escape
	 */
	public function addInfo($key, $label, $position = 100, $escape = true, $class = '') {
		if( $escape ) {
			$label	= htmlentities($label, ENT_QUOTES, 'UTF-8', false);
		}
		$label	= str_replace("\n", '<br />', $label);

		$this->elements[$key] = array(
			'class'		=> $class ? $class : $key,
			'key'		=> $key,
			'label'		=> $label,
			'position'	=> (int) $position
		);
	}



	/**
	 * Add email address as quickinfo
	 * Shortcut to addInfo
	 *
	 * @param	String		$key
	 * @param	String		$email
	 * @param	String		$fullName
	 * @param	Integer		$position
	 */
	public function addEmail($key, $email, $fullName = '', $position = 100) {
		$email	= htmlspecialchars($email);

		if( $fullName !== '' ) {
			$fullName	= htmlspecialchars($fullName);
			$item		=  '<a href="mailto:' . $fullName . '%3c' . $email . '%3e">' . $email . '</a>';
		} else {
			$item	= '<a href="mailto:' . $email . '">' . $email . '</a>';
		}

		$this->addHTML($key, $item, $position);
	}



	/**
	 * Add HTML item. Will not be escaped
	 *
	 * @param	String		$key
	 * @param	String		$html
	 * @param	Integer		$position
	 */
	public function addHTML($key, $html, $position = 100, $class = '') {
		$this->addInfo($key, $html, $position, false, $class);
	}



	/**
	 * Remove an info by key
	 *
	 * @param	String		$key
	 */
	public function removeInfo($key) {
		unset($this->elements[$key]);
	}



	/**
	 * Get current infos
	 *
	 * @return	Array
	 */
	public function getInfos() {
		return TodoyuArray::sortByLabel($this->elements, 'position');
	}



	/**
	 * Get infos encoded as json
	 *
	 * @return	String
	 */
	public function getJSON() {
		return json_encode($this->getInfos());
	}



	/**
	 * Print info struct json encoded
	 */
	public function printJSON() {
		TodoyuHeader::sendTypeJSON();

		echo $this->getJSON();
	}



	/**
	 * Remove all infos
	 */
	public function clear() {
		$this->elements = array();
	}

}

?>