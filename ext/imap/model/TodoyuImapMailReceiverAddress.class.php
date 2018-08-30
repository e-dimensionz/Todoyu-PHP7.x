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
 * Email receiver type: IMAP address
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuImapMailReceiverAddress extends TodoyuMailReceiver implements TodoyuMailReceiverInterface {

	/**
	 * @var	TodoyuImapAddress	Address
	 */
	protected $address;

	/**
	 * Initialize
	 *
	 * @param	Integer		$idAddress
	 */
	public function __construct($idAddress) {
		parent::__construct('imapaddress', $idAddress);

		$this->address	= TodoyuImapAddressManager::getAddress($idAddress);
	}



	/**
	 * Get name
	 *
	 * @return	String
	 */
	public function getName() {
		return $this->address->getName();
	}



	/**
	 * Get email address
	 *
	 * @return	String
	 */
	public function getAddress() {
		return $this->address->getAddress();
	}

}

?>