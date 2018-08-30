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
 * Object class for Todoyu contactinfo types
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactContactInfoType extends TodoyuBaseObject {

	/**
	 * Constructor of the class
	 *
	 * @param	Integer		$idContactInfoType
	 */
	public function __construct($idContactInfoType) {
		parent::__construct($idContactInfoType, 'ext_contact_contactinfotype');
	}



	/**
	 * Gets the header title of the contact info element (the header shown also when the element is collapsed)
	 *
	 * @return	String
	 */
	public function getTitle() {
		return Todoyu::Label($this->get('title'));
	}



	/**
	 * Checks if the contactinfotype is public or not
	 *
	 * @return	Boolean
	 */
	public function isPublic() {
		return intval($this->data['is_public']) === 1;
	}

}
?>