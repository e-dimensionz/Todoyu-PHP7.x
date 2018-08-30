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

	// Define autoload config array
Todoyu::$CONFIG['AUTOLOAD'] = array(
	'static' => array(
		'core/controller',
		'core/model',
		'core/model/document',
		'core/model/form'
	),
	'ext'	=> array(
		'controller',
		'model'
	)
);

	// Template (dwoo) path config
Todoyu::$CONFIG['TEMPLATE']		= array(
	'compile'	=> PATH_CACHE . DIR_SEP . 'tmpl' . DIR_SEP . 'compile',
	'cache'		=> PATH_CACHE . DIR_SEP . 'tmpl' . DIR_SEP . 'cache',
);

	// Bad tags which are encoded by the HtmlFilter
Todoyu::$CONFIG['SECURITY']['badHtmlTags'] = array('script', 'noscript', 'iframe', 'frameset',/* 'a',*/ 'div', 'input', 'textarea', 'select', 'form', 'base', 'object', 'param', 'embed', 'body', 'head', 'base', 'basefont', 'option', 'optgroup', 'meta', 'img');



	// Set (not) allowed paths for TodoyuFileManager::sendFile()
Todoyu::$CONFIG['sendFile']['allow']	= array(PATH_FILES);
Todoyu::$CONFIG['sendFile']['disallow']	= array();



Todoyu::$CONFIG['AUTH']['loginCookieName']	= 'todoyulogin';

Todoyu::$CONFIG['EXT_REQUEST_HANDLER'] = array();

Todoyu::$CONFIG['CHMOD'] = array(
	'file'	=> 0775,
	'folder'=> 0775
);

	// Add URL substitution to text auto-linkage
TodoyuHookManager::registerHook('core', 'substituteLinkableElements', 'TodoyuString::replaceUrlWithLink');

	// Add IE scripts hook to page
TodoyuHookManager::registerHook('core', 'renderPage', 'TodoyuPageAssetManager::addInternetExplorerAssets');

	// Localization defaults
Todoyu::$CONFIG['SYSTEM']['locale']		= 'en_GB';
Todoyu::$CONFIG['SYSTEM']['timezone']	= 'Europe/Zurich';

	// List size for paging
Todoyu::$CONFIG['LIST']['size']	= 30;


	// Add core onLoad hooks
TodoyuHookManager::registerHook('core', 'requestVars', 'TodoyuRequest::hookSetDefaultRequestVars', 10);
TodoyuHookManager::registerHook('core', 'requestVars', 'TodoyuCookieLogin::hookTryCookieLogin', 20);
TodoyuHookManager::registerHook('core', 'requestVars', 'TodoyuAuth::hookSendNotLoggedInForAjaxRequest', 30);
TodoyuHookManager::registerHook('core', 'requestVars', 'TodoyuAuth::hookRedirectToLoginIfNotLoggedIn', 1000);

	// Set locale path for core
TodoyuLabelManager::addCustomPath('core', 'core');

	// Setup password requirements
Todoyu::$CONFIG['SETTINGS']['passwordStrength'] = array(
	'minLength'			=> 6,
	'hasLowerCase'		=> true,
	'hasUpperCase'		=> true,
	'hasNumbers'		=> false,
	'hasSpecialChars'	=> false
);

Todoyu::$CONFIG['CREATE'] = array(
	'engines'	=> array()
);

	// Enable todoyu initialization
Todoyu::$CONFIG['INIT']	= true;

Todoyu::$CONFIG['CHECK_DENIED_RIGHTS']	= false;


	// Export Config
Todoyu::$CONFIG['EXPORT']['CSV']	= array(
	'delimiter'			=> ';',				// Field delimiter
	'enclosure'			=> '"',				// Field enclosure (wrap for fields)
	'charset'			=> 'utf-8',			// Charset of the file
	'useTableHeaders'	=> true				// Print headers in the file?
);

	// Add email receiver type: 'simple'
TodoyuMailReceiverManager::addType('simple', 'TodoyuMailReceiverSimple');


Todoyu::$CONFIG['WITHOUT_EXTENSIONS'] = false;

?>