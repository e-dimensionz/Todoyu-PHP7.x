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
 * Todoyu bookmark object
 *
 * @package		Todoyu
 * @subpackage	Bookmark
 */
class TodoyuBookmarkBookmark extends TodoyuBaseObject {

	/**
	 * @var String		Default table for database requests
	 */
	const TABLE	= 'ext_bookmark_bookmark';



	/**
	 * Constructor of the class
	 *
	 * @param	Integer	$idBookmark
	 */
	public function __construct($idBookmark) {
		parent::__construct($idBookmark, 'ext_bookmark_bookmark');
	}



	/**
	 * Get bookmark template data
	 *
	 * @return	Array
	 */
	public function getTemplateData() {
		return parent::getTemplateData();
	}



	/**
	 * Get item ID of bookmark
	 *
	 * @return	Integer
	 */
	public function getItemID() {
		return $this->getInt('id_item');
	}



	/**
	 * Get item type of bookmark
	 *
	 * @return	Integer
	 */
	public function getItemType() {
		return $this->getInt('type');
	}



	/**
	 * Get label of bookmark
	 *
	 * @return	Integer
	 */
	public function getTitle() {
		return $this->get('title');
	}

}

?>