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
 * Mail address with optional name
 * Entry from address book which is generated from sent and received mails without a link to an existing user
 *
 * @package			Todoyu
 * @subpackage		Imap
 */
class TodoyuImapAddress extends TodoyuBaseObject {

	/**
	 * Initialize
	 *
	 * @param	Integer		$idAddress
	 */
	public function __construct($idAddress) {
		parent::__construct($idAddress, 'ext_imap_address');
	}



	/**
	 * Get mail address
	 *
	 * @return	String
	 */
	public function getAddress() {
		return $this->get('address');
	}



	/**
	 * Get name
	 *
	 * @return	String
	 */
	public function getName() {
		return $this->get('name');
	}



	/**
	 * Check whether address has a name attached
	 *
	 * @return	Boolean
	 */
	public function hasName() {
		return $this->getName() !== '';
	}



	/**
	 * Get address label
	 * With name: John Doe (john@doe.com)
	 * Without name: john@doe.com
	 *
	 * @return	String
	 */
	public function getLabel() {
		$label	= $this->getAddress();

		if( $this->hasName() ) {
			$label	= $this->getName() . ' (' . $label . ')';
		}

		return $label;
	}

	public function getTemplateData($loadForeignData = false) {
		return parent::getTemplateData();
	}

}

?>