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
 * Assets (JS, CSS, SWF, etc.) requirements for search extension
 *
 * @package		Todoyu
 * @subpackage	Search
 */

Todoyu::$CONFIG['EXT']['search']['assets'] = array(
	'js' => array(
		array(
			'file'		=> 'ext/search/asset/js/Ext.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/search/asset/js/HeadletQuickSearch.js',
			'position'	=> 110
		),
		array(
			'file' => 'ext/search/asset/js/Preference.js',
			'position' => 102
		),
		array(
			'file' => 'ext/search/asset/js/FilterWidget.js',
			'position' => 101
		),
		array(
			'file' => 'ext/search/asset/js/Filter.js',
			'position' => 102
		),
		array(
			'file' => 'ext/search/asset/js/FilterControl.js',
			'position' => 103
		),
		array(
			'file' => 'ext/search/asset/js/FilterConditions.js',
			'position' => 104
		),
		array(
			'file' => 'ext/search/asset/js/FilterWidgetArea.js',
			'position' => 105
		),
		array(
			'file' => 'ext/search/asset/js/PanelWidgetSearchFilterList.js',
			'position' => 110
		),
		array(
			'file' => 'ext/search/asset/js/ActionPanel.js',
			'position' => 111
		),
		array(
			'file' => 'ext/search/asset/js/FilterSorting.js',
			'position' => 112
		)
	),
	'css' => array(
		array(
			'file'		=> 'ext/search/asset/css/ext.scss',
			'media'		=> 'all',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/search/asset/css/headlet-quicksearch.scss',
			'media'		=> 'all',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/search/asset/css/filterwidgets.scss',
			'media'		=> 'all',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/search/asset/css/panelwidget-searchfilterlist.scss',
			'media'		=> 'all',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/search/asset/css/print.scss',
			'media'		=> 'print',
			'position'	=> 120
		)
	)
);

?>