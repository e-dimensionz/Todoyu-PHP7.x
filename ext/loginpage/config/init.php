<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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

Todoyu::$CONFIG['EXT']['loginpage']['extendedContentHooks'] = array();

	// Seconds to wait after failed login
Todoyu::$CONFIG['EXT']['loginpage']['waitAtFailLogin'] = 2;
	// Documentation URLs: how to enable cookies / javaScript in browsers
Todoyu::$CONFIG['EXT']['loginpage']['manuallinks']['cookies']		= 'http://doc.todoyu.com/index.php?id=manual-cookies';
Todoyu::$CONFIG['EXT']['loginpage']['manuallinks']['javascript']	= 'http://doc.todoyu.com/index.php?id=manual-javascript';

/* ----------------------------
	Menu Tabs Configuration
   ---------------------------- */
Todoyu::$CONFIG['EXT']['loginpage']['tabs']	= array(
	array(
		'position'	=> 10,
		'key'		=> 'login',
		'label'		=> 'loginpage.ext.tab.login',
		'href'		=> '?ext=loginpage&amp;controller=ext',
	),
	array(
		'position'	=> 20,
		'key'		=> 'help',
		'label'		=> 'loginpage.ext.tab.help',
		'href'		=> 'http://www.todoyu.com/community/forum/?utm_source=todoyulogin&amp;utm_medium=web&amp;utm_campaign=todoyu',
		'target'	=> '_blank'
	),
	array(
		'position'	=> 30,
		'key'		=> 'blog',
		'label'		=> 'loginpage.ext.tab.blog',
		'href'		=> 'http://www.todoyu.com/community/blog/?utm_source=todoyulogin&amp;utm_medium=web&amp;utm_campaign=todoyu',
		'target'	=> '_blank'
	)
);



/* ------------
	Widgets
   ------------ */
	// Feed URL for todoyu news widget
Todoyu::$CONFIG['EXT']['loginpage']['panelWidgetLoginNews'] = array(
	'url'	=> 'http://www.todoyu.com/?id=loginnews&type=101',
	'age'	=> 36000 // 10 * 3600
);

?>