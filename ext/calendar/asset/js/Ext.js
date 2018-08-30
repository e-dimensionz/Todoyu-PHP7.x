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
 * Main calendar object
 *
 * @class		Calendar
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.calendar	= {

	/**
	 * Instantiate panel widgets
	 *
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},

	/**
	 * Headlet container
	 *
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet: {},

	/**
	 * List of observed elements (to be easy able to stop observing)
	 *
	 * @property	createEventObserverElements
	 * @type		Array
	 */
	createEventObserverElements:			[],

	/**
	 * @property	showEventQuickinfoObservedElements
	 * @type		Array
	 */
	showEventQuickinfoObservedElements:		[],

	/**
	 * @property	showHolidayQuickinfoObservedElements
	 * @type		Array
	 */
	showHolidayQuickinfoObservedElements:	[],

	/**
	 * @property	updateEventObserverElements
	 * @type		Array
	 */
	updateEventObserverElements:			[],

	/**
	 * Extend sCal options (weekdaystart = monday, yearprev = symbol to go backwards, yearnext = symbol to go forwards
	 *
	 * @property	calOptions
	 * @type		Object
	 */
	calOptions: {
		weekdaystart:	1,
		yearprev:		'&laquo;&laquo;',
		yearnext:		'&raquo;&raquo;'
	},

	/**
	 * Height of one our in pixel for day and week view
	 *
	 * @property	hourHeight
	 * @type		Number
	 */
	hourHeight: 42,



	/**
	 * Init calendar
	 *
	 * @method	init
	 */
	init: function() {
		this.addHooks();

			// Only initialize panelwidgets and body in calendar view
		if( this.isInCalendarArea() ) {
			this.addPanelWidgetObservers();
			this.CalendarBody.init();
			this.Navi.init();
			this.Event.Series.init();
		}
	},



	/**
	 * Check whether current area is calendar
	 *
	 * @method	isInCalendarArea
	 * @return	{Boolean}
	 */
	isInCalendarArea: function() {
		return Todoyu.isInArea('calendar');
	},



	/**
	 * Get event element
	 *
	 * @method	getEvent
	 * @param	{Element}	idEvent
	 */
	getEvent: function(idEvent) {
		return $('event-static-' + idEvent);
	},



	/**
	 * Add various JS hooks
	 *
	 * @method	addHooks
	 */
	addHooks: function() {
			// Add event save hook
		Todoyu.Hook.add('calendar.ext.quickevent.saved', this.onQuickEventSaved.bind(this));

			// Add event edit hook for event type
		Todoyu.Hook.add('calendar.event.editType', this.Event.Edit.checkHideField.bind(this.Event.Edit));

// @todo	consolidate hook adding - this hook is called upon addition..
		Todoyu.Hook.add('headlet.quickcreate.event.popupOpened', this.QuickCreateEvent.onPopupOpened());

			// Add event save hook
		Todoyu.Hook.add('calendar.event.saved', this.onEventSaved.bind(this));
			
			// Add init handler for edit form init
		Todoyu.Hook.add('form.display', this.Event.Edit.onFormDisplay.bind(this.Event.Edit));
	},



	/**
	 * Install general calendar observer
	 *
	 * @method	addPanelWidgetObservers
	 */
	addPanelWidgetObservers: function() {
		Todoyu.PanelWidget.observe('calendar', this.onDateChanged.bind(this));
		Todoyu.PanelWidget.observe('staffselector', this.onStaffSelectionChanges.bind(this));
		Todoyu.PanelWidget.observe('eventtypeselector', this.onEventTypeSelectionChanges.bind(this));
	},



	/**
	 * Install all calendar quick-infos
	 *
	 * @method	installQuickInfos
	 */
	installQuickInfos: function() {
		this.QuickInfo.Birthday.install();
		this.QuickInfo.Static.install();
		this.QuickInfo.Holiday.install();
	},



	/**
	 * Get selected date
	 *
	 * @method	getDate
	 * @return	{Date}	JavaScript date
	 */
	getDate: function() {
		return this.PanelWidget.Calendar.getDate();
	},



	/**
	 * Set selected date timestamp
	 *
	 * @method	setDate
	 * @param	{Date}	date	JavaScript timestamp
	 */
	setDate: function(date) {
		this.PanelWidget.Calendar.setDate(date, true);
	},



	/**
	 * Get calendar time (timestamp)
	 *
	 * @method	getTime
	 * @return	{Number}
	 */
	getTime: function() {
		return this.getDate().getTime() / 1000;
	},



	/**
	 * Set calendar time (timestamp
	 *
	 * @method	setTime
	 * @param	{Number}		time
	 * @param	{Boolean}		noExternalUpdate
	 */
	setTime: function(time, noExternalUpdate) {
		this.PanelWidget.Calendar.setTime(time, noExternalUpdate);
	},



	/**
	 * Get day string of selected date
	 *
	 * @method	getDateString
	 * @return	{String}
	 */
	getDateString: function() {
		return Todoyu.Time.getDateString(this.getDate())
	},



	/**
	 * Get day start timestamp of (selected day in) calendar
	 *
	 * @method	getDayStart
	 * @return	{Date}
	 */
	getDayStart: function() {
		return Todoyu.Time.getDayStart(this.getDate());
	},



	/**
	 * Get time of day start
	 *
	 * @method	getDayStartTime
	 * @return	{Number}
	 */
	getDayStartTime: function() {
		return this.getDayStart().getTime() / 1000;
	},



	/**
	 * Get starting day of week in calendar that contains the currently selected day
	 *
	 * @method	getWeekStart
	 * @return	{Date}
	 */
	getWeekStart: function() {
		return Todoyu.Time.getWeekStart(this.getDate());
	},



	/**
	 * Get time of week start
	 *
	 * @method	getWeekStartTime
	 * @return	{Number}
	 */
	getWeekStartTime: function() {
		return this.getWeekStart().getTime() / 1000;
	},



	/**
	 * Get active tab in calendar
	 *
	 * @method	getActiveTab
	 * @return	{String}
	 */
	getActiveTab: function() {
		return this.Tabs.getActive();
	},



	/**
	 * Set active tab in calendar (only set data, no update)
	 *
	 * @method	setActiveTab
	 * @param	{Object}	tab
	 */
	setActiveTab: function(tab) {
		this.Tabs.setActive(tab);
	},



	/**
	 * Event handler: onDateChanged
	 *
	 * @method	onDateChanged
	 * @param	{String}	widgetName
	 * @param	{Object}	update
	 */
	onDateChanged: function(widgetName, update) {
		var time	= update.date.getTime() / 1000;

		this.show(null, time);
	},



	/**
	 * Handler for staff selection changes
	 *
	 * @method	onStaffSelectionChanges
	 * @param	{String}		widgetName
	 * @param	{Array}		persons
	 */
	onStaffSelectionChanges: function(widgetName, persons) {
		this.refresh();
	},



	/**
	 * Handler for eventType selection changes
	 *
	 * @method	onEventTypeSelectionChanges
	 * @param	{String}	widgetName
	 * @param	{Array}		eventTypes
	 */
	onEventTypeSelectionChanges: function(widgetName, eventTypes) {
		this.refresh();
	},



	/**
	 * Handler for hook 'onEventSaved'
	 *
	 * @method	onEventSaved
	 * @param	{Number}		idEvent
	 */
	onEventSaved: function(idEvent) {
		if( this.isInCalendarArea() ) {
			this.refresh();
		}
	},



	/**
	 * Event click handler
	 *
	 * @method	onEventClick
	 * @param	{Event}		event
	 */
	onEventClick: function(event) {
		var idEvent	= event.findElement('div').id.split('-').last();

		this.Event.updateEvent(idEvent);
	},



	/**
	 * Callback for calendar body update
	 *
	 * @method	onCalendarBodyUpdated
	 * @param	{Ajax.Response}			response
	 */
	onCalendarBodyUpdated: function(response) {
		this.CalendarBody.init();
	},



	/**
	 * Update the calendar body area
	 *
	 * @method	updateCalendarBody
	 * @param	{String}		url
	 * @param	{Hash}		options
	 */
	updateCalendarBody: function(url, options) {
		Todoyu.Ui.update('calendar-body', url, options);
	},



	/**
	 * Refresh calendar with current settings
	 *
	 * @method	refresh
	 */
	refresh: function() {
		this.show();
	},



	/**
	 * Update calendar body with new config
	 *
	 * @method	show
	 * @param	{String}		tab
	 * @param	{Number}		time
	 */
	show: function(tab, time) {
			// Close special tabs (edit,view)
		this.Tabs.closeSpecialTabs();
			// Make sure calendar is visible
		this.showCalendar();
			// Hide quickinfo
		Todoyu.QuickInfo.hide();

			// Get active tab and set it
		if( !tab ) {
			tab = this.getActiveTab();
		}
			// Set new time if given as parameter
		if( time ) {
			this.setTime(time);
		}

			// Update visibility of hours-range / weekend options
		this.Navi.toggleViewOptions(tab);
		this.setActiveTab(tab);

		var url		= Todoyu.getUrl('calendar', 'calendar');
		var options	= {
			parameters: {
				action:	'update',
				tab:	this.getActiveTab(),
				date:	this.getDateString()
			},
			onComplete: this.onCalendarBodyUpdated.bind(this)
		};
			// Update view
		this.updateCalendarBody(url, options);
	},



	/**
	 * Show day by dateString
	 *
	 * @method	showDay
	 * @param	{String}	dateString		Format: Y-m-d (2010-08-15)
	 */
	showDay: function(dateString) {
		var parts	= dateString.split('-');
		var date	= new Date(parts[0], parts[1] - 1, parts[2], 0, 0, 0);
		var time	= date.getTime() / 1000;

		this.show('day', time);
	},



	/**
	 * Show week by dateString
	 *
	 * @method	showWeek
	 * @param	{String}	dateString		Format: Y-m-d (2010-08-15)
	 */
	showWeek: function(dateString) {
		var parts	= dateString.split('-');
		var date	= new Date(parts[0], parts[1] - 1, parts[2], 0, 0, 0);
		var time	= date.getTime() / 1000;

		this.show('week', time);
	},



	/**
	 * Set calendar title
	 *
	 * @method	setTitle
	 * @param	{String}		title
	 */
	setTitle: function(title) {
		this.Navi.setTitle(title);
	},



	/**
	 * Add event with popup
	 *
	 * @method	addEvent
	 * @param	{Number}		time
	 */
	addEvent: function(time) {
		if( Todoyu.Time.isTimeInPast(time) ) {
			this.showPastDateWarning();
		}

		this.Event.Edit.open(0, time);
	},



	/**
	 * Save preferences
	 *
	 * @method	savePref
	 * @param	{String}	action
	 * @param	{String}	value
	 * @param	{Number}	idItem
	 * @param	{String}	onComplete
	 */
	savePref: function(action, value, idItem, onComplete) {
		Todoyu.Pref.save('calendar', action, value, idItem, onComplete);
	},



	/**
	 * Hide calendar container
	 *
	 * @method	hideCalendar
	 */
	hideCalendar: function() {
		$('calendar').hide();
	},



	/**
	 * Show calendar container. Available containers: calendar, view, edit
	 *
	 * @method	showCalendar
	 */
	showCalendar: function() {
		$('calendar').show();
	},



	/**
	 * Hook callback when quick event was saved
	 *
	 * @method	onQuickEventSaved
	 */
	onQuickEventSaved: function() {
		if( this.isInCalendarArea() ) {
			this.refresh();
		}
	},



	/**
	 * Show info about creating an event in the past
	 *
	 * @method	showPastDateWarning
	 */
	showPastDateWarning: function() {
		Todoyu.notifyInfo('[LLL:calendar.ext.pastDateWarning]', 0, 'calendar.event.pastCreate');
	}

};