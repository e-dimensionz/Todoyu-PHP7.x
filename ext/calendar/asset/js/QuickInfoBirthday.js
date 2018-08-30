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
 * @module	Calendar
 */

/**
 * Calendar birthday quickinfo (Todoyu.Ext.calendar.Quickinfo.Birthday)
 *
 * @namespace	Todoyu.Ext.calendar.QuickInfoBirthday
 */
Todoyu.Ext.calendar.QuickInfo.Birthday	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:		Todoyu.Ext.calendar,

	/**
	 * Birthday quickinfo stays for 5 seconds in cache. Prevents same quickinfo for all years once loaded
	 *
	 * @property	cacheTime
	 * @type		Number
	 */
	cacheTime: 5,

	/**
	 * Selector (sizzle) for event quickinfo
	 *
	 * @property	selector
	 * @type		String
	 */
	selector:	'div.quickInfoBirthday',



	/**
	 * Install quickinfo for events
	 *
	 * @method	install
	 */
	install: function() {
		Todoyu.QuickInfo.setCacheTime('birthday', this.cacheTime);

		this.ext.QuickInfo.install('birthday');
	},



	/**
	 * Uninstall quickinfo for events
	 *
	 * @method	uninstall
	 */
	uninstall: function() {
		this.ext.QuickInfo.uninstall('birthday');
	},



	/**
	 * Remove given calendar event quickinfo element from cache
	 *
	 * @method	removeFromCache
	 * @param	{Number}	idEvent
	 */
	removeFromCache: function(idEvent) {
		Todoyu.QuickInfo.removeFromCache('birthday' + idEvent);
	}

};