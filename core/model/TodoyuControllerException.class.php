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
 * Action controller exception
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuControllerException extends Exception {
	/**
	 * Extension key
	 *
	 * @var	String
	 */
	private $ext;

	/**
	 * Controller
	 *
	 * @var	String
	 */
	private $ctrl;

	/**
	 * Action
	 *
	 * @var	String
	 */
	private $action;



	/**
	 * Initialize
	 *
	 * @param	String		$ext
	 * @param	String		$ctrl
	 * @param	String		$action
	 * @param	String		$message
	 * @param	Integer		$code
	 */
	public function __construct($ext, $ctrl, $action, $message, $code = 0) {
		parent::__construct($message, $code);

		$this->ext		= $ext;
		$this->ctrl		= $ctrl;
		$this->action	= $action;
	}



	/**
	 * Get extension key
	 *
	 * @return	String
	 */
	public function getExt() {
		return $this->ext;
	}



	/**
	 * Get controller name
	 *
	 * @return	String
	 */
	public function getController() {
		return $this->ctrl;
	}



	/**
	 * Get action
	 *
	 * @return	String
	 */
	public function getAction() {
		return $this->action;
	}



	/**
	 * Print exception with TodoyuDebug
	 */
	public function printError() {
		TodoyuDebug::printInFirebug($this->getMessage(), 'Controller Exception');
	}

}

?>