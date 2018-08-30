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
 * FirePhp/FireBug Logger
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuLoggerFirePhp implements TodoyuLoggerIf {

	/**
	 * Initialize logger
	 *
	 * @param	Array	$config
	 */
	public function __construct(array $config) {
		TodoyuLogger::addIgnoreFile(basename(__FILE__));
	}



	/**
	 * Write log message in firebug
	 *
	 * @param	String		$message
	 * @param	Integer		$level
	 * @param	Mixed		$data
	 * @param	Array		$info
	 * @param	String		$requestKey
	 */
	public function log($message, $level, $data, $info, $requestKey) {
		$title	= '[' . $info['fileshort'] . ':' . $info['line'] . ']';
		$text	= $message . '  [' . $level . ']';

		try {

			switch($level) {
				case TodoyuLogger::LEVEL_FATAL:
				case TodoyuLogger::LEVEL_ERROR:
					TodoyuDebug::firePhp()->error($text, $title);
					break;


				case TodoyuLogger::LEVEL_SECURITY:
					TodoyuDebug::firePhp()->warn($text, $title);
					break;


				case TodoyuLogger::LEVEL_NOTICE:
					TodoyuDebug::firePhp()->info($text, $title);
					break;


				case TodoyuLogger::LEVEL_DEBUG:
				default:
					TodoyuDebug::firePhp()->log($text, $title);
					break;
			}

			if( ! empty($data) ) {
				TodoyuDebug::firePhp()->log($data, '#');
//				TodoyuDebug::firePhp()->log(debug_backtrace(), '#');
			}
		} catch(Exception $e) {
//			echo '<strong>PROBLEM WITH FIREBUG</strong><br />' . $e->getMessage();
		}
	}

}

?>