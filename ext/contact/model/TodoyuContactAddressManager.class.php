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
 * Address Manager
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactAddressManager {

	/**
	 * Ext DB table
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_address';



	/*
	 * Config for person and company
	 *
	 * @var	Array
	 */
	private static $mmConfig	= array(
		'person'	=> array(
			'table'	=> 'ext_contact_mm_person_address',
			'field'	=> 'id_person'
		),
		'company'	=> array(
			'table'	=> 'ext_contact_mm_company_address',
			'field'	=> 'id_company'
		)
	);



	/**
	 * Return the requested Address object
	 *
	 * @param	Integer			$idAddress
	 * @return	TodoyuContactAddress
	 */
	public static function getAddress($idAddress) {
		$idAddress	= intval($idAddress);

		return TodoyuRecordManager::getRecord('TodoyuContactAddress', $idAddress);
	}



	/**
	 * Save the address record to table ext_contact_address
	 * returns the ID of the saved record
	 *
	 * @param	Array	$data
	 * @return	Integer
	 */
	public static function saveAddress(array $data) {
		$idAddress	= intval($data['id']);

		if( $idAddress === 0 ) {
			$idAddress = self::addAddress();
		}

		self::updateAddress($idAddress, $data);

		self::removeFromCache($idAddress);

		return $idAddress;
	}



	/**
	 * Add address record to DB
	 *
	 * @param	Array	$data
	 * @return	Integer
	 */
	public static function addAddress(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update address record in DB
	 *
	 * @param	Integer		$idAddress
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateAddress($idAddress, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idAddress, $data);
	}



	/**
	 * Set given address record in DB deleted
	 *
	 * @param	Integer		$idAddress
	 */
	public static function deleteAddress($idAddress) {
		$update	= array(
			'deleted'	=> 1
		);

		self::updateAddress($idAddress, $update);
	}



	/**
	 * Remove record from cache
	 *
	 * @param	Integer	$idAddress
	 */
	protected static function removeFromCache($idAddress) {
		$idAddress	= intval($idAddress);

		TodoyuRecordManager::removeRecordCache('TodoyuContactAddress', $idAddress);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idAddress);
	}



	/**
	 * Get all addresses of given company
	 *
	 * @param	Integer	$idCompany
	 * @param	String	$addressFields		e.g 'ext_contact_address.*' to get all fields, otherwise comma separated field names
	 * @param	String	$orderBy
	 * @return	Array
	 */
	public static function getCompanyAddresses($idCompany, $addressFields = '', $orderBy = 'city') {
		$idCompany	= intval($idCompany);

		$fields		= '	ext_contact_mm_company_address.*,
						' . ( $addressFields != '' ? $addressFields : self::TABLE . '.*' )
						. ',' . self::TABLE . '.id addrId';

		$tables		= '	ext_contact_mm_company_address'
					. ',' . self::TABLE;
		$where		= '		ext_contact_mm_company_address.id_company	= ' . $idCompany .
					  ' AND	' . self::TABLE . '.id						= ext_contact_mm_company_address.id_address';
		$orderBy	= 'city';
		$groupBy	= $limit	= '';
		$indexField	= 'addrId';

		return Todoyu::db()->getArray($fields, $tables, $where,  $groupBy, $orderBy, $limit, $indexField);
	}



	/**
	 * Get label for address type
	 *
	 * @param	Integer		$idAddressType
	 * @return	String
	 */
	public static function getAddresstypeLabel($idAddressType) {
		$idAddressType	= intval($idAddressType);

		return Todoyu::Label('contact.ext.address.attr.addresstype.' . $idAddressType);
	}



	/**
	 * Get IDs of the most used countries over all address records
	 *
	 * @return	Integer[]
	 */
	public static function getMostUsedCountryIDs() {
		$field	= 'id_country';
		$table	= self::TABLE;
		$where	= '';
		$group	= 'id_country';
		$order	= 'COUNT(*) DESC';
		$limit	= intval(Todoyu::$CONFIG['EXT']['contact']['numFavoriteCountries']);

		return Todoyu::db()->getColumn($field, $table, $where, $group, $order, $limit);
	}



	/**
	 * Search stored addresses for cities, optionally filter by search-word
	 *
	 * @param	String		$sword
	 * @return  String[]
	 */
	public static function searchStoredCities($sword = '') {
		$field	= 'city';
		$table	= self::TABLE;
		$where	= '';

		if( !empty($limitByRelationTable) ) {

		}

		if( !empty($sword) ) {
			$swords	= explode(' ', trim($sword));
			$where	= TodoyuSql::buildLikeQueryPart($swords, array(self::TABLE . '.city'))
					. ' AND		' . self::TABLE . '.deleted		= 0';
		}

		$group	= 'city';
		$order	= 'city';

		return Todoyu::db()->getColumn($field, $table, $where, $group, $order);
	}



	/**
	 * Get label of given address
	 *
	 * @param	Integer	$idAddress
	 * @return	String
	 */
	public static function getLabel($idAddress) {
		$idAddress	= intval($idAddress);

		if( $idAddress > 0 ) {
			$address		= self::getAddress($idAddress);
			$label			= $address->getLabel();
		} else {
			$label	= '';
		}

		return $label;
	}



	/**
	 * Delete linked addresses
	 *
	 * @param	String		$mmTable
	 * @param	Integer		$idRecord
	 * @param	Array		$currentAddressIDs
	 * @param	String		$fieldRecord
	 * @param	String		$fieldInfo
	 * @return	Integer							Amount of deleted records
	 */
	public static function deleteLinkedAddresses($mmTable, $idRecord, array $currentAddressIDs,  $fieldRecord, $fieldInfo = 'id_address') {
		return TodoyuDbHelper::deleteOtherMmRecords($mmTable, 'ext_contact_address', $idRecord, $currentAddressIDs, $fieldRecord, $fieldInfo);
	}



	/**
	 * Load data hook for address forms
	 *
	 * @param	Array		$data
	 * @param	Integer		$idAddress
	 * @param	Array		$params
	 * @return	Array
	 */
	public static function hookAddressLoadFormData(array $data, $idAddress, array $params = array()) {
		if( $idAddress === 0 ) {
			$timezone			= Todoyu::$CONFIG['SYSTEM']['timezone'];
			$data['id_timezone']= TodoyuStaticRecords::getTimezoneID($timezone);
		}

		return $data;
	}



	/**
	 * Prepare found duplicated address-records
	 *
	 * @param	Array		$searchWords
	 * @return	Array
	 */
	public static function getDuplicatedAddresses(array $searchWords) {
		$records	= array();

		$duplicates = self::searchForDuplicatedAddresses($searchWords);

		if ( sizeof($duplicates) > 0 ) {
			foreach ($duplicates as $duplicatedAddressInfo) {
				$label = '';

				if ( $duplicatedAddressInfo['id_person'] ) {
					if ( TodoyuContactRights::isAddresstypeOfPersonSeeAllowed($duplicatedAddressInfo['id_person'], $duplicatedAddressInfo['id_addresstype']) ) {
						$label = TodoyuContactPersonManager::getPerson($duplicatedAddressInfo['id_person'])->getFullName(true);
					}
				} else if ( $duplicatedAddressInfo['id_company'] ) {
					if ( TodoyuContactRights::isAddresstypeOfCompanySeeAllowed($duplicatedAddressInfo['id_company'], $duplicatedAddressInfo['id_addresstype']) ) {
						$label = TodoyuContactCompanyManager::getCompany($duplicatedAddressInfo['id_company'])->getTitle();
					}
				}

				if ( $label ) {
					$records[]['title'] = self::getAddresstypeLabel($duplicatedAddressInfo['id_addresstype']) . ' - ' . $label;
				}
			}
		}

		return $records;
	}



	/**
	 * Search for duplicated addresses for person & company
	 *
	 * @param	Array	$searchWords
	 * @return	Array
	 */
	protected static function searchForDuplicatedAddresses(array $searchWords) {
		$results = array();

		foreach(self::$mmConfig as $mmConfig) {
			$fields	= 'a.id,'
					. 'a.id_addresstype,
					. mm.' . $mmConfig['field'];
			$tables	= self::TABLE . ' a, '
					 . $mmConfig['table'] . ' mm';
			$where	= ' mm.id_address							= a.id'
					. '	AND a.deleted = 0'
					. ' AND ' . TodoyuSql::buildLikeQueryPart($searchWords, array('a.street', 'a.zip', 'a.city', 'a.postbox'));

			$results = array_merge($results, Todoyu::db()->getArray($fields, $tables, $where));
		}

		return $results;
	}
}

?>