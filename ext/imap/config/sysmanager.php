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

	// Add records configuration to DB records management of sysadmin area
TodoyuSysmanagerExtManager::addRecordConfig('imap', 'address', array(
	'label'			=> 'imap.ext.address',
	'description'	=> 'imap.ext.record.address.description',
	'list'			=> 'TodoyuImapAddressManager::getRecordsListingItems',
	'form'			=> 'ext/imap/config/form/admin/address.xml',
	'object'		=> 'TodoyuImapAddress',
	'delete'		=> 'TodoyuImapAddressManager::removeAddress',
	'save'			=> 'TodoyuImapAddressManager::saveAddress',
	'table'			=> 'ext_imap_address'
));

TodoyuSysmanagerExtManager::addRecordConfig('imap', 'account', array(
	'label'			=> 'imap.ext.account',
	'description'	=> 'imap.ext.record.account.description',
	'list'			=> 'TodoyuImapImapAccountManager::getRecordsListingItems',
	'form'			=> 'ext/imap/config/form/admin/account.xml',
	'object'		=> 'TodoyuImapImapAccount',
	'delete'		=> 'TodoyuImapImapAccountManager::removeAccount',
	'save'			=> 'TodoyuImapImapAccountManager::saveAccount',
	'table'			=> 'ext_imap_account',
	'onRecordDisplayJsCallback' => 'Todoyu.Ext.imap.initAccountForm()'
));

?>