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
 * (Static-)Country manager
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCountryManager {

	const TABLE = 'static_country';

	/**
	 *
	 *  
	 * @param	Integer		$idCountry
	 * @return	TodoyuCountry
	 */
	public static function getCountry($idCountry) {
		return TodoyuRecordManager::getRecord('TodoyuCountry', $idCountry);
	}



	/**
	 * Get country by ISO code (alpha2 or alpha3)
	 *
	 * @param	String		$code
	 * @param	Boolean		$alpha3
	 * @return	TodoyuCountry
	 */
	public static function getCountryByISO($code, $alpha3 = false) {
		$idCountry = self::getCountryIdByISO($code, $alpha3);

		return self::getCountry($idCountry);
	}



	/**
	 * Get country ID by ISO code
	 *
	 * @param	String		$code
	 * @param	Boolean		$isAlpha3
	 * @return	Integer
	 */
	public static function getCountryIdByISO($code, $isAlpha3 = false) {
		$fields = 'id';
		$wField = $isAlpha3 ? 'iso_alpha3' : 'iso_alpha2';
		$where	= $wField . ' = ' . TodoyuSql::quote($code, true);

		return (int) Todoyu::db()->getFieldValue($fields, self::TABLE, $where);
	}

}

?>