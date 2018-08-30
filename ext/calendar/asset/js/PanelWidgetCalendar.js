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
 * Panel widget JS: Calendar
 *
 * @namespace	Todoyu.Ext.calendar.PanelWidget.Calendar
 */
Todoyu.Ext.calendar.PanelWidget.Calendar	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:			Todoyu.Ext.calendar,

	/**
	 * @property	key
	 * @type		String
	 */
	key:			'calendar',

	/**
	 * @property	calName
	 * @type		String
	 */
	calName:		'panelwidget-calendar-jscalendar',

	/**
	 * jscalendar object
	 *
	 * @property	Calendar
	 * @type		Object
	 */
	Calendar:		null,

	/**
	 * @property	prefSavingEnabled
	 * @type		Boolean
	 */
	prefSavingEnabled:	true,

	/**
	 * Update of the calender is delayed. Timeout is stored here
	 *
	 * @property	updateTimeout
	 * @type		Function
	 */
	updateTimeout:		null,

	/**
	 * Seconds for update delay
	 *
	 * @property	updateTimeoutWait
	 * @type		Number
	 */
	updateTimeoutWait:	0.2,



	/**
	 * Initialize calendar widget
	 *
	 * @method	init
	 * @param	{String}		dateString	Formatted dateString Y-m-d
	 * @param	{Number}		firstDayOfWeek
	 */
	init: function(dateString, firstDayOfWeek) {
		var date	= Todoyu.Time.parseIsoString(dateString);
		date.setHours(0, 0, 0, 0);

		var parent	= $('panelwidget-calendar-jscalendar');
			// construct a calendar giving only the "selected" handler.
		this.Calendar	= new Calendar(firstDayOfWeek, null, this.onDateSelected.bind(this), null);

		this.Calendar.weekNumbers		= true;
		this.Calendar.showsOtherMonths	= true;
		this.Calendar.setDateFormat("%A, %B %e");

		this.Calendar.create(parent);
		this.Calendar.setDate(date);

		this.Calendar.show();
	},



	/**
	 * Get current calendar date
	 *
	 * @method	getDate
	 * @return	{Date}
	 */
	getDate: function() {
		return this.Calendar.date;
	},



	/**
	 * Set current calendar date
	 *
	 * @method	setDate
	 * @param	{Number}	date
	 * @param	{Boolean}	noExternalUpdate
	 */
	setDate: function(date, noExternalUpdate) {
		date.setHours(0, 0, 0, 0);

		this.Calendar.setDate(date);
		this.Calendar.onUpdateTime();
	},



	/**
	 * Get selected calendar date as UNIX stamp
	 *
	 * @method	getTime
	 * @return	{Number}	UNIX timestamp
	 */
	getTime: function() {
		return this.getDate().getTime() / 1000;
	},



	/**
	 * Set selected calendar date from given UNIX timestamp
	 *
	 * @method	setTime
	 * @param	{Number}	timestamp
	 * @param	{Boolean}	noExternalUpdate
	 */
	setTime: function(timestamp, noExternalUpdate) {
		var date = new Date(timestamp * 1000);

		this.setDate(date, noExternalUpdate);
	},



	/**
	 * When navigating the shown time range or selecting a date
	 *
	 * @method	onDateSelected
	 */
	onDateSelected: function() {
		this.onUpdate('day', true);
	},



	/**
	 * General update event handler
	 *
	 * @method	onUpdate
	 * @param	{String}	mode		'day' = A day has been selected
	 * @param	{Number}	delay
	 */
	onUpdate: function(mode, delay) {
		if( this.updateTimeout !== null ) {
			window.clearTimeout(this.updateTimeout);
		}

		if( delay ) {
			this.updateTimeout	= this.onUpdate.bind(this).delay(this.updateTimeoutWait, mode, false);
		} else {
			Todoyu.PanelWidget.fire(this.key, {
				mode:	mode,
				date:	this.getDate()
			});
		}
	},



	/**
	 * Shift selected date of calendar widget by given duration (amount of seconds)
	 *
	 * @method	shiftDate
	 * @param	{Number}	duration
	 * @param	{Boolean}	saveDatePreference
	 */
	shiftDate: function(duration, saveDatePreference) {
		this.prefSavingEnabled	= saveDatePreference;
		this.setTime(this.getTime() + duration);
		this.prefSavingEnabled	= true;
	},



	/**
	 * Save the current date
	 *
	 * @method	saveCurrentDate
	 */
	saveCurrentDate: function() {
		if( this.prefSavingEnabled ) {
			Todoyu.Pref.save('calendar', 'date', this.getTime());
		}
	}

};