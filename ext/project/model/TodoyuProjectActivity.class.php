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
 * Task activity object
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectActivity extends TodoyuBaseObject {

	/**
	 * Constructor
	 *
	 * @param	Integer	$idActivity
	 */
	public function __construct($idActivity) {
		$idActivity	= intval($idActivity);

		parent::__construct($idActivity, 'ext_project_activity');
	}





	/**
	 * Get title of activity
	 *
	 * @param	Boolean		$parse
	 * @return	String
	 */
	public function getTitle($parse = true) {
		return $parse ? Todoyu::Label($this->get('title')) : $this->get('title');
	}

}

?>