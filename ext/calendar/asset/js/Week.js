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
 * Handle week view
 *
 * @namespace	Todoyu.Ext.calendar.Week
 */
Todoyu.Ext.calendar.Week	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.calendar,


	/**
	 * Check whether weekend is displayed
	 *
	 * @method	isWeekendDisplayed
	 * @return	{Boolean}
	 */
	isWeekendDisplayed: function() {
		return this.getNumDays() === 7;
	},



	/**
	 * Check whether compact view is active
	 *
	 * @method	isCompactView
	 * @return	{Boolean}
	 */
	isCompactView: function() {
		return this.getNumDays() === 5;
	},



	/**
	 * Check whether today is displayed
	 *
	 * @method	isTodayDisplayed
	 * @return	{Boolean}
	 */
	isTodayDisplayed: function() {
		return $('gridHeader') && $('gridHeader').down('.today');
	},



	/**
	 * Check whether first day of week is sunday
	 *
	 * @return	{Boolean}
	 */
	isFirstDaySunday: function() {
		return Todoyu.Config.system.firstDayOfWeek === 0;
	},



	/**
	 * Get number of displayed days
	 *
	 * @method	getNumDays
	 * @return	{Number}
	 */
	getNumDays: function() {
		return this.ext.CalendarBody.getAmountDisplayedDays();
	},



	/**
	 * Get snap config for drag and drop in week view
	 *
	 * @method	getDragDropSnap
	 * @param	{Number}		x
	 * @param	{Number}		y
	 * @param	{Draggable}		draggable
	 * @return	{Number[]}
	 */
	getDragDropSnap: function(x, y, draggable) {
		var verSnap	= this.ext.DragDrop.verticalHourSnap;
		var horSnap, horMax;

		if( this.isWeekendDisplayed() ) {
			horSnap	= 124.7;
			horMax	= 750;
		} else {
			horSnap	= 175.7;
			horMax	= 710;
		}

		x = Math.round(x / horSnap) * horSnap;
		y = Math.round(y / verSnap) * verSnap;

			// Keep in horizontal range
		if( x < 0 ) {
			x = 0;
		} else if( x > horMax ) {
			x = horMax;
		}

			// Keep in vertical range
		if( y < 0 ) {
			y = 0;
		}

		return [x, y];
	},



	/**
	 * Get options for drag 'n drop
	 *
	 * @method	getDragDropOptions
	 * @return	{Object}
	 */
	getDragDropOptions: function() {
		return {
			snap: this.getDragDropSnap.bind(this)
		};
	},



	/**
	 * Initialize day events drag and drop in week
	 */
	initDayEventsDragdrop: function() {
		var options			= Object.clone(this.ext.DragDrop.defaultDraggableOptions);

		options.constraint	= 'horizontal';
		options.snap		= this.isCompactView() ? 175 : 126; // Day pixel-width
		options.onStart		= this.onStartDragDayEvent.bind(this);
		options.onEnd		= this.onEndDragDayEvent.bind(this);

		this.getDayEvents().each(function(eventElement){
			new Draggable(eventElement, options);
		}, this);
	},



	/**
	 * Get all all-day long event elements, except the noAccess classed
	 *
	 * @method	getDayEvents
	 * @return	{Element[]}
	 */
	getDayEvents: function() {
		return this.ext.DragDrop.getDayEvents();
	},



	/**
	 * Handler when dragging event item starts
	 *
	 * @method	onStartDragDayEvent
	 * @param	{Object}		dragInfo		Information about dragging
	 * @param	{Event}			event
	 */
	onStartDragDayEvent: function(dragInfo, event) {
		this.ext.DragDrop.initDraggableRevertToOrigin(dragInfo.element);
		Todoyu.QuickInfo.hide(true);
	},



	/**
	 * Handler when dragging of all-day event ends
	 *
	 * @method	onEndDragDayEvent
	 * @param	{Object}		dragInfo		Information about dragging
	 * @param	{Event}			domEvent
	 */
	onEndDragDayEvent: function(dragInfo, domEvent) {
		var idEvent	= dragInfo.element.id.split('-').last();

		this.saveAllDayEventDrop(idEvent, dragInfo);
	},



	/**
	 * Get week start depending on first day of week and complex view
	 *
	 * @return	{Date}
	 */
	getWeekStart: function() {
		var weekStart = this.ext.getWeekStart();
		
		if( this.isFirstDaySunday() && this.isCompactView() ) {
			weekStart.addDays(1);
		}
		
		return weekStart;
	},



	/**
	 * Save new position after dropping of all-day event
	 *
	 * @method	saveWeekDrop
	 * @param	{Number}	idEvent
	 * @param	{Object}	dragInfo
	 */
	saveAllDayEventDrop: function(idEvent, dragInfo) {
		var dayWidth		= this.getDayColWidth(); // amountDaysInWeek === 7 ? 88 : 123;
		var weekStart		= this.getWeekStart();
		var offsetLeft		= dragInfo.element.positionedOffset().left;
		var dayOfWeek		= Math.floor((offsetLeft) / dayWidth);

		if( dayOfWeek >= 0 && dayOfWeek < this.getNumDays() ) {
				// Shift starting day date, keep starting time of day
			var dropDate	= weekStart.addDays(dayOfWeek, true);

			this.ext.DragDrop.saveDropping('week', idEvent, dropDate);
		} else {
			dragInfo.element.revertToOrigin();
		}
	},



	/**
	 * Get width of day column
	 *
	 * @method	getDayColWidth
	 * @return	{Number}
	 */
	getDayColWidth: function() {
		return $('tgTable').down('.dayCol').getWidth();
	},



	/**
	 * Calculate date for drop position
	 *
	 * @method	getDropDate
	 * @param	{Object}	dragInfo
	 * @return	{Date}
	 */
	getDropDate: function(dragInfo) {
		var timeColWidth= 42;
		var dayWidth	= this.getDayColWidth();
		var offset		= dragInfo.element.positionedOffset();
			// Offset fix (offset seems to be shifted one hour)
		offset.top	+= this.ext.hourHeight;

		var weekStart	= this.ext.getWeekStartTime();
		var dayIndex	= Math.round(Math.abs(offset.left - timeColWidth) / dayWidth);

		var dayHours	= Math.round((offset.top / this.ext.hourHeight) * 4) / 4;
		var timestamp	= weekStart + Todoyu.Time.seconds.day * dayIndex + dayHours * Todoyu.Time.seconds.hour;

		return new Date(timestamp * 1000);
	},



	/**
	 * Get date for event position
	 *
	 * @method	getDateForPosition
	 * @param	{Number}	x
	 * @param	{Number}	y
	 * @return	{Number}
	 */
	getDateForPosition: function(x, y) {
		var weekStart	= this.ext.getWeekStartTime();
		var dayIndex	= this.getDayIndex(x);
		var offsetTop	= this.ext.CalendarBody.getFixedTopOffset(y);
		var dayTime		= this.ext.CalendarBody.getDayOffset(offsetTop);
		var dayShift	= dayIndex * Todoyu.Time.seconds.day;

		return weekStart + dayShift + dayTime;
	},



	/**
	 * Get day index for week
	 *
	 * @method	getDayIndex
	 * @param	{Number}	leftOffset
	 */
	getDayIndex: function(leftOffset) {
		var boxOffsetLeft	= $('calendarBody').cumulativeOffset().left + 43;
		var dayColWidth		= this.getDayColWidth();
		var offsetLeft		= leftOffset - boxOffsetLeft;
		var dayIndex		= Math.floor(offsetLeft / dayColWidth);
		dayIndex			= dayIndex < 0 ? 0 : dayIndex < this.getNumDays() ? dayIndex : this.getNumDays() - 1;

		return dayIndex;
	}

};