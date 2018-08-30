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
 * Update script for command line
 */

	// Make sure only CLI access is allowed
chdir( dirname(dirname(dirname(__FILE__))) );

	// Load global config
require_once('core/inc/global.php');

	// Deactivate extensions during update
Todoyu::$CONFIG['INIT'] = false;

	// Load default init script
require_once('core/inc/init.php');

	// Prevent HTTP request
TodoyuCli::assertShell();

	// Clear all cache
TodoyuInstallerManager::clearCache();
	// Run special version updates (PHP+SQL) which are not handled by tables.sql autoupdates
TodoyuInstallerManager::runCoreVersionUpdates();
	// Update database based on the tables.sql files of the installed extensions
TodoyuSQLManager::updateDatabaseFromTableFiles();
	// Remove index.html file if restored by svn update
TodoyuInstallerManager::removeIndexRedirector();

?>