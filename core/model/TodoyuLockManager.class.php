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
 * Locking table for table records
 * Multiple extension can lock a record. This is only useful, if the host table
 * checks for lock records
 * 
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuLockManager {

	/**
	 * @var	String	Default table for database requests
	 */
	const TABLE = 'system_lock';


	/**
	 * Lock a record
	 *
	 * @param	Integer		$ext
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 */
	public static function lock($ext, $table, $idRecord) {
		if( ! self::isLockedByExt($ext, $table, $idRecord) ) {
			$data	= array(
				'ext'		=> (int) $ext,
				'table'		=> $table,
				'id_record'	=> (int) $idRecord
			);

			Todoyu::db()->doInsert(self::TABLE, $data);
		}
	}



	/**
	 * Unlock a record for an extension
	 *
	 * @param	Integer		$ext
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 */
	public static function unlock($ext, $table, $idRecord) {
		$where	= '		`ext`		= ' . (int) $ext
				. ' AND	`table`		= ' . TodoyuSql::quote($table, true)
				. ' AND `id_record`	= ' . (int) $idRecord;

		Todoyu::db()->doDelete(self::TABLE, $where, 1);
	}



	/**
	 * Check if a record is locked
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Boolean
	 */
	public static function isLocked($table, $idRecord) {
		$where	= '		`table`		= ' . TodoyuSql::quote($table, true)
				. ' AND `id_record`	= ' . (int) $idRecord;

		return Todoyu::db()->hasResult('id', self::TABLE, $where);
	}



	/**
	 * Check if one of the records is locked
	 *
	 * @param	String		$table
	 * @param	Array		$recordIDs
	 * @return	Boolean
	 */
	public static function areLocked($table, array $recordIDs) {
		$recordIDs	= TodoyuArray::intval($recordIDs, true, true);

		if( sizeof($recordIDs) === 0 ) {
			return false;
		}

		$where	= '		`table`		= ' . TodoyuSql::quote($table, true)
				. ' AND `id_record`	IN(' . implode(',', $recordIDs) . ')';

		return Todoyu::db()->hasResult('id', self::TABLE, $where);
	}



	/**
	 * Check if record is locked by an extension
	 *
	 * @param	Integer		$extID
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Boolean
	 */
	public static function isLockedByExt($extID, $table, $idRecord) {
		$where	= '		`ext`		= ' . (int) $extID
				. ' AND	`table`		= ' . TodoyuSql::quote($table, true)
				. ' AND `id_record`	= ' . (int) $idRecord;

		return Todoyu::db()->hasResult('id', self::TABLE, $where);
	}



	/**
	 * Get number of lock for the record
	 *
	 * @param	String		$table
	 * @param	Integer		$idRecord
	 * @return	Integer
	 */
	public static function getNumLocks($table, $idRecord) {
		$field	= 'id';
		$where	= '		`table`		= ' . TodoyuSql::quote($table, true)
				. ' AND `id_record`	= ' . (int) $idRecord;

		$result	= Todoyu::db()->doSelect($field, self::TABLE, $where);
		$numRows= Todoyu::db()->getNumRows($result);

		Todoyu::db()->freeResult($result);

		return $numRows;
	}



}

?>