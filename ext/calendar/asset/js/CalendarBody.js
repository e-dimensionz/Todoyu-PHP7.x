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
 * Calendar body
 *
 * @class		CalendarBody
 * @namespace	Todoyu.Ext.calendar
 */
Todoyu.Ext.calendar.CalendarBody	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:			Todoyu.Ext.calendar,

	/**
	 * @property	calendarBody
	 * @type		Element
	 */
	calendarBody:	null,

	/**
	 * Compact hours range preset
	 *
	 * @type	Array
	 */
	compactRange: {
		start:	8,
		end:	18
	},



	/**
	 * Init calendar body
	 *
	 * @method	init
	 */
	init: function() {
		if( !this.isLoaded() ) {
			return;
		}

			// Ensure the calendarBody is there (it's missing when editing an event initially)
		this.calendarBody	= $('calendarBody');

		this.installContextMenu();
		this.installObserversCreateEvent();

		this.ext.installQuickInfos();
		this.ext.Event.installObservers();

		if( this.ext.getActiveTab() !== 'month' ) {
			this.applyCompactView();
			this.HourMarker.init();
		}

			// Init drag and drop
		this.ext.DragDrop.init();

			// Call hooked callbacks
		Todoyu.Hook.exec('calendarBody.init');
	},



	/**
	 * Check whether calendar body is loaded
	 *
	 * @method	isLoaded
	 */
	isLoaded: function() {
		return Todoyu.exists('calendarBody');
	},



	/**
	 * Set compact view range limits
	 *
	 * @method	setCompactViewRange
	 * @param	{Number}	start
	 * @param	{Number}	end
	 * @param	{Boolean}	apply
	 */
	setCompactViewRange: function(start, end, apply) {
		this.compactRange = {
			start:	start,
			end:	end
		};

		if( apply !== false ) {
			this.applyCompactView();
		}
	},



	/**
	 * Install calendar body context menu
	 *
	 * @method	installContextMenu
	 */
	installContextMenu: function() {
		this.ext.ContextMenuCalendarBody.attach();
	},



	/**
	 * Toggle full day view mode
	 *
	 * @method toggleFullDayView
	 */
	toggleFullDayView: function() {
		this.setFullHeight(! this.isFullHeight(), true);
		this.HourMarker.updatePosition();
	},



	/**
	 * Save changed pref, reload calendar with toggled display of weekend (sat+sun)
	 *
	 * @method toggleWeekend
	 */
	toggleWeekend: function() {
		var url		= Todoyu.getUrl('calendar', 'preference');
		var options	= {
			parameters: {
				action:		'toggleDisplayWeekend'
			},
			onComplete: this.onWeekendToggled.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * After display of weekend has been toggled: refresh calendar week view
	 *
	 * @method	onWeekendToggled
	 */
	onWeekendToggled: function() {
		this.ext.show('week');
	},



	/**
	 * Get amount of currently displayed days (per week: 5/7)
	 *
	 * @method	getAmountDisplayedDays
	 * @return	{Number}
	 */
	getAmountDisplayedDays: function() {
		return $('tgTable').select('td.dayCol').size();
	},



	/**
	 * Get calendar body height
	 *
	 * @method	getHeight
	 * @return	{Number}
	 */
	getHeight: function() {
		return this.calendarBody.getHeight();
	},



	/**
	 * Set calendar body display mode to full day height
	 *
	 * @method	setFullHeight
	 * @param	{Boolean}		showFullHeight
	 * @param	{Boolean}		savePref
	 */
	setFullHeight: function(showFullHeight, savePref) {
		if( showFullHeight ) { // Show all hours
				// Switch to full hours view
			this.calendarBody.addClassName('full');
			this.calendarBody.style.height	= 'auto';
			Todoyu.Helper.setScrollTop(this.calendarBody, 0);
		} else { // Show compact range
				// Switch to restrained hours view
			this.calendarBody.removeClassName('full');

			var numVisibleHours		= this.compactRange.end - this.compactRange.start + 1;

			this.calendarBody.style.height	= (this.ext.hourHeight * numVisibleHours) + 'px'; //42px = height of one hour
			this.calendarBody.scrollTop		= this.ext.hourHeight * this.compactRange.start;
		}

		if( savePref ) {
			this.saveFullDayViewPref();
		}
	},



	/**
	 * @method	applyCompactView
	 */
	applyCompactView: function() {
		this.setFullHeight(this.isFullHeight(), false);
	},



	/**
	 * Save full day viewing mode preference
	 *
	 * @method	saveFullDayViewPref
	 */
	saveFullDayViewPref: function(){
		this.ext.savePref('fulldayview', this.isFullHeight() ? 1 : 0);
	},



	/**
	 * Get resp. timestamp to mouse coordinates inside current calendar view (day / week / month)
	 *
	 * @method	getTimeOfMouseCoordinates
	 * @param	{Number}		x
	 * @param	{Number}		y
	 * @return	{Number}		timestamp
	 */
	getTimeForPosition: function(x, y) {
		var calendarMode= this.ext.getActiveTab();
		var timestamp;

			// Calculate timestamp from coordinate in current mode
		switch(calendarMode) {
			case 'day':
				timestamp	= this.ext.Day.getDateForPosition(x, y);
				break;

			case 'week':
				timestamp	= this.ext.Week.getDateForPosition(x, y);
				break;
		}

		return timestamp;
	},



	/**
	 * Get pixel-offset of day display, used to comprehend visual margins of hours in day / week mode
	 *
	 * @method	getDayOffset
	 * @param	{Number}		offsetTop
	 * @return	{Number}
	 */
	getDayOffset: function(offsetTop) {
		var seconds	= (offsetTop / 1009) * Todoyu.Time.seconds.day;
			// Round to quarter hours, get time parts (hours, minutes, seconds)
		seconds	= Math.round(seconds / 900) * 900;

		var timeInfo	= Todoyu.Time.getTimeParts(seconds);

		return timeInfo.hours * Todoyu.Time.seconds.hour + timeInfo.minutes * Todoyu.Time.seconds.minute;
	},



	/**
	 * Check whether today is currently displayed in calendar
	 *
	 * @method	isTodayDisplay
	 * @return	{Boolean}
	 */
	isTodayDisplayed: function() {
		switch( this.ext.getActiveTab() ) {
			case 'day':
				return  this.ext.Day.isTodayDisplayed();
				break;
			case 'week':
				return this.ext.Week.isTodayDisplayed();
				break;
			case 'month':
				return typeof $('gridContainer').down('.ttoday') === 'object';
				break;
			default:
				return false;
		}
	},



	/**
	 * Install create event observer
	 *
	 * @method	installObserversCreateEvent
	 */
	installObserversCreateEvent: function() {
		var tab	= this.ext.getActiveTab();

		if( tab === 'month' ) {
			this.calendarBody.on('dblclick', 'td', this.onEventCreateMonth.bind(this));
		} else {
			this.calendarBody.on('dblclick', 'td.dayCol', this.onEventCreateDayWeek.bind(this));
		}
	},



	/**
	 * Handle event creation in day or week viewing mode
	 *
	 * @method	inEventCreateDayWeek
	 * @param	{Event}	event
	 */
	onEventCreateDayWeek: function(event) {
		var time	= this.getTimeForPosition(event.pointerX(), event.pointerY());

		this.addEventOnTime(time);
	},



	/**
	 * Handle event creation in month viewing mode
	 * Date is in string format to ignore timezone offsets
	 * (we just want the day, don't care about the local time)
	 *
	 * @method	onEventCreateMonth
	 * @param	{Event}		event
	 * @param	{Element}	cell
	 */
	onEventCreateMonth: function(event, cell) {
			// Get timestamp of the date in local timezone (will be reconverted later into the same timestamp again)
		var time	= Todoyu.Time.date2Time(cell.id.split('-').slice(1).join('-'));

		this.addEventOnTime(time);
	},



	/**
	 * Add an event on a given time
	 *
	 * @method  addEventOnTime
	 * @param	{Number}	time
	 */
	addEventOnTime: function(time) {
		this.ext.addEvent(time);
	},



	/**
	 * Get current view range (start to end hour)
	 *
	 * @method	getCompactViewRange
	 * @return	{Object}
	 */
	getCompactViewRange: function() {
		return this.compactRange;
	},



	/**
	 * Get first shown hour
	 *
	 * @method	getCompactRangeStart
	 * @return  {Number}
	 */
	getCompactRangeStart: function() {
		return this.compactRange.start;
	},



	/**
	 * Get last shown hour
	 *
	 * @method	getCompactRangeEnd
	 * @return  {Number}
	 */
	getCompactRangeEnd: function() {
		return this.compactRange.end;
	},



	/**
	 * Check whether calendar body (hours range) is set to full height
	 *
	 * @method	isFullHeight
	 * @return	{Boolean}
	 */
	isFullHeight: function() {
		return this.calendarBody.hasClassName('full');
	},



	/**
	 * Get top offset depending on current view range and full day view
	 *
	 * @method	getFixedTopOffset
	 * @param	{Number}	topOffset		In pixels
	 * @return	{Number}	Correct offset inside the calendar body area
	 */
	getFixedTopOffset: function(topOffset) {
		var boxOffsetTop= $('calendarBody').cumulativeOffset().top;
		var offsetTop	= topOffset - boxOffsetTop;

		if( !this.isFullHeight() ) {
			offsetTop += this.getCompactViewRange().start * this.ext.hourHeight;
		}

		return offsetTop;
	}

};