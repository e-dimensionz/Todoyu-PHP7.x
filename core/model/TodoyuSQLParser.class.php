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
 * SQL parser
 *
 * @package		Todoyu
 * @subpackage	Installer
 * @todo		Check for unused old/deprecated methods and remove
 */
class TodoyuSQLParser {

	/**
	 * Extract all table names from SQL
	 *
	 * @param	String	$sql
	 * @return	Array
	 */
	private static function extractTableNames($sql) {
		$tableNames	= array();

		$parts	= explode(';', $sql);
		foreach($parts as $sql) {
			$pattern	= '/TABLE\\s*([IF NOT EXISTS\\s]*)[\\w\'`]([a-z_]+)[\\w\'`]\s\\(/';
			preg_match($pattern, $sql, $matches);

			foreach($matches as $match) {
				$tableName	= str_replace(
					array('TABLE ', 'IF NOT EXISTS', '', '(', '\'', '`', ' '),
					'',
					$match
				);

				if( strlen($tableName) > 0 ) {
					$tableNames[]	= $tableName;
				}
			}
		}

		$tableNames	= array_unique($tableNames);

		return $tableNames;
	}



	/**
	 * Extract one table name from SQL
	 *
	 * @param	String	$sql
	 * @return	String
	 */
	private static function extractSingleTableName($sql) {
		$tableName	= self::extractTableNames($sql);

		return isset($tableName[0]) ? $tableName[0] : false;
	}



	/**
	 * Extract table keys from SQL
	 *
	 * @param	String	$sql
	 * @return	Array
	 */
	private static function extractTableKeys($sql) {
		$sql	= trim($sql);
		$keys	= array();

		if( $sql !== '' ) {
			$keysSQL= explode(",\n", $sql);
			$pattern= '/([A-Za-z]*(?:\s*)KEY) (?:`(\w+)`)*(?:\s*)\((.*)\)/';

			foreach($keysSQL as $sql) {
				preg_match($pattern, $sql, $match);

				$keys[] = array(
					'type'	=> $match[1] === 'PRIMARY KEY' ? 'PRIMARY' : ($match[1] === 'KEY' ? 'INDEX' : trim(str_ireplace('KEY', '', $match[1]))),
					'name'	=> $match[1] === 'PRIMARY KEY' ? 'PRIMARY' : $match[2],
					'fields'=> explode(',', str_replace('`', '', $match[3]))
				);
			}
		}

		return $keys;
	}



	/**
	 * Extract column name from SQL
	 *
	 * @param	String	$sql
	 * @return	String
	 */
	private static function extractColumnName($sql) {
		$sql	= trim($sql);
		$pattern= '/(?<=`).*(?=`)/';
		preg_match($pattern, $sql, $matches);

		if( count($matches) > 0 ) {
			$name	= $matches[0];
		} else {
			$name	= false;
		}

		return $name;
	}



	/**
	 * Extract column type declaration
	 *
	 * @param	String	$columnSQL
	 * @return	String
	 */
	private static function extractColumnType($columnSQL) {
		$remove	= array(
			'NOT NULL',
			'NULL',
			'AUTO_INCREMENT'
		);

		$default	= self::extractColumnDefault($columnSQL);
		if( $default !== '' ) {
			$remove[] = $default;
		}

		$pattern	= '/`\w+`(.*)/';
		preg_match($pattern, $columnSQL, $matches);

		$type		= trim(str_ireplace($remove, '', $matches[1]));

		return $type;
	}



	/**
	 * Extract column attributes declaration
	 *
	 * @todo	cleanup
	 *
	 * @param	String	$sql
	 * @return	String
	 */
	private static function extractColumnAttributes($sql) {
		return '';

//		$sql	= trim($sql);
//		$pattern= '/(?<=\\)\\s)[a-zA-Z]*/';
//		preg_match($pattern, $sql, $matches);
//
//		if( count($matches) > 0 ) {
//			$attributes	= trim($matches[0]);
//			$attributes	= ! in_array($attributes, array('NOT', 'DEFAULT')) ? $attributes : '';
//		} else {
//			$attributes	= false;
//		}
//
//		return $attributes;
	}



