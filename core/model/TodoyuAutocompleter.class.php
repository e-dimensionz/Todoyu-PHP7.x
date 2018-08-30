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
 * Handle autocompleter results
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuAutocompleter {

	/**
	 * List of registered autocompleters
	 * @var	Array
	 */
	private static $autocompleter = array();

	/**
	 * Register given autocompleter
	 *
	 * @param	String	$name
	 * @param	String	$function
	 * @param	Array	$restrict
	 */
	public static function addAutocompleter($name, $function, array $restrict = array()) {
		self::$autocompleter[$name]	= array(
			'function'	=> $function,
			'restrict'	=> $restrict
		);
	}



	/**
	 * Get given autocompleter configuration
	 *
	 * @param	String	$name
	 * @return	Array
	 */
	public static function getAutocompleter($name) {
		return self::$autocompleter[$name];
	}



	/**
	 * Render suggestion results list of given autocompleter
	 *
	 * @param	String	$name
	 * @param	String	$input
	 * @param	Array	$formData
	 * @return	String
	 */
	public static function renderAutocompleteList($name, $input, array $formData = array()) {
		$results	= self::getResults($name, $input, $formData);

		return TodoyuRenderer::renderAutocompleteResults($results);
	}



	/**
	 * Get autocompleter results for given input
	 *
	 * @param	String		$name			Name of the autocompleter type
	 * @param	String		$input			Text the user entered
	 * @param	Array		$formData		All other form data
	 * @return	Array
	 */
	public static function getResults($name, $input, array $formData = array()) {
		$autocompleter	= self::getAutocompleter($name);
		$result			= array();

			// Check for restrictions
		if( isset($autocompleter['restrict']) ) {
			Todoyu::restrict($autocompleter['restrict'][0], $autocompleter['restrict'][1]);
		}

			// Call data source function for results
		if( TodoyuFunction::isFunctionReference($autocompleter['function']) ) {
			$result	= TodoyuFunction::callUserFunction($autocompleter['function'], $input, $formData, $name);
		} else {
			TodoyuLogger::logError('Invalid autocomplete function for name "' . $name . '": ' . $autocompleter['function']);
		}

		return TodoyuArray::assure($result);
	}

}

?>