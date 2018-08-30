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
 * Manager class Todoyu for contact infos
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactContactInfoManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_contactinfo';


	/*
	 * Config for person and company
	 *
	 * @var	Array
	 */
	private static $mmConfig	= array(
		'person'	=> array(
			'table'	=> 'ext_contact_mm_person_contactinfo',
			'field'	=> 'id_person'
		),
		'company'	=> array(
			'table'	=> 'ext_contact_mm_company_contactinfo',
			'field'	=> 'id_company'
		)
	);



	/**
	 * Get contactinfo object
	 *
	 * @param	Integer		$idContactInfo
	 * @return	TodoyuContactContactInfo
	 */
	public static function getContactinfo($idContactInfo) {
		$idContactInfo	= intval($idContactInfo);

		return TodoyuRecordManager::getRecord('TodoyuContactContactInfo', $idContactInfo);
	}



	/**
	 * Get name of given contact info type
	 *
	 * @param	Integer	$idContactInfoType
	 * @return	String
	 */
	public static function getContactInfoTypeName($idContactInfoType) {
		$idContactInfoType = intval($idContactInfoType);

		$label = Todoyu::db()->getFieldValue('title', 'ext_contact_contactinfotype', 'id = ' . $idContactInfoType);

		return Todoyu::Label($label);
	}



	/**
	 * Saves contact infos
	 *
	 * @param	Array	$data
	 * @return	Integer
	 */
	public static function saveContactInfos(array $data) {
		$idContactinfo	= intval($data['id']);

		if( $idContactinfo === 0 ) {
			$idContactinfo = self::add();
		}

		// Add form save handler here

		self::update($idContactinfo, $data);

		self::removeFromCache($idContactinfo);

		return $idContactinfo;
	}



	/**
	 * Add contactinfo record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function add(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update contactinfo record
	 *
	 * @param	Integer		$idContactinfo
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function update($idContactinfo, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idContactinfo, $data);
	}






	/**
	 * Removes record from cache
	 *
	 * @param	Integer		$idContactInfo
	 */
	public static function removeFromCache($idContactInfo) {
		$idContactInfo	= intval($idContactInfo);

		TodoyuRecordManager::removeRecordCache('TodoyuContactContactInfo', $idContactInfo);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idContactInfo);
	}



	/**
	 * Delete contact informations which are linked over an mm-table.
	 * Deletes all except the given IDs
	 *
	 * @param	String		$key						Type key
	 * @param	Integer		$idRecord					Record ID which is linked to a contact info
	 * @param	Array		$currentContactInfoIDs		Contact info IDs which should stay linked with the record
	 * @param	String		$fieldRecord				Field name for the record ID
	 * @param	String		$fieldInfo					Field name for the contact info ID
	 * @return	Integer		Number of deleted records
	 */
	public static function deleteLinkedContactInfos($key, $idRecord, array $currentContactInfoIDs,  $fieldRecord, $fieldInfo = 'id_contactinfo') {
		return TodoyuDbHelper::deleteOtherMmRecords(self::$mmConfig[$key]['table'], 'ext_contact_contactinfo', $idRecord, $currentContactInfoIDs, $fieldRecord, $fieldInfo);
	}



	/**
	 * Get contact infos of given element (person or company)
	 *
	 * @param	String			$key
	 * @param	Integer			$idElement
	 * @param	Integer|Boolean	$category
	 * @param	String|Boolean	$type
	 * @param	Boolean			$onlyPreferred
	 * @return	Array[]
	 */
	protected static function getContactInfos($key, $idElement, $category = 0, $type = false, $onlyPreferred = false) {
		$idElement	= intval($idElement);
		$category	= intval($category);

		if( !isset(self::$mmConfig[$key]) ) {
			TodoyuLogger::logError('Key <' . $key . '> for contact info type missing or invalid');
			return array();
		}

		$fields	= '	ci.*,
					cit.key,
					cit.title';
		$tables	= '	ext_contact_contactinfo ci,
					ext_contact_contactinfotype cit,
					' . self::$mmConfig[$key]['table'] . ' mm';
		$where	= '		mm.' . self::$mmConfig[$key]['field'] . '	= ' . $idElement
				. ' AND	mm.id_contactinfo							= ci.id '
				. '	AND	ci.id_contactinfotype						= cit.id '
				. '	AND ci.deleted = 0';
		$order	= '	ci.is_preferred DESC,
					ci.id_contactinfotype ASC';

		if( $onlyPreferred ) {
			$where .= ' AND ci.is_preferred = 1';
		}

		if( $category !== 0 ) {
			$where .= ' AND cit.category = ' . $category;
		}

		if( $type !== false ) {
			$where .= ' AND cit.key LIKE \'%' . TodoyuSql::escape($type) . '%\'';
		}

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * @param	String		$contactInfo
	 * @return	Array
	 */
	public static function getContactInfoDuplicates($contactInfo) {
		$duplicates	= array();
		$records	= array();

		if( trim($contactInfo) ) {
			$duplicates = self::searchForDuplicatedContactInfo($contactInfo);
		}

		if ( sizeof($duplicates) > 0 ) {
			foreach ($duplicates as $duplicatedContactInfo) {
				$label = '';

				if ( $duplicatedContactInfo['id_person'] ) {
					if ( TodoyuContactRights::isContactinfotypeOfPersonSeeAllowed($duplicatedContactInfo['id_person'], $duplicatedContactInfo['id']) ) {
						$label = TodoyuContactPersonManager::getPerson($duplicatedContactInfo['id_person'])->getFullName(true);
					}
				} else if ( $duplicatedContactInfo['id_company'] ) {
					if ( TodoyuContactRights::isContactinfotypeOfCompanySeeAllowed($duplicatedContactInfo['id_company'], $duplicatedContactInfo['id']) ) {
						$label = TodoyuContactCompanyManager::getCompany($duplicatedContactInfo['id_company'])->getTitle();
					}
				}

				if ( $label ) {
					$records[]['title'] = Todoyu::Label($duplicatedContactInfo['title']) . ' - ' . $label;
				}
			}
		}

		return $records;
	}



	/**
	 * Search for a contact info
	 *
	 * @param	String		$contactInfo
	 */
	protected static function searchForDuplicatedContactInfo($contactInfo) {
		$results = array();

		foreach(self::$mmConfig as $key => $mmConfig) {
			$fields	= '	ci.*,
					cit.key,
					cit.title,
					mm.' . $mmConfig['field'];
			$tables	= '	ext_contact_contactinfo ci,
					ext_contact_contactinfotype cit,
					' . $mmConfig['table'] . ' mm';
			$where	= ' mm.id_contactinfo							= ci.id '
					. '	AND	ci.id_contactinfotype						= cit.id '
					. '	AND ci.deleted = 0'
					. ' AND ' . TodoyuSql::buildLikeQueryPart(array($contactInfo), array('ci.info'));


			$results = array_merge($results, Todoyu::db()->getArray($fields, $tables, $where));
		}

		return $results;
	}



	/**
	 * Renders contact informations
	 *
	 * @param	Array	$data
	 * @return	String
	 */
	public static function renderContactInformation($contactInfoData) {
		$contactInfoData['html'] = TodoyuString::htmlentities($contactInfoData['info']);

		if( intval($contactInfoData['infotype_category']) === CONTACT_INFOTYPE_CATEGORY_EMAIL) {
			$contactInfoData['html'] = TodoyuString::buildMailtoATag($contactInfoData['info'], $contactInfoData['info']);
		}

		return $contactInfoData;
	}
}

?>