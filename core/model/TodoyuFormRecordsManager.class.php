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
 * Manage form element records selector
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuFormRecordsManager {

	/**
	 * @var	String[]		List of records source callbacks
	 */
	protected static $listCallback = array();



	/**
	 * Add a records type form element
	 *
	 * @param	String		$type
	 * @param	String		$formElementClass
	 * @param	String		$listCallback
	 */
	public static function addType($type, $formElementClass, $listCallback) {
		self::$listCallback[$type] = $listCallback;

		TodoyuFormManager::addFieldTypeRecords($type, $formElementClass);
	}



	/**
	 * Get list callback function
	 *
	 * @param	String		$type
	 * @return	String
	 */
	protected static function getListCallback($type) {
		return self::$listCallback[$type];
	}



	/**
	 *
	 * @param	String		$type
	 * @param	String[]	$searchWords
	 * @param	String[]	$ignoreKeys
	 * @param	Array		$params
	 * @return	Array[]
	 */
	public static function getListItems($type, array $searchWords, array $ignoreKeys = array(), array $params = array()) {
		TodoyuExtensions::loadAllForm();

		$callback	= self::getListCallback($type);
		$searchWords= TodoyuArray::trim($searchWords, true);
		$ignoreKeys	= TodoyuArray::trim($ignoreKeys, true);

		if( TodoyuFunction::isFunctionReference($callback) ) {
			return TodoyuFunction::callUserFunction($callback, $searchWords, $ignoreKeys, $params, $type);
		} else {
			TodoyuLogger::logError('Invalid records type. No listing callback found for <' . $type . '>');
			return array();
		}
	}

}

?>