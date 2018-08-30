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
 * Drag 'n Drop support for events in the calendar
 *
 * @namespace	Todoyu.Ext.calendar.DragDrop
 */
Todoyu.Ext.calendar.DragDrop	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.calendar,

	/**
	 * Stores Draggables
	 *
	 * @type		Array
	 */
	draggables: [],

	/**
	 * Default draggable options for all tree views
	 *
	 * @property	defaultDraggableOptions
	 * @type		Object
	 */
	defaultDraggableOptions:	{
		handle:		'head'
	},

	/**
	 * Draggable options
	 *
	 * @property	draggableOptions
	 * @type		Object
	 */
	draggableOptions:	{},

	/**
	 * Storage for modified event data when saving is interrupted by confirmation (e.g. overbooking warning)
	 *
	 * @property	droppedEventData
	 * @type		Object
	 */
	droppedEventData:	{},

	/**
	 * Vertical pixel snapping = 42 / 4
	 *
	 * @property	verticalHourSnap
	 * @type		Number
	 */
	verticalHourSnap:	10.5,



	/**
	 * Initialize dragging on start
	 *
	 * @method	init
	 */
	init: function() {
			// Initialize options for current view
		this.initOptions();

			// Add drag functions to all event and full-day event items
		this.makeEventsDraggable();

		var tab	= this.ext.getActiveTab();

			// Add dragging for full-day event items in date header row of week view
		if( tab === 'week' ) {
			this.ext.Week.initDayEventsDragdrop();
		}

			// Add drop functions to day containers in month view
		if( tab === 'month' ) {
			this.ext.Month.createDayDropZones();
		}
	},



	/**
	 * Set drag options for current tab
	 *
	 * @method	initOptions
	 */
	initOptions: function() {
		var tab	= this.ext.getActiveTab();
		var tabOptions = {};

			// Clone default options (assign would make a reference)
		this.draggableOptions	= Object.clone(this.defaultDraggableOptions);

			// Add event handlers
		this.draggableOptions.onStart	= this.onStart.bind(this, tab);
		this.draggableOptions.onDrag	= this.onDrag.bind(this, tab);
		this.draggableOptions.onEnd		= this.onEnd.bind(this, tab);

		switch(tab) {
			case 'day':
				tabOptions	= this.getOptionsDay();
				break;
			case 'week':
				tabOptions	= this.getOptionsWeek();
				break;
			case 'month':
				tabOptions	= this.getOptionsMonth();
				break;
		}

		Object.extend(this.draggableOptions, tabOptions);
	},



	/**
	 * Get options for day
	 *
	 * @method	getOptionsDay
	 * @return	{Object}
	 */
	getOptionsDay: function() {
		return {
			snap: 		this.verticalHourSnap,
			constraint: 'vertical'
		};
	},



	/**
	 * Get options for week
	 *
	 * @method	getOptionsWeek
	 * @return	{Object}
	 */
	getOptionsWeek: function() {
		return this.ext.Week.getDragDropOptions();
	},



	/**
	 * Get options for month
	 *
	 * @method	getOptionsMonth
	 * @return	{Object}
	 */
	getOptionsMonth: function() {
		return this.ext.Month.getDragOptions();
	},



	/**
	 * Get all event accessible elements from inside given DOM element
	 *
	 * @method	getEvents
	 * @param	{String}	parentElementID
	 * @return	{Element[]}
	 */
	getDraggableEventItems: function(parentElementID) {
		return $(parentElementID).select('.event').filter(function(element){
			return element.hasClassName('canEdit');
		});
	},



	/**
	 * Get all event elements in the calendar, except the noAccess classed, optionally includes also all-day events
	 *
	 * @method	getEvents
	 * @return	{Array}
	 */
	getDraggableEvents: function() {
		return this.getDraggableEventItems('calendarBody');
	},



	/**
	 * Get all all-day long event elements, except the noAccess classed
	 *
	 * @method	getDayEvents
	 * @return	{Element[]}
	 */
	getDayEvents: function() {
		return this.getDraggableEventItems('gridHeader');
	},



	/**
	 * Add drag functions to all events
	 *
	 * @method	makeEventsDraggable
	 */
	makeEventsDraggable: function() {
		this.draggables	= [];

		this.getDraggableEvents().each(function(eventElement){
			this.draggables.push( new Draggable(eventElement, this.draggableOptions) );
		}, this);
	},



	/**
	 * @method	stopEventsDraggability
	 */
	stopEventsDraggability: function() {
		this.draggables.invoke('destroy');
	},



	/**
	 * Change parent of an event. So it's on top of all the other elements and
	 * can be dragged over them all (else only dragging in the current parent would be possible)
	 *
	 * @method	moveEventToTopContainer
	 * @param	{Element}	element
	 */
	moveEventToTopContainer: function(element) {
		$('calendarBody').insert(element);
	},



	/**
	 * Add event draggable item auto-reverting function
	 *
	 * @method	initDraggableRevertToOrigin
	 * @param	{Element}	element
	 */
	initDraggableRevertToOrigin: function(element) {
		element.revertToOrigin	= this.revertToOrigin.bind(element, element.parentNode);
	},



	/**
	 * Make the element a child of its original parent (before dragging) again
	 * this points to the element itself
	 *
	 * @method	revertToOrigin
	 * @param	{Element}	originalParent
	 */
	revertToOrigin: function(originalParent) {
		$(originalParent).insert(this);
			this.setStyle({
				position:	'relative',
				left:		'0px',
				top:		'0px'
		});
	},



	/**
	 * Handler when dragging event item starts
	 *
	 * @method	onStart
	 * @param	{String}		tab				Current tab
	 * @param	{Object}		dragInfo		Information about dragging
	 * @param	{Event}			event
	 */
	onStart: function(tab, dragInfo, event) {
		switch( tab ) {
			case 'day':
				break;

			case 'week':
				this.moveEventToTopContainer(dragInfo.element);
				this.initDraggableRevertToOrigin(dragInfo.element);
					// Add left margin to prevent hovering the hours column
				dragInfo.element.setStyle({
					marginLeft:	'42px'
				});
				break;

			case 'month':
				this.moveEventToTopContainer(dragInfo.element);
				dragInfo.element.setStyle({
					position:	'absolute',
					width:		'130px'
				});
				break;
		}
	},



	/**
	 * Handler when mouse is moved during dragging (called very often!)
	 *
	 * @method	onDrag
	 * @param	{String}		tab				Current tab
	 * @param	{Object}		dragInfo		Information about dragging
	 * @param	{Event}			event
	 */
	onDrag: function(tab, dragInfo, event) {
		Todoyu.QuickInfo.disable();
	},



	/**
	 * Handler when dragging ends
	 *
	 * @method	onEnd
	 * @param	{String}		tab				Current tab
	 * @param	{Object}		dragInfo		Information about dragging
	 * @param	{Event}			event
	 */
	onEnd: function(tab, dragInfo, event) {
		var idEvent	= dragInfo.element.id.split('-').last();

		Todoyu.Hook.exec('calendar.event.drop', idEvent, dragInfo, event);

		switch( tab ) {
			case 'day':
				this.saveDayDrop(idEvent, dragInfo);
				break;

			case 'week':
				this.saveWeekDrop(idEvent, dragInfo);
				break;

			case 'month':
				dragInfo.element.setStyle({
					position: 'relative'
				});
				break;
		}

		Todoyu.QuickInfo.enable();
	},



	/**
	 * Save new position when dropped in day view
	 *
	 * @method	saveDayDrop
	 * @param	{Number}	idEvent
	 * @param	{Object}	dragInfo
	 */
	saveDayDrop: function(idEvent, dragInfo) {
		var topOffset	= dragInfo.element.positionedOffset().top;
		var daySeconds	= Math.round((topOffset / this.ext.hourHeight) * Todoyu.Time.seconds.hour);
		var dayStart	= this.ext.getTime();
		var newDateStart= new Date((dayStart + daySeconds) * 1000);

		this.saveDropping('day', idEvent, newDateStart);
	},



	/**
	 * Save new position when dropped event item in week view
	 *
	 * @method	saveWeekDrop
	 * @param	{Number}	idEvent
	 * @param	{Object}	event
	 */
	saveWeekDrop: function(idEvent, event) {
		var newDate	= this.ext.Week.getDropDate(event);

		this.saveDropping('week', idEvent, newDate, false);
	},



	/**
	 * Save new date of an event
	 *
	 * @todo	add check whether date changed or dropped into old position, if unchanged: do not save,notify,mail...
	 * @method	saveDropping
	 * @param	{String}	tab				'week' or 'month'
	 * @param	{Number}	idEvent
	 * @param	{Date}		date
	 * @param	{Boolean}	[isConfirmed]
	 */
	saveDropping: function(tab, idEvent, date, isConfirmed) {
		isConfirmed	= isConfirmed ? 1 : 0;

		if( Todoyu.Time.isDateInPast(date) ) {
			this.ext.showPastDateWarning();
		}

		var dateStr	= Todoyu.Time.getDateTimeString(date);

		var url		= Todoyu.getUrl('calendar', 'event');
		var options	= {
			parameters: {
				action:		'dragDrop',
				event:		idEvent,
				date:		dateStr,
				tab:		tab,
				confirmed:	isConfirmed
			},
			onComplete: this.onDroppingSaved.bind(this, tab, idEvent, date)
		};

		this.ext.QuickInfo.Static.removeFromCache(idEvent);

			// Disable dragging (is enabled automatically with calendar refresh after saving has finished)
		this.stopEventsDraggability();

		Todoyu.send(url, options);
	},



	/**
	 * Save new date of event that required the user to confirm saving of changes (e.g. overbooking warning)
	 *
	 * @method	saveDroppingConfirmed
	 */
	saveDroppingConfirmed: function() {
		var tab		= this.ext.getActiveTab();
		var idEvent	= this.droppedEventData.id;
		var date	= this.droppedEventData.date;

		this.saveDropping(tab, idEvent, date, true);
	},



	/**
	 * Handler when changed date of event was saved
	 * Refresh screen to render overlapping events properly
	 *
	 * @method	onDroppingSaved
	 * @param	{String}			tab
	 * @param	{Number}			idEvent
	 * @param	{Date}				date
	 * @param	{Ajax.Response}		response
	 */
	onDroppingSaved: function(tab, idEvent, date, response) {
		if( response.hasTodoyuHeader('overbookingwarning') ) {
				// Overbooking detected and is allowed - warn and ask for confirmation
			this.droppedEventData	= {
				id:		idEvent,
				date:	date
			};

			var warning	= response.getTodoyuHeader('overbookingwarning');
			Todoyu.Popups.openContent('Warning', warning, 'Overbooking Warning', 376);
		} else {
			if( response.hasTodoyuError() ) {
					// Overbooking detected and is disallowed - show notification
				Todoyu.notifyError(response.responseText, 'calendar.event.drop');
			} else {
					// Have mailing popup shown
				this.ext.Event.Mail.showPopup(idEvent, 'update');
				Todoyu.notifySuccess('[LLL:calendar.event.dropped]', 'calendar.event.drop');
				Todoyu.Hook.exec('calendar.event.moved', idEvent, date);
			}

				// Refresh to have event pop into place or revert
			this.ext.refresh();
		}
	}

};