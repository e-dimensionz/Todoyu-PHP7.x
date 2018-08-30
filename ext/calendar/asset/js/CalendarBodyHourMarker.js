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
 * Control of marker layer of current hour in day and week viewing mode.
 * Only displayed if today is within the shown range.
 *
 * @module		Calendar
 * @namespace	Todoyu.Ext.calendar.CalendarBody
 */
Todoyu.Ext.calendar.CalendarBody.HourMarker	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,

	/**
	 * Dark area of past time for current day
	 *
	 * @property	currentTimePastArea
	 * @type		Element
	 */
	currentTimePastArea: null,

	/**
	 * Red line for current time on current day
	 *
	 * @property	currentTimeMarker
	 * @type		Element
	 */
	currentTimeMarker: null,
	

	/**
	 * Periodical executor
	 *
	 * @property	pe
	 * @type		PeriodicalExecuter
	 */
	pe: null,



	/**
	 * Initialize current hour marker
	 *
	 * @method	init
	 */
	init: function() {
		var activeTab	= this.ext.getActiveTab();
		if( (activeTab === 'day' || activeTab === 'week') && this.isTodayDisplayed() && this.isCurrentHourDisplayed() ) {
			this.markCurrentHourDigit();

				// Add marker layer underneath current hour into DOM
			this.createMarkerElements();
			this.pe = new PeriodicalExecuter(this.updatePosition.bind(this), 60);
		} else {
			this.hideMarker();
		}
	},



	/**
	 * Check whether current day is displayed
	 *
	 * @method	isTodayDisplayed
	 * @return	{Boolean}
	 */
	isTodayDisplayed: function() {
		return this.ext.CalendarBody.isTodayDisplayed();
	},



	/**
	 * Check whether current hour is displayed
	 *
	 * @method  isCurrentHourDisplayed
	 * @return	{Boolean}
	 */
	isCurrentHourDisplayed: function() {
		if( this.ext.CalendarBody.isFullHeight() ) {
			return true;
		}

		var currentHour = Todoyu.Time.getCurrentHourOfDay();
		var range		= Todoyu.Ext.calendar.CalendarBody.getCompactViewRange();

		return currentHour >= range.start && currentHour <= range.end;
	},



	/**
	 * Insert layer to mark current hour into DOM
	 *
	 * @method	insertMarkerLayer
	 */
	createMarkerElements: function() {
		this.currentTimePastArea = new Element('div', {
			id:			'currentTimePastArea',
			className:	this.ext.getActiveTab() + 'Mode'
		});
		this.currentTimeMarker	= new Element('div', {
			id:			'currentTimeMarker',
			className:	this.ext.getActiveTab() + 'Mode'
		});

		$('gridContainer').insert(this.currentTimePastArea);
		$('gridContainer').insert(this.currentTimeMarker);

		this.updatePosition();
	},



	/**
	 * Check whether the current hour marker exists in DOM
	 *
	 * @method	areMarkersCreated
	 * @return	{Boolean}
	 */
	areMarkersCreated: function() {
		return Todoyu.exists('currentTimePastArea');
	},



	/**
	 * Hide the marker
	 *
	 * @method	hideMarker
	 */
	hideMarker: function() {
		if( this.currentTimePastArea ) {
			this.currentTimePastArea.hide()
		}
	},



	/**
	 * Show the marker, if not available: add to DOM
	 *
	 * @method	showMarker
	 */
	showMarker: function() {
		if( !this.areMarkersCreated() ) {
			this.createMarkerElements();
		}

		this.currentTimePastArea.show();
	},



	/**
	 * Get hour cells
	 *
	 * @method	getHourCells
	 * @return	{Element[]}
	 */
	getHourCells: function() {
		return this.ext.CalendarBody.calendarBody.down('.colHours').select('div');
	},



	/**
	 * Get cell containing the digit (to the left of the actual calendar content) of the current hour
	 *
	 * @method	getCurrentHourCell
	 * @return	{Element}
	 */
	getCurrentHourCell: function() {
		var currentHour	= Todoyu.Time.getCurrentHourOfDay();

		return $('calendarBody').down('.colHours div', currentHour);
	},



	/**
	 * Get first hour cell (0:00)
	 *
	 * @method	getFirstHourCell
	 * @return	{Element}
	 */
	getFirstHourCell: function() {
		return $('calendarBody').down('.colHours div');
	},



	/**
	 * Mark current hour digit (bold font style)
	 *
	 * @method	markCurrentHourDigit
	 */
	markCurrentHourDigit: function() {
		this.getCurrentHourCell().addClassName('currentHour');
	},



	/**
	 * Update marker layer to indicate current hour + minutes
	 *
	 * @method	updatePosition
	 */
	updatePosition: function() {
		var activeTab	= this.ext.getActiveTab();
		if( (activeTab === 'day' || activeTab === 'week') && this.isTodayDisplayed() && this.isCurrentHourDisplayed() ) {
			if( !this.areMarkersCreated() ) {
				this.createMarkerElements();
			}

			var height	= this.getHeight();
			var width	= this.getWidth();
			var left	= this.getTodayOffsetLeft();

			this.currentTimePastArea.setStyle({
				width:	width,
				height:	height,
				left:	left
			});

			this.currentTimeMarker.setStyle({
				width:	width,
				top:	height,
				left:	left
			});

			this.showMarker();
		} else {
			this.hideMarker();
		}
	},



	/**
	 * Get height of marker resp. to current hours view range and time of day
	 *
	 * @method	getHeight
	 * @return	{String}
	 */
	getHeight: function() {
		var currentHour		= Todoyu.Time.getCurrentHourOfDay();
		var currentMinutes	= Todoyu.Time.getCurrentMinutesOfHour();

		var pastHoursShown;
		if( this.ext.CalendarBody.isFullHeight() ) {
				// Full hours range 00:00 to 23:00
			pastHoursShown	= currentHour;
		} else {
				// Limited view range of hours
			var firstHour	= this.ext.CalendarBody.getCompactRangeStart();
			pastHoursShown	= currentHour - firstHour;
		}

		var gridHeader		= $('gridHeader');
		var headerheight	= gridHeader ? gridHeader.getHeight() : 0;
		var heightHours		= pastHoursShown * this.ext.hourHeight;
		var heightMinutes	= currentMinutes / 1.5;

		return heightHours + heightMinutes + headerheight + 'px';
	},



	/**
	 * Get horizontal offset for current hour marker layer from day offset
	 *
	 * @method	getOffsetLeft
	 * @return	{String}
	 */
	getTodayOffsetLeft: function() {
		var activeTab	= this.ext.getActiveTab();
		var offsetLeft	= 0;

			// Week: Get left offset via today's column
		if( activeTab === 'week') {
			var todayHeaderCell = $('gridHeader').down('th.today');
			offsetLeft	= todayHeaderCell.offsetLeft;

			if( this.ext.Week.isWeekendDisplayed() ) {
				offsetLeft -= 1;
			}
		}

		return offsetLeft + 'px';
	},



	/**
	 * Get usable width of marker from day column
	 *
	 * @method	getMarkerWidth
	 * @return	{String}
	 */
	getWidth: function() {
		var activeTab	= this.ext.getActiveTab();
		var width, widthFix = 2;

		if( activeTab === 'day' ) {
			width	= '100%';
		} else if( activeTab === 'week' ) {
			width	= (this.ext.Week.getDayColWidth() - 2) + 'px';
		}

		return width;
	}

};