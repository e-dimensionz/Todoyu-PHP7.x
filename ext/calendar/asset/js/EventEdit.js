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
 * Functions for event edit
 *
 * @module		Calendar
 * @namespace	Todoyu.Ext.calendar.Event.Edit
 */
Todoyu.Ext.calendar.Event.Edit	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,

	/**
	 * @property	lastAssignedUserIDs
	 * @type		Array
	 */
	lastAssignedUserIDs: [],

	/**
	 * @property	initialized
	 * @type		Boolean
	 */
	initialized: false,



	/**
	 * Open edit view for an event
	 *
	 * @method	open
	 * @param	{Number}		idEvent
	 * @param	{Number}		time
	 * @param	{Object}		[options]
	 */
	open: function(idEvent, time, options) {
		Todoyu.QuickInfo.hide();
		Todoyu.Ui.scrollToTop();

			// Add edit tab and cleanup previous view (close open view tab / hide calendar)
		this.addTab('');
		if( Todoyu.Tabs.hasTab('calendar', 'view') ) {
			this.ext.Event.View.close();
		}
		this.ext.hideCalendar();

		this.loadForm(idEvent, time, options);
	},



	/**
	 * Open edit view for event from detail view
	 *
	 * @method	openFormDetailView
	 * @param	{Number}		idEvent
	 */
	openFromDetailView: function(idEvent) {
		this.cancelEdit();
		this.open(idEvent, 0);
	},



	/**
	 * Load edit form for an event
	 *
	 * @method	loadForm
	 * @param	{Number}		idEvent
	 * @param	{Number}		time
	 * @param	{Object}		[extraOptions]
	 */
	loadForm: function(idEvent, time, extraOptions) {
		extraOptions= extraOptions || {};
		var url		= Todoyu.getUrl('calendar', 'event');
		var options	= {
			parameters: {
				action:		'edit',
				event:		idEvent
			},
			onComplete: this.onFormLoaded.bind(this, idEvent, extraOptions)
		};
		var target	= 'calendar-edit';

		if( Object.keys(extraOptions).size() > 0 ) {
			options.parameters.options = Object.toJSON(extraOptions)
		}
		if( time ) {
			options.parameters.date = Todoyu.Time.getDateTimeString(time);
		}

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Handler when edit form is loaded
	 *
	 * @method	onFormLoaded
	 * @param	{Number}			idEvent
	 * @param	{Object}			extraOptions
	 * @param	{Ajax.Response}		response
	 */
	onFormLoaded: function(idEvent, extraOptions, response) {
		var tabLabel	= response.getTodoyuHeader('tabLabel');

		this.setTabLabel(tabLabel);
		this.initForm(idEvent, extraOptions);

		this.show();
	},



	/**
	 * Initialize form on display if not already initialized by response handler
	 *
	 * @method	onFormDisplay
	 * @param	{String}	idForm
	 * @param	{String}	formName
	 * @param	{Number}	idRecord
	 */
	onFormDisplay: function(idForm, formName, idRecord) {
		if( idForm === 'event-form' && !this.initialized ) {
			this.initForm(idRecord);
		}
	},



	/**
	 * Initialize form
	 *
	 * @method	initForm
	 * @param	{Number}	idEvent
	 * @param	{Object}	[extraOptions]
	 */
	initForm: function(idEvent, extraOptions) {
		extraOptions = extraOptions || {};

		this.observeEventType();
		this.observeDayEvent();
		this.observeDateFields();
		this.observeAssignedUsers(idEvent);

		this.ext.Event.Series.initForm(idEvent, extraOptions.seriesEdit);

			// Toggle fields, change format if day event is active (wait for init if calendar)
		this.toggleDateFields.bind(this).defer();
		this.updateVisibleFields.bind(this).defer();

		this.initialized = true;
	},



	/**
	 * Observe day event field for changes
	 *
	 * @method	observeDayEvent
	 */
	observeDayEvent: function() {
		$('event-field-is-dayevent').on('click', this.onDayEventChanged.bind(this));
	},



	/**
	 * Handle day event option change
	 *
	 * @method	onDayEventChanged
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onDayEventChanged: function(event, element) {
		this.toggleDateFields();
	},



	/**
	 * Handle change of selected event type
	 *
	 * @method	onChangeEventtype
	 * @param	{Element}	inputField
	 */
	onChangeEventtype: function(inputField) {
		var idInputFieldArr		= inputField.id.split('-').without('is');
		var idFieldEventType	= idInputFieldArr.join('-').replace('dayevent', 'eventtype');

		var isDayEvent	= $('event-field-is-dayevent').checked;
		var url			= Todoyu.getUrl('calendar', 'event');

		var options = {
			parameters: {
				action: 'updateeventtpyes',
				isDayEvent: isDayEvent ? 1 : 0,
				value: $(idFieldEventType).getValue()
			},
			onComplete: this.onUpdateEventTypes.bind(this, idFieldEventType)
		};

		Todoyu.Ui.update(idFieldEventType, url, options);
	},



	/**
	 * Fills the found options to the selector
	 * Highlights the selector for 2 seconds
	 *
	 * @method	onUpdateCompanyAddressRecords
	 * @param	{String}			idTarget
	 * @param	{Ajax.Response}		response
	 */
	onUpdateEventTypes: function(idTarget, response) {
		new Effect.Highlight(idTarget, {
					duration: 2.0
				});
	},



	/**
	 * Toggle date fields depending on all-day event flag
	 *
	 * @param	{Boolean}	forceDayEvent		Force day event. If not set or false, check the day event field
	 * @method	toggleDateFields
	 */
	toggleDateFields: function(forceDayEvent) {
		var isDayEvent	= forceDayEvent ? true : $('event-field-is-dayevent').checked;

		var classMethod,
			newConfig,
			elementDateStart= $('event-field-date-start'),
			elementDateEnd	= $('event-field-date-end');

		if( isDayEvent ) {
			newConfig	= {
				ifFormat: 	Todoyu.Config.dateFormat.date,
				showsTime:	false
			};
			classMethod	= 'addClassName';
		} else {
			newConfig	= {
				ifFormat: 	Todoyu.Config.dateFormat.datetime,
				showsTime:	true
			};
			classMethod	= 'removeClassName';
		}

		Todoyu.DateField.changeCalendarConfig(elementDateStart, newConfig);
		Todoyu.DateField.changeCalendarConfig(elementDateEnd, newConfig);

		elementDateStart[classMethod]('dayEvent');
		elementDateEnd[classMethod]('dayEvent');
	},



	/**
	 * Event type change observer
	 *
	 * @method	observeEventType
	 */
	observeEventType: function() {
		$('event-field-eventtype').on('change', this.onEventTypeChange.bind(this));
	},



	/**
	 * Handle event type change
	 *
	 * @method	onEventTypeChange
	 * @param	{Event}		event
	 */
	onEventTypeChange: function(event) {
		var forceDayEvent = false;

		var eventType	= $F('event-field-eventtype');
		if( this.isForcedDayEventType(eventType) ) {
				// Exception: birthdays are always all-day (UI:date instead datetime)
			forceDayEvent	= true;
		}

		this.toggleDateFields(forceDayEvent); // To toggle hours if required
		this.updateVisibleFields();
	},



	/**
	 * Check whether event type forces a day event
	 *
	 * @method	isForcedDayEventType
	 * @param	{Number}	eventType
	 * @return	{Boolean}
	 */
	isForcedDayEventType: function(eventType) {
		return eventType == this.ext.Event.eventTypeID.birthday;
	},



	/**
	 * Install observer on event form date fields
	 *
	 * @method	observeDates
	 */
	observeDateFields: function() {
			// Install date field observers
		$('event-field-date-start').on(	'change', ':input',	this.onDateChanged.bind(this));
		$('event-field-date-end').on(	'change', ':input',	this.onDateChanged.bind(this));
	},



	/**
	 * @method	onDateChanged
	 */
	onDateChanged: function() {
		//@todo implement refresh with overbooking-check/warning (series has it already)
	},



	/**
	 * Event participants change observer
	 *
	 * @method	observeEventType
	 * @param	{Number}			idEvent
	 */
	observeAssignedUsers: function(idEvent) {
			// Remember start selection
		this.lastAssignedUserIDs = this.getAssignedUserIDs();

		$('event-field-persons-storage').on('change', this.onAssignedUsersEvent.bind(this, idEvent));
	},



	/**
	 * Handle events on assigned users.
	 * Check whether anything changed for real
	 *
	 * @method	onAssignedUsersEvent
	 * @param	{Number}	idEvent
	 * @param	{Event}		event
	 */
	onAssignedUsersEvent: function(idEvent, event) {
		var assignedUsers	= this.getAssignedUserIDs();

		if( assignedUsers.join(',') !== this.lastAssignedUserIDs.join(',') ) {
			this.lastAssignedUserIDs = assignedUsers;
			this.onAssignedUsersChanged(idEvent);
		}
	},



	/**
	 * Unhide all given (possibly hidden) fields
	 *
	 * @method	showAllFields
	 * @Array	fields
	 */
	showFields: function(fields) {
		fields.invoke('removeClassName', 'hidden');
	},



	/**
	 * Update the field visibility in the form according to selected type of event
	 *
	 * @method	updateVisibleFields
	 */
	updateVisibleFields: function() {
		var eventType	= $F('event-field-eventtype');
		var fieldsToHide= [];

		var allFields	= $('event-form').select('div.fElement');
		this.showFields(allFields);

			// Extract field names
		var allFieldNames	= allFields.collect(function(field){
			return field.id.replace('formElement-event-field-', '');
		});

			// Get registered 'eventtype' hook-functions
		var checkHooks	= Todoyu.Hook.get('calendar.event.editType');

			// Check all fields, if a hooks wants to hide it
		allFieldNames.each(function(fieldName){
				// Check all hooks if they want to hide the field
			checkHooks.each(function(hookCallback){
				if( hookCallback(fieldName, eventType) ) {
					fieldsToHide.push(fieldName);
				}
			}, this);
		}, this);

		fieldsToHide.each(function(fieldName){
			this.hideField(fieldName, 'event');
		}, this);

		this.initAutonotificationComment();
	},



	/**
	 * Hide auto-notification info if there are no participants receiving an auto-mail
	 *
	 * @method	initAutonotificationComment
	 */
	initAutonotificationComment: function() {
		var idCommentElement		= 'formElement-event-field-autonotification-comment-inputbox';
		var hasNoAutonotification	= $(idCommentElement).down('.commenttext').innerHTML.indexOf('id="person-') === -1;

		if( hasNoAutonotification ) {
			$('event-fieldset-autoemail').hide();
		}
	},



	/**
	 * Update manual and automatic email receiver options
	 *
	 * @method	onChangeParticipants
	 * @param	{Number}				idEvent
	 */
	onAssignedUsersChanged: function(idEvent) {
		this.updateAutoMailComment(idEvent);
	},



	/**
	 * @method	updateAutoMailComment
	 * @param	{Number}	idEvent
	 */
	updateAutoMailComment: function(idEvent) {
		var personIDs	= this.getAssignedUserIDs();

		var url		= Todoyu.getUrl('calendar', 'mail');
		var options	= {
			parameters: {
				action:		'autoMailComment',
				event:		idEvent,
				persons:	personIDs.join(',')
			},
			onComplete: this.onAutoMailCommentUpdated.bind(this, idEvent)
		};
		var target	= $('formElement-event-field-autonotification-comment-inputbox').down('.commenttext');

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Update selectable manual reminder receivers: disable auto-notified participating persons
	 *
	 * @method	onAutoNotifiedPersonsUpdated
	 * @param	{Number}						idEvent
	 * @param	{Ajax.Response}					response
	 */
	onAutoMailCommentUpdated: function(idEvent, response) {
		var automailPersonIDs = response.getTodoyuHeader('autoMailPersons');

		if( automailPersonIDs.length > 0 ) {
				// There are persons to be auto-notified
			Todoyu.Ui.twinkle($('event-fieldset-autoemail'));
		} else {
				// None of the participants receives an auto-notification
			$('event-fieldset-autoemail').fade();
		}
	},



	/**
	 * Get IDs of assigned users
	 *
	 * @method	getAssignedUserIDs
	 * @return	{Array}
	 */
	getAssignedUserIDs: function() {
		return $F('event-field-persons-storage') || [];
	},




	/**
	 * Check whether a field has to be hidden for an event type
	 *
	 * @method	checkHideField
	 * @param	{String}	fieldName
	 * @param	{Number}	eventType
	 */
	checkHideField: function(fieldName, eventType) {
		eventType	= parseInt(eventType, 10);
		var fields	= [];

		switch(eventType) {
				// Birthday
			case Todoyu.Ext.calendar.Event.eventTypeID.birthday:

				fields	= ['is-dayevent', 'date-end', 'person', 'place'];
				break;

				// Away official
			case Todoyu.Ext.calendar.Event.eventTypeID.awayofficial:
				fields	= ['is-private'];
				break;

				// Reminder
			case Todoyu.Ext.calendar.Event.eventTypeID.reminder:
				fields	= ['date-end'];
				break;
		}

		return fields.include(fieldName);
	},



	/**
	 * Hide a field in the event form
	 *
	 * @method	hideField
	 * @param	{String}		fieldName
	 * @param	{String}		formName
	 */
	hideField: function(fieldName, formName) {
		formName	= formName ? formName : 'event';

		var field	= $('formElement-' + formName + '-field-' + fieldName);

		if( field ) {
				// hide field
			field.addClassName('hidden');

				// Uncheck checkboxes (not relevant is not visible)
			field.select(':checkbox').each(function(checkbox){
				checkbox.checked = false;
			});
				// Clear text inputs
			field.select(':text').each(function(input){
				input.value = '';
			});
		}
	},



	/**
	 * Add the edit tab
	 *
	 * @method	addTab
	 * @param	{String}	label
	 */
	addTab: function(label) {
		if( ! Todoyu.Tabs.hasTab('calendar', 'edit') ) {
			Todoyu.Tabs.addTab('calendar', 'edit', '', label, true, false);
		}
	},



	/**
	 * Close edit view
	 *
	 * @method	close
	 */
	close: function() {
		if( Todoyu.exists('calendar-tab-edit') ) {
			this.removeTab();
		}
		if( Todoyu.exists('calendar-edit') ) {
			this.hide();
			this.ext.showCalendar();
			$('calendar-edit').update('');
		}
	},



	/**
	 * Set edit tab label
	 *
	 * @method	setTabLabel
	 * @param	{String}		label
	 */
	setTabLabel: function(label) {
		Todoyu.Tabs.setLabel('calendar', 'edit', label);
	},



	/**
	 * Check if edit view is active
	 *
	 * @method	isActive
	 * @return	{Boolean}
	 */
	isActive: function() {
		return Todoyu.exists('calendar-tab-edit');
	},



	/**
	 * Remove edit tab
	 *
	 * @method	removeTab
	 */
	removeTab: function() {
		if( Todoyu.exists('calendar-tab-edit') ) {
			$('calendar-tab-edit').remove();
		}
	},



	/**
	 * Show edit container
	 *
	 * @method	show
	 */
	show: function() {
		$('calendar-edit').show();
	},



	/**
	 * Hide edit container
	 *
	 * @method	hide
	 */
	hide: function() {
		$('calendar-edit').hide();
	},



	/**
	 * Save the event.
	 * If overbooking is allowed and warning has been confirmed, save even overbooked entries.
	 *
	 * @method	saveEvent
	 * @param	{Boolean}	[isOverbookingConfirmed]
	 */
	saveEvent: function(isOverbookingConfirmed) {
		isOverbookingConfirmed	= isOverbookingConfirmed || false;

		var eventForm	= $('event-form');

		Todoyu.Ui.closeRTE(eventForm);
		eventForm.request({
			parameters: {
				action:					'save',
				isOverbookingConfirmed:	isOverbookingConfirmed ? 1 : 0,
				area:					Todoyu.getArea()
			},
			onComplete: this.onEventSaved.bind(this)
		});
	},



	/**
	 * Handler after event saved
	 *
	 * @method	onEventSaved
	 * @param	{Ajax.Response}	response
	 */
	onEventSaved: function(response) {
		var idEvent	= response.getTodoyuHeader('event');

		if( response.hasTodoyuError() ) {
				// Notify of invalid data
			Todoyu.notifyError('[LLL:calendar.event.saved.error]', 'calendar.event.saved');
			$('event-form').replace(response.responseText);
			this.initForm(idEvent);
		} else if( response.hasTodoyuHeader('overbookingwarning') ) {
				// Show overbooking warning + confirmation prompt
			this.updateInlineOverbookingWarning(response.getTodoyuHeader('overbookingwarningInline'));
			var warning	= response.getTodoyuHeader('overbookingwarning');
			Todoyu.Popups.openContent('Warning', warning, 'Overbooking Warning', 376);
		} else {
				// Event saved - exec hooks, clean event record cache and notify success
			this.notifyEventSaved(response);

			Todoyu.Hook.exec('calendar.event.saved', idEvent);
			this.ext.QuickInfo.Static.removeFromCache(idEvent);

				// Update calendar body showing time of the saved event and close the edit form
			var time	= response.getTodoyuHeader('time');

				// Show last active tab for selected time
			this.ext.setTime(time);
			this.ext.refresh();
			this.close();
		}
	},



	/**
	 * Update event edit form's inline overbooking warning
	 *
	 * @method	renderOverbookingWarningInline
	 * @param	{String}	warningContent
	 */
	updateInlineOverbookingWarning: function(warningContent) {
			// Remove old warning
		if( Todoyu.exists('overbooking-warning-inline') ) {
			$('overbooking-warning-inline').remove();
		}
			// Render and insert current warning
		var inlineWarning	= new Element('div', {
			id:			'overbooking-warning-inline',
			'class':	'errorMessage'
		}).update(warningContent);

		$('formElement-event-field-persons-inputbox').insert(inlineWarning);
	},



	/**
	 * Send email notification headers after event has been saved
	 *
	 * @method	notifyEventSaved
	 * @param	{Ajax.Response}	response
	 */
	notifyEventSaved: function(response) {
		if( response.getTodoyuHeader('sentEmail') ) {
			Todoyu.notifySuccess('[LLL:calendar.event.mail.notification.sent]', 'calendar.notification.sent');
		}
		if( response.getTodoyuHeader('sentAutoEmail') ) {
			Todoyu.notifySuccess('[LLL:calendar.event.mail.notification.autosent]', 'calendar.notification.autosent');
		}

		Todoyu.notifySuccess('[LLL:calendar.event.saved.ok]', 'calendar.event.saved');
	},



	/**
	 * Close event form
	 *
	 * @method	cancelEdit
	 */
	cancelEdit: function(){
		this.ext.show();
		this.close();
	},



	/**
	 * Handler when event person assignment field is auto-completed
	 *
	 * @method	onPersonAcCompleted
	 * @param	{Ajax.Response}			response
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onPersonAcCompleted: function(response, autocompleter) {
		if( response.isEmptyAcResult() ) {
			Todoyu.notifyInfo('[LLL:calendar.event.ac.personassignment.notFoundInfo]', 'calendar.person.notfound');
			return false;
		}
	},



	/**
	 * Update label of the person selector in an event
	 *
	 * @method	onPersonAcSelected
	 * @param	{Element}				inputField
	 * @param	{Element}				idField
	 * @param	{String}				selectedValue
	 * @param	{String}				selectedText
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onPersonAcSelected: function(inputField, idField, selectedValue, selectedText, autocompleter) {
		$(inputField).up('div.databaseRelation').down('span.label').update(selectedText);
	},



	/**
	 *
	 * @param	{String}		fieldID
	 */
	validatePersonHoliday: function(fieldID) {
		var personIDs	= $('event-field-persons-storage').getValue();
		var start		= $('event-field-date-start').getValue();
		var end			= $('event-field-date-end').getValue();

		var url = Todoyu.getUrl('calendar', 'event');
		var options = {
			parameters: {
				action: 'validateUserHoliday',
				personIDs: JSON.stringify(personIDs),
				dateStart: start,
				dateEnd: end
			},
			onComplete: this.onPersonHolidayValidated.bind(this)
		};


		Todoyu.send(url, options);
	},



	/**
	 *
	 * @param	{Ajax.Response}	response
	 */
	onPersonHolidayValidated: function(response) {
		var fieldIDPersons	= 'event-field-persons-search';

		var error	= response.getTodoyuHeader('holidays');
		Todoyu.Form.setFieldWarningStatus(fieldIDPersons, error);

		if( error ) {
			Todoyu.FormValidator.addWarningMessage(fieldIDPersons, response.responseText, false);
		} else {
			Todoyu.FormValidator.removeWarningMessage(fieldIDPersons);
		}
	}
};