	/**
	 * Extract column null declaration
	 *
	 * @param	String	$sql
	 * @return	String
	 */
	private static function extractColumnNull($sql) {
		$sql	= trim($sql);
		$pattern= '/(NOT NULL|NULL)/';
		preg_match($pattern, $sql, $matches);

		if( count($matches) > 0) {
			$null	= $matches[0];
		} else {
			$null	= false;
		}

		return $null;
	}



	/**
	 * Extract column default declaration
	 *
	 * @param	String	$columnSQL
	 * @return	String
	 */
	private static function extractColumnDefault($columnSQL) {
		$columnSQL	= str_replace('default ', 'DEFAULT ', $columnSQL);
		$pattern	= '/DEFAULT (\')?[\w,\(\)\.\d]*(\')?/';
		preg_match($pattern, $columnSQL, $match);

		return trim($match[0]);
	}



	/**
	 * Extract extra from SQL column declaration
	 *
	 * @param	String	$columnSQL
	 * @return	String
	 */
	private static function extractColumnExtra($columnSQL) {
		$extra	= '';

		if( stristr($columnSQL, 'AUTO_INCREMENT') ) {
			$extra = 'AUTO_INCREMENT';
		}

		return $extra;
	}



	/**
	 * Extract table structure definition from SQL (separated into table and columns definition)
	 *
	 * @param	String	$sql
	 * @return	Array
	 */
	private static function extractColumns($sql) {
		$sql		= str_replace("\n", ' ', $sql);
		$columns	= array();

			// Extract code for all columns
		$pattern	= '/(?<=\\(\\s).*(?=.PRIMARY)/';
//		$pattern	= '/(?<=\\(\\s)(.|\\s)*(?=\\).)/';
		preg_match($pattern, $sql, $matches);

		if( count($matches) > 0 ) {
			$allColumnsSql	= $matches[0];
				// Split into columns
			$colsSqlArr	= explode(',', $allColumnsSql);
			foreach($colsSqlArr as $columnSql) {
				$columnSql	= trim($columnSql);
				if( strstr($columnSql, 'PRIMARY KEY') === false ) {
					$columnName	= self::extractColumnName($columnSql);

					if( strlen($columnName) > 0 ) {
						$columns[$columnName]['field']		= '`' . $columnName . '`';
						$columns[$columnName]['type']		= self::extractColumnType($columnSql);
//						$columns[$columnName]['collation']	= '';
						$columns[$columnName]['attributes']	= self::extractColumnAttributes($columnSql);
						$columns[$columnName]['null']		= self::extractColumnNull($columnSql);
						$columns[$columnName]['default']	= self::extractColumnDefault($columnSql);

						$columns[$columnName]['extra']		= self::extractColumnExtra($columnSql, $columns[$columnName]);
							// 'extra' and 'attributes' confused? swop them!
							//	@todo fix extraction regex to prevent this
						if( strstr($columns[$columnName]['extra'], 'SIGNED') !== false && $columns[$columnName]['attributes'] == '' ) {
							$columns[$columnName]['attributes']	= strtolower($columns[$columnName]['extra']);
							$columns[$columnName]['extra']	= '';
						}
					}
				}
			}
		}

		return $columns;
	}




	/**
	 * Render query to carry out DB updates
	 *
	 * @param	String	$action
	 * @param	String	$tableName
	 * @param	String	$colName
	 * @param	Array	$colStructure
	 * @param	Array	$allTableStructure
	 * @return	String
	 */
	private static function getUpdatingQuery($action, $tableName, $colName, array $colStructure, $allTablesStructure = array()) {
		switch($action) {
				// Create table
			case 'CREATE':
				$tableColumnsSql	= self::getMultipleColumnsQueryPart($allTablesStructure[$tableName]['columns']);
				$keys				= self::getKeysQueryPart($allTablesStructure[$tableName]['keys']);

				$query	= 'CREATE TABLE `' . $tableName . '` ( '	. "\n"
						. $tableColumnsSql . ', '					. "\n"
						. 'PRIMARY KEY  (`id`)'
						. ($keys !== '' ? ', '. "\n" : '')
						. $keys										. "\n"
						. ') ENGINE=MyISAM  DEFAULT CHARSET=utf8 ; ' . "\n";
				break;

				// Add column
			case 'ADD':
				$query	= 'ALTER TABLE `' . $tableName . '` ADD '	. "\n"
						. self::getFieldColumnsQueryPart($colStructure)
						. ';';
				break;

				// Alter column
			case 'ALTER':
				$query	= 'ALTER TABLE `' . $tableName . '` CHANGE ' . "\n"
						. self::getFieldColumnsQueryPart($colStructure)
						. ';';
				break;
		}

		return $query;
	}



