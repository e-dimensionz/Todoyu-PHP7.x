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
 * Various validators
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuValidator {

	/**
	 * Validate string being email address
	 *
	 * @param	String		$value
	 * @return	Boolean
	 */
	public static function isEmail($value) {
		if( ! self::isNotEmpty($value) ) {
			return false;
		}

		return preg_match('/^[A-Za-z0-9\._-]+[@][A-Za-z0-9\._-]{2,}\.[A-Za-z0-9]{2,}$/', $value) === 1;
	}



	/**
	 * Validate variable being numeric digit
	 *
	 * @param	String		$value
	 * @return	Boolean
	 */
	public static function isDigit($value) {
		return trim((int) $value) === trim($value);
	}



	/**
	 * Check if value is a number
	 *
	 * @param	String		$value
	 * @return	Boolean
	 */
	public static function isNumber($value ) {
		return is_numeric($value);
	}



	/**
	 * Check if value is decimal
	 *
	 * @param	String		$value
	 * @return	Boolean
	 */
	public static function isDecimal($value) {
		return trim(floatval($value)) === trim($value);
	}



	/**
	 * Validate string not being empty
	 *
	 * @param	String		$value
	 * @return	String
	 */
	public static function isNotEmpty($value) {
		return trim($value) !== '' && trim($value) !== '0';
	}



	/**
	 * Check if number is in range. The numbers are intrepreted as float values
	 *
	 * @param	Float		$value
	 * @param	Float		$bottom
	 * @param	Float		$top
	 * @param	Boolean		$allowRanges
	 * @return	Boolean
	 */
	public static function isInRange($value, $bottom, $top, $allowRanges = true) {
		$value	= floatval($value);
		$bottom	= floatval($bottom);
		$top	= floatval($top);

		if( $allowRanges ) {
			return $value >= $bottom && $value <= $top;
		} else {
			return $value > $bottom && $value < $top;
		}
	}



	/**
	 *
	 * @param	Float		$value
	 * @param	Float		$minimum
	 * @param	Boolean		$allowBorder
	 * @return	Boolean
	 */
	public static function isMin($value, $minimum, $allowBorder = true) {
		$value	= floatval($value);
		$minimum= floatval($minimum);

		if( $allowBorder ) {
			return $value >= $minimum;
		} else {
			return $value > $minimum;
		}
	}



	/**
	 * Check whether the value is lower than the maximum
	 * 
	 * @param	Float		$value
	 * @param	Float		$maximum
	 * @param	Boolean		$allowBorder
	 * @return	Boolean
	 */
	public static function isMax($value, $maximum, $allowBorder = true) {
		$value	= floatval($value);
		$maximum= floatval($maximum);

		if( $allowBorder ) {
			return $value <= $maximum;
		} else {
			return $value < $maximum;
		}
	}



	/**
	 * Validate timestamp not being at 00:00:00
	 *
	 * @param	String		$value
	 * @return	Boolean
	 */
	public static function isNotZerotime($value) {
		$parts	= explode(':', $value);
		$check	= (int) $parts[0] + (int) $parts[1];

		return $check > 0;
	}



	/**
	 * Validate string being of at least given minimum length
	 *
	 * @param	String		$value
	 * @param	Integer		$minLength
	 * @return	Boolean
	 */
	public static function hasMinLength($value, $minLength) {
		$minLength	= (int) $minLength;

		return strlen(trim($value)) >= $minLength;
	}



	/**
	 * Validate string not being longer than given length
	 *
	 * @param	String		$value
	 * @param	Integer		$maxLength
	 * @return	Boolean
	 */
	public static function hasMaxLength($value, $maxLength) {
		$maxLength	= (int) $maxLength;

		return strlen(trim($value)) <= $maxLength;
	}



	/**
	 * Validate string not containing given illegal characters
	 *
	 * @param	String		$value
	 * @param	Array		$validatorConfig
	 * @return	Boolean
	 * @todo	Chars should be a direct parameter. Add wrapper in TodoyuFormValidator
	 */
	public static function illegalChars($value, array $validatorConfig = array()) {
		$chars	= $validatorConfig['char'];

		if( is_array($chars) ) {
			foreach($chars as $char) {
				if( stristr($value, $char) !== false ) {
					return false;
				}
			}
		}

		return true;
	}

}

?>