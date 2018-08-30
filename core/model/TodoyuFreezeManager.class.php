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
 * [Enter Class Description]
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuFreezeManager {

	/**
	 * @var String		Default table for database requests
	 */
	const TABLE = 'system_freeze';

	/**
	 * Freeze an element
	 * Check first if there is already an identical freeze of this element.
	 * If a freeze is already available, get the freeze ID, otherwise create a new freeze
	 *
	 * @param	String		$type				The type of the freeze. Mostly the base table
	 * @param	Integer		$idElement			ID of the element
	 * @param	Mixed		$element			The element. Can be simple data, array or object. Will be serialized for storage
	 * @param	Boolean		$ignoreMissing
	 * @return	Integer		Freeze ID
	 */
	public static function freeze($type, $idElement, $element, $ignoreMissing = false) {
		$freeze	= self::unfreezeElement($type, $idElement, $ignoreMissing);
		$hash	= md5(serialize($element));

		if( $freeze !== false && $freeze['hash'] === $hash && $freeze['element_type'] === $type && $freeze['element_id'] == $idElement ) {
			$idFreeze	= $freeze['id'];
		} else {
			$idFreeze	= self::saveFreeze($type, $idElement, $element);
		}

		return $idFreeze;
	}



	/**
	 * Unfreeze a freezed element
	 *
	 * @param	Integer		$idFreeze
	 * @return	Mixed
	 */
	public static function unfreeze($idFreeze) {
		$idFreeze	= (int) $idFreeze;
		$freeze		= TodoyuRecordManager::getRecordData(self::TABLE, $idFreeze);

		if( $freeze !== false ) {
			return unserialize($freeze['data']);
		} else {
			TodoyuLogger::logError('Tried to unfreeze a not available object with ID: ' . $idFreeze);
			return false;
		}
	}



	/**
	 * Freeze a record of a table as array
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @param	Boolean		$ignoreMissing
	 * @return	Integer		Freeze ID
	 */
	public static function freezeRecord($table, $idRecord, $ignoreMissing = false) {
		$record	= TodoyuRecordManager::getRecordData($table, $idRecord);

		return self::freeze($table, $idRecord, $record, $ignoreMissing);
	}



	/**
	 * Freeze an object based on class an record ID
	 * Object will be generated and immediately freezed
	 *
	 * @param	String		$class
	 * @param	Integer		$idRecord
	 * @param	Boolean		$ignoreMissing
	 * @return	Integer
	 */
	public static function freezeObject($class, $idRecord, $ignoreMissing = false) {
		$object	= TodoyuRecordManager::getRecord($class, $idRecord);

		return self::freeze($class, $idRecord, $object, $ignoreMissing);
	}



	/**
	 * Get a freeze by type and ID
	 *
	 * @param	String			$type
	 * @param	Integer			$idElement
	 * @param	Boolean			$ignoreMissing		Don't log an error if unfreezing failed, because we're just checking if it's available
	 * @return	Mixed|Boolean	Restored element or false
	 */
	public static function unfreezeElement($type, $idElement, $ignoreMissing = false) {
		$idElement	= (int) $idElement;
		$data		= false;

		$fields	= '*';
		$where	= '		`element_type`	= ' . TodoyuSql::quote($type)
				. ' AND	`element_id`		= ' . $idElement;
		$order	= 'date_create DESC';

		$backup	= Todoyu::db()->getRecordByQuery($fields, self::TABLE, $where, $order);

		if( $backup !== false ) {
			$data = unserialize($backup['data']);
		} elseif( $ignoreMissing !== true ) {
			TodoyuLogger::logError('Failed to unfreeze element of type "' . $type . '" with ID=<' . $idElement . '>');
		}

		return $data;
	}



	/**
	 * Save element in database as freeze
	 *
	 * @param	String		$type
	 * @param	Integer		$idElement
	 * @param	Mixed		$element
	 * @return	Integer
	 */
	private static function saveFreeze($type, $idElement, $element) {
		$elementData	= serialize($element);

		$data	= array(
			'element_type'	=> $type,
			'element_id'	=> (int) $idElement,
			'data'			=> $elementData,
			'hash'			=> md5($elementData)
		);

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}

}

?>