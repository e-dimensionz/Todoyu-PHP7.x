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
 * Init todoyu installer
 *
 * @package		Todoyu
 * @subpackage	Installer
 */

	// Activate error reporting
error_reporting(E_ALL ^ E_NOTICE);

TodoyuLabelManager::addCustomPath('install', 'install');

	// Assets (JS, (S)CSS) requirements for installer
Todoyu::$CONFIG['INSTALLER']['assets']	= array(
	'css'	=> array(
		'core/asset/css/base.scss',
		'core/asset/css/layout.scss',
		'core/asset/css/panel.scss',
		'core/asset/css/form.scss',
		'core/asset/css/button.scss',
		'install/asset/css/installer.scss'
	),
	'js'	=> array(
		'../lib/js/prototype.js',
		'asset/js/TodoyuInstaller.js'
	)
);

?>