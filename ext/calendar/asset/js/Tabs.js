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
 * Calendar tabs functions
 *
 * @namespace	Todoyu.Ext.calendar.Tabs
 */
Todoyu.Ext.calendar.Tabs	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,

	/**
	 * Key of current active tab (day, week, month, view)
	 *
	 * @property	active
	 * @type		String
	 */
	active: null,



	/**
	 * On selecting a tab
	 *
	 * @method	onSelect
	 * @param	{Event}	event
	 * @param	{String}	tabKey
	 */
	onSelect: function(event, tabKey) {
		switch(tabKey) {
				// Click on edit/view tab does nothing
			case 'edit':
			case 'view':
				break;

				// Click on add tab add a new edit tab
			case 'add':
				this.ext.Event.Edit.open(0);
				break;

				// Click on view tabs changes calendar view
			default:
				this.closeSpecialTabs();
				this.active	= tabKey;
				this.ext.show(tabKey);
				break;
		}
	},



	/**
	 * Close special (event -view / -edit) tabs if open
	 *
	 * @method	closeSpecialTabs
	 */
	closeSpecialTabs: function() {
		if( this.ext.Event.Edit.isActive() ) {
			this.ext.Event.Edit.close();
		}
		if( this.ext.Event.View.isActive() ) {
			this.ext.Event.View.close();
		}
	},



	/**
	 * Get active tab ID
	 *
	 * @method	getActive
	 * @return	String		e.g 'month' / 'week' / ...
	 */
	getActive: function() {
		if( this.active === null ) {
			this.active	= Todoyu.Tabs.getActiveKey('calendar');
		}

		return this.active;
	},



	/**
	 * Set given tab as currently active one
	 *
	 * @method	setActive
	 * @param	{String}		tab		'month' / 'week' / ...
	 */
	setActive: function(tab) {
			// Make sure the given tab exists, otherwise use month tab by default
		tab	= $('calendar-tab-' + tab) ? tab : 'month';

			// Activate the tab
		this.active	= tab;
		Todoyu.Tabs.setActive('calendar', tab);
	},



	/**
	 * Save pref: key of given tab
	 *
	 * @method	saveTabSelection
	 * @param	{String}	tabKey		'day' / 'week' / 'month'
	 */
	saveTabSelection: function(tabKey) {
		this.ext.savePref('tab', tabKey);
	}

};