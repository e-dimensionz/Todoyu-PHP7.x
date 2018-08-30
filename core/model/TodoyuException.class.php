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
 * Todoyu exception
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuException extends Exception {

	/**
	 * Log all exceptions
	 *
	 * @param	String			$message
	 * @param	Integer			$code
	 * @param	Exception|null	$previous
	 */
	public function __construct($message, $code = 0, $previous = null) {
		TodoyuLogger::logError('Exception: ' . $message);

		if( version_compare(PHP_VERSION, '5.3.0') === -1 ) {
			parent::__construct($message, $code);
		} else {
			parent::__construct($message, $code, $previous);
		}
	}

}

?>