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
 * @namespace	Todoyu.Ext.calendar.QuickInfoHoliday
 */
Todoyu.Ext.calendar.QuickInfo.Holiday	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:		Todoyu.Ext.calendar,



	/**
	 * Selector (sizzle) for holiday quickinfo
	 *
	 * @property	selector
	 * @type		String
	 */
	selector:	'span.quickInfoHoliday',



	/**
	 * Install quickinfo for holidays
	 *
	 * @method	install
	 */
	install: function() {
		this.ext.QuickInfo.install('holiday');
	},



	/**
	 * Uninstall quickinfo for events
	 *
	 * @method	uninstall
	 */
	uninstall: function() {
		this.ext.QuickInfo.uninstall('holiday');
	},



	/**
	 * Remove given calendar event quickinfo element from cache
	 *
	 * @method	removeFromCache
	 * @param	{Number}	idEvent
	 */
	removeFromCache: function(idEvent) {
		Todoyu.QuickInfo.removeFromCache('holiday' + idEvent);
	}

};