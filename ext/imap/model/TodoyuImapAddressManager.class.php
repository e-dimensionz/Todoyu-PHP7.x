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
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapAddressManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_imap_address';

	/**
	 * @var	String		Record class name
	 */
	const CLASSNAME = 'TodoyuImapAddress';



	/**
	 * Get ID of address record of given email address.
	 *
	 * @param	String		$address
	 * @return	Integer
	 */
	public static function getAddressIDbyAddress($address) {
		$field	= 'id';
		$table	= self::TABLE;
		$where	= '		address = ' . TodoyuSql::quote($address, true)
				. ' AND deleted = 0';

		TodoyuCache::disable();

		$idAddress	= Todoyu::db()->getFieldValue($field, $table, $where, '', '', 1);

		TodoyuCache::enable();

		return intval($idAddress);
	}



	/**
	 * Get address
	 *
	 * @param	Integer				$idAddress
	 * @return	TodoyuImapAddress
	 */
	public static function getAddress($idAddress) {
		return TodoyuRecordManager::getRecord(self::CLASSNAME, $idAddress);
	}



	/**
	 * Get all IMAP email addresses from the database
	 *
	 * @return	Array	admin readable array of entries
	 */
	public static function getRecordsListingItems() {
		$items 			= array();
		$addresses	= self::getAllAddresses();

		foreach($addresses as $address) {
			$items[] = array(
				'id'	=> $address->getID(),
				'label'	=> $address->getLabel()
			);
		}

		return $items;
	}



	/**
	 * Get all (optionally only active ones) IMAP email addresses
	 *
	 * @return	TodoyuImapAddress[]
	 */
	public static function getAllAddresses() {
		$field	= 'id';
		$where	= 'deleted = 0 ';
		$order	= 'name,address';

		$addressIDs	= Todoyu::db()->getColumn($field, self::TABLE, $where, '', $order);

		return TodoyuRecordManager::getRecordList(self::CLASSNAME, $addressIDs);
	}



	/**
	 * Get address label
	 *
	 * @param	Integer		$idAddress
	 * @return	String
	 */
	public static function getLabel($idAddress) {
		return self::getAddress($idAddress)->getLabel();
	}



	/**
	 * Create new address record from given data
	 *
	 * @param	String	$address
	 * @param	String	$name
	 * @return	Integer				Record ID
	 */
	public static function addAddress($address, $name = '') {
		$data	= array(
			'address'	=> trim($address),
			'name'		=> trim($name),
		);

		return self::saveAddress($data, 0);
	}



	/**
	 * Store new address record
	 *
	 * @param	Array		$data
	 * @param	Integer		$idAddress
	 * @return	Integer					Record ID
	 */
	public static function saveAddress($data, $idAddress = 0) {
		$idAddress	= intval($data['id']);

		if( $idAddress == 0 ) {
			$idAddress = TodoyuRecordManager::addRecord(self::TABLE, array());
		}

		$xmlPath= 'ext/imap/config/form/admin/address.xml';
			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idAddress);

		TodoyuRecordManager::updateRecord(self::TABLE, $idAddress, $data);

		return $idAddress;
	}



	/**
	 * @param	Array		$data
	 * @param	Integer		$idAddress
	 */
	public static function updateAddress($data, $idAddress) {
		$idAddress	= intval($idAddress);

		self::saveAddress($data, $idAddress);
	}



	/**
	 * Removes given IMAP email address from the database
	 *
	 * @param	Integer	$idAddress
	 */
	public static function removeAddress($idAddress) {
		$idAddress	= intval($idAddress);

		TodoyuHookManager::callHook('imap', 'address.remove', array($idAddress));

		TodoyuRecordManager::deleteRecord(self::TABLE, $idAddress);
	}



	/**
	 * @param	Array 		$searchWords
	 * @param	Integer[]	$ignoreIDs
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function searchAddresses(array $searchWords, array $ignoreIDs = array(), $limit = 500) {
			// Abort empty input
		if( sizeof($searchWords) === 0 ) {
			return array();
		}

		$limit			= intval($limit);
		$searchFields	= array(
			'address',
			'name'
		);
		$likePart	= ' AND ' . TodoyuSql::buildLikeQueryPart($searchWords, $searchFields);

		if( sizeof($ignoreIDs) ) {
			$ignorePart	= ' AND ' . TodoyuSql::buildInListQueryPart($ignoreIDs, 'id', true, true);
		} else {
			$ignorePart	= '';
		}

		$fields	= '	id,
					address,
					name';
		$where	= '		deleted	= 0'
				. $likePart
				. $ignorePart;
		$order	= '	name,
					address,
					date_create DESC';


		return Todoyu::db()->getArray($fields, self::TABLE, $where, '', $order, $limit);
	}



	/**
	 * Get matching addresses
	 *
	 * @param	String[]	$searchWords
	 * @param	Integer[]	$ignoreIDs
	 * @return	Array
	 */
	public static function getMatchingAddresses(array $searchWords, array $ignoreIDs = array()) {
		$addresses	= self::searchAddresses($searchWords, $ignoreIDs, 30);
		$addressItems= array();

		foreach($addresses as $addressData) {
			$address	= self::getAddress($addressData['id']);

			$addressItems[] = array(
				'id'	=> $address['id'],
				'label'	=> $address->getLabel()
			);
		}

		return $addressItems;
	}



	/**
	 * Get matching address items for display in AC list with mixed types
	 * Prefix all address IDs and include resp. classname attributes
	 * Extracted emails from given address items and remove IMAP address items with identical email address
	 *
	 * @param	String[]	$searchWords
	 * @param	Integer[]	$ignoreIDs
	 * @param	Array		$persons
	 * @return	Array
	 */
	public static function getMatchingAddressesForMixedList(array $searchWords, array $ignoreIDs = array(), array $persons = array()) {
		$addresses	= self::getMatchingAddresses($searchWords, $ignoreIDs);

			// Prefix IDs with mail receiver type key: 'imapaddress' and add resp. classname to all items
		$typeKeyMailReceiver	= 'imapaddress';
		foreach($addresses as $index => $addressData) {
			$addresses[$index]['id']		= $typeKeyMailReceiver . ':' . $addressData['id'];
			$addresses[$index]['className']	= $typeKeyMailReceiver;
		}

			// Find and remove IMAP address items with address which is identically contained in the person items' labels already
		if( count($persons) > 0 ) {
			$emailsOfPersons	= self::extractEmailAddressesFromRecords($persons);
			foreach($addresses as $index => $address) {
				$emailAddress	= self::extractEmailAddressFromLabel($address['label']);
				if( in_array($emailAddress, $emailsOfPersons) ) {
					unset($addresses[$index]);
				}
			}
		}


		return $addresses;
	}



	/**
	 * Get matching email receiver tuples for mail receivers records selector
	 *
	 * @param	String[]		$searchWords
	 * @param	String[]		$ignoreTuples
	 * @param	Array			$params
	 * @return	String[]
	 */
	public static function getMatchingEmailReceiverImapAddress(array $searchWords, array $ignoreTuples = array(), array $params) {
		$ignoreIDs	= array();
		$tuples		= array();

		foreach($ignoreTuples as $ignoreTuple) {
			list($type, $idRecord) = explode(':', $ignoreTuple, 2);

			if( $type === 'imapaddress' ) {
				$ignoreIDs[] = $idRecord;
			}
		}

		$addresses	= self::searchAddresses($searchWords, $ignoreIDs, 30);

		foreach($addresses as $address) {
			$tuples[] = 'imapaddress:' . $address['id'];
		}

		return $tuples;
	}



	/**
	 * Extract email addresses from given records' labels
	 *
	 * @param	Array	$records	Records array, containing a 'label' attribute for each item
	 * @return	String[]
	 */
	private static function extractEmailAddressesFromRecords(array $records) {
		$emailAddresses	= array();
		foreach($records as $record) {
			$emailAddresses[]	= self::extractEmailAddressFromLabel($record['label']);
		}

		return $emailAddresses;
	}



	/**
	 * Extract email address from given label
	 *
	 * @param	String	$label
	 * @return	Mixed
	 */
	private static function extractEmailAddressFromLabel($label) {
		$pattern	= '/\(.*@.*\..*\)/';
		preg_match($pattern, $label, $match);

		return $match[0];
	}



	/**
	 * @param	Integer	$idMessage
	 * @param	String	$type
	 * @return	TodoyuImapAddress[]
	 */
	public static function getAddressesByType($idMessage, $type) {
		$addressIDs	= self::getAddressIDsByType($idMessage, $type);

		return TodoyuRecordManager::getRecordList('TodoyuImapAddress', $addressIDs);
	}



	/**
	 * Get IDs of addresses by message and type
	 *
	 * @param	Integer		$idMessage
	 * @param	Integer		$type
	 * @return	Integer[]
	 */
	public static function getAddressIDsByType($idMessage, $type) {
		$idMessage	= intval($idMessage);
		$type		= intval($type);

		$field	= 'id_address';
		$table	= 'ext_imap_mm_message_address';
		$where	= '		`id_message`= ' . $idMessage
				. ' AND	`type`		= ' . $type;

		return Todoyu::db()->getColumn($field, $table, $where);
	}



	/**
	 * @param	Integer		$idMessage
	 * @param	Boolean		$addressUnique
	 * @param	Integer		$type
	 * @return	Array[]
	 */
	public static function getMessageAddresses($idMessage, $addressUnique = false, $type = IMAP_ADDRESS_TYPE_TO) {
		$idMessage	= intval($idMessage);
		$type		= intval($type);

			// Get to-addresses of message
		$fields	= '	mmma.*,
					addr.*';
		$table	= '	ext_imap_address			addr,
					ext_imap_mm_message_address	mmma';
		$where	= '		mmma.id_address		= addr.id
					AND mmma.id_message		= ' . $idMessage .
				  ' AND	mmma.type			= ' . $type
				. '	AND	addr.deleted		= 0';
		$group	= '	mmma.id_address';
		$order	= '	addr.name,
					addr.address';

			// If addresses should be unique, group by ID
		if( $addressUnique ) {
			$group	= 'addr.id';
		}

		return Todoyu::db()->getArray($fields, $table, $where, $group, $order);
	}



	/**
	 * @static
	 * @param array $searchWords
	 * @param array $ignoreKeys
	 * @return	Array[]
	 */
	public static function getMatchingPersonAndAddress(array $searchWords, array $ignoreKeys = array()) {
					// Get person items (IDs prefixed with 'contactperson')
		$personItems		= TodoyuImapManager::getMatchingEmailPersons($searchWords, $ignoreKeys);
			// Get IMAP address items (IDs prefixed with 'imapaddress'), emails that also occur in $persons items are removed
		$mailAddressItems	= TodoyuImapAddressManager::getMatchingAddressesForMixedList($searchWords, $ignoreKeys, $personItems);

		return array_merge($personItems, $mailAddressItems);
	}

}

?>