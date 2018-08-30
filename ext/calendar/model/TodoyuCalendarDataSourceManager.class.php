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
 * Manage data sources which provide events
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarDataSourceManager {

	/**
	 * Registered configurations for data sources
	 *
	 * @var	Array[]
	 */
	private static $dataSourceConfigs	= array();


	/**
	 * Add a new data source
	 *
	 * @param	String		$name
	 * @param	String		$className
	 * @param	Array		$config
	 */
	public static function addDataSource($name, $className, array $config = array()) {
		self::$dataSourceConfigs[$name]	= array(
			'name'		=> $name,
			'className'	=> $className,
			'config'	=> $config
		);
	}



	/**
	 * Get names of all data sources
	 *
	 * @return	String[]
	 */
	public static function getDataSourceNames() {
		return array_keys(self::$dataSourceConfigs);
	}



	/**
	 * Get a data source
	 *
	 * @param	String			$name
	 * @param	TodoyuDayRange	$range
	 * @param	Array			$filters
	 * @return	TodoyuCalendarDataSource|False
	 */
	public static function getDataSource($name, TodoyuDayRange $range, array $filters = array()) {
		if( isset(self::$dataSourceConfigs[$name]) ) {
			$className	= self::getDataSourceClassName($name);
			$config		= self::getDataSourceConfig($name);
			$dataSource	= new $className($config, $range, $filters);
		} else {
			$dataSource	= false;
		}

		return $dataSource;
	}



	/**
	 * Get class name of data source
	 *
	 * @param	String		$name
	 * @return	String
	 */
	protected static function getDataSourceClassName($name) {
		return self::$dataSourceConfigs[$name]['className'];
	}



	/**
	 * Get config of data source
	 *
	 * @param	String		$name
	 * @return	Array
	 */
	protected function getDataSourceConfig($name) {
		return TodoyuArray::assure(self::$dataSourceConfigs[$name]['config']);
	}



	/**
	 * Get event from all data sources
	 *
	 * @param	TodoyuDayRange	$range
	 * @param	Array			$filters
	 * @return	TodoyuCalendarEvent[]
	 */
	public static function getEvents(TodoyuDayRange $range, array $filters = array()) {
		$dataSourceNames= self::getDataSourceNames();
		$events	= array();

		foreach($dataSourceNames as $dataSourceName) {
			$dataSourceEvents	= self::getDataSourceEvents($dataSourceName, $range, $filters);
			$events				= array_merge($events, $dataSourceEvents);
		}

		return $events;
	}



	/**
	 * Get events of a data source
	 *
	 * @param	String			$dataSourceName
	 * @param	TodoyuDayRange	$range
	 * @param	Array			$filters
	 * @return	TodoyuCalendarEvent[]
	 */
	public static function getDataSourceEvents($dataSourceName, TodoyuDayRange $range, array $filters = array()) {
		return self::getDataSource($dataSourceName, $range, $filters)->getEvents();
	}



	/**
	 * Get amount of events provided by the data source
	 *
	 * @param	String			$dataSourceName
	 * @param	TodoyuDayRange	$range
	 * @param	Array			$filters
	 * @return	Integer
	 */
	public static function getDataSourceEventCount($dataSourceName, TodoyuDayRange $range, array $filters = array()) {
		return self::getDataSource($dataSourceName, $range, $filters)->getEventCount();
	}



	/**
	 * Get event object of data source
	 *
	 * @param	String		$dataSourceName		Name of the data source
	 * @param	Integer		$idEvent
	 * @param	Array		$params
	 * @return	TodoyuCalendarEvent
	 */
	public static function getEvent($dataSourceName, $idEvent, array $params = array()) {
		$className	= self::getDataSourceClassName($dataSourceName);

		return call_user_func(array($className, 'getEvent'), $idEvent, $params);
	}

}

?>