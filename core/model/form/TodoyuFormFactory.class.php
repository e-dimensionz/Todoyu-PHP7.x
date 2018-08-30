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
 * Factory to create new form field elements with their registered classes
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormFactory {

	/**
	 * Get class which represents an object of the requested type
	 * A new instance will be created with the NEW operator
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getClass($type) {
		return TodoyuFormManager::getTypeClass($type);
	}



	/**
	 * Get the template for the input type
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getTemplate($type) {
		return TodoyuFormManager::getTypeTemplate($type);
	}



	/**
	 * Create a field of a type within its parent fieldset
	 *
	 * @param	String		$type		Type of the field
	 * @param	String		$name		Name of the field
	 * @param	TodoyuFormFieldset	$fieldset	Parent fieldset
	 * @param	Array		$config		Configuration array (XML child nodes)
	 * @return	TodoyuFormElement
	 */
	public static function createField($type, $name, TodoyuFormFieldset $fieldset, array $config = array()) {
		$class = self::getClass($type);

		if( ! is_null($class) && class_exists($class, true) ) {
			return  new $class($name, $fieldset, $config);
		} else {
			return false;
		}
	}

}

?>