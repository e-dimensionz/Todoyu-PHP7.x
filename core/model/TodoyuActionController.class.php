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
 * Base action controller which will be extended by every action controller
 * of the extensions
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
abstract class TodoyuActionController {
	/**
	 * Request parameters
	 *
	 * @var	Array
	 */
	protected $params;



	/**
	 * Constructor
	 * Set params and call init function
	 *
	 * @param	 Array		$params
	 */
	public final function __construct(array $params) {
		$this->params	= $params;

		$this->init($params);
	}



	/**
	 * Destructor is unused at the moment
	 */
	public function __destruct() {

	}



	/**
	 * Get words of classname (split into camelCase separated words)
	 *
	 * @return	Array
	 */
	protected final function getClassNameParts() {
		return TodoyuString::splitCamelCase(get_class($this));
	}



	/**
	 * Init function
	 * Can be overriden in extended class. Can be used as constructor alternative
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		// Override for custom init
	}



	/**
	 * Run requested action
	 * If requested action is not defined in current object, check for _unknowAction.
	 * If _unknownAction() is defined, call it. First parameter is original action name, second are all parameters
	 *
	 * @param		String		$action
	 * @return		String
	 * @throws	TodoyuControllerException
	 */
	public final function runAction($action = 'default') {
		$result	= false;

			// Check if action exists
		if( $this->isAction($action) ) {
				// Access granted
			$method	= $this->getActionMethodName($action);
			$result	= call_user_func(array($this, $method), $this->params);
			// If action method not found
		} elseif( method_exists($this, '_unknownAction') ) {
			$result	= call_user_func(array($this, '_unknownAction'), $action, $this->params);
		} else {
			throw new TodoyuControllerException(EXT, get_class($this), $action, 'Action "' . $action . '" not found in ' . get_class($this));
		}

		return $result;
	}



	/**
	 * Prototype of _unknownAction
	 *
	 * @param	String		$action
	 * @param	Array		$params
	 */
	protected function _unknownAction($action, array $params) {
		die('Unknown action: ' . htmlentities($action, ENT_QUOTES, 'UTF-8', false));
	}



	/**
	 * Get method name for the action
	 *
	 * @param	String		$action
	 * @return	String
	 */
	protected function getActionMethodName($action) {
		return strtolower($action) . 'Action';
	}



	/**
	 * Check if action exists (method is defined)
	 *
	 * @param	String		$action
	 * @return	Boolean
	 */
	public function isAction($action) {
		$funcName	= $this->getActionMethodName($action);

		return method_exists($this, $funcName);
	}



	/**
	 * Forward the request to anonther action controller
	 *
	 * @param	String		$ext
	 * @param	String		$controller
	 * @param	String		$action
	 * @param	Array		$params
	 * @return	String
	 */
	public function forward($ext, $controller, $action, array $params = array()) {
		$controllerObject	= TodoyuActionDispatcher::getControllerObject($ext, $controller, $params);

			// Execute action
		try {
			return $controllerObject->runAction($action);
		} catch(TodoyuControllerException $e) {
			$e->printError();
		} catch(Exception $e) {
			die("Error: " . $e->getMessage());
		}

		return ''; // make sure to return always a string
	}



	/**
	 * Dummy default action
	 * If controller is called without an action and defaultAction is not defined in
	 * extended object, print error
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		die('THERE IS NO DEFAULT ACTION DEFINED');
	}

}

?>