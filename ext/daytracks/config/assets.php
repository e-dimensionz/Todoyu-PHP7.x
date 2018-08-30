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
 * Assets (JS, CSS, SWF, etc.) requirements for daytracks extension
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */

Todoyu::$CONFIG['EXT']['daytracks']['assets'] = array(
	'js' => array(
		array(
			'file'		=> 'ext/daytracks/asset/js/Ext.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/daytracks/asset/js/History.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/daytracks/asset/js/PanelWidgetDaytracks.js',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/daytracks/asset/js/PanelWidgetDaytracksContextmenu.js',
			'position'	=> 111
		),
		array(
			'file'		=> 'ext/daytracks/asset/js/Export.js',
			'position'	=> 112
		),
		array(
			'file'		=> 'ext/daytracks/asset/js/ExportMultiAc.js',
			'position'	=> 113
		)
	),
	'css' => array(
		array(
			'file'		=> 'ext/daytracks/asset/css/ext.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/daytracks/asset/css/history.scss',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/daytracks/asset/css/panelwidget-daytracks.scss',
			'position'	=> 110
		)
	)
);


?>