	/**
	 * Create SQL query from to given column structure
	 *
	 * @param	Array	$colStructure
	 * @return	String
	 */
	private static function getFieldColumnsQueryPart(array $colStructure) {
		$query	= $colStructure['field'] . ' '
				. $colStructure['type'] . ' '
				. $colStructure['attributes'] . ' '
				. $colStructure['null'] . ' '
				. $colStructure['default'] . ' '
				. $colStructure['extra'] . ' ';

		return str_replace('  ', ' ', $query);
	}




	/**
	 * Parse given columns structure and retrieve query parts
	 *
	 * @param	Array	$columnsStructure
	 * @return	String
	 */
	private static function getMultipleColumnsQueryPart($columnsStructure) {
		$queryParts	= array();
		foreach($columnsStructure as $colName => $colProps) {
			$queryParts	[]= self::getFieldColumnsQueryPart($colProps);
		}
		$query	= implode(', ' . "\n", $queryParts);

		return $query;
	}



	/**
	 * Create comma + newline separated list from given keys array
	 *
	 * @param	Array	$keysArr
	 * @return	String
	 */
	private static function getKeysQueryPart(array $keysArr) {
		$query	= implode(', ' . "\n", $keysArr);

		return trim($query);
	}



	/**
	 * Parse given SQL create query and retrieve general (table, extra, keys) and column stats (field, type, attributes, null, default, extra)
	 *
	 * @param	String	$query
	 * @return	Array
	 */
	public static function parseCreateQuery($query) {
		$info	= array(
			'table'		=> '',
			'columns'	=> array()
		);

		$patternAll	= '/CREATE TABLE ([A-Za-z ]*)`(\w+)` \((.*)\)(.*)/is';
		preg_match_all($patternAll, $query, $matches);

		$columnsKeySQL	= self::splitColumnKeySQL($matches[3][0]);
		$columnsSQL		= explode(",\n", trim($columnsKeySQL['columns']));

		$info['table']	= $matches[2][0];
		$info['extra']	= $matches[4][0];
		$info['keys']	= self::extractTableKeys($columnsKeySQL['keys']);

			// Parse columns
		foreach($columnsSQL as $columnSQL) {
				// Parse normal column
			$columnName	= self::extractColumnName($columnSQL);

			$info['columns'][$columnName] = array(
				'field'		=> $columnName,
				'type'		=> self::extractColumnType($columnSQL),
				'attributes'=> self::extractColumnAttributes($columnSQL),
				'null'		=> self::extractColumnNull($columnSQL),
				'default'	=> self::extractColumnDefault($columnSQL),
				'extra'		=> self::extractColumnExtra($columnSQL)
			);
		}

		return $info;
	}



	/**
	 * Split given SQL to extract column keys
	 *
	 * @param	String	$SQL
	 * @return	Array
	 */
	private static function splitColumnKeySQL($SQL) {
		$strPosPrimary	= stripos($SQL, 'PRIMARY KEY ');
		$strPosUnique	= stripos($SQL, 'UNIQUE KEY ');
		$strPosFulltext	= stripos($SQL, 'FULLTEXT KEY ');
		$strPosKey		= stripos($SQL, 'KEY ');
		$info			= array(
			'columns'	=> $SQL
		);
		$keyPositions	= array();

		if( $strPosPrimary !== false )	$keyPositions[] = $strPosPrimary;
		if( $strPosUnique !== false )	$keyPositions[] = $strPosUnique;
		if( $strPosFulltext !== false ) $keyPositions[] = $strPosFulltext;
		if( $strPosKey !== false )		$keyPositions[] = $strPosKey;

		if( sizeof($keyPositions) > 0 ) {
			$pos	= min($keyPositions);

			$info['columns']= trim(substr($SQL, 0, $pos - 1), ',');
			$info['keys']	= substr($SQL, $pos);
		}

		return $info;
	}

}

?>