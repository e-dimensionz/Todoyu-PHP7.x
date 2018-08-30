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
 * Send HTTP headers
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuHeader {

	/**
	 * Current content type
	 *
	 * @var	String
	 */
	private static $type = 'HTML';

	/**
	 * Status flag if headers already have been sent (with the Header class)
	 *
	 * @var	Boolean
	 */
	private static $sent = false;



	/**
	 * Set sent status
	 */
	public static function setSent() {
		self::$sent = true;
	}



	/**
	 * Check if headers already have been sent
	 *
	 * @return	Boolean
	 */
	public static function isSent() {
		return self::$sent;
	}



	/**
	 * Set type as currently sent content type
	 *
	 * @param	String	$type
	 */
	public static function setType($type) {
		self::$type = $type;
	}



	/**
	 * Get currently set content type
	 *
	 * @return	String
	 */
	public static function getType() {
		return self::$type;
	}



	/**
	 * Send headers for download
	 *
	 * @param	String		$mimeType
	 * @param	String		$filename
	 * @param	Integer		$fileSize
	 * @param	Integer		$fileModTime
	 * @param	Boolean		$asAttachment
	 */
	public static function sendDownloadHeaders($mimeType, $filename, $fileSize, $fileModTime = null, $asAttachment = true) {
		$fileSize	= (int) $fileSize;

		self::sendHeader('Content-Description', 'File Transfer');
		self::sendHeader('Content-Type', $mimeType);
		self::sendHeader('Content-Transfer-Encoding', 'binary');
		self::sendHeader('Expires', date('r', NOW + 600));
		self::sendHeader('Cache-Control', 'must-revalidate');	// Attention: no-cache will not work with https + internet explorer!
		self::sendHeader('Pragma', 'public');

		if( $asAttachment ) {
			self::sendHeader('Content-disposition', 'attachment; filename="' . addslashes($filename) . '"');
		}

			// Chrome seems to have problems with content length
			// (temporary) workaround
		if( ! TodoyuBrowserInfo::isChrome() ) {
			self::sendHeader('Content-length', $fileSize);
		}

		if( ! is_null($fileModTime) ) {
			self::sendHeader('Last-Modified', date('r', $fileModTime));
		}
	}



	/**
	 * Send HTML header
	 */
	public static function sendTypeHTML() {
		self::setType('HTML');
		self::sendContentType('text/html');
	}



	/**
	 * Send JSON header
	 */
	public static function sendTypeJSON() {
		self::setType('JSON');
		self::sendContentType('application/json');
	}



	/**
	 * Send XML header
	 */
	public static function sendTypeXML() {
		self::setType('XML');
		self::sendContentType('text/xml');
	}



	/**
	 * Send plain text header
	 */
	public static function sendTypeText() {
		self::setType('TEXT');
		self::sendContentType('text/plain');
	}



	/**
	 * Send content type header
	 *
	 * @param	String		$type
	 * @param	String		$charset
	 */
	public static function sendContentType($type, $charset = 'utf-8') {
		self::sendHeader('Content-type', "$type;charset=$charset");
	}



	/**
	 * Send headers to prevent any caching
	 */
	public static function sendNoCacheHeaders() {
		self::sendHeader('Cache-Control', 'no-cache, must-revalidate');
		self::sendHeader('Expires', date('r', 0));
		self::sendHeader('Pragma', 'no-cache');
	}



	/**
	 * Send a hash header
	 *
	 * @param	String		$hash
	 */
	public static function sendHashHeader($hash) {
		self::sendTodoyuHeader('Hash', $hash);
	}



	/**
	 * Send a header prefixed with 'Todoyu-'
	 * The value is automatically JSON encoded to make sure it's an ASCII string
	 *
	 * @param	String		$name
	 * @param	String		$value
	 */
	public static function sendTodoyuHeader($name, $value) {
		self::sendHeader('Todoyu-' . $name, json_encode($value));
	}



	/**
	 * Send HTTP header
	 *
	 * @param	Integer		$code
	 */
	public static function sendHTTPHeader($code) {
		header('HTTP/1.0 ' . $code);
	}



	/**
	 * Send HTTP error header
	 *
	 */
	public static function sendHTTPErrorHeader() {
		self::sendHTTPHeader(503);
	}



	/**
	 * Send Todoyu error header, which marks the current response as failed
	 * This means mostly a submission (form value) was not valid and the form
	 * has to be displayed again
	 *
	 * Can automatically be checked by js: var hasError = response.hasTodoyuError()
	 */
	public static function sendTodoyuErrorHeader() {
		self::sendTodoyuHeader('error', 1);
	}



	/**
	 * Send todoyu error header and error message
	 * @param	String		$message
	 */
	public static function sendTodoyuError($message) {
		self::sendTodoyuErrorHeader();
		self::sendTodoyuHeader('errorMessage', $message);
	}



	/**
	 * Send a no access header to inform the script that the request has been canceled
	 */
	public static function sendNoAccessHeader() {
		self::sendTodoyuHeader('noAccess', 1);
	}



	/**
	 * Send custom header
	 *
	 * @param	String		$name
	 * @param	String		$value
	 */
	public static function sendHeader($name, $value) {
		header($name . ': ' . $value);
	}



	/**
	 * Redirect to a todoyu page
	 *
	 * @param	String		$ext
	 * @param	String		$controller
	 * @param	Array		$addParams
	 */
	public static function redirect($ext, $controller = 'ext', array $addParams = array()) {
		$params		= array(
			'ext'		=> $ext,
			'controller'=> $controller
		);
		$params	= array_merge($params, $addParams);

		$url	= TodoyuString::buildUrl($params, '', true);

		self::location($url, true);
	}



	/**
	 * Send new location header to browser
	 *
	 * @param	String		$url
	 * @param	Boolean		$exit
	 */
	public static function location($url, $exit = true) {
		self::sendHeader('Location', $url);

		if( $exit ) {
			exit();
		}
	}



	/**
	 * Reload request with the same arguments
	 * All arguments will be converted to GET arguments
	 */
	public static function reload() {
		$remove			= array('ext', 'controller');
		$requestParams	= TodoyuRequest::getAll();
		$redirectParams	= array_diff($requestParams, $remove);

		if( empty($requestParams['ext']) ) {
			$requestParams['ext'] = Todoyu::$CONFIG['FE']['DEFAULT']['ext'];
		}
		if( empty($requestParams['controller']) ) {
			$requestParams['controller'] = Todoyu::$CONFIG['FE']['DEFAULT']['controller'];
		}

		self::redirect($requestParams['ext'], $requestParams['controller'], $redirectParams);
	}

}

?>