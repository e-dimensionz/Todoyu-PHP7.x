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
 * Static records handling
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuStaticRecords {

	/**
	 * Locale config
	 *
	 * @var	Array
	 */
	private static $localeConfig = array(
		'country'	=> array(
			'value'		=> 'id',
			'label'		=> 'iso_alpha3',
			'locale'	=> 'core.static_country'
		),
		'country_zone' => array(
			'value'		=> 'id',
			'label'		=> 'iso_alpha3_country',
			'locale'	=> 'core.static_country_zone'
		),
		'currency'	=> array(
			'value'		=> 'id',
			'label'		=> 'code',
			'locale'	=> 'currency.static_currency'
		)
	);



	/**
	 * Get table name for type
	 *
	 * @param	String		$type
	 * @return	String
	 */
	private static function getTable($type) {
		return 'static_' . strtolower(trim($type));
	}



	/**
	 * Get static record by ID
	 *
	 * @param	String		$type
	 * @param	Integer		$idRecord
	 * @return	Array
	 */
	public static function getRecord($type, $idRecord) {
		$table		= self::getTable($type);
		$idRecord	= (int) $idRecord;

		return TodoyuRecordManager::getRecordData($table, $idRecord);
	}



	/**
	 * Get static record by field value
	 *
	 * @param	String		$type
	 * @param	String		$field
	 * @param	String		$value
	 * @return	Array
	 */
	public static function getRecordByField($type, $field, $value) {
		$conditions	= array($field => $value);
		$records	= self::getRecords($type, $conditions, '', 1);

		return $records[0];
	}



	/**
	 * Get records which match the conditions
	 *
	 * @param	String		$type
	 * @param	Array		$conditions
	 * @param	String		$order
	 * @param	String		$limit
	 * @return	Array
	 */
	public static function getRecords($type, array $conditions = array(), $order = '', $limit = '') {
		$fields	= '*';
		$table	= self::getTable($type);
		$wheres	= array();

		foreach($conditions as $fieldName => $value) {
			$wheres[]	= TodoyuSql::quoteFieldname($fieldName) . ' = ' . TodoyuSql::quote($value);
		}

		$where	= implode(' AND ', $wheres);

		return Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit);
	}



	/**
	 * Get all records of a type
	 * Can be limited by the where clause
	 *
	 * @param	String		$type
	 * @param	String		$where
	 * @param	String		$order
	 * @return	Array
	 */
	public static function getAllRecords($type, $where = '', $order = '') {
		$table	= self::getTable($type);

		return TodoyuRecordManager::getAllRecords($table, $where, $order);
	}



	/**
	 * Get a label for a static record
	 *
	 * @param	String		$type
	 * @param	String		$key
	 * @return	String
	 */
	public static function getLabel($type, $key) {
		$labelKey	= self::$localeConfig[$type]['locale'] . '.' . $key;

		return Todoyu::Label($labelKey);
	}



	/**
	 * Get options based on the static records
	 *
	 * @param	String		$type
	 * @param	Array		$conditions
	 * @param	Boolean		$sort
	 * @param	Boolean		$localize
	 * @return	Array
	 */
	public static function getRecordOptions($type, array $conditions = array(), $sort = true, $localize = true) {
		$records	= self::getRecords($type, $conditions);
		$keyLabel	= self::$localeConfig[$type]['label'];
		$keyValue	= self::$localeConfig[$type]['value'];

			// Localize record
		if( $localize ) {
			foreach($records as $index => $record) {
				$records[$index]['label'] = self::getLabel($type, $record[$keyLabel]);
			}
			$keyLabel = 'label';
		}

			// Reform the array to work as options source
		$reformConfig	= array(
				$keyValue	=> 'value',
				$keyLabel	=> 'label'
		);
		$options= TodoyuArray::reform($records, $reformConfig);

			// Sort array by label
		$options= TodoyuArray::sortByLabel($options, 'label');

		return $options;
	}



	/**
	 * Get a timezone record
	 *
	 * @param	Integer		$idTimezone
	 * @return	Array
	 */
	public static function getTimezone($idTimezone) {
		$idTimezone	= (int) $idTimezone;

		return self::getRecord('timezone', $idTimezone);
	}



	/**
	 * Get ID of a timezone
	 *
	 * @param	String		$timezone
	 * @return	Integer
	 */
	public static function getTimezoneID($timezone) {
		$field	= 'id';
		$table	= 'static_timezone';
		$where	= 'timezone = ' . TodoyuSql::quote($timezone, true);

		return (int) Todoyu::db()->getFieldValue($field, $table, $where);
	}



	/**
	 * Get all timezone records
	 *
	 * @return	Array
	 */
	public static function getAllTimezones() {
		return self::getAllRecords('timezone', '', 'timezone');
	}



	/**
	 * Get country record
	 *
	 * @param	Integer		$idCountry
	 * @return	Array
	 */
	public static function getCountry($idCountry) {
		$idCountry	= (int) $idCountry;

		return self::getRecord('country', $idCountry);
	}



	/**
	 * Get a country record by ISO number
	 *
	 * @param	Integer		$countryIsoNumber
	 * @return	Array
	 */
	public static function getCountryByISO($countryIsoNumber) {
		$countryIsoNumber	= (int) $countryIsoNumber;

		$fields	= '*';
		$table	= 'static_country';
		$where	= '	iso_num	= ' . $countryIsoNumber;

		return Todoyu::db()->getRecordByQuery($fields, $table, $where);
	}



	/**
	 * Get label (name) of given country
	 *
	 * @param	Integer		$idCountry
	 * @return	String
	 */
	public static function getCountryLabel($idCountry) {
		$idCountry	= (int) $idCountry;

		$record	= self::getCountry($idCountry);
		$alpha3	= $record['iso_alpha3'];

		return Todoyu::Label('core.static_country.' . $alpha3);
	}



	/**
	 * Get country zone records for a country
	 *
	 * @param	Integer		$idCountry
	 * @return	Array
	 */
	public static function getCountryZones($idCountry) {
		$idCountry	= (int) $idCountry;

		$countryZones	= array();

		if( $idCountry !== 0 ) {
			$country	= self::getCountry($idCountry);
			$countryIso	= (int) $country['iso_num'];

			$fields	= '	id,
						iso_alpha3_country,
						code';
			$table	= 'static_country_zone';
			$where	= 'iso_num_country = ' . $countryIso;

			$countryZones = Todoyu::db()->getArray($fields, $table, $where);
		}

		return $countryZones;
	}



	/**
	 * Get options based on the countries
	 *
	 * @return	Array
	 */
	public static function getCountryOptions() {
		return self::getRecordOptions('country');
	}



	/**
	 * Get country zone options of a country based on the country zones
	 *
	 * @param	Integer		$idCountry
	 * @return	Array
	 */
	public static function getCountryZoneOptions($idCountry) {
		$idCountry		= (int) $idCountry;
		$countryZones	= self::getCountryZones($idCountry);
		$options		= array();

		foreach($countryZones as $countryZone) {
			$options[] = array(
				'value'	=> $countryZone['id'],
				'label'	=> self::getLabel('country_zone', $countryZone['iso_alpha3_country'] . '.' . $countryZone['code'])
			);
		}

		if( sizeof($options) === 0 ) {
			$options[]	= array(
				'value'		=> 'disabled',
				'label'		=> 'contact.ext.address.noRegion',
				'disabled'	=> true,
				'class'		=> 'error'
			);
		}

		return $options;
	}



	/**
	 * Get options array for select form field element
	 *
	 * @return	Array
	 */
	public static function getCurrencyOptions() {
		return self::getRecordOptions('currency');
	}

}

?>