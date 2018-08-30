<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * Object class for person's role in a project
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProjectrole extends TodoyuBaseObject {

	/**
	 * Constructor
	 *
	 * @param	Integer	$idProjectrole
	 */
	public function __construct($idProjectrole) {
		parent::__construct($idProjectrole, 'ext_project_role');
	}



	/**
	 * Get title of projectrole
	 *
	 * @return	String
	 */
	public function getTitle() {
		return Todoyu::Label($this->get('title'));
	}



	/**
	 * Get record label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return $this->getTitle();
	}

}

?>