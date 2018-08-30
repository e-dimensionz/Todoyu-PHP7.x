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
 * Todoyu token object
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuToken extends TodoyuBaseObject {

	/**
	 * Constructor
	 *
	 * @param	Integer		$idToken
	 */
	public function __construct($idToken) {
		parent::__construct($idToken, 'system_token');
	}



	/**
	 * Get token ext ID
	 *
	 * @return	Integer
	 */
	public function getExtID() {
		return (int) $this->data['ext'];
	}



	/**
	 * Get token type ID
	 *
	 * @return	Integer
	 */
	public function getTokenType() {
		return (int) $this->data['token_type'];
	}



	/**
	 * Get token hash
	 *
	 * @return	String
	 */
	public function getHash() {
		return $this->data['hash'];
	}



	/**
	 * Check whether token is valid (owner must be active and not deleted)
	 *
	 * @return	Boolean
	 */
	public function isValid() {
			// Token record must exits
		if( $this->getID() === 0 ) {
			return false;
		}

			// Owner person must have active login and be not deleted
		$idPersonOwner	= $this->getPersonID('owner');
		$personOwner	= TodoyuContactPersonManager::getPerson($idPersonOwner);
		if( $personOwner->isDeleted() || $personOwner->get('is_active') == 0 ) {
			return false;
		}

		return true;
	}

}

?>