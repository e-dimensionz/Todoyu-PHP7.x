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
 * Global error handler
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuErrorHandler {

	/**
	 * Flag. If true, the default PHP error handler will be called after the custom one
	 *
	 * @var	Boolean
	 */
	private static $ignoreErros = array(E_NOTICE, E_STRICT, E_DEPRECATED);


	/**
	 * Only handle error when true
	 * Useful to suppress warnings. Be careful with this!
	 */
	private static $active	= true;



	/**
	 * Handler for TodoyuDbException. Print well formatted error information
	 * in the current output format
	 *
	 * @param	TodoyuDbException		$exception
	 */
	public static function handleTodoyuDbException(TodoyuDbException $exception) {
		if( TodoyuDebug::isActive() ) {
			ob_end_clean();

				// Send HTTP error header
			TodoyuHeader::sendHTTPErrorHeader();

				// Send own error header
			self::sendPhpErrorHeader('Database error: ' . $exception->getMessage());

			$type = TodoyuHeader::getType();

			if( TodoyuRequest::isAjaxRequest() || $type === 'TEXT' ) {
				echo $exception->getErrorAsPlain();
			} elseif( $type === 'JSON' ) {
				echo $exception->getErrorAsJson();
			} else {
				echo $exception->getErrorAsHtml(true);
			}

			exit();
		} else {
			TodoyuLogger::logError('Database error backtrace: ' . print_r(debug_backtrace(), true));
			self::endScriptClean('Database error!');
		}
	}



	/**
	 * Handle normal PHP errors. Disabled at the moment!
	 *
	 * @todo	Decide which errors are reported to the log
	 * @param	Integer		$errorno
	 * @param	String		$errorstr
	 * @param	String		$file
	 * @param	Integer		$line
	 * @param	Array		$context
	 * @return	Boolean
	 */
	public static function handleError($errorno, $errorstr, $file, $line, $context=null) {
		if( self::$active !== true ) {
			return true;
		}

			// If not a notice, log it
		if( ! in_array($errorno, self::$ignoreErros) ) {
			TodoyuLogger::logError('PHP ERROR: [' . $errorno . '] ' . $errorstr);
				// Send HTTP error header
			TodoyuHeader::sendHTTPErrorHeader();

			$info	= TodoyuFileManager::pathWeb($file) . ' : ' . $line;

			self::sendPhpErrorHeader('PHP ERROR: ' . $errorstr . ' # ' . $info);
		}

			// If debugging, call normal error handler to display the error
		if( TodoyuDebug::isActive() ) {
			return false;
		} else {
			return true;
		}
	}



	/**
	 * Clean up and die
	 *
	 * @param	String	$message
	 */
	public static function endScriptClean($message) {
		ob_clean();

			// Send HTTP error header
		TodoyuHeader::sendHTTPErrorHeader();

		TodoyuHeader::sendTypeText();
		die('ERROR: ' . $message);
	}



	/**
	 * Render simple error message
	 *
	 * @param	String		$title			Error title
	 * @param	String		$message		Error message
	 * @return	String
	 */
	public static function renderError($title, $message) {
		$tmpl	= 'core/view/error.tmpl';
		$data	= array(
			'title'		=> $title,
			'message'	=> $message
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Send a PHP error header
	 *
	 * @param	String		$errorMessage
	 */
	public static function sendPhpErrorHeader($errorMessage) {
		TodoyuHeader::sendTodoyuHeader('Php-Error', $errorMessage);
	}



	/**
	 * Enable/disable error handling. If disabled, all errors are ignored
	 *
	 * @param	Boolean		$active
	 */
	public static function setActive($active = true) {
		self::$active = (boolean)$active;
	}

}

?>