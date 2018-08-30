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

	// Register callbacks of allowed sysmanager modules
if( TodoyuAuth::isAdmin() ) { // Config manager
	TodoyuSysmanagerManager::addModule('config', 'sysmanager.ext.menu.config', 'TodoyuSysmanagerSystemConfigRenderer::renderModule', 40);
	Todoyu::$CONFIG['EXT']['sysmanager']['defaultModule'] = 'config';
}
if( TodoyuAuth::isAdmin() ) { // Rights manager
	TodoyuSysmanagerManager::addModule('rights', 'sysmanager.ext.menu.rights', 'TodoyuSysmanagerRightsEditorRenderer::renderModule', 30);
	Todoyu::$CONFIG['EXT']['sysmanager']['defaultModule'] = 'rights';
}
if( Todoyu::allowed('sysmanager', 'general:records') ) { // Records manager
	TodoyuSysmanagerManager::addModule('records', 'sysmanager.ext.menu.records', 'TodoyuSysmanagerExtRecordRenderer::renderModule', 20);
	Todoyu::$CONFIG['EXT']['sysmanager']['defaultModule'] = 'records';

		// Add SMTP account record configuration to DB records management
	TodoyuSysmanagerExtManager::addRecordConfig('sysmanager', 'smtpaccount', array(
		'label'			=> 'sysmanager.ext.record.smtpaccount.title',
		'description'	=> 'sysmanager.ext.record.smtpaccount.description',
		'list'			=> 'TodoyuSysmanagerSmtpAccountManager::getRecordsListingItems',
		'form'			=> 'ext/sysmanager/config/form/admin/smtp-account.xml',
		'object'		=> 'TodoyuSysmanagerSmtpAccount',
		'delete'		=> 'TodoyuSysmanagerSmtpAccountManager::removeAccount',
		'save'			=> 'TodoyuSysmanagerSmtpAccountManager::saveAccount',
		'table'			=> 'ext_sysmanager_smtpaccount'
	));
}

if( Todoyu::allowed('sysmanager', 'general:extensions') ) { // Extension manager
	TodoyuSysmanagerManager::addModule('extensions', 'sysmanager.ext.menu.extensions', 'TodoyuSysmanagerExtManagerRenderer::renderModule', 10);
	Todoyu::$CONFIG['EXT']['sysmanager']['defaultModule'] = 'extensions';
}

?>