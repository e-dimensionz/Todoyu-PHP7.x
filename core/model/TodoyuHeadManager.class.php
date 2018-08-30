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
 * Manage headlets. Register in config and get registered headlets for area
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuHeadManager {

	/**
	 * Headlets
	 *
	 * @var	Array
	 */
	private static $headlets = array();


	/**
	 * Add a new headlet
	 *
	 * @param	String		$className
	 * @param	Integer		$initPosition
	 */
	public static function addHeadlet($className, $initPosition = 100) {
		self::$headlets[] = array(
			'class'		=> $className,
			'position'	=> (int) $initPosition
		);
	}



	/**
	 * Remove all registered headlets (empty header)
	 */
	public static function removeAll() {
		self::$headlets = array();
	}



	/**
	 * Render head with headlets
	 *
	 * @return	String
	 */
	public static function render() {
		$tmpl	= 'core/view/head.tmpl';
		$data	= array(
			'headlets'	=> array()
		);

		$headlets	= TodoyuArray::sortByLabel(self::$headlets, 'position', true);

		foreach($headlets as $headletConfig) {
			$className	= $headletConfig['class'];
			$name		= strtolower($className); // implode('', array_slice($classParts, 3));
			/**
			 * @var	TodoyuHeadlet	$headlet
			 */
			$headlet	= new $className();

			if( ! $headlet->isEmpty() ) {
				$data['headlets'][$name] = array(
					'name'		=> $name,
					'label'		=> $headlet->getLabel(),
					'phpClass'	=> $className,
					'content'	=> $headlet->render(),
					'type'		=> $headlet->getType(),
					'class'		=> $headlet->getClass()
				);
			}
		}

		return Todoyu::render($tmpl, $data);
	}

}

?>