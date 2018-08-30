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
 * Constants for imap extension
 *
 * @package		Todoyu
 * @subpackage	Imap
 */

	// Email address types (ext_imap_mm_message_address)
define('IMAP_ADDRESS_TYPE_TO',		1);
define('IMAP_ADDRESS_TYPE_CC',		2);
define('IMAP_ADDRESS_TYPE_REPLYTO',	3);

	// Message states: all, recent, unread, deleted
define('IMAP_MESSAGE_STATE_ALL',		'Nmsgs');
define('IMAP_MESSAGE_STATE_RECENT',		'Recent');
define('IMAP_MESSAGE_STATE_UNREAD',		'Unread');
define('IMAP_MESSAGE_STATE_DELETED',	'Deleted');

	// Mailbox folder search patterns
define('IMAP_FOLDER_SEARCHPATTERN_ALL',				'*');
define('IMAP_FOLDER_SEARCHPATTERN_CURRENTLEVEL',	'%');

	// Content encoding
define('IMAP_ENCODING_7BIT',	0);
define('IMAP_ENCODING_8BIT',	1);
define('IMAP_ENCODING_BINARY',	2);
define('IMAP_ENCODING_BASE64',	3);
define('IMAP_ENCODING_QUOTED_PRINTABLE',	4);
define('IMAP_ENCODING_OTHER',	5);

define('IMAP_MESSAGE_TYPE_TEXT', 		0);
define('IMAP_MESSAGE_TYPE_MULTIPART', 	1);
define('IMAP_MESSAGE_TYPE_MESSAGE', 	2);
define('IMAP_MESSAGE_TYPE_APPLICATION', 3);
define('IMAP_MESSAGE_TYPE_AUDIO', 		4);
define('IMAP_MESSAGE_TYPE_IMAGE', 		5);
define('IMAP_MESSAGE_TYPE_VIDEO', 		6);
define('IMAP_MESSAGE_TYPE_OTHER', 		7);

	// Storage path
define('IMAP_PATH_MESSAGES', PATH_FILES . '/imap/message'); // Raw messages (eml)
define('IMAP_PATH_ATTACHMENT', PATH_FILES . '/imap/attachment'); // Attachment files
define('IMAP_PATH_INLINEIMAGE', PATH_FILES . '/imap/inlineimage'); // Multipart inline images

?>