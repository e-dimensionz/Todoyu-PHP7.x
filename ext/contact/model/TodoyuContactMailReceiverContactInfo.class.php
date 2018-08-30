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
 * Contact info mail receiver
 * The receiver address is based on an email contact info
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactMailReceiverContactInfo extends TodoyuMailReceiver {

	/**
	 * @var	TodoyuContactPerson			Person
	 */
	protected $person;

	/**
	 * @var	TodoyuContactContactInfo	Contact info
	 */
	protected $contactInfo;



	/**
	 * Initialize
	 *
	 * @param	Integer		$idContactInfo
	 */
	public function __construct($idContactInfo) {
		parent::__construct('contactinfo', $idContactInfo);

		$this->initPerson();
		$this->initContactInfo();
	}



	/**
	 * Initialize the person
	 *
	 */
	protected function initPerson() {
		$field	= 'id_person';
		$table	= 'ext_contact_mm_person_contactinfo';
		$where	= '	id_contactinfo	= ' . $this->getRecordID();

		$idPerson	= Todoyu::db()->getFieldValue($field, $table, $where, '', '', 1);

		$this->person	= TodoyuContactPersonManager::getPerson($idPerson);
	}



	/**
	 * Initialize the contact info
	 *
	 */
	protected function initContactInfo() {
		$this->contactInfo	= TodoyuContactContactInfoManager::getContactinfo($this->getRecordID());
	}



	/**
	 * Get person
	 *
	 * @return	TodoyuContactPerson
	 */
	public function getPerson() {
		return $this->person;
	}



	/**
	 * Get name of the person
	 *
	 * @return	String
	 */
	public function getName() {
		return $this->getPerson()->getFullName();
	}



	/**
	 * Get address
	 *
	 * @return	String
	 */
	public function getAddress() {
		return $this->contactInfo->getInfo();
	}

}

?>