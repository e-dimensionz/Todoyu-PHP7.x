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
 * Methods to import IMAP mails into todoyu + manage the IMAP message records
 *
 * @package			Todoyu
 * @subpackage		Imap
 */
class TodoyuImapMessageManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_imap_message';



	/**
	 * Get message
	 *
	 * @param	Integer		$idMessage
	 * @return	TodoyuImapMessage
	 */
	public static function getMessage($idMessage) {
		return TodoyuRecordManager::getRecord('TodoyuImapMessage', $idMessage);
	}



	/**
	 * Add a new email record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addMessage(array $data = array()) {
		$idMessage = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('imap', 'message.add', array($idMessage));

		return $idMessage;
	}



	/**
	 * Delete a message in the database (set deleted flag to 1)
	 *
	 * @param	Integer		$idMessage
	 */
	public static function deleteMessage($idMessage) {
		$idMessage	= intval($idMessage);

		$data	= array(
			'deleted'	=> 1
		);

		self::updateMessage($idMessage, $data);

		TodoyuHookManager::callHook('imap', 'message.delete', array($idMessage));
	}



	/**
	 * Update data of message with given ID
	 *
	 * @param	Integer		$idMessage
	 * @param	Array		$data
	 */
	public static function updateMessage($idMessage, array $data) {
		TodoyuRecordManager::removeRecordCache('TodoyuImapMessage', $idMessage);

		TodoyuRecordManager::updateRecord(self::TABLE, $idMessage, $data);

		TodoyuHookManager::callHook('imap', 'message.update', array($idMessage, $data));
	}



	/**
	 *
	 *
	 * @param	Array		$addressData
	 * @return	Integer
	 */
	public static function saveAddress(array $addressData) {
		$name	= trim($addressData['name']);
		$address= trim($addressData['address']);

		$idAddress	= TodoyuImapAddressManager::getAddressIDbyAddress($address, true);

		if( $idAddress === 0 ) {
			$idAddress	= TodoyuImapAddressManager::addAddress($address, $name);
		}

		return $idAddress;
	}



	/**
	 * Save an address to the address book and link it to the message
	 *
	 * @param	Integer		$idMessage
	 * @param	Array[]		$typeAddresses
	 * @param	Integer		$type
	 */
	public static function saveAddressesToAddressBook($idMessage, array $typeAddresses, $type) {
		if( is_array($typeAddresses) ) {
				// Store TO address(es): ext_imap_address + ext_imap_mm_message_address
			foreach($typeAddresses as $typeAddress) {
				$idTypeAddress = self::saveAddressToAddressBook($typeAddress);

				self::addLinkAddressMessage($type, $idTypeAddress, $idMessage);
			}
		}
	}



	/**
	 * Save an address to the address book
	 *
	 * @param	Array	$addressData
	 * @return	Integer
	 */
	public static function saveAddressToAddressBook(array $addressData) {
		$name		= trim($addressData['name']);
		$address	= trim($addressData['address']);
		$idAddress	= TodoyuImapAddressManager::getAddressIDbyAddress($address);

		if( $idAddress === 0 ) {
			$idAddress	= TodoyuImapAddressManager::addAddress($address, $name);
		}

		return $idAddress;
	}



	/**
	 * Link an address to a message with a type
	 *
	 * @param	Integer		$type
	 * @param	Integer		$idAddress
	 * @param	Integer		$idMessage
	 * @return	Integer
	 */
	public static function addLinkAddressMessage($type, $idAddress, $idMessage) {
		$extraData	= array(
			'type'	=> $type
		);

		return TodoyuDbHelper::addMMLink('ext_imap_mm_message_address', 'id_message', 'id_address', $idMessage, $idAddress, $extraData);
	}



	/**
	 * Get ID of address with given credentials (create newly if unknown) and store as address relation of given type of given message record.
	 *
	 * @param	Integer		$idMessage
	 * @param	String		$address
	 * @param	String		$name
	 * @param	String		$column
	 * @todo	Used anywhere?
	 */
	public static function updateMessageAddress($idMessage, $address, $name, $column = 'id_email_from') {
		$idAddress	= TodoyuImapAddressManager::getAddressIDbyAddress($address, true);

		if( $idAddress === 0 ) {
			$idAddress	= TodoyuImapAddressManager::addAddress($address, $name);
		}

		self::updateMessage($idMessage, array($column => $idAddress));
	}



	/**
	 * Filter given message_id's: reduce to ones of not yet imported messages
	 *
	 * @param	Integer		$idAccount
	 * @param	Array		$messageNumberIdMap
	 * @return	Array
	 */
	public static function reduceMessageIDsToNotYetImported($idAccount, array $messageNumberIdMap) {
		$importedMessageIDs	= self::getImportedMessageIds($idAccount, $messageNumberIdMap);

		foreach($messageNumberIdMap as $messageNo => $messageId) {
			if( in_array($messageId, $importedMessageIDs) ) {
				unset($messageNumberIdMap[$messageNo]);
			}
		}

		return $messageNumberIdMap;
	}



	/**
	 * Get message ids of imported messages which are the same as in the map list
	 *
	 * @param	Integer		$idAccount
	 * @param	Array		$messageNumberIdMap
	 * @return	Integer[]
	 */
	public static function getImportedMessageIds($idAccount, array $messageNumberIdMap) {
		$idAccount	= intval($idAccount);

		$field	= 'message_id';
		$table	= self::TABLE;
		$where	= '		id_account	= ' . $idAccount
				. ' AND ' . TodoyuSql::buildInListQueryPart($messageNumberIdMap, 'message_id', false);
		$order	= 'id DESC'; // only the latest
		$limit	= 500; // Limit to last 500

		return Todoyu::db()->getColumn($field, $table, $where, '', $order, $limit);
	}



	/**
	 * Get all "to" addresses of given message
	 *
	 * @param	Integer		$idMessage
	 * @param	Boolean		$addressUnique
	 * @return	Array[]
	 */
	public static function getMessageAddressesTo($idMessage, $addressUnique = false) {
		return self::getMessageAddresses($idMessage, $addressUnique, IMAP_ADDRESS_TYPE_TO);
	}



	/**
	 * Get all "cc" addresses of given message
	 *
	 * @param	Integer		$idMessage
	 * @param	Boolean		$addressUnique
	 * @return	Array[]
	 */
	public static function getMessageAddressesCc($idMessage, $addressUnique = false) {
		return self::getMessageAddresses($idMessage, $addressUnique, IMAP_ADDRESS_TYPE_CC);
	}



	/**
	 * Get all "replyto" addresses of given message
	 *
	 * @param	Integer		$idMessage
	 * @param	Boolean		$addressUnique
	 * @return	Array[]
	 */
	public static function getMessageAddressesReplyTo($idMessage, $addressUnique = false) {
		return self::getMessageAddresses($idMessage, $addressUnique, IMAP_ADDRESS_TYPE_REPLYTO);
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
	 * Replace src path of inline images with server url
	 *
	 * @param	String		$html
	 * @return	String
	 */
	public static function replaceInlineImagePaths($html) {
		$pattern	= '/(cid:([^"]+))/';
		$replacement= 'index.php?ext=imap&amp;controller=message&amp;action=inlineimage&amp;image=\2';

		return preg_replace($pattern, $replacement, $html);
	}



	/**
	 * Clean message id. Remove angle brackets
	 *
	 * @param	String		$messageId
	 * @return	String
	 */
	public static function cleanID($messageId) {
		return trim(str_replace(array('<', '>'), '', $messageId));
	}



	/**
	 * Get ID of message by message-id
	 *
	 * @param	String		$messageId
	 * @return	Integer
	 */
	public static function getRecordIdByMessageId($messageId) {
		$field	= 'id';
		$where	= '	message_id = ' . TodoyuSql::quote($messageId, true);

		$idMessage	= Todoyu::db()->getFieldValue($field, self::TABLE, $where, '', '', 1);

		return intval($idMessage);
	}

}

?>