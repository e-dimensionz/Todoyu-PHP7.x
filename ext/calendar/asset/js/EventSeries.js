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
 * Event series
 *
 * @module		Calendar
 * @namespace	Todoyu.Ext.calendar.Event.Series
 */
Todoyu.Ext.calendar.Event.Series = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.calendar,

	/**
	 * Dialog popup
	 */
	popup: null,

	/**
	 * Init on page load
	 *
	 * @method	init
	 */
	init: function() {
		this.initHooks();
	},



	/**
	 * Register hooks
	 *
	 * @method	initHooks
	 */
	initHooks: function() {
		Todoyu.Hook.add('calendar.event.drop', this.onEventDropped.bind(this));
	},



	/**
	 * Initialize
	 *
	 * @method	initEditView
	 * @param	{Number}	idEvent
	 */
	initForm: function(idEvent) {
		if( this.isSeriesEdit() ) {
				// Observe normal event fields
			this.observeStandardFields(idEvent);

				// Observe series fieldset for onChange
			this.getContainer().on('change', ':input',	this.onSeriesFieldChange.bind(this));

				// Add special handling for save button
			$('event-field-save').removeAttribute('onclick');
			$('event-field-save').on('click', this.onSaveButtonClick.bind(this));
		}
	},



	/**
	 * Observe normal event fields for change which affect the series
	 *
	 * @method	observeStandardFields
	 * @param	{Number}	idEvent
	 */
	observeStandardFields: function(idEvent) {
		$('event-field-eventtype').on('change', this.onStandardFieldChanged.bind(this, idEvent));
		$('event-field-persons-storage').on('change', this.onStandardFieldChanged.bind(this, idEvent));
		$('event-field-date-start').on('change', ':input',	this.onStandardFieldChanged.bind(this, idEvent));
		$('event-field-date-end').on('change', ':input',	this.onStandardFieldChanged.bind(this, idEvent));
	},



	/**
	 * Handle standard field change
	 *
	 * @method	onStandardFieldChanged
	 * @param	{Number}	idEvent
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onStandardFieldChanged: function(idEvent, event, element) {
		if( this.isSeriesActivated() ) {
			this.updateConfigFields();
		}
	},



	/**
	 * Handle event drop
	 *
	 * @method	onEventDropped
	 * @param	{Number}	idEvent
	 * @param	{Object}	dragInfo
	 * @param	{Event}		event
	 */
	onEventDropped: function(idEvent, dragInfo, event) {
		if( this.isSeriesEvent(idEvent) ) {
			Todoyu.notifyInfo('[LLL:calendar.series.eventTakeOutNotification]', 7);
		}
	},



	/**
	 * Check whether edit view is in series mode
	 * Check whether the frequency field is preset
	 *
	 * @method	isSeriesEvent
	 * @return	{Boolean}
	 */
	isSeriesEdit: function() {
		return Todoyu.exists('event-field-seriesfrequency');
	},



	/**
	 * Get container (fieldset)
	 *
	 * @method	getContainer
	 * @return	{Element}
	 */
	getContainer: function() {
		return $('event-fieldset-series');
	},



	/**
	 * Get series URL
	 *
	 * @method	getUrl
	 * @return	{String}
	 */
	getUrl: function() {
		return Todoyu.getUrl('calendar', 'series');
	},



	/**
	 * Handle changes on series config fields
	 *
	 * @method	onSeriesFieldChange
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onSeriesFieldChange: function(event, element) {
		this.updateConfigFields();
	},



	/**
	 * Update series configuration fields (including possibly shown overbooking warnings)
	 *
	 * @method	updateConfigFields
	 */
	updateConfigFields: function() {
		this.showSeriesLoaderMessage();

		var url		= this.getUrl();
		var options	= {
			parameters: {
				action:	'config',
				data:	this.getFormData()
			},
			onComplete: this.onConfigUpdate.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Display series config form AJAX loader message
	 *
	 * @method	showOverbookingLoaderMessage
	 */
	showSeriesLoaderMessage: function() {
		var seriesLegend	= $('event-fieldset-series-legend');
		var loaderContent	= new Element('a', { className:	'ajaxloader' }).update('[LLL:calendar.series.ajaxloader.overbooking]');

		seriesLegend.update(loaderContent);
	},



	/**
	 * Hide series config form AJAX loader message
	 *
	 * @method	hideSeriesLoaderMessage
	 */
	hideSeriesLoaderMessage: function() {
		var seriesLegend	= $('event-fieldset-series-legend');
		seriesLegend.update('[LLL:calendar.form.legend.series]');
	},



	/**
	 * @method	onConfigUpdate
	 * @param	{Ajax.Response}		response
	 */
	onConfigUpdate: function(response) {
			// Remove all elements
		this.getContainer().select('.fElement').invoke('remove');
			// Paste in new fields
		this.getContainer().insert(response.responseText);

		this.hideSeriesLoaderMessage();
	},



	/**
	 * Assigned users of series event changed: update series config to show possible overbookings
	 *
	 * @method	onAssignedUsersChanged
	 * @param	{Number}					idEvent
	 */
	onAssignedUsersChanged: function(idEvent) {
		this.updateConfigFields();
	},



	/**
	 * Show dialog for editing event or series
	 *
	 * @method	askSeriesEdit
	 * @param	{Number}	idSeries
	 * @param	{Number}	idEvent
	 */
	askSeriesEdit: function(idSeries, idEvent) {
		this.popup = new this.ext.DialogChoiceSeriesEdit(this.onSeriesEventEditSelection.bind(this, idSeries, idEvent), {
			series:	idSeries,
			event:	idEvent
		});
	},



	/**
	 * @method	onSeriesEventEditSelection
	 * @param	{Number}	idSeries
	 * @param	{Number}	idEvent
	 * @param	{String}	selection		'series' or 'event'
	 * @param	{Unknown}	data
	 */
	onSeriesEventEditSelection: function(idSeries, idEvent, selection, data) {
		var options	= {};

		if( selection === 'series' ) {
			options.seriesEdit = true;
		}

		this.ext.Event.Edit.open(idEvent, 0, options);
	},



	/**
	 * Show dialog to delete event or series
	 *
	 * @method  askSeriesDelete
	 * @param	{Number}	idSeries
	 * @param	{Number}	idEvent
	 */
	askSeriesDelete: function(idSeries, idEvent) {
		this.popup = new this.ext.DialogChoiceSeriesDelete(this.onSeriesEventDeleteSelection.bind(this, idSeries, idEvent), {
			series: idSeries,
			event: idEvent
		});
	},



	/**
	 * Handle selection of event delete dialog
	 *
	 * @method  onSeriesEventDeleteSelection
	 * @param	{Number}	idSeries
	 * @param	{Number}	idEvent
	 * @param	{String}	selection		'series' or 'event'
	 * @param	{Object}	data
	 */
	onSeriesEventDeleteSelection: function(idSeries, idEvent, selection, data) {
		switch(selection) {
			case 'series':
				this.removeSeries(idSeries, data.event);
				break;
			case 'event':
				this.ext.Event.removeEvent(idEvent);
				break;
		}
	},



	/**
	 * Show dialog to save all or just following event
	 *
	 * @method  askSeriesSave
	 */
	askSeriesSave: function() {
		this.popup = new this.ext.DialogChoiceSeriesSave(this.onSeriesEventSaveSelection.bind(this));
	},



	/**
	 * Handle selection of event save dialog
	 *
	 * @method	onSeriesEventSaveSelection
	 * @param	{String}	selection			'all' / 'future'
	 * @param	{Object}	data
	 */
	onSeriesEventSaveSelection: function(selection, data) {
		switch(selection) {
			case 'all':
				this.setFutureFlag(false);
				break;

			case 'future':
				this.setFutureFlag(true);
				break;
		}

		this.callOriginalSaveMethod();
	},



	/**
	 * Call original save method, depending on current context
	 * Call normal or quickcreate save method
	 *
	 * @method	callOriginalSaveMethod
	 */
	callOriginalSaveMethod: function() {
		var quickCreatePopup	= $('quickcreate');

		if( quickCreatePopup && quickCreatePopup.visible() ) {
			var form = quickCreatePopup.down('form');
			this.ext.QuickCreateEvent.save(form)
		} else {
			this.ext.Event.Edit.saveEvent(true);
		}
	},



	/**
	 * Set flag in hidden form field to enable future save mode
	 * Only following events will be updated
	 *
	 * @method  setFutureFlag
	 * @param	{Boolean}	flag
	 */
	setFutureFlag: function(flag) {
		$('event-field-serieseditfuture').value = flag ? 1 : 0;
	},



	/**
	 * Handle save button click
	 * Inject dialog if required
	 *
	 * @method  onSaveButtonClick
	 * @param	{Event}		event
	 */
	onSaveButtonClick: function(event) {
		if( this.isSeriesSaveQuestionRequired() ) {
			this.askSeriesSave();
		} else {
			this.callOriginalSaveMethod();
		}
	},



	/**
	 * Check whether series frequency is selected/enabled
	 *
	 * @method  hasFrequency
	 * @return	{Boolean}
	 */
	hasFrequency: function() {
		return $F('event-field-seriesfrequency') > 0;
	},



	/**
	 * Check whether series is activated (frequency selected)
	 *
	 * @method	isSeriesActivated
	 * @return	{Boolean}
	 */
	isSeriesActivated: function() {
		return this.hasFrequency();
	},



	/**
	 * Check whether edit form is for a new event (create)
	 *
	 * @method	isNewEvent
	 * @return	{Boolean}
	 */
	isNewEvent: function() {
		return $F('event-field-id') == 0;
	},



	/**
	 * Check whether dialog for series save is required
	 * The dialog is required if: series is enabled and we are updating an existing event
	 *
	 * @method	isSeriesSaveQuestionRequired
	 * @return	{Boolean}
	 */
	isSeriesSaveQuestionRequired: function() {
		return this.hasFrequency() && !this.isNewEvent();
	},



	/**
	 * Get form data
	 *
	 * @method	getFormData
	 * @return	{Object}
	 */
	getFormData: function() {
		return $('event-form').serialize();
	},



	/**
	 * Remove a series
	 *
	 * @method	removeSeries
	 * @param	{Number}	idSeries		Series to remove
	 * @param	{Number}	idEvent			Event on which the delete request was made
	 */
	removeSeries: function(idSeries, idEvent) {
		this.fadeSeriesEvents(idSeries, true);

		var url		= this.getUrl();
		var options	= {
			parameters: {
				action:	'delete',
				series:	idSeries
			},
			onComplete: this.onSeriesRemoved.bind(this, idSeries, idEvent)
		};

		Todoyu.send(url, options);

			// Show mail popup
		this.ext.Event.Mail.showPopup(idEvent, 'delete', {
			series: true
		});
	},



	/**
	 * Handle series remove response
	 *
	 * @method	onSeriesRemoved
	 * @param	{Number}		idSeries
	 * @param	{Number}		idEvent
	 * @param	{Ajax.Response}	response
	 */
	onSeriesRemoved: function(idSeries, idEvent, response) {

	},



	/**
	 * Get series ID of an event. Extract from class
	 *
	 * @method  getSeriesID
	 * @param	{Number}	idEvent
	 * @return	{Number|Boolean}
	 */
	getSeriesID: function(idEvent) {
		var eventElement	= this.ext.getEvent(idEvent);

		return this.getSeriesIDFromElement(eventElement);
	},



	/**
	 * Get series ID from event element
	 * Extracts series ID from special series class name
	 * Format: seriesXXX
	 *
	 * @method	getSeriesIDFromElement
	 * @param	{Element}	eventElement
	 * @return	{Number|Boolean}
	 */
	getSeriesIDFromElement: function(eventElement) {
		eventElement = $(eventElement);

		if( eventElement ) {
			var seriesClassName = $(eventElement).getClassNames().find(function(className){
				return className.startsWith('series');
			});

			if( seriesClassName ) {
				return seriesClassName.substr(6);
			}
		}

		return false;
	},



	/**
	 * Check whether event with ID idEvent is a series event
	 *
	 * @method	isSeriesEvent
	 * @param	{Number}	idEvent
	 * @return	{Boolean}
	 */
	isSeriesEvent: function(idEvent) {
		return this.getSeriesID(idEvent) !== false;
	},



	/**
	 * Check whether eventElement is a series event
	 *
	 * @method	isSeriesEventElement
	 * @param	{Element}	eventElement
	 * @return	{Boolean}
	 */
	isSeriesEventElement: function(eventElement) {
		return this.getSeriesIDFromElement(eventElement) !== false;
	},



	/**
	 * Fade out all not past events of a series
	 * Remove elements after fade out
	 *
	 * @method	fadeSeriesEvents
	 * @param	{Number}	idSeries
	 * @param	{Boolean}	[futureEventsOnly]
	 */
	fadeSeriesEvents: function(idSeries, futureEventsOnly) {
		futureEventsOnly = futureEventsOnly ? futureEventsOnly : false;

		var events	= this[futureEventsOnly ? 'getSeriesFutureEventElements' : 'getSeriesEventElements'](idSeries);

		events.each(function(eventElement) {
			eventElement.fade({
				afterFinish: function(effect) {
					eventElement.remove();
				}
			});
		});
	},



	/**
	 * @method	getSeriesEventElements
	 * @param	{Number}	idSeries
	 * @return	{Element[]}
	 */
	getSeriesEventElements: function(idSeries) {
		return $$('.event.series' + idSeries);
	},



	/**
	 * @method	getSeriesFutureEventElements
	 * @param	{Number}	idSeries
	 * @return	{Element[]}
	 */
	getSeriesFutureEventElements: function(idSeries) {
		var events		= this.getSeriesEventElements(idSeries);
		var todayTime	= Todoyu.Time.getTodayDate();

		var eventDate, eventTimestamp;
		events.each(function(eventEl, index) {
			eventDate = eventEl.up('td').id.replace('createEventAt-', '');
			eventTimestamp	= Todoyu.Time.date2Time(eventDate) * 1000;

			if( eventTimestamp <= todayTime ) {
				events.remove(index);
			}
		});

		return events;
	},



	/**
	 * Check which dates should be disabled in the end of series date field calendar
	 *
	 * @method	seriesEndCalendarDisableCheck
	 * @param	{Date}	date
	 * @return	{Boolean}
	 */
	seriesEndCalendarDisableCheck: function(date) {
		var dateStart = this.getEventDateStart();

		return date < dateStart;
	},



	/**
	 * Get start date of event
	 *
	 * @method	getEventDateStart
	 * @return	{Date}
	 */
	getEventDateStart: function() {
		return Todoyu.DateField.getDate('event-field-date-start', true);
	}

};