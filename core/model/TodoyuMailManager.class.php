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
 * Manage mail DB logs
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuMailManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'system_log_email';



	/**
	 * Get mailer type
	 * 0 = mail() function
	 * string = custom type
	 *
	 * @return	String
	 */
	public static function getSystemMailerType() {
		return Todoyu::$CONFIG['SYSTEM']['mailer'];
	}



	/**
	 * Save log record about receivers (e.g. persons) the given mail has been sent to
	 *
	 * @param	Integer		$extID				EXTID of extension the record belongs to
	 * @param	Integer		$type				Type of record (comment, event, etc.) the email refers to
	 * @param	Integer		$idRecord			ID of record the email refers to
	 * @param	Array		$receiverTuples		Mail receiver tuples ('type:ID' or 'ID' which defaults the type to 'contactperson')
	 */
	public static function saveMailsSent($extID, $type, $idRecord, array $receiverTuples = array() ) {
		$extID		= (int) $extID;
		$type		= (int) $type;
		$idRecord	= (int) $idRecord;
		$receiverTuples	= TodoyuArray::trim($receiverTuples);

		foreach($receiverTuples as $receiverTuple) {
			$mailReceiver	= TodoyuMailReceiverManager::getMailReceiver($receiverTuple);

			$receiverType	= $mailReceiver->getType();

			self::addMailSent($extID, $type, $idRecord, $receiverTuple, $receiverType);
		}
	}



	/**
	 * Log sent email of given type to given person
	 *
	 * @param	Integer		$extID			EXTID of extension the record belongs to
	 * @param	Integer		$type			Type of record (comment, event, etc.) the email refers to
	 * @param	Integer		$idRecord		ID of record the email refers to
	 * @param	Integer		$idReceiver
	 * @param	String		$receiverType
	 */
	public static function addMailSent($extID, $type, $idRecord, $idReceiver, $receiverType = 'contactperson') {
		$extID		= (int) $extID;
		$type		= (int) $type;
		$idRecord	= (int) $idRecord;
		$idReceiver	= (int) $idReceiver;

		$data	= array(
			'date_create'		=> NOW,
			'id_person_create'	=> Todoyu::personid(),
			'ext'				=> $extID,
			'record_type'		=> $type,
			'id_record'			=> $idRecord,
			'id_receiver'		=> $idReceiver,
			'receiver_type'		=> $receiverType
		);

		TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Get receivers the given comment has been emailed to
	 *
	 * @param	Integer		$extID			EXTID of extension the record belongs to
	 * @param	Integer		$type			Type of record (comment, event, etc.) the email refers to
	 * @param	Integer		$idRecord		ID of record the email refers to
	 * @return	TodoyuMailReceiverInterface[]
	 */
	public static function getEmailReceivers($extID, $type, $idRecord) {
		$extID		= (int) $extID;
		$type		= (int) $type;
		$idRecord	= (int) $idRecord;

		$fields	= '	e.id_receiver,
					e.receiver_type';
		$table	= '		system_log_email e';
		$where	= '		e.ext				= ' . $extID .
				'	AND	e.record_type		= \'' . $type . '\' ' .
				'	AND	e.id_record			= ' . $idRecord;

		$receiverRecords	= Todoyu::db()->getArray($fields, $table, $where);

		$receiverObjects	= array();
		foreach($receiverRecords as $recordData) {
			$receiverTuple	= $recordData['receiver_type'] . ':' . $recordData['id_receiver'];
			$receiverObjects[$receiverTuple]	= TodoyuMailReceiverManager::getMailReceiver($receiverTuple);
		}

		return $receiverObjects;
	}

}

?>