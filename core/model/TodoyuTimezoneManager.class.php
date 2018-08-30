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
 * Manage timezones
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuTimezoneManager {

	/**
	 * Get all timezone records
	 *
	 * @return	Array[]
	 */
	public static function getTimezones() {
		return TodoyuStaticRecords::getAllTimezones();

	}



	/**
	 * Get all timezone records with offset data
	 * Adds keys: offset, offsetFormat
	 *
	 * @return	Array[]
	 */
	public static function getTimezonesWithOffset() {
		$timezones	= self::getTimezones();

		foreach($timezones as $index => $timezone) {
			$date	= new DateTime('now', new DateTimeZone($timezone['timezone']));
			$timezones[$index]['offset'] 		= $date->getOffset();
			$timezones[$index]['offsetFormat']	= $date->format('P');
		}

		return $timezones;
	}



	/**
	 * Get timezones grouped by region
	 *
	 * @param	Boolean		$withOffset
	 * @return	Array[]
	 */
	public static function getTimezonesGrouped($withOffset = false) {
		$timezones	= $withOffset ? self::getTimezonesWithOffset() : self::getTimezones();
		$grouped	= array();

		foreach($timezones as $timezone) {
			list($region, $country)	= explode('/', $timezone['timezone']);

			$grouped[$region][] = $timezone;
		}

		return $grouped;
	}



	/**
	 * Get grouped timezone options
	 *
	 * @param	Boolean		$withOffset
	 * @param	Boolean		$useIdAsValue		Should be the ID of the name used as value
	 * @return	Array[]
	 */
	public static function getTimezonesGroupedOptions($withOffset = false, $useIdAsValue = false) {
		$groupedTimezones	= self::getTimezonesGrouped($withOffset);
		$groupedOptions		= array();
		$valueField			= $useIdAsValue ? 'id' : 'timezone';

		foreach($groupedTimezones as $region => $regionTimezones) {
			foreach($regionTimezones as $timezone) {
				$groupedOptions[$region][] = array(
					'value'	=> $timezone[$valueField],
					'label'	=> $timezone['timezone'] . ($withOffset ? ' (UTC ' . $timezone['offsetFormat'] . ') ' : ''),
				);
			}
		}

		return $groupedOptions;
	}
}

?>