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
 * Installer
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuDbAnalyzer {

	/**
	 * Get available databases on server
	 *
	 * @param	Array		$dbConfig		Connection configuration [server,username,password]
	 * @return	Array
	 */
	public static function getDatabasesOnServer(array $dbConfig) {
		$databases	= array();
		$ignore		= array(
			'information_schema',
			'mysql',
			'phpmyadmin'
		);

		$link	= mysqli_connect($dbConfig['server'], $dbConfig['username'], $dbConfig['password']);
		if( $link !== false ) {
			$resource	= mysqli_query($link,'SHOW DATABASES');

			if( $resource !== false ) {
				$rows		= TodoyuDatabase::resourceToArray($resource);
				$databases	= TodoyuArray::getColumn($rows, 'Database');
				$databases	= array_diff($databases, $ignore);
			} else {
				TodoyuLogger::logError('Can\'t get list of databases on server: ' . mysqli_error($link));
			}
		} else {
			TodoyuLogger::logError('Can\'t connect to the database: ' . mysqli_error($link));
		}

		return $databases;
	}



	/**
	 * Get tables in a database
	 * Create a new connection to get the tables
	 *
	 * @param	Array		$dbConfig		server,username,password,database
	 * @return	Array
	 */
	public static function getDatabaseTables(array $dbConfig) {
		$link		= mysqli_connect($dbConfig['server'], $dbConfig['username'], $dbConfig['password']);
		$query		= 'SHOW TABLES FROM ' . $dbConfig['database'];
		$resource	= mysqli_query($link, $query);

		$rows		= TodoyuDatabase::resourceToArray($resource);
		$databases	= TodoyuArray::getColumn($rows, 'Tables_in_' . $dbConfig['database']);

		return $databases;
	}



	/**
	 * Check if database connection data is valid
	 *
	 * @param	String		$server
	 * @param	String		$username
	 * @param	String		$password
	 * @return	Boolean
	 * @throws	Exception
	 */
	public static function checkDbConnection($server, $username, $password) {
		$status	= @mysqli_connect($server, $username, $password);
		$info	= array(
			'status'	=> true
		);

		if( !$status ) {
			$info['status']	= false;
			$info['error']	= mysqli_error($status);
		}

		return $info;
	}



	/**
	 * Get table and column structure from database.
	 * Only check for tables with the todoyu format:
	 *  - ext_*
	 *  - static_*
	 *  - system_*
	 *
	 * @return	Array
	 */
	public static function getTableStructures() {
		$fields	= '	TABLE_NAME,
					COLUMN_NAME,
					COLUMN_DEFAULT,
					IS_NULLABLE,
					DATA_TYPE,
					CHARACTER_MAXIMUM_LENGTH,
					CHARACTER_SET_NAME,
					COLUMN_TYPE,
					EXTRA';
		$table	= 'INFORMATION_SCHEMA.COLUMNS';
		$where	= '	`TABLE_SCHEMA` = ' . TodoyuSql::quote(Todoyu::db()->getConfig('database')) .
				  ' AND	(`TABLE_NAME` LIKE \'system_%\' OR `TABLE_NAME` LIKE \'ext_%\' OR `TABLE_NAME` LIKE \'static_%\')';
		$order	= 'TABLE_NAME';

		$columns= Todoyu::db()->getArray($fields, $table, $where, '', $order);

		$structure	= array();

		foreach($columns as $column) {
			$tableName	= $column['TABLE_NAME'];
			$columnName	= $column['COLUMN_NAME'];

				// If table not yet registered, add table information
			if( ! array_key_exists($tableName, $structure) ) {
				$structure[$tableName] = array(
					'table'		=> $tableName,
					'columns'	=> array(),
					'extra'		=> '',
					'keys'		=> array()
				);

					// Find keys in database
				$tableKeysRaw	= Todoyu::db()->getTableKeys($tableName);
				$tableKeys		= array();

					// Group keys by name (if keys are over more than one field, multiple rows are found)
				foreach($tableKeysRaw as $tableKeyRaw) {
					$tableKeys[$tableKeyRaw['Key_name']][] = $tableKeyRaw;
				}

					// Extract key informations (name, type, fields)
				foreach($tableKeys as $keyName => $tableKey) {
					$key	= array(
						'name'	=> $tableKey[0]['Key_name']
					);

						// Find type
					if( $keyName === 'PRIMARY' ) {
						$key['type']	= 'PRIMARY';
					} elseif( ((int) ($tableKey[0]['Non_unique'])) === 0 ) {
						$key['type']	= 'UNIQUE';
					} elseif( $tableKey[0]['Index_type'] === 'FULLTEXT' ) {
						$key['type']	= 'FULLTEXT';
					} else {
						$key['type']	= 'INDEX';
					}

						// Get fields
					$key['fields']	= TodoyuArray::getColumn($tableKey, 'Column_name');

					$structure[$tableName]['keys'][] = $key;
				}
			}

				// Process column
			$structure[$tableName]['columns'][$columnName] = array(
				'field'		=> $columnName,
				'type'		=> $column['COLUMN_TYPE'],
				'attributes'=> '',
				'null'		=> $column['IS_NULLABLE'] === 'YES' ? '' : 'NOT NULL',
				'default'	=> 'DEFAULT \'' . $column['COLUMN_DEFAULT'] . '\'',
				'extra'		=> strtoupper($column['EXTRA'])
			);
		}

		return $structure;
	}

}

?>