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

/**
 * Assets (JS, CSS, SWF, etc.) requirements for assets extension
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
Todoyu::$CONFIG['EXT']['assets']['assets']	= array(
	'js'	=> array(
		array(
			'file'		=> 'ext/assets/asset/js/Ext.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/assets/asset/js/List.js',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/assets/asset/js/Upload.js',
			'position'	=> 120
		),
		array(
			'file'		=> 'ext/assets/asset/js/RecordEdit.js',
			'position'	=> 120
		),
		array(
			'file'		=> 'ext/assets/asset/js/QuickInfoAsset.js',
			'position'	=> 120
		),
		array(
			'file'		=> 'lib/js/md5.js',
			'position'	=> 26
		),
		array(
			'file'		=> 'ext/assets/asset/js/RecordSelectAsset.js',
			'position'	=> 130
		)
	),
	'css'	=> array(
		array(
			'file'	=> 'ext/assets/asset/css/ext.scss'
		),
		array(
			'file'	=> 'ext/assets/asset/css/list.scss'
		),
		array(
			'file'	=> 'ext/assets/asset/css/task.scss'
		),
		array(
			'file'	=> 'ext/assets/asset/css/mime.scss'
		)
	)
);

?>