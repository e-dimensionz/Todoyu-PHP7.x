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
 * Short description of the current class
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class MyCamelCaseClassName {

	/**
	 * Some description
	 *
	 * @var	String
	 */
	private $myVar = '';



	/**
	 * Add a good method description. Don't tell the obvious stuff!
	 *
	 * @param	Integer			$idRecord
	 * @param	Array			$data
	 * @param	AnotherClass 	$otherClass			Use typehints where possible
	 * @return	Integer			A function always has one type of "return value"
	 */
	public function doSomething($idRecord, array $data, AnotherClass $otherClass) {
			// Always validate parameters (in public functions)
		$idRecord	= intval($idRecord);

			// Check what you really want to know, and do this as exactly as possible
		if( $idRecord === 0 ) {

		}

		return $idRecord;
	}



	/**
	 * Render functions should always start with "render"
	 * A render function only should compose the data for the template.
	 * To calculate data or get data from the database, use a manager function
	 *
	 * @return	String
	 */
	public static function renderSomething() {
			// Define the path to the template file
		$tmpl	= 'ext/project/view/something.tmpl';
			// Define the data array for Dwoo
		$data	= array(
			'key1'	=> 'somedata',
			'subkey'=> array(
				'sub1'	=> 3,
				'sub55'	=> 5555
			)
		);

		return Todoyu::render($tmpl, $data);
	}
}

?>