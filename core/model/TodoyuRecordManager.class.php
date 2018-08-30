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
 * Records manager
 * Helper functions to handle database records and prevent double code in all the manager classes
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuRecordManager {

	/**
	 * Get a record object
	 *
	 * @param	String		$className
	 * @param	Integer		$idRecord
	 * @return	TodoyuBaseObject
	 */
	public static function getRecord($className, $idRecord) {
		$idRecord	= (int) $idRecord;

		if( class_exists($className, true) ) {
			$cacheKey	= self::makeClassKey($className, $idRecord);

			if( TodoyuCache::isIn($cacheKey) ) {
				$object = TodoyuCache::get($cacheKey);
			} else {
				$object = new $className($idRecord);
				TodoyuCache::set($cacheKey, $object);
			}

			return $object;
		} else {
			TodoyuLogger::logError('Record class not found: "' . $className . '", Record ID: ' . $idRecord);
			return false;
		}
	}



	/**
	 * Get a list of records of a specific class
	 *
	 * @param	String		$className
	 * @param	Array		$recordIDs
	 * @return	TodoyuBaseObject[]		List of objects
	 */
	public static function getRecordList($className, array $recordIDs) {
		$records	= array();

		foreach($recordIDs as $idRecord) {
			$records[$idRecord] = self::getRecord($className, $idRecord);
		}

		return $records;
	}



	/**
	 * Remove a record from cache
	 *
	 * @param	String		$className
	 * @param	Integer		$idRecord
	 */
	public static function removeRecordCache($className, $idRecord) {
		$idRecord	= (int) $idRecord;
		$cacheKey	= self::makeClassKey($className, $idRecord);

		TodoyuCache::remove($cacheKey);

			// Call cache cleanup hooks
		TodoyuHookManager::callHook('core', 'removeCacheRecord', array($className, $idRecord, $cacheKey));
		TodoyuHookManager::callHook('core', 'removeCacheRecord' . $className, array($idRecord, $cacheKey));
	}



	/**
	 * Remove a record query from the cache. This is necessary to force
	 * a new created object to load the data again from the database
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 */
	public static function removeRecordQueryCache($table, $idRecord) {
		$idRecord	= (int) $idRecord;
		$cacheKey	= self::makeRecordQueryKey($table, $idRecord);

		TodoyuCache::remove($cacheKey);

			// Call cache cleanup hooks
		TodoyuHookManager::callHook('core', 'removeCacheQuery', array($table, $idRecord, $cacheKey));
	}



	/**
	 * Make a cache key for a record query based on the table and the record ID
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	String
	 */
	public static function makeRecordQueryKey($table, $idRecord) {
		return $table . ':' . (int) $idRecord;
	}



	/**
	 * Make a cache key for a record class based on the classname and the record ID
	 *
	 * @param	String		$className
	 * @param	Integer		$idRecord
	 * @return	String		Cache key
	 */
	public static function makeClassKey($className, $idRecord) {
		return $className . ':' . (int) $idRecord;
	}



	/**
	 * Get all records of given type
	 *
	 * @param	String	$table
	 * @param	String	$where
	 * @param	String	$order
	 * @return	Array
	 */
	public static function getAllRecords($table, $where = 'deleted = 0', $order = 'title') {
		return Todoyu::db()->getArray('*', $table, $where, '', $order);
	}



	/**
	 * Get record data as array
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Array
	 * @deprecated
	 * @todo	Switch to objects, instead using flat arrays
	 */
	public static function getRecordData($table, $idRecord) {
		$idRecord	= (int) $idRecord;

		return Todoyu::db()->getRecord($table, $idRecord);
	}



	/**
	 * Save a record
	 *
	 * @param	String		$table
	 * @param	Array		$data
	 * @param	Array		$noQuoteFields
	 * @return	Integer
	 */
	public static function saveRecord($table, array $data, array $noQuoteFields = array()) {
		$idRecord	= intval($data['id']);

		if( $idRecord === 0 ) {
			$idRecord	= self::addRecord($table, $data, $noQuoteFields);
		} else {
			self::updateRecord($table, $idRecord, $data, $noQuoteFields);
		}

		return $idRecord;
	}



	/**
	 * Add a record to database
	 * Set date_create and id_person_create
	 *
	 * @param	String		$table
	 * @param	Array		$data
	 * @param	Array		$noQuoteFields
	 * @return	Integer		record ID
	 */
	public static function addRecord($table, array $data, array $noQuoteFields = array()) {
		unset($data['id']);

		$data['date_create']		= NOW;
		$data['date_update']		= NOW;
		$data['id_person_create']	= Todoyu::personid();

		$idRecord	=  Todoyu::db()->addRecord($table, $data, $noQuoteFields);

		TodoyuHookManager::callHook('core', 'record.add', array($table, $idRecord));

		return $idRecord;
	}



	/**
	 * Update a record in the database
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @param	Array		$data
	 * @param	Array		$noQuoteFields
	 * @return	Boolean
	 */
	public static function updateRecord($table, $idRecord, array $data, array $noQuoteFields = array()) {
		$idRecord	= (int) $idRecord;

		unset($data['id']);
		unset($data['date_create']);
		unset($data['id_person_create']);

		$data['date_update'] = NOW;

		return self::updateRecordRaw($table, $idRecord, $data, $noQuoteFields);
	}



	/**
	 * Update a record in the database without any automatic cleanup
	 *
	 * @internal
	 * @note	Use updateRecord() method, except you have very special requirements!
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @param	Array		$data
	 * @param	Array		$noQuoteFields
	 * @return	Boolean
	 */
	public static function updateRecordRaw($table, $idRecord, array $data, array $noQuoteFields = array()) {
		$idRecord	= (int) $idRecord;

			// Remove from cache
		self::removeRecordQueryCache($table, $idRecord);

		$success	= Todoyu::db()->updateRecord($table, $idRecord, $data, $noQuoteFields);

		TodoyuHookManager::callHook('core', 'record.update', array($table, $idRecord, $data));

		return $success;
	}



	/**
	 * Update multiple records in the database
	 *
	 * @param	String		$table
	 * @param	String		$where
	 * @param	Array		$data
	 * @param	Array		$noQuoteFields
	 * @return	Integer		Number of updated records
	 */
	public static function updateRecords($table, $where, array $data, array $noQuoteFields = array()) {
		unset($data['id']);
		unset($data['date_create']);
		unset($data['id_person_create']);

		$data['date_update'] = NOW;

		return Todoyu::db()->doUpdate($table, $where, $data, $noQuoteFields);
	}



	/**
	 * Delete a record (set deleted flag)
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Boolean
	 */
	public static function deleteRecord($table, $idRecord) {
		$idRecord	= (int) $idRecord;
		$data		= array(
			'deleted'	=> 1
		);

		$result	= self::updateRecord($table, $idRecord, $data);

		TodoyuHookManager::callHook('core', 'record.delete', array($table, $idRecord));

		return $result;
	}



	/**
	 * Delete multiple records at once
	 *
	 * @param	String		$table
	 * @param	Array		$recordIDs
	 * @return	Integer		Number of deleted records
	 */
	public static function deleteRecordsByID($table, array $recordIDs) {
		$recordIDs	= TodoyuArray::intval($recordIDs);

		if( sizeof($recordIDs) > 0 ) {
			$where	= 'id IN(' . implode(',', $recordIDs) . ')';
			$data	= array(
				'deleted'	=> 1
			);

			return self::updateRecords($table, $where, $data);
		} else {
			return 0;
		}
	}



	/**
	 * Delete records by where statement (records are just set deleted)
	 *
	 * @param	String		$table
	 * @param	String		$where
	 * @return	Integer
	 */
	public static function deleteRecords($table, $where) {
		$data	= array(
			'deleted'	=> 1
		);

		return self::updateRecords($table, $where, $data);
	}



	/**
	 * Check if a record exists
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Boolean
	 */
	public static function isRecord($table, $idRecord) {
		$idRecord	= (int) $idRecord;

		return Todoyu::db()->isRecord($table, $idRecord);
	}

}

?>