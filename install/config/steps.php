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
 * Setup installer steps (installation, updating) order
 *
 * @package		Todoyu
 * @subpackage	Installer
 */

Todoyu::$CONFIG['INSTALLER']['install'] = array(
	'locale',
	'license',
	'servercheck',
	'dbconnection',
	'dbselect',
	'importtables',
	'systemconfig',
	'adminaccount',
	'importdemodata',
	'finish'
);

Todoyu::$CONFIG['INSTALLER']['update'] = array(
	'update',
	'updatetocurrentversion',
	'finishupdate'
);

	// Installation steps
define('INSTALLER_INITIALSTEP_INSTALL', 'locale');
define('INSTALLER_INITIALSTEP_UPDATE', 'update');

Todoyu::$CONFIG['INSTALLER']['steps'] = array(
		// Select language for installer and system preset
	'locale'	=> array(
		'process'	=> 'TodoyuInstallerManager::processLocale',
		'render'	=> 'TodoyuInstallerRenderer::renderLocale',
		'tmpl'		=> '00_locale.tmpl'
	),
		// Accept end user license
	'license' => array(
		'process'	=> 'TodoyuInstallerManager::processLicense',
		'render'	=> 'TodoyuInstallerRenderer::renderLicense',
		'tmpl'		=> '01_license.tmpl'
	),
	'servercheck' => array(
			// Check server compatibility
		'process'	=> 'TodoyuInstallerManager::processServerCheck',
		'render'	=> 'TodoyuInstallerRenderer::renderServerCheck',
		'tmpl'		=> '02_servercheck.tmpl',
		'fileCheck'	=> array(
			'files' => true,
			'config' => true,
			'log'	=> true,
			'cache/tmpl/compile' => true,
			'config/db.php' => true,
			'config/extensions.php' => true,
			'config/extconf.php' => true,
			'index.html' => false
		)
	),
	'dbconnection' => array(
			// Configure DB connection details
		'process'	=> 'TodoyuInstallerManager::processDbConnection',
		'render'	=> 'TodoyuInstallerRenderer::renderDbConnection',
		'tmpl'		=> '03_dbconnection.tmpl'
	),
	'dbselect' => array(
			// Configure to select existing or create new DB. Save DB connection data
		'process'	=> 'TodoyuInstallerManager::processDbSelect',
		'render'	=> 'TodoyuInstallerRenderer::renderDbSelect',
		'tmpl'		=> '04_dbselect.tmpl'
	),
	'importtables' => array(
			// Preview static data, than import it
		'process'	=> 'TodoyuInstallerManager::processImportDbTables',
		'render'	=> 'TodoyuInstallerRenderer::renderImportDbTables',
		'tmpl'		=> '05_importtables.tmpl'
	),
	'systemconfig' => array(
			// Update system config file (/config/system.php)
		'process'	=> 'TodoyuInstallerManager::processSystemConfig',
		'render'	=> 'TodoyuInstallerRenderer::renderSystemConfig',
		'tmpl'		=> '06_systemconfig.tmpl'
	),
	'adminaccount' => array(
		'process'	=> 'TodoyuInstallerManager::processAdminAccount',
		'render'	=> 'TodoyuInstallerRenderer::renderAdminAccount',
		'tmpl'		=> '07_adminaccount.tmpl'
	),
	'importdemodata'=> array(
		'process'	=> 'TodoyuInstallerManager::processImportDemoData',
		'render'	=> 'TodoyuInstallerRenderer::renderImportDemoData',
		'tmpl'		=> '08_importdemodata.tmpl'
	),
	'finish' => array(
		'process'	=> 'TodoyuInstallerManager::processFinish',
		'render'	=> 'TodoyuInstallerRenderer::renderFinish',
		'tmpl'		=> '09_finish.tmpl'
	),



		// ------------------------ Update steps ---------------------
	'update' => array(
		'process'	=> 'TodoyuInstallerManager::processUpdate',
		'render'	=> 'TodoyuInstallerRenderer::renderUpdate',
		'tmpl'		=> '10_update.tmpl'
	),
	'updatetocurrentversion' => array(
			// Mandatory version updates
		'process'	=> 'TodoyuInstallerManager::processUpdateToCurrentVersion',
		'render'	=> 'TodoyuInstallerRenderer::renderUpdateToCurrentVersion',
		'tmpl'		=> '12_updatetocurrentversion.tmpl',
	),
	'finishupdate' => array(
		'process'	=> 'TodoyuInstallerManager::processFinishUpdate',
		'render'	=> 'TodoyuInstallerRenderer::renderFinishUpdate',
		'tmpl'		=> '13_finishupdate.tmpl'
	)
);

?>