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
 * Action controller request dispatcher
 * Handle request and call the action controller
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuActionDispatcher {

	/**
	 * Get request extension.
	 * Fallback for last requested extension and default
	 *
	 * @return	String
	 */
	private static function getExtension() {
		$ext	= TodoyuRequest::getExt();

		if( ! is_string($ext) ) {
			$ext	= TodoyuPreferenceManager::getLastExt();

			if( !$ext ) {
				$ext = Todoyu::$CONFIG['FE']['DEFAULT']['ext'];
			}
		}

		return $ext;
	}



	/**
	 * Ger request controller
	 * Fallback for default controller
	 *
	 * @return	String
	 */
	private static function getController() {
		$ctrl	= TodoyuRequest::getController();

		if( ! is_string($ctrl) ) {
			$ctrl = Todoyu::$CONFIG['FE']['DEFAULT']['controller'];
		}

		return $ctrl;
	}



	/**
	 * Get request command/action
	 *
	 * @return	String
	 */
	private static function getAction() {
		return TodoyuRequest::getAction();
	}



	/**
	 * Dispatch request. Call selected controller
	 */
	public static function dispatch() {
		//self::callExtOnRequestHandler(EXT);

		if( self::isController(EXT, CONTROLLER) ) {
			$params		= TodoyuRequest::getAll();
			$controller	= self::getControllerObject(EXT, CONTROLLER, $params);
		} else {
			self::errorControllerNotFound(EXT, CONTROLLER);
			exit();
		}

			// Execute action
		try {
			echo $controller->runAction(ACTION);
		} catch(TodoyuControllerException $e) {
			$e->printError();
		}
	}



	/**
	 * Call extension request handler function
	 *
	 * @param	String		$ext
	 */
	private static function callExtOnRequestHandler($ext) {
		$handler	= isset( Todoyu::$CONFIG['EXT_REQUEST_HANDLER'][$ext] ) ? Todoyu::$CONFIG['EXT_REQUEST_HANDLER'][$ext] : null;

		if( ! empty($handler) && TodoyuFunction::isFunctionReference($handler) ) {
			TodoyuFunction::callUserFunction($handler);
		}
	}



	/**
	 * Register an extension request handler
	 * This functions will be called on every extension request
	 *
	 * @param	String		$ext
	 * @param	String		$function
	 */
	public static function registerRequestHandler($ext, $function) {
		Todoyu::$CONFIG['EXT_REQUEST_HANDLER'][$ext] = $function;
	}



	/**
	 * Print error message if requested controller not found
	 *
	 * @param	String		$ext
	 * @param	String		$controller
	 */
	private static function errorControllerNotFound($ext, $controller) {
		ob_clean();

		TodoyuLogger::logFatal('Request controller not found ' . $ext . '/' . $controller);

		TodoyuHeader::sendHTTPHeader(404);

		$tmpl	= 'core/view/controller_error.tmpl';
		$data	= array(
			'ext'			=> $ext,
			'controller'	=> $controller,
			'className'		=> self::getControllerClassName($ext, $controller),
			'pathWeb'		=> PATH_WEB
		);

		echo Todoyu::render($tmpl, $data);

		exit();
	}



	/**
	 * Get class name for action controller
	 * Classname is prefixed with "Todoyu", camel case ext and controller and postfixed with "ActionController"
	 *
	 * @param	String		$ext
	 * @param	String		$controller
	 * @return	String
	 */
	private static function getControllerClassName($ext, $controller) {
		return 'Todoyu' . ucfirst(trim($ext)) . ucfirst(trim($controller)) . 'ActionController';
	}



	/**
	 * Get action controller object for the $ext-$controller combination
	 * @param	String		$ext
	 * @param	String		$controller
	 * @param	Array		$params
	 * @return	TodoyuActionController
	 */
	public static function getControllerObject($ext = '', $controller = '', array $params = array()) {
		$controllerClassName= self::getControllerClassName($ext, $controller);
		$instance			= new $controllerClassName($params);

		return $instance;
	}



	/**
	 * Check if a controller class exists
	 *
	 * @param	String		$ext
	 * @param	String		$controller
	 * @return	Boolean
	 */
	public static function isController($ext, $controller) {
		$controllerClassName = self::getControllerClassName($ext, $controller);

		return class_exists($controllerClassName, true);
	}

}

?>