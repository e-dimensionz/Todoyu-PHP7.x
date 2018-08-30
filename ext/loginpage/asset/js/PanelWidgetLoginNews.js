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
 * @module	Loginpage
 */

/**
 * Loginnews Panelwidget
 * Load news from todoyu website to a panelwidget displayed at the loginscreen
 *
 * @package		Todoyu
 * @subpackage	Loginpage
 */
Todoyu.Ext.loginpage.PanelWidget.LoginNews = {

	/**
	 * Handler when news is loaded: Show iFrame content instead of static news dummy
	 * Evoked from within the news file being fetched from todoyu.com
	 *
	 * @method	newsLoaded
	 */
	newsLoaded: function() {
		var live	= $('news-live');
		var local	= $('news-local');

		local.hide();

		Effect.SlideDown('news-live', {
			duration: 0.3
		});
	}

};
