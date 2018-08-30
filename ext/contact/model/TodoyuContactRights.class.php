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
 * Contact rights
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactRights {

	/**
	 * Deny access because if given right is not given
	 *
	 * @param	String	$right
	 */
	private static function deny($right) {
		Todoyu::deny('contact', $right);
	}



	/**
	 * Restricts adding of contact record
	 *
	 * @param	String		$record
	 */
	public static function restrictRecordAdd($record) {
		$record	= trim($record);
		if( $record === 'person' ) {
			TodoyuContactPersonRights::restrictAdd();
		} else if( $record === 'company' ) {
			TodoyuContactCompanyRights::restrictAdd();
		} else {
			self::deny('contact:unkonwnrecord');
		}
	}



	/**
	 * Restricts editing of contact record
	 *
	 * @param	String		$record
	 * @param	Integer		$idRecord
	 */
	public static function restrictRecordEdit($record, $idRecord) {
		$idRecord	= intval($idRecord);
		$record	= trim($record);

		if( $record === 'person' ) {
			TodoyuContactPersonRights::restrictEdit($idRecord);
		} else if( $record === 'company' ) {
			TodoyuContactCompanyRights::restrictEdit($idRecord);
		} else {
			self::deny('contact:unkonwnrecord');
		}


	}



	/**
	 * Check whether seeing of contact info type of given person is allowed for current person
	 *
	 * @param	Integer		$idPerson
	 * @param	Integer		$idContactinfoType
	 * @return	Boolean
	 */
	public static function isContactinfotypeOfPersonSeeAllowed($idPerson, $idContactinfoType) {
		$idPerson			= intval($idPerson);
		$idContactinfoType	= intval($idContactinfoType);

		if( TodoyuAuth::isAdmin() || $idPerson === Todoyu::personid() ) {
			return true;
		}

		return self::isContactinfotypeSeeAllowed($idContactinfoType);
	}



	/**
	 * Check whether seeing of contact info type of given company is allowed for current person
	 *
	 * @param	Integer		$idCompany
	 * @param	Integer		$idContactinfoType
	 * @return	Boolean
	 */
	public static function isContactinfotypeOfCompanySeeAllowed($idCompany, $idContactinfoType) {
		$idCompany			= intval($idCompany);
		$idContactinfoType	= intval($idContactinfoType);

		$employers	= TodoyuContactPersonManager::getPerson(Todoyu::personid())->getCompanyIDs();

		if( TodoyuAuth::isAdmin() || in_array($idCompany, $employers) ) {
			return true;
		}

		return self::isContactinfotypeSeeAllowed($idContactinfoType);
	}



	/**
	 * Check whether seeing of given contact info type is allowed for current person
	 *
	 * @param	Integer		$idContactInfoType
	 * @return	Boolean
	 */
	public static function isContactinfotypeSeeAllowed($idContactInfoType) {
		$idContactInfoType	= intval($idContactInfoType);

		if( Todoyu::allowed('contact', 'relation:seeAllContactinfotypes') ) {
			return true;
		}

		return TodoyuContactContactInfoTypeManager::getContactInfoType($idContactInfoType)->isPublic();
	}



	/**
	 * Checks whether seeing of address type of given person is allowed for current person
	 *
	 * @param	Integer		$idPerson
	 * @param	Integer		$idAddressType
	 * @return	Boolean
	 */
	public static function isAddresstypeOfPersonSeeAllowed($idPerson, $idAddressType) {
		$idPerson			= intval($idPerson);
		$idAddressType		= intval($idAddressType);

		if( TodoyuAuth::isAdmin() || $idPerson === Todoyu::personid() ) {
			return true;
		}

		return self::isAddresstypeSeeAllowed($idAddressType);
	}



	/**
	 * Checks whether seeing of address type of given company is allowed for current person
	 *
	 * @param	Integer		$idCompany
	 * @param	Integer		$idAddressType
	 * @return	Boolean
	 */
	public static function isAddresstypeOfCompanySeeAllowed($idCompany, $idAddressType) {
		$idCompany		= intval($idCompany);
		$idAddressType	= intval($idAddressType);

		$employers	= TodoyuContactPersonManager::getPerson(Todoyu::personid())->getCompanyIDs();


		if( TodoyuAuth::isAdmin() || in_array($idCompany, $employers) ) {
			return true;
		}

		return self::isAddresstypeSeeAllowed($idAddressType);
	}



	/**
	 * Check whether seeing of address type is allowed for current person
	 *
	 * @param	Integer		$idAddressType
	 * @return	Boolean
	 */
	public static function isAddresstypeSeeAllowed($idAddressType) {
		$idAddressType	= intval($idAddressType);

		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		switch($idAddressType) {
			case 1:
				return Todoyu::allowed('contact', 'relation:seeHomeAddress');
				break;
			case 2:
				return Todoyu::allowed('contact', 'relation:seeBusinessAddress');
				break;
			case 3:
				return Todoyu::allowed('contact', 'relation:seeBillingAddress');
				break;
		}

		return false;
	}



	/**
	 * Restricts usage of contact export
	 */
	public static function restrictExport() {
		if( ! Todoyu::allowed('contact', 'panelwidgets:export') ) {
			self::deny('panelwidgets:export');
		}
	}
}

?>