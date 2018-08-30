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
 * Contact info for company
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactContactInfoManagerCompany extends TodoyuContactContactInfoManager {

	/**
	 * Delete all linked contact info records of given company
	 *
	 * @todo	see comment in above function 'removeContactinfoLinks'
	 *
	 * @param	Integer		$idCompany
	 */
	public static function deleteContactInfos($idCompany) {
		self::deleteLinkedContactInfos('company', $idCompany, array(), 'id_company');
	}



	/**
	 * Get email addresses of given types of given person
	 *
	 * @param	Integer			$idPerson
	 * @param	String|Boolean	$type
	 * @param	Boolean			$onlyPreferred
	 * @return	Array
	 */
	public static function getEmails($idPerson, $type = false, $onlyPreferred = false) {
		return self::getContactInfos('company', $idPerson, CONTACT_INFOTYPE_CATEGORY_EMAIL, $type, $onlyPreferred);
	}



	/**
	 * Get phone numbers of given types of given person
	 *
	 * @param	Integer			$idCompany
	 * @param	String|Boolean	$type
	 * @param	Boolean			$onlyPreferred
	 * @return	Array
	 */
	public static function getPhones($idCompany, $type = false, $onlyPreferred = false) {
		return self::getContactInfos('company', $idCompany, CONTACT_INFOTYPE_CATEGORY_PHONE, $type, $onlyPreferred);
	}



	/**
	 * Get preferred email of a company
	 * First check system email, than check "contactinfo" records. Look for preferred emails
	 *
	 * @param	Integer		$idCompany
	 * @return	String
	 */
	public static function getPreferredEmail($idCompany) {
		$idCompany		= intval($idCompany);
		$Company		= TodoyuContactCompanyManager::getCompany($idCompany);

		$email = array();

		if( empty($email) ) {
			$contactEmails	= self::getContactInfos('company', $idCompany, CONTACT_INFOTYPE_CATEGORY_EMAIL);
			if( sizeof($contactEmails) > 0 ) {
				$email = $contactEmails[0]['info'];
			}
		}

		return $email;
	}

}

?>