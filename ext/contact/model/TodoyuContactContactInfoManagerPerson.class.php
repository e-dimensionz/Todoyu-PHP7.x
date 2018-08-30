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
 * Contact info person
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactContactInfoManagerPerson extends TodoyuContactContactInfoManager {

	/**
	 * Delete all linked contact info records of given person
	 *
	 * @todo	see comment in above function 'removeContactinfoLinks'
	 * @param	Integer		$idPerson
	 */
	public static function deleteContactInfos($idPerson) {
		self::deleteLinkedContactInfos('person', $idPerson, array(), 'id_person');
	}



	/**
	 * Get email addresses of given types of given person
	 *
	 * @param	Integer			$idPerson
	 * @param	String|Boolean	$type
	 * @param	Boolean			$onlyPreferred
	 * @return	Array[]
	 */
	public static function getEmails($idPerson, $type = false, $onlyPreferred = false) {
		return self::getContactInfos('person', $idPerson, CONTACT_INFOTYPE_CATEGORY_EMAIL, $type, $onlyPreferred);
	}



	/**
	 * Get phone numbers of given types of given person
	 *
	 * @param	Integer			$idPerson
	 * @param	String|Boolean	$type
	 * @param	Boolean			$onlyPreferred
	 * @return	Array
	 */
	public static function getPhones($idPerson, $type = false, $onlyPreferred = false) {
		return self::getContactInfos('person', $idPerson, CONTACT_INFOTYPE_CATEGORY_PHONE, $type, $onlyPreferred);
	}



	/**
	 * Get preferred email of a person
	 * First check system email, than check "contactinfo" records. Look for preferred emails
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getPreferredEmail($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuContactPersonManager::getPerson($idPerson);

		$email		= $person->getEmail();

		if( empty($email) ) {
			$contactEmails	= self::getContactInfos('person', $idPerson, CONTACT_INFOTYPE_CATEGORY_EMAIL);
			if( sizeof($contactEmails) > 0 ) {
				$email = $contactEmails[0]['info'];
			}
		}

		return $email;
	}



	/**
	 * Get IDs of matching email contact infos
	 *
	 * @param	String[]	$searchWords
	 * @param	Integer[]	$ignoreIDs
	 * @param	Array		$params
	 * @return	Integer[]
	 */
	public static function getMatchingEmailContactInfoIDs(array $searchWords, array $ignoreIDs = array(), array $params = array()) {
		$ignoreIDs	= TodoyuArray::intval($ignoreIDs);

		$fields	= '	ci.id';
		$tables	= '	ext_contact_person p,
					ext_contact_contactinfo ci,
					ext_contact_contactinfotype cit,
					ext_contact_mm_person_contactinfo mm';
		$where	= '		mm.id_person			= p.id'
				. ' AND	mm.id_contactinfo		= ci.id'
				. '	AND	ci.id_contactinfotype	= cit.id'
				. ' AND cit.category			= ' . CONTACT_INFOTYPE_CATEGORY_EMAIL
				. '	AND ci.deleted = 0';

		$searchFields	= array('p.username', 'p.email', 'p.firstname', 'p.lastname', 'p.shortname', 'ci.info');
		$where			.= ' AND ' . TodoyuSql::buildLikeQueryPart($searchWords, $searchFields);

			// Add ignore IDs
		if( sizeof($ignoreIDs) > 0 ) {
			$where .= ' AND ' . TodoyuSql::buildInListQueryPart($ignoreIDs, 'ci.id', true, true);
		}

		if( ! Todoyu::allowed('contact', 'person:seeAllPersons') ) {
			$where .= ' AND p.' . TodoyuContactPersonRights::getAllowedToBeSeenPersonsWhereClause();
		}

		return Todoyu::db()->getColumn($fields, $tables, $where, '', '', 20, 'id');
	}

}

?>