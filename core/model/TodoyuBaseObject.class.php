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

//require_once( PATH_LIB . '/php/dwoo/Dwoo/IDataProvider.php');

/**
 * Add basic and lot used access functions for internal member vars
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
class TodoyuBaseObject implements ArrayAccess, Dwoo_IDataProvider {

	/**
	 * Record data (database row)
	 *
	 * @var	Array
	 */
	protected $data = array();

	/**
	 * Cache for extra data, so they have to be fetched only once
	 *
	 * @var	Array
	 */
	protected $cache = array();

	/**
	 * Table of the record
	 *
	 * @var	String
	 */
	protected $table;




	/**
	 * Initialize object. Only load data from database, when $idRecord is not zero
	 *
	 * @param	Integer		$idRecord
	 * @param	String		$table
	 */
	protected function __construct($idRecord, $table) {
		$idRecord	= (int) $idRecord;
		$this->table= trim(strtolower($table));

		if( $idRecord > 0 ) {
			$recordData	= Todoyu::db()->getRecord($table, $idRecord);
			if( is_array($recordData) ) {
				$this->data = $recordData;
			} else {
				TodoyuLogger::logError('Record not found! ID: "' . $idRecord . '", TABLE: "' . $table . '"');
//				die('<pre>'. print_r(debug_backtrace(false),true)) . '</pre>';
			}
		} else {
			//TodoyuLogger::logNotice('Record with ID 0 created (new object or missing data?) Table: ' . $table);
		}
	}



	/**
	 * Fallback for not defined getters. If a getter for a member variable is not defined,
	 * this function will be called and try to get the value from $this->data
	 * This is only for getters, so parameters are ignored
	 *
	 * @deprecated
	 * @notice	Define your own getters
	 * @param	String		$methodName
	 * @param	Array		$params
	 * @return	String
	 */
	public function __call($methodName, $params) {
		$methodName	= strtolower($methodName);
		$dataKey	= str_replace('get', '', $methodName);

		if( substr($methodName, 0, 3) === 'get' && isset($this->data[$dataKey]) ) {
			return $this->get($dataKey);
		} else {
			TodoyuLogger::logNotice('Data "' . $dataKey . '" not found in ' . get_class($this) . ' (ID:' . $this->data['id'] . ')', $this->data);
			return '';
		}
	}



	/**
	 * Fallback for direct member access.
	 * First it checks for a getter function, if not available try to find the data in $this->data
	 *
	 * @deprecated
	 * @notice	Define your own getters
	 * @param	String		$memberName
	 * @return	String
	 */
	public function __get($memberName) {
		$dataKey	= strtolower($memberName);
		$methodName	= 'get' . $memberName;

		if( method_exists($this, $methodName) ) {
			return call_user_func(array($this, $methodName));
		} elseif( array_key_exists($dataKey, $this->data) ) {
			return $this->get($dataKey);
		} else {
			TodoyuLogger::logNotice('Data [' . $dataKey . '] not found in object [' . get_class($this) . ']');
			return '';
		}
	}



	/**
	 * Get record ID
	 *
	 * @return	Integer
	 */
	public function getID() {
		return $this->getInt('id');
	}



	/**
	 * Get data from internal record storage
	 *
	 * @param	String		$key
	 * @return	Mixed
	 */
	public function get($key) {
		return $this->data[$key] ?? null;
	}



	/**
	 * Get data as integer
	 *
	 * @param	String		$fieldName
	 * @return	Integer
	 */
	public function getInt($fieldName) {
		return intval($this->get($fieldName));
	}



	/**
	 * Check whether a 'flag' field is set
	 * Flag fields are boolean fields (tinyint(1) with 0 or 1) in the database
	 *
	 * @param	String		$flagName
	 * @return	Boolean
	 */
	public function isFlagSet($flagName) {
		return $this->getInt($flagName) === 1;
	}



	/**
	 * Set a value
	 * Sets the value only in the object, this in not persistent
	 *
	 * @param	String		$key
	 * @param	Mixed		$value
	 */
	public function set($key, $value) {
		$this->data[$key] = $value;
	}



	/**
	 * Check whether a property is set
	 *
	 * @param	String		$key
	 * @return	Boolean
	 */
	public function has($key) {
		return isset($this->data[$key]);
	}



	/**
	 * Update the object and the database
	 *
	 * @param	Array	$data
	 */
	protected function update(array $data) {
			// Update database
		TodoyuRecordManager::updateRecord($this->table, $this->getID(), $data);
			// Update internal record
		$this->data = array_merge($this->data, $data);
			// Remove record query cache
		TodoyuRecordManager::removeRecordQueryCache($this->table, $this->getID());
	}



	/**
	 * Update a single field
	 *
	 * @param	String		$fieldName
	 * @param	Mixed		$value			Scalar value
	 */
	protected function updateField($fieldName, $value) {
		$this->update(array(
			$fieldName	=> $value
		));
	}



	/**
	 * Check whether a property is not empty
	 *
	 * @param	String		$key
	 * @return	Boolean
	 */
	public function notEmpty($key) {
		return !empty($this->data[$key]);
	}



	/**
	 * Inject data.
	 * Useful if user initialized without an ID to avoid an extra request
	 *
	 * @param	Array	$data
	 */
	public function injectData(array $data = array()) {
		$this->data = $data;
	}



	/**
	 * Check if current user is creator of the record
	 *
	 * @return	Boolean
	 */
	public function isCurrentPersonCreator() {
		return $this->getPersonCreateID() === Todoyu::personid();
	}



	/**
	 * Get data array
	 *
	 * @return	Array
	 */
	public function getObjectData() {
		return $this->data;
	}



	/**
	 * Get date create
	 *
	 * @return	Integer
	 */
	public function getDateCreate() {
		return $this->getInt('date_create');
	}



	/**
	 * Get date update
	 *
	 * @return	Integer
	 */
	public function getDateUpdate() {
		return $this->getInt('date_update');
	}



	/**
	 * Check whether record was updated at least once
	 *
	 * @return	Boolean
	 */
	public function isUpdated() {
		return $this->getDateCreate() !== $this->getDateUpdate();
	}



	/**
	 * Get user ID of a specific type (create, update, assigned, etc)
	 *
	 * @param	String		$type
	 * @return	Integer
	 */
	public function getPersonID($type) {
		$dataKey = 'id_person_' . strtolower($type);

		return $this->getInt($dataKey);
	}



	/**
	 * Get user of a specific type (create, update, assigned, etc)
	 *
	 * @param	String		$type
	 * @return	TodoyuContactPerson
	 */
	public function getPerson($type = null) {
		$idPerson = $this->getPersonID($type);

		return TodoyuContactPersonManager::getPerson($idPerson);
	}



	/**
	 * Get ID of the creator person
	 *
	 * @return	Integer
	 */
	public function getPersonCreateID() {
		return $this->getPersonID('create');
	}



	/**
	 * Get the creator person
	 *
	 * @return	TodoyuContactPerson
	 */
	public function getPersonCreate() {
		return $this->getPerson('create');
	}



	/**
	 *
	 *
	 * @param	String	$key
	 * @return	Boolean
	 */
	protected function isInCache($key) {
		return isset($this->cache[$key]);
	}



	/**
	 * Get item from cache
	 *
	 * @param	String	$key
	 * @return	Mixed
	 */
	protected function getCacheItem($key) {
		return $this->cache[$key];
	}



	/**
	 * Add item to cache
	 *
	 * @param	String	$key
	 * @param	Mixed	$item
	 */
	protected function addToCache($key, $item) {
		$this->cache[$key] = $item;
	}



	/**
	 * Get data array for template rendering
	 *
	 * @return	Array
	 */
	public function getTemplateData() {
		return $this->data;
	}



	/**
	 * Checks if the record is deleted
	 *
	 * @return	Boolean
	 */
	public function isDeleted() {
		return $this->getInt('deleted') === 1;
	}



	/**
	 * Called by empty() and isset() on member variables
	 *
	 * @magic
	 * @deprecated
	 * @param	String		$memberName
	 * @return	Boolean
	 */
	public function __isset($memberName) {
		return isset($this->data[$memberName]);
	}



	/**
	 * Array access function to check if an attribute
	 * is set in the internal record storage
	 *
	 * Usage: $obj = new Obj(); isset($obj['id_person'])
	 *
	 * @magic
	 * @deprecated
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function offsetExists($name) {
		return isset($this->data[$name]);
	}



	/**
	 * Array access function to delete an attribute
	 * in the internal record storage
	 *
	 * Usage: $obj = new Obj(); unset($obj['id_person'])
	 *
	 * @magic
	 * @deprecated
	 * @param	String		$name
	 */
	public function offsetUnset($name) {
		unset($this->data[$name]);
	}



	/**
	 * Array access function to set an attribute
	 * in the internal record storage
	 *
	 * Usage: $obj = new Obj(); $obj['id_person'] = 53;
	 *
	 * @magic
	 * @deprecated
	 * @param	String		$name
	 * @param	String		$value
	 */
	public function offsetSet($name, $value) {
		$this->data[$name] = $value;
	}



	/**
	 * Array access function to get an attribute
	 * from the internal record storage
	 *
	 * Usage: $obj = new Obj(); echo $obj['id_person'];
	 *
	 * @magic
	 * @deprecated
	 * @param	String		$name
	 * @return	String
	 */
	public function offsetGet($name) {
		return $this->get($name);
	}



	/**
	 * Alias for getTemplateData to implement Dwoo_IDataProvider
	 *
	 * @return	Array
	 */
	public function getData() {
		return $this->getTemplateData();
	}
}

?>