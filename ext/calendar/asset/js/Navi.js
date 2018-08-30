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
 * Calendar navigation
 *
 * @namespace	Todoyu.Ext.calendar.Navi
 */
Todoyu.Ext.calendar.Navi	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,



	/**
	 * Init calendar navigation
	 *
	 * @method	init
	 */
	init: function() {
		var tab	= this.ext.getActiveTab();
		this.toggleViewOptions(tab);
	},



	/**
	 * Set calendar title
	 *
	 * @method	setTitle
	 * @param	{String}		title
	 */
	setTitle: function(title) {
		$('calendar-title').update(title);
	},



	/**
	 * Get current active calendar tab
	 *
	 * @method	getActiveTab
	 */
	getActiveTab: function() {
		return this.ext.Tabs.getActive();
	},



	/**
	 * Get up-/ down-shifted date
	 *
	 * @method	getDirectionDate
	 * @param	{Boolean}		up
	 * @return	{Date}
	 */
	getDirectionDate: function(up) {
		var tab		= this.getActiveTab();
		var time	= this.ext.getTime();

		var newTime	= Todoyu.Time.getShiftedTime(time, tab, up);

		return new Date(newTime * 1000);
	},



	/**
	 * Get down-shifted date
	 *
	 * @method	getBackwardDate
	 * @return	{Number}
	 */
	getBackwardDate: function() {
		return this.getDirectionDate(false);
	},



	/**
	 * Go backward in time
	 *
	 * @method	goBackward
	 */
	goBackward: function() {
		var date	= this.getBackwardDate();
		var time	= date.getTime() / 1000;

		this.ext.show(null, time);
	},



	/**
	 * Get up-shifted date
	 *
	 * @method	getForwardDate
	 * @return	{Number}
	 */
	getForwardDate: function() {
		return this.getDirectionDate(true);
	},



	/**
	 * Go forward in time
	 *
	 * @method	goForward
	 */
	goForward: function() {
		var date	= this.getForwardDate();
		var time	= date.getTime() / 1000;

		this.ext.show(null, time);
	},



	/**
	 * Get today date
	 *
	 * @method	getTodayDate
	 * @return	{Date}
	 */
	getTodayDate: function() {
		return Todoyu.Time.getTodayDate();
	},



	/**
	 * Go to day of current "today"
	 *
	 * @method	goToday
	 */
	goToday: function() {
		var date	= this.getTodayDate();
		var time	= date.getTime() / 1000;

		this.ext.show(null, time);
	},



	/**
	 * Update visibility of hours-range + weekend options
	 * Hours-range toggle: only available in day/week view
	 * Weekend toggle: only available in week view
	 *
	 * @method	toggleViewOptions
	 * @param	{String}	[tab]
	 */
	toggleViewOptions: function(tab) {
		if( !tab ) {
			tab = this.getActiveTab();
		}

		if( tab === 'day' || tab == 'week' ) {
			$('calendar-quicknav-toggleDayView').show();
		} else {
			$('calendar-quicknav-toggleDayView').hide();
		}

		if( tab === 'week' ) {
			$('calendar-quicknav-toggleWeekend').show();
		} else {
			$('calendar-quicknav-toggleWeekend').hide();
		}
	}

};