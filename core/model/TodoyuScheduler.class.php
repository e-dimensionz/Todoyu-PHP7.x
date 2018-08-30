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
 * Run scheduled task in defined intervals
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuScheduler {

	/**
	 * Table for job log
	 */
	const TABLE = 'system_scheduler';

	/**
	 * Registered jobs
	 *
	 * @var	Array
	 */
	private static $jobs = array();

	/**
	 * Cached last execution dates for classes
	 *
	 * @var	Array
	 */
	private static $lastExecutionDates;

	/**
	 * Path to block file
	 *
	 * @var	String
	 */
	private static $blockFile	= 'cache/scheduler';

	/**
	 * Time to wait until block file is ignored
	 *
	 * @var	Integer		Minutes
	 */
	private static $blockTime	= 5;



	/**
	 * Execute all due jobs
	 *
	 * @return Boolean|Void
	 */
	public static function run() {
			// Prevent executing if a recent block file exists (means scheduler still running)
		if( self::hasRecentBlockFile() ) {
			return false;
		}

		TodoyuCli::setCliMode();

			// Check for forced class name
		$jobClassName	= $_SERVER['argv'][1] ? $_SERVER['argv'][1] : false;

			// Show help
		if( $jobClassName === 'help' ) {
			self::printUsageInfoForConsole();
			exit();
		}

			// Create block file for this execution
		self::createBlockFile();

			// Execute the jobs
		self::executeJobs($jobClassName);

			// Remove block file
		self::removeBlockFile();

		return true;
	}


	/**
	 * Echos schedular-usage
	 */
	private static function printUsageInfoForConsole() {
		echo TodoyuFileManager::getFileContent('core/view/scheduler-usage.tmpl');
	}



	/**
	 * Execute the given job or all due jobs if no job is given
	 *
	 * @param	String|Boolean		$jobClassName			False or name of a specific job
	 */
	private static function executeJobs($jobClassName = false) {
			// Cache last execution dates for all jobs
		self::cacheLastExecutionDates();

		if( !$jobClassName ) {
				// Get all due jobs and execute them
			$jobs	= self::getDueJobs();
		} else {
				// Set fixed job
			$jobs	= array(
				$jobClassName => self::getJob($jobClassName)
			);
		}

		foreach($jobs as $jobConfig) {
			if( class_exists($jobConfig['class'], true) ) {
				$options	= TodoyuArray::assure($jobConfig['options']);
				$frequency	= (int) $jobConfig['crontime'];

				try {
					/**
					 * @var	TodoyuSchedulerJob	$job
					 */
					$job	= new $jobConfig['class']($options, $frequency);

					$success= $job->execute();
					$message= '';
				} catch(Exception $e) {
					$success= false;
					$message= $e->getMessage();
				}

				self::logExecution($jobConfig['class'], $success, $message);
			}
		}
	}



	/**
	 * Add a job. Will be executed when it's due
	 *
	 * @param	String			$className		Class which implements the job
	 * @param	String|Integer	$cronTime		Crontab syntax or offset in minutes
	 * @param	Array			$options		Job options
	 */
	public static function addJob($className, $cronTime, array $options = array()) {
		self::$jobs[$className] = array(
			'class'		=> $className,
			'crontime'	=> $cronTime,
			'options'	=> $options
		);
	}



	/**
	 * Get a job by class name
	 *
	 * @param	String		$className
	 * @return	Array
	 */
	private static function getJob($className) {
		return self::$jobs[$className];
	}



	/**
	 * Create a block file to prevent parallel executions
	 *
	 */
	private static function createBlockFile() {
		TodoyuFileManager::touch(self::$blockFile);
	}



	/**
	 * Remove block file
	 */
	private static function removeBlockFile() {
		TodoyuFileManager::deleteFile(self::$blockFile);
	}



	/**
	 * Check whether a recent block file exists
	 * When the file exists, it means the scheduler is still running or crashed
	 *
	 * @return	Boolean
	 */
	private static function hasRecentBlockFile() {
		$path	= TodoyuFileManager::pathAbsolute(self::$blockFile);

		if( ! file_exists($path) ) {
			return false;
		}

		$time	= filemtime($path);

		return $time + self::$blockTime * 60 > NOW;
	}



	/**
	 * Get all due jobs
	 *
	 * @return	Array
	 */
	public static function getDueJobs() {
		$dueJobs	= array();

		foreach(self::$jobs as $jobConfig) {
			if( self::isJobDue($jobConfig['class'], $jobConfig['crontime']) ) {
				$dueJobs[] = $jobConfig;
			}
		}

		return $dueJobs;
	}


	/**
	 * Check whether job is due
	 * Compare interval with last execution and current time
	 *
	 * @param	String			$className
	 * @param	String|Integer	$cronTime
	 * @return	Boolean
	 */
	private static function isJobDue($className, $cronTime) {
			// Shortcut for forced execution
		if( $cronTime === 0 ) {
			return true;
		}

		$lastExecutionDate	= self::getLastExecutionDate($className);

		if( $lastExecutionDate === 0 ) {
			return true;
		}

		if( is_numeric($cronTime) ) {
			$seconds	= ((int) $cronTime) * 60;

			return $lastExecutionDate + $seconds < NOW;
		} else {
			return self::isJobDueByCrontime($cronTime, $lastExecutionDate);
		}
	}



	/**
	 * Check whether a job is due by Crontab syntax
	 *
	 * @todo	Implement
	 * @param	String		$cronTime
	 * @param	Integer		$lastExecutionDate
	 * @return	Boolean
	 */
	private static function isJobDueByCrontime($cronTime, $lastExecutionDate) {
		return true;
	}



	/**
	 * Get last execution log record
	 *
	 * @param	String		$className
	 * @return	Array
	 */
	private static function getLastExecution($className) {
		$fields	= '*';
		$where	= 'class = ' . TodoyuSql::quote($className, true);
		$order	= 'date_execute DESC';

		return Todoyu::db()->getRecordByQuery($fields, self::TABLE, $where, '', $order);
	}



	/**
	 * Get date of last execution
	 *
	 * @param	String		$className
	 * @return	Integer		Timestamp
	 */
	private static function getLastExecutionDate($className) {
		return (int) self::$lastExecutionDates[$className];
	}



	/**
	 * Cache last execution dates of all jobs which are registered
	 */
	private static function cacheLastExecutionDates() {
		if( is_null(self::$lastExecutionDates) ) {
			$classes	= self::getJobClasses();

			self::$lastExecutionDates = array();

			if( sizeof($classes) > 0 ) {
				$fields	= '	MAX(date_execute) as date_execute,
							class';
				$group	= '	class';

				$executions	= Todoyu::db()->getArray($fields, self::TABLE, '', $group);

				foreach($executions as $execution) {
					self::$lastExecutionDates[$execution['class']] = $execution['date_execute'];
				}
			}
		}
	}



	/**
	 * Get all class names of registered jobs
	 *
	 * @return	Array
	 */
	private static function getJobClasses() {
		return TodoyuArray::getColumn(self::$jobs, 'class');
	}



	/**
	 * Log an execution
	 *
	 * @param	String		$className
	 * @param	Boolean		$success
	 * @param	String		$message
	 * @return	Integer		Log ID
	 */
	private static function logExecution($className, $success = true, $message = '') {
		$data	= array(
			'date_execute'	=> NOW,
			'class'			=> $className,
			'is_success'	=> $success ? 1 : 0,
			'message'		=> $message
		);

		return Todoyu::db()->addRecord(self::TABLE, $data);
	}

}

?>