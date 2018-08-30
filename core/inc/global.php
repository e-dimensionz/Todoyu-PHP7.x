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

	// Set session cookie HTTP only
@ini_set('session.cookie_httponly', 1);
	// Force long session data lifetime (5 hours)
@ini_set('session.gc_maxlifetime', 3600 * 5);
	// Ignore errors of type notice
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
	// Set character encoding to utf-8
mb_internal_encoding('UTF-8');
	// Set session lifetime to 5 hours
session_cache_expire(300);
	// Set default dummy timezone
date_default_timezone_set('Europe/Paris');

	// Start session
session_start();


	// Define basic constants
require_once( dirname(dirname(__FILE__)) . '/config/constants.php' );

	// Add todoyu include path
set_include_path(get_include_path() . PATH_SEPARATOR . PATH);
	// Add PEAR to include path
set_include_path(get_include_path() . PATH_SEPARATOR . PATH_PEAR);

	// Load dwoo
require_once( PATH_LIB . '/php/dwoo/dwooAutoload.php' );

	// Load basic classes
require_once( PATH_CORE . '/model/Todoyu.class.php' );
require_once( PATH_CORE . '/model/TodoyuDatabase.class.php' );
require_once( PATH_CORE . '/model/TodoyuAuth.class.php' );
require_once( PATH_CORE . '/model/TodoyuBaseObject.class.php' );
require_once( PATH_CORE . '/model/TodoyuExtensions.class.php' );
require_once( PATH_CORE . '/model/TodoyuSession.class.php' );
require_once( PATH_CORE . '/model/TodoyuLabelManager.class.php' );
require_once( PATH_CORE . '/model/TodoyuCache.class.php' );
require_once( PATH_CORE . '/model/TodoyuLogger.class.php' );
require_once( PATH_CORE . '/model/TodoyuRequest.class.php' );
require_once( PATH_CORE . '/model/TodoyuActionController.class.php' );
require_once( PATH_CORE . '/model/TodoyuActionDispatcher.class.php' );
require_once( PATH_CORE . '/model/TodoyuArray.class.php' );
require_once( PATH_CORE . '/model/TodoyuPreferenceManager.class.php' );
require_once( PATH_CORE . '/model/TodoyuFileManager.class.php' );
require_once( PATH_CORE . '/model/TodoyuRightsManager.class.php' );
require_once( PATH_CORE . '/model/TodoyuHookManager.class.php' );
require_once( PATH_CORE . '/model/TodoyuHeader.class.php' );
require_once( PATH_CORE . '/model/TodoyuPanelWidgetManager.class.php' );
require_once( PATH_CORE . '/model/TodoyuErrorHandler.class.php' );
require_once( PATH_CORE . '/model/TodoyuAutoloader.class.php' );

	// Include basic person classes
require_once(PATH_EXT . '/contact/model/TodoyuContactPerson.class.php');
require_once(PATH_EXT . '/contact/model/TodoyuContactPersonManager.class.php');
require_once( PATH_EXT .  '/contact/model/TodoyuContactPreferences.class.php' );

	// Load development classes
require_once( PATH_CORE . '/model/TodoyuDebug.class.php' );
require_once( PATH_LIB . '/php/FirePHPCore/FirePHP.class.php' );

	// Register autoloader
spl_autoload_register( array('TodoyuAutoloader', 'load') );

	// Register error handler
set_error_handler(array('TodoyuErrorHandler', 'handleError'));

	// Load global functions @todo: Only load dwoo plugins when needed
require_once( PATH_CORE . '/inc/version.php' );
require_once( PATH_CORE . '/model/dwoo/plugins.php' );
require_once( PATH_CORE . '/model/dwoo/Dwoo_Plugin_restrict.php' );
require_once( PATH_CORE . '/model/dwoo/Dwoo_Plugin_restrictAdmin.php' );
require_once( PATH_CORE . '/model/dwoo/Dwoo_Plugin_restrictIfNone.php' );
require_once( PATH_CORE . '/model/dwoo/Dwoo_Plugin_restrictOrOwn.php' );
require_once( PATH_CORE . '/model/dwoo/Dwoo_Plugin_restrictInternal.php' );

	// Include strptime function if not defined on windows
if( ! function_exists('strptime') ) {
	require_once( PATH_CORE . '/inc/strptime.function.php' );
}

	// Load installed extension list
require_once( PATH_LOCALCONF . '/extensions.php');

	// Load basic core config
require_once( PATH_CONFIG . '/config.php');
require_once( PATH_CONFIG . '/locales.php');
require_once( PATH_CONFIG . '/fe.php');
require_once( PATH_CONFIG . '/assets.php');
require_once( PATH_CONFIG . '/cache.php');
require_once( PATH_CONFIG . '/colors.php');


	// Load local config
require_once( PATH_LOCALCONF . '/db.php');
require_once( PATH_LOCALCONF . '/system.php');
require_once( PATH_LOCALCONF . '/settings.php');
require_once( PATH_LOCALCONF . '/config.php');

	// Load extconf
TodoyuExtensions::loadExtConf();

?>