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
 * SQL helper functions
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuSql {

	/**
	 * Build a select query
	 *
	 * @param	String		$fields
	 * @param	String		$table
	 * @param	String		$where
	 * @param	String		$groupBy
	 * @param	String		$orderBy
	 * @param	String		$limit
	 * @return	String
	 */
	public static function buildSELECTquery($fields, $table, $where = '', $groupBy = '', $orderBy = '', $limit = '') {
		$query = 'SELECT ' . $fields . ' FROM ' . $table;

		if( $where != '' ) {
			$query .= ' WHERE ' . $where;
		}

		if( $groupBy != '' ) {
			$query .= ' GROUP BY ' . $groupBy;
		}

		if( $orderBy != '' ) {
			$query .= ' ORDER BY ' . $orderBy;
		}

		if( $limit != '' ) {
			$query .= ' LIMIT ' . $limit;
		}

		return $query;
	}



	/**
	 * Build insert query
	 *
	 * @param	String		$table
	 * @param	Array		$fieldNameValues
	 * @param	Array		$noQuoteFields
	 * @return	String
	 */
	public static function buildINSERTquery($table, array $fieldNameValues, array $noQuoteFields = array()) {
		$fieldNames		= implode(',', self::backtickArray(array_keys($fieldNameValues)));
		$fieldValues	= implode(',', array_values(self::quoteArray($fieldNameValues, $noQuoteFields, true)));
		$table			= self::quoteTablename($table);

		$query = 'INSERT INTO ' . $table
				. ' (' . $fieldNames . ')'
				. ' VALUES(' . $fieldValues . ')';

		return $query;
	}



	/**
	 * Build delete query
	 *
	 * @param	String		$table
	 * @param	String		$where
	 * @param	String		$limit
	 * @return	String
	 */
	public static function buildDELETEquery($table, $where, $limit = '') {
		$table	= self::quoteTablename($table);
		$query	= 'DELETE FROM ' . $table . ' WHERE ' . $where;

		if( $limit != '' ) {
			$query .= ' LIMIT ' . $limit;
		}

		return $query;
	}



	/**
	 * Build an update query
	 *
	 * @param	String		$table
	 * @param	String		$where
	 * @param	Array		$fieldNameValues
	 * @param	Array		$noQuoteFields
	 * @return	String
	 */
	public static function buildUPDATEquery($table, $where, array $fieldNameValues, array $noQuoteFields = array()) {
		$fieldNameValues= self::escapeArray($fieldNameValues, true, $noQuoteFields);
		$table			= self::quoteTablename($table);
		$fields			= array();

		foreach($fieldNameValues as $key => $quotedValue) {
			$fields[] = self::backtick($key) . ' = ' . $quotedValue;
		}

		$query = 'UPDATE ' . $table . ' SET ';
		$query .= implode(', ', $fields);

		if( !empty($where) ) {
			$query .= ' WHERE ' . $where;
		}

		return $query;
	}



	/**
	 * Build "WHERE IN()" query part
	 *
	 * @param	Array		$values
	 * @param	String		$fieldName
	 * @param	Boolean		$isInt				Values are integers?
	 * @param	Boolean		$negate				Negate using NOT?
	 * @param	Boolean		$quoteStrings		Quote non-integer values?
	 * @return	String
	 */
	public static function buildInListQueryPart(array $values, $fieldName, $isInt = true, $negate = false, $quoteStrings = true) {
		if( sizeof($values) === 0 ) {
			return $negate ? '1' : '0'; // no values: negate = always ok, normal = no result
		}
		$values 	= array_unique($values);
		$fieldName= self::backtick($fieldName);

			// Implode values array to list
		if( $isInt ) {
			$values = TodoyuArray::intImplode($values, ',');
		} elseif( $quoteStrings && !$isInt ) {
			$values = TodoyuArray::implodeQuoted($values, ',');
		} else {
			$values = implode(',', $values);
		}

		return $fieldName . ($negate ? ' NOT ' : ' ') . 'IN(' . $values . ')';
	}



	/**
	 * Wrap value in backticks
	 *
	 * @param	String		$value
	 * @return	String
	 */
	public static function backtick($value) {
		if( stristr($value, '.') !== false ) {
			$value = str_replace('.', '`.`', $value);
		}

		return '`' . $value . '`';
	}



	/**
	 * Quote table name with backticks
	 * tablename => `tablename`
	 * Don't quote if the table name contains a whitespace => this means there may be multiple tables or an alias
	 *
	 * @param	String		$tableName
	 * @return	String
	 */
	public static function quoteTablename($tableName) {
		return strpos($tableName, ' ') === false && strpos($tableName, ',') === false ? self::backtick($tableName) : $tableName;
	}



	/**
	 * Build a boolean invert SQL command
	 *
	 * @param	String		$fieldName
	 * @param	String		$table
	 * @return	String
	 */
	public static function buildBooleanInvertQueryPart($fieldName, $table = '') {
		return self::quoteFieldname($fieldName, $table) . ' XOR 1';
	}



	/**
	 * Build a FIND_IN_SET SQL statement so search in a comma separated field
	 *
	 * @param	String		$value
	 * @param	String		$fieldName
	 * @return	String
	 */
	public static function buildFindInSetQueryPart($value, $fieldName) {
		$value		= self::quote($value, true);
		$fieldName	= self::quoteFieldname($fieldName);

		return 'FIND_IN_SET(' . $value . ', ' . $fieldName . ') != 0';
	}



	/**
	 * Build a like query to search multiple strings in multiple fields with LIKE %word%
	 *
	 * @param	Array		$searchWords			Words to search for
	 * @param	Array		$searchInFields			Fields which have to match the $searchWords
	 * @param	Boolean		$negate
	 * @param	Boolean		$allOr
	 * @return	String		Where part condition
	 */
	public static function buildLikeQueryPart(array $searchWords, array $searchInFields, $negate = false, $allOr = false) {
		$searchWords		= self::escapeArray($searchWords);
		$fieldWheres		= array();
		$innerConjunction	= $negate ? ' AND ' : ' OR ';
		$outerConjunction	= $allOr ? ' OR ' : ' AND ';
		$negation			= $negate ? ' NOT ' : ' ';

			// Build an AND-group for all search words
		foreach($searchWords as $searchWord) {
			$fieldCompare 		= array();
			$preparedSearchWord	= self::escapeLikeWildCards($searchWord);

				// Build an OR-group for all search fields
			foreach($searchInFields as $fieldName) {
				$fieldCompare[] = self::quoteFieldname($fieldName) . $negation . 'LIKE \'%' . $preparedSearchWord . '%\'';
			}

				// Concatenate field WHEREs with each words inside
			$fieldWheres[] = implode($innerConjunction, $fieldCompare);
		}

		return '((' . implode(')' . $outerConjunction . '(', $fieldWheres) . '))';
	}



	/**
	 * Escape like wild cards in string
	 * Info: The string should already be escaped for mysql,
	 * which prevents problems with double escaping and dangerous combinations of backslashes
	 *
	 * @param	String		$string
	 * @return	String
	 */
	public static function escapeLikeWildCards($string) {
		return str_replace(array('%', '?'), array('\\%', '\\?'), $string);
	}



	/**
	 * Quote a field name. Optionally, the table name is prefixed
	 *
	 * @param	String		$fieldName
	 * @param	String		$tableName
	 * @return	String		Field name in backticks
	 */
	public static function quoteFieldname($fieldName, $tableName = '') {
		$fieldName	= self::backtick($fieldName);

		if( $tableName !== '' ) {
			$fieldName = self::quoteTablename($tableName) . '.' . $fieldName;
		}

		return $fieldName;
	}



	/**
	 * Escape string for queries
	 *
	 * @param	String		$string
	 * @return	String
	 */
	public static function escape($string) {
		return is_float($string) ? str_replace(',', '.', (string) $string) : mysqli_real_escape_string(Todoyu::db()->link, $string);
	}



	/**
	 * Quote a string value.
	 *
	 * @param	String		$value
	 * @param	Boolean		$escape
	 * @return	String
	 */
	public static function quote($value, $escape = false) {
		$value = $escape ? self::escape($value) : $value;

		return '\'' . $value . '\'';
	}



	/**
	 * Quote all fields in an array
	 *
	 * @param	Array		$array
	 * @param	Array		$noQuoteFields
	 * @param	Boolean		$escape
	 * @return	Array
	 */
	public static function quoteArray(array $array, array $noQuoteFields = array(), $escape = true) {
		foreach($array as $key => $value) {
			if( !in_array($key, $noQuoteFields) ) {
				$array[$key] = self::quote($value, $escape);
			}
		}

		return $array;
	}



	/**
	 * Wrap all elements of an array in backticks
	 *
	 * @param	Array		$array
	 * @return	Array
	 */
	public static function backtickArray(array $array) {
		return array_map(array('TodoyuSql', 'backtick'), $array);
	}



	/**
	 * Escape all values in the array
	 * Optional it's available to quote all fields. $noQuoteFields can disable this function for specific fields
	 *
	 * @param	Array		$array				Array to escape (name => value pairs)
	 * @param	Boolean		$quoteFields		Quote the fields (field will be surrounded by single quotes:')
	 * @param	Array		$noQuoteFields		If $quoteFields is enabled, this fields will be ignored for quoting
	 * @return	Array
	 */
	public static function escapeArray(array $array, $quoteFields = false, array $noQuoteFields = array()) {
			// Only escape the field if they will not be quoted, quoteArray() escapes the field by itself
		if( $quoteFields  ) {
			$array = self::quoteArray($array, $noQuoteFields, true);
		} else {
			foreach($array as $key => $value) {
				$array[$key] =  self::escape($value);
			}
		}

		return $array;
	}

}

?>