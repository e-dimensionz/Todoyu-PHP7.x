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
 * Manage raw messages
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapRawMessageManager {

	/**
	 * Get raw message
	 *
	 * @param	String		$messageKey
	 * @return	TodoyuImapRawMessage
	 */
	public static function getRawMessage($messageKey) {
		return new TodoyuImapRawMessage($messageKey);
	}



	/**
	 * Add a raw message record
	 * The message key is the relative path of the message file
	 * Year and month are used as folders
	 * Ex: /var/www/path/to/todoyu/files/imap/message/2012/10/ks3afhecdabef389jdabeda34535bedabesdfhassdf.eml
	 *
	 * @param	String		$header
	 * @param	String		$body
	 * @return	String		Path of the message file relative to message storage path
	 */
	public static function saveRawMessage($header, $body) {
		$content		= $header . "\r\n\r\n" . $body;
		$messageKey		= sha1($header);
		$messageKeyPath	= date('Y') . '/' . date('m') . '/' . $messageKey;
		$storagePath	= self::getMessagePath($messageKeyPath);

		TodoyuFileManager::saveFileContent($storagePath, $content);

		return $messageKeyPath;
	}



	/**
	 * Get message storage path
	 *
	 * @param	String		$messageKey		Relative message filename
	 * @return	String		Absolute path the message file
	 */
	public static function getMessagePath($messageKey) {
		return TodoyuFileManager::pathAbsolute(IMAP_PATH_MESSAGES . '/' . $messageKey . '.eml');
	}

}

?>