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
 * Imap mailbox manager
 *
 * @package			Todoyu
 * @subpackage		Imap
 */
class TodoyuImapMailboxManager {

	protected static $mailboxes = array();



	/**
	 * Try to connect to IMAP mailbox.
	 * Returns TodoyuImapMailbox instance / FALSE if connection failed
	 *
	 * @param	TodoyuImapImapAccount	$account
	 * @param	Array				$options
	 * @return	TodoyuImapMailbox
	 */
	public static function getMailbox(TodoyuImapImapAccount $account, array $options = array()) {
		$ident	= md5($account->getID() . json_encode($options));

		if( !isset(self::$mailboxes[$ident]) ) {
			self::$mailboxes[$ident] = new TodoyuImapMailbox($account, $options);
		}

		return self::$mailboxes[$ident];
	}



	/**
	 * Convert UTF-8 string to UTF7-IMAP encoding
	 *
	 * @param	String		$utf8String
	 * @return	String
	 */
	public static function getAsImapUtf7($utf8String) {
		return mb_convert_encoding($utf8String, 'UTF7-IMAP', 'UTF-8');
	}



	/**
	 * Replace invalid characters of folder names
	 * Encode
	 *
	 * @todo	Is there a special escape character we can use? Not found yet =(
	 * @param	String		$folderName
	 * @return	String
	 */
	public static function getEscapedFolderName($folderName) {
		$badCharacters	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['imap']['mailboxName']['badChars']);
		$replace		= trim(Todoyu::$CONFIG['EXT']['imap']['mailboxName']['replaceWith']);

		return str_replace($badCharacters, $replace, $folderName);
	}

}

?>