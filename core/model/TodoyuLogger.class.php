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
 * Log information in various systems
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuLogger {

	/**
	 * Log levels
	 */
	const LEVEL_CORE	= 0;
	const LEVEL_DEBUG	= 1;
	const LEVEL_NOTICE	= 2;
	const LEVEL_ERROR	= 3;
	const LEVEL_SECURITY= 4;
	const LEVEL_FATAL	= 5;


	/**
	 * Log instance. Singleton
	 *
	 * @var	TodoyuLogger
	 */
	private static $instance = null;

	/**
	 * Classnames of registered loggers
	 *
	 * @var	Array
	 */
	private $loggerNames	= array();

	/**
	 * Logger instances of the classes registered in $this->loggerNames
	 */
	private $loggerInstances = null;

	/**
	 * Log level. Only messages with minimum this level are logged
	 * Levels are defined as constants TodoyuLogger::LEVEL_*
	 *
	 * Levels:
	 *	0: Debug Message
	 *	1: Notice
	 *	2: Logical Error
	 *	3: Security Error
	 *	4: Fatal Error
	 *
	 * @var	Integer
	 */
	private $minimumLevel	= 0;

	/**
	 * Unique key for current request. So we can group the log messages by request
	 *
	 * @var	String
	 */
	private $requestKey;

	/**
	 * Ignore this files when detecting file where logging was executed
	 *
	 * @var	Array
	 */
	private $filesSkip = array(
		'TodoyuLogger.class.php',
		'TodoyuErrorHandler.class.php',
		'TodoyuDebug.class.php',
		'Todoyu.class.php'
	);

	private $filesIgnore = array();



	/**
	 * Get the logger instance. Singleton
	 *
	 * @return	TodoyuLogger
	 */
	public static function getInstance() {
		if( is_null(self::$instance) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}



	/**
	 * Called by getInstance for Singleton Pattern
	 */
	private function __construct() {
		$this->minimumLevel = $this->getLogLevel();
		$this->requestKey	= substr(md5(microtime(true) . session_id()), 0, 10);
	}



	/**
	 * Get log level
	 *
	 * @return	Integer
	 */
	private function getLogLevel() {
		if( isset(Todoyu::$CONFIG['SYSTEM']['logLevel']) ) {
			return (int) Todoyu::$CONFIG['SYSTEM']['logLevel'];
		} else {
			return self::LEVEL_ERROR;
		}
	}



	/**
	 * Get instances of all loggers
	 * The logger objects are not created until they are used
	 *
	 * @return	Array
	 */
	private function getLoggerInstances() {
		if( is_null($this->loggerInstances) ) {
			$this->loggerInstances = array();
			foreach($this->loggerNames as $logger) {
				$className	= $logger['class'];
				$this->loggerInstances[] = new $className($logger['config']);
			}
		}

		return $this->loggerInstances;
	}



	/**
	 * Log a message. The message will be processed by all loggers
	 *
	 * @param	String		$message		Log message
	 * @param	Integer		$eventLevel			Log level of current message
	 * @param	Mixed		$data			An additional data container (for debugging)
	 * @return	Boolean		Logged event
	 */
	private function log($message, $eventLevel = 0, $data = null) {
		$backtrace	= debug_backtrace(false);
		$info		= false;
		$eventLevel	= (int) $eventLevel;

			// Check if minimum requirement for logging is set
		if( $eventLevel < $this->minimumLevel ) {
			return false;
		}

			// Find file in backtrace which is not on ignore list
		foreach($backtrace as $btElement) {
			if( ! in_array(basename($btElement['file']), $this->filesSkip) ) {
				$info = $btElement;
				break;
			}
		}

			// If no file found which is not skipped, cancel
		if( !$info ) {
			return false;
		}

			// If found file is an ignore file, cancel
		if( in_array(basename($info['file']), $this->filesIgnore) ) {
			return false;
		}

		$info['fileshort']	= TodoyuFileManager::pathWeb($info['file']);

		$loggers	= $this->getLoggerInstances();

		foreach($loggers as $logger) {
			/**
			 * @var	TodoyuLoggerIf	$logger
			 */
			$logger->log($message, $eventLevel, $data, $info, $this->requestKey);
		}

		return true;
	}



	/**
	 * Add file name which will be ignored while looking for the error
	 * position in the backtrace
	 *
	 * @param	String		$filename
	 */
	public static function addSkipFile($filename) {
		self::getInstance()->filesSkip[] = $filename;
	}



	/**
	 * Add file name where error will not be logged
	 *
	 * @param	String		$filename
	 */
	public static function addIgnoreFile($filename) {
		self::getInstance()->filesIgnore[] = $filename;
	}



	/**
	 * Add a logger class. Class is just provided as string and will be
	 * instantiated on the first use of the log
	 *
	 * @param	String		$className
	 * @param	Array		$config
	 */
	public static function addLogger($className, array $config = array()) {
		self::getInstance()->loggerNames[] = array(
			'class'	=> $className,
			'config'=> $config
		);
	}



	/**
	 * Log debug message
	 *
	 * @param	String		$message
	 * @param	Mixed		$data
	 */
	public static function logCore($message, $data = null) {
		self::getInstance()->log($message, self::LEVEL_CORE, $data);
	}



	/**
	 * Log debug message
	 *
	 * @param	String		$message
	 * @param	Mixed		$data
	 */
	public static function logDebug($message, $data = null) {
		self::getInstance()->log($message, self::LEVEL_DEBUG, $data);
	}



	/**
	 * Log notice message
	 *
	 * @param	String		$message
	 * @param	Mixed		$data
	 */
	public static function logNotice($message, $data = null) {
		self::getInstance()->log($message, self::LEVEL_NOTICE, $data);
	}



	/**
	 * Log error message
	 *
	 * @param	String		$message
	 * @param	Mixed		$data
	 */
	public static function logError($message, $data = null) {
		self::getInstance()->log($message, self::LEVEL_ERROR, $data);
	}



	/**
	 * Log security message
	 *
	 * @param	String		$message
	 * @param	Mixed		$data
	 */
	public static function logSecurity($message, $data = null) {
		self::getInstance()->log($message, self::LEVEL_SECURITY, $data);
	}



	/**
	 * Log fatal message
	 *
	 * @param	String		$message
	 * @param	Mixed		$data
	 */
	public static function logFatal($message, $data = null) {
		self::getInstance()->log($message, self::LEVEL_FATAL, $data);
	}

}

?>