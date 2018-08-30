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
 * Manage comment mail DB logs
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentMailManager {

	/**
	 * Save comment mail as sent for each receiver
	 *
	 * @param	Integer							$idComment
	 * @param	TodoyuMailReceiverInterface[]	$mailReceivers
	 */
	public static function saveMailSent($idComment, array $mailReceivers) {
		foreach($mailReceivers as $mailReceiver) {
			$idReceiver		= $mailReceiver->getRecordID();
			$receiverType	= $mailReceiver->getType();

			TodoyuMailManager::addMailSent(EXTID_COMMENT, COMMENT_TYPE_COMMENT, $idComment, $idReceiver, $receiverType);
		}
	}



	/**
	 * Log sent comment email of given comment to given person
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idPerson
	 */
	public static function addMailSent($idComment, $idPerson) {
		TodoyuMailManager::addMailSent(EXTID_COMMENT, COMMENT_TYPE_COMMENT, $idComment, $idPerson);
	}



	/**
	 * Get receivers the given comment has been sent to by email
	 *
	 * @param	Integer		$idComment
	 * @return	TodoyuMailReceiverInterface[]
	 */
	public static function getEmailReceivers($idComment) {
		return TodoyuMailManager::getEmailReceivers(EXTID_COMMENT, COMMENT_TYPE_COMMENT, $idComment);
	}

}

?>