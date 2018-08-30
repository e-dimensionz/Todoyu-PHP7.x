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
 * Wrapper for request inputs
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuRequest {

	/**
	 * Get parameter from request. POST data is preferred if available
	 *
	 * @param	String		$name			name of the variable
	 * @param	Boolean		$intVal			Apply intval() on the value
	 * @return	Mixed
	 */
	public static function getParam($name, $intVal = false) {
			// Retrieve value from _POST or _GET if set, otherwise set NULL
		if( isset($_POST[$name]) ) {
			$value	= $_POST[$name];
		} elseif( isset($_GET[$name]) ) {
			$value	= $_GET[$name];
		} else {
			$value	= NULL;
		}

		if( $intVal ) {
			$value = (int) $value;
		}

			// Strip slashes on string values
		if( is_string($value) ) {
			$value = stripslashes($value);
		}

			// Strip slashes on array values
		if( is_array($value) ) {
			$value = TodoyuArray::stripslashes($value);
		}

		return $value;
	}



	/**
	 * Get all request data. POST overrides GET
	 *
	 * @return	Array
	 */
	public static function getAll() {
		$get	= $_GET;
		$post	= $_POST;

		if( function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() === 1 ) {
			$get	= TodoyuArray::stripslashes($get);
			$post	= TodoyuArray::stripslashes($post);
		}

		return array_merge($get, $post);
	}



	/**
	 * Get request parameter
	 *
	 * @param	String		$name
	 * @return	Mixed		String, Array or Null
	 */
	public static function get($name) {
		$all	= self::getAll();

		return $all[$name];
	}



	/**
	 * Get request header data
	 *
	 * @param	String		$name
	 * @return	String
	 */
	public static function getHeader($name) {
		$name	= 'HTTP_' . strtoupper(str_replace('-', '_', $name));

		return $_SERVER[$name];
	}



	/**
	 * Get request method
	 *
	 * @return	String
	 */
	public static function getMethod() {
		return $_SERVER['REQUEST_METHOD'];
	}



	/**
	 * Get information for an uploaded file
	 *
	 * @param	String				$name
	 * @param	String|Boolean		$container
	 * @return	Array|Boolean
	 */
	public static function getUploadFile($name, $container = false) {
		$file	= false;
		$info	= $container ? $_FILES[$container] : $_FILES;

		if( is_array($info) ) {
			$file	= array(
				'name'		=> $info['name'][$name],
				'type'		=> $info['type'][$name],
				'tmp_name'	=> $info['tmp_name'][$name],
				'error'		=> (int) $info['error'][$name],
				'size'		=> (int) $info['size'][$name]
			);
		}

		return $file;
	}



	/**
	 * Check if request is a POST request
	 *
	 * @return	Boolean
	 */
	public static function isPostRequest() {
		return self::getMethod() === 'POST';
	}



	/**
	 * Get current referrer URL
	 *
	 * @return	String
	 */
	public static function getReferer() {
		return getenv('HTTP_REFERER');
	}



	/**
	 * Get currently requested URL
	 *
	 * @return	String
	 */
	public static function getRequestUrl() {
		return $_SERVER['REQUEST_URI'];
	}



	/**
	 * Get requested extension
	 *
	 * @return	String
	 */
	public static function getExt() {
		return self::getParam('ext');
	}



	/**
	 * Get requested action
	 *
	 * @return	String
	 */
	public static function getController() {
		return self::getParam('controller');
	}



	/**
	 * Get command if set
	 *
	 * @return	String
	 */
	public static function getAction() {
		return self::getParam('action');
	}



	/**
	 * Get area of current request
	 *
	 * @return	String
	 */
	public static function getArea() {
		$area	= self::getParam('area');

		if( is_null($area) ) {
			$area = self::getParam('ext');
		}

		if( is_null($area) ) {
			$area = TodoyuPreferenceManager::getLastExt();
		}

		return $area;
	}



	/**
	 * Get area ID of current request
	 *
	 * @return	Integer
	 */
	public static function getAreaID() {
		return TodoyuExtensions::getExtID(self::getArea());
	}



	/**
	 * Check header if this is an AJAX request
	 *
	 * @return	Boolean
	 */
	public static function isAjaxRequest() {
		return $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}



	/**
	 * Get the four basic request vars which are always necessary
	 *
	 * @return	Array		[ext,ctrl,action,area]
	 */
	public static function getBasicRequestVars() {
		return array(
			'ext'		=> self::getParam('ext'),
			'ctrl'		=> self::getParam('controller'),
			'action'	=> self::getParam('action'),
			'area'		=> self::getAreaID(),
			'areaExt'	=> self::getArea()
		);
	}



	/**
	 * Get current valid request vars
	 * The basic request vars (ext,controller,action,area) will be processed by
	 * the core/onload hooks. These hooks can modify the request vars (for login or what ever)
	 *
	 * @return	Array
	 */
	public static function getCurrentRequestVars() {
		$requestVars	= self::getBasicRequestVars();
		$requestVars	= TodoyuHookManager::callHookDataModifier('core', 'requestVars', $requestVars, array($requestVars));

		return $requestVars;
	}



	/**
	 * Set the default request vars if they are not defined in the request
	 * This is the first hook which processes the request vars
	 *
	 * @param	Array		$requestVars				Current request vars (may have been modified)
	 * @param	Array		$originalRequestVars		Originally provided request vars
	 * @return	Array
	 */
	public static function hookSetDefaultRequestVars(array $requestVars, array $originalRequestVars) {
			// Check ext for a valid string and set defaults if needed
		if( empty($requestVars['ext']) ) {
			$ext = false;

			if( TodoyuAuth::isLoggedIn() ) {
				$ext	= TodoyuPreferenceManager::getLastExt();
			}

			if( !$ext ) {
				$ext = Todoyu::$CONFIG['FE']['DEFAULT']['ext'];
			}

			$requestVars['ext'] = $ext;
		}

			// Check controller
		if( empty($requestVars['ctrl']) ) {
			$requestVars['ctrl'] = Todoyu::$CONFIG['FE']['DEFAULT']['controller'];
		}

			// Check command
		if( empty($requestVars['action']) ) {
			$requestVars['action']	= 'default';
		}

		return $requestVars;
	}



	/**
	 * Search, verify and define the main request variables as constants
	 */
	public static function initRequest() {
			// Get valid request variables (here it will be checked for login, etc)
		$requestVars	= self::getCurrentRequestVars();

			// Set definitive request vars as constants
		define('EXT',		$requestVars['ext']);
		define('CONTROLLER',$requestVars['ctrl']);
		define('ACTION',	$requestVars['action']);
		define('AREA',		$requestVars['area']);
		define('AREAEXT',	$requestVars['areaExt']);
	}



	/**
	 * Send a POST request to another server
	 *
	 * @param	String			$host			Host name
	 * @param	String			$getQuery		Get query params. Ex: index.php?foo=bar
	 * @param	Array			$data			Data to send over post
	 * @param	String			$dataVar		Name of the data var
	 * @param	Array			$headers		Extra header to send with the request
	 * @param	Integer			$port			Server port
	 * @param	Integer			$timeout		Timeout
	 * @return	Array
	 * @throws	TodoyuException
	 */
	public static function sendPostRequest($host, $getQuery = '', array $data = array(), $dataVar = 'data', array $headers = array(), $port = 80, $timeout = 10) {
		TodoyuLogger::logCore('Open connection to host ' . $host);

			// Disable error handler
		TodoyuErrorHandler::setActive(false);
			// Open socket
		$sock = fsockopen("tcp://$host", $port, $errno, $errstr, $timeout);
			// Enable error handler
		TodoyuErrorHandler::setActive(true);

			// Check whether connection was successful
		if( ! $sock ) {
			throw new TodoyuException('Cannot connect to host "' . $host . '" (' . $errno . ', ' . $errstr . ')');
		}

			// Encode data
		$postData	= $dataVar . '=' . urlencode(json_encode($data));

		TodoyuLogger::logCore('Start sending data to host ' . $host);

			// Send HTTP headers
		fwrite($sock, "POST /$getQuery HTTP/1.0\r\n");
		fwrite($sock, "Host: $host\r\n");
		fwrite($sock, "Content-type: application/x-www-form-urlencoded\r\n");
		fwrite($sock, "Content-length: " . strlen($postData) . "\r\n");
		fwrite($sock, "Accept: */*\r\n");

			// Send extra headers
		foreach($headers as $name => $value) {
			fwrite($sock, "$name: $value\r\n");
		}

			// Send data
		fwrite($sock, "\r\n");
		fwrite($sock, "$postData\r\n");
		fwrite($sock, "\r\n");

		TodoyuLogger::logCore('Start reading response data from host ' . $host);

			// Receive data
		$content	= '';
		while( ! feof($sock) ) {
			$line		= fgets($sock, 2048);
			$content	.= $line;
		}

		fclose($sock);

		TodoyuLogger::logCore('Closed connection to host ' . $host);

			// Parse response data
		$requestParts	= explode("\r\n\r\n", $content, 2);
		$responseHeaders= TodoyuString::extractHeadersFromString($requestParts[0]);
		$responseContent= $requestParts[1];

		return array(
			'headers'	=> $responseHeaders,
			'content'	=> $responseContent
		);
	}



	/**
	 * Check whether HTTPS is enabled
	 *
	 * @return	Boolean
	 */
	public static function isSecureRequest() {
		return $_SERVER['HTTPS'] === 'on';
	}

}

?>