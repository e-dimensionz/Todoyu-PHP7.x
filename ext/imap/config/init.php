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
spl_autoload_register(function ($class_name) {
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/ext/imap/model/".$class_name.".class.php"))
	{
		require_once $_SERVER['DOCUMENT_ROOT']."/ext/imap/model/".$class_name.".class.php";
	}
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/ext/imap/controller/".$class_name.".class.php"))
	{
		require_once $_SERVER['DOCUMENT_ROOT']."/ext/imap/model/".$class_name.".class.php";
	}
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/ext/admin/controller/".$class_name.".class.php"))
	{
		require_once $_SERVER['DOCUMENT_ROOT']."/ext/admin/controller/".$class_name.".class.php";
	}
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/ext/admin/model/".$class_name.".class.php"))
	{
		require_once $_SERVER['DOCUMENT_ROOT']."/ext/admin/model/".$class_name.".class.php";
	}
});
	// Add allowed paths where files can be downloaded from
Todoyu::$CONFIG['sendFile']['allow'][]	= TodoyuImapAttachmentManager::getStorageBasePath();

	// Add email receiver type: 'imapaddress'
TodoyuMailReceiverManager::addType('imapaddress', 'TodoyuImapMailReceiverAddress');

	// Records selector: mail address
TodoyuFormRecordsManager::addType('mailAddress', 'TodoyuImapFormElement_RecordsMailAddress', 'TodoyuSlaImapManager::getMatchingMailAddresses');

	// Records selector: Person and address
TodoyuFormRecordsManager::addType('personAndAddress', 'TodoyuImapFormElement_RecordsPersonAndAddress', 'TodoyuImapAddressManager::getMatchingPersonAndAddress');

if( Todoyu::allowed('contact', 'person:seeAllPersons') ) {
	TodoyuMailReceiverManager::addSearchCallback('TodoyuImapAddressManager::getMatchingEmailReceiverImapAddress');
}

Todoyu::$CONFIG['EXT']['imap'] = array(
	'mailboxName' => array(
		'badChars'	=> array(
			'/',
			'.',
			':',
			'\\'
		),
		'replaceWith' => '_'
	),
	'deleteFiles'	=> false // Physically delete files or use only 'deleted' flag?
);

?>
