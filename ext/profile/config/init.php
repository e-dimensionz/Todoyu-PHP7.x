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

/* ------------------------------------
	Tabs for profile > general area
   ------------------------------------ */
Todoyu::$CONFIG['EXT']['profile']['generalTabs'] = array(
	array(
		'id'			=> 'main',
		'label'			=> 'profile.ext.general.main.tab'
	),
	array(
		'id'			=> 'password',
		'label'			=> 'profile.ext.general.password.tab',
		'require'		=> 'profile.settings:password'
	)
);



/* ------------------------------------
	Add general module to profile area
   ------------------------------------ */
TodoyuProfileManager::addModule('general', array(
	'position'	=> 0,
	'tabs'		=> 'TodoyuProfileGeneralRenderer::renderTabs',
	'content'	=> 'TodoyuProfileGeneralRenderer::renderContent',
	'label'		=> 'profile.ext.module.general',
	'class'		=> 'general'
));

?>