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
 * Assets (JS, CSS, SWF, etc.) requirements for portal extension
 *
 * @package		Todoyu
 * @subpackage	Portal
 */

Todoyu::$CONFIG['EXT']['portal']['assets'] = array(
	'js' => array(
		array(
			'file'		=> 'ext/portal/asset/js/Ext.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/portal/asset/js/Tab.js',
			'position'	=> 102
		),
		array(
			'file' => 'ext/portal/asset/js/PanelWidgetFilterPresetList.js',
			'position' => 110
		)
	),
	'css' => array(
		array(
			'file'		=> 'ext/portal/asset/css/ext.scss',
			'position'	=> 100
		),
		array(
			'file' => 'ext/portal/asset/css/panelwidget-filterpresetlist.scss',
			'position' => 110
		)
	)
);

?>