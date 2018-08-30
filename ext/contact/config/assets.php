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
 * Assets registration of contact extension
 */
Todoyu::$CONFIG['EXT']['contact']['assets'] = array(
	'js'	=> array(
		array(
			'file'		=> 'ext/contact/asset/js/Ext.js',
			'position'	=> 100
		),
			// Add creation engines to quick create headlet
		array(
			'file'		=> 'ext/contact/asset/js/QuickCreateCompany.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/contact/asset/js/Autocomplete.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/contact/asset/js/Person.js',
			'position'	=> 105
		),
		array(
			'file'		=> 'ext/contact/asset/js/Company.js',
			'position'	=> 106
		),
		array(
			'file'		=> 'ext/contact/asset/js/QuickCreatePerson.js',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/contact/asset/js/QuickInfoPerson.js',
			'position'	=> 200
		),
		array(
			'file'		=> 'ext/contact/asset/js/PanelWidgetContactSearch.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/contact/asset/js/PanelWidgetStaffSelector.js',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/contact/asset/js/PanelWidgetStaffList.js',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/contact/asset/js/Address.js',
			'position'	=> 111
		),
		array(
			'file'		=> 'ext/contact/asset/js/PanelWidgetContactExport.js',
			'position'	=> 112
		),
		array(
			'file'		=> 'ext/contact/asset/js/Upload.js',
			'position'	=> 113
		),
		array(
			'file'		=> 'ext/contact/asset/js/Profile.js',
			'position'	=> 114
		)
	),
	'css'	=> array(
		array(
			'file'		=> 'ext/contact/asset/css/quickinfo.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/contact/asset/css/ext.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/contact/asset/css/panelwidget-staffselector.scss',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/contact/asset/css/panelwidget-stafflist.scss',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/contact/asset/css/panelwidget-contactexport.scss',
			'position'	=> 111
		)
	)
);

?>