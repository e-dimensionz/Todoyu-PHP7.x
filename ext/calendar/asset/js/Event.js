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
 * Calendar Events
 *
 * @namespace	Todoyu.Ext.calendar.Event
 */
Todoyu.Ext.calendar.Event	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,

	/**
	 * @property	eventTypeID
	 * @type		Object
	 */
	eventTypeID: {
		general:		1,
		away:			2,
		birthday:		3,
		vacation:		4,
		education:		5,
		meeting:		6,
		awayofficial:	7,
		homeoffice:		8,
//		paper:			9,
//		carton:			10,
		compensation:	11,
		milestone:		12,
		reminder:		13
	},

	/**
	 * Possible types of actions on event records
	 *
	 * @property	operationTypeID
	 * @type		Object
	 */
	operationTypeID: {
		create:		1,
		update:		2,
		remove:		3
	},



	/**
	 * Install observers
	 *
	 * @method	installObservers
	 */
	installObservers: function() {
			// Observe all events in the calendar
		$('calendar-body').select('.event').each(function(eventElement) {
			eventElement.on('dblclick', '.event', this.onEventDblClick.bind(this));
		}, this);

		this.ext.ContextMenuEvent.attach();
	},





	/**
	 * Event double click handler
	 *
	 * @method	onEventDblClick
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onEventDblClick: function(event, element) {
		event.stop();

		var idEvent		= element.id.split('-').last();

		if( element.hasClassName('hasAccess') ) {
			this.show(idEvent);
		} else{
			Todoyu.notifyError('[LLL:calendar.event.noAccess]');
		}
	},



	/**
	 * Show event
	 *
	 * @method	show
	 * @param	{Number}		idEvent
	 */
	show: function(idEvent) {
		this.ext.Event.View.open(idEvent);
	},



	/**
	 * Edit event
	 *
	 * @method	edit
	 * @param	{Number}		idEvent
	 */
	edit: function(idEvent) {
		var idSeries	= this.Series.getSeriesID(idEvent);

		if( idSeries ) {
			this.Series.askSeriesEdit(idSeries, idEvent);
		} else {
			this.Edit.open(idEvent);
		}
	},



	/**
	 * Redirect the browser to the calendar and open edit view
	 * Use this to start edit view from another area
	 *
	 * @method	jumpToEventEditView
	 * @param	{Number}	idEvent
	 * @param	{Object}	[options]
	 */
	jumpToEventEditView: function(idEvent, options) {
		options		= options || {};
		var params	= {
			tab:		'edit',
			event:		idEvent
		};

		if( Object.keys(options).size() > 0 ) {
			params.options = options;
		}

		Todoyu.goTo('calendar', 'ext', params);
	},




	/**
	 * Remove event
	 *
	 * @method	remove
	 * @param	{Number}		idEvent
	 */
	remove: function(idEvent) {
		var idSeries	= this.Series.getSeriesID(idEvent);

		if( idSeries ) {
			this.Series.askSeriesDelete(idSeries, idEvent);
		} else {
			if( confirm('[LLL:calendar.event.delete.confirm]') ) {
				this.removeEvent(idEvent);
			}
		}
	},



	/**
	 * Remove the (single) event
	 *
	 * @method	removeEvent
	 * @param	{Number}	idEvent
	 */
	removeEvent: function(idEvent) {
			// Show mailing popup
		this.Mail.showPopup(idEvent, 'delete');

			// Remove the event
		this.fadeOut(idEvent);

		var url		= Todoyu.getUrl('calendar', 'event');
		var options	= {
			parameters: {
				action:	'delete',
				event:	idEvent
			},
			onComplete: this.onEventRemoved.bind(this, idEvent)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handle 'on removed' event
	 *
	 * @method	onRemoved
	 * @param	{Number}			idEvent
	 * @param	{Ajax.Response}		response
	 */
	onEventRemoved: function(idEvent, response) {
			// Refresh view
		if( this.ext.isInCalendarArea() ) {
			this.ext.refresh();
		}
		if( Todoyu.Ext.portal.isInPortalArea() ) {
			this.ext.EventPortal.reduceAppointmentCounter();
		}

		this.ext.Reminder.Popup.dismissPlannedEventPopup(idEvent);

		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('[LLL:calendar.event.delete.error]');
		} else {
			Todoyu.notifySuccess('[LLL:calendar.event.delete.success]');
		}
	},



	/**
	 * @method	fadeOut
	 * @param	{Number}	idEvent
	 */
	fadeOut: function(idEvent) {
		$('event-static-' + idEvent).fade();
	},



	/**
	 * Automatically set the ending date to the same value as the starting date in a form
	 *
	 * @method	updateEnddate
	 * @param	{String}	formName	Name of the XML-form
	*/
	updateEnddate:function(formName) {
		if( $(formName + '-0-field-enddate') ) {
			$(formName + '-0-field-enddate').value	= $F(formName + '-0-field-startdate');
		}
	},



	/**
	 * Show given event in given view (day / week / month) of calendar
	 *
	 * @method	goToEventInCalendar
	 * @param	{Number}		idEvent
	 * @param	{Number}		date
	 * @param	{String}		[view]
	 */
	goToEventInCalendar: function(idEvent, date, view) {
		view 		= view || 'day';
		var params	= {
			tab:	view,
			date:	date
		};

		Todoyu.goTo('calendar', '', params);
	},



	/**
	 * Add an event on the same time as the selected one
	 *
	 * @method	addEventOnSameTime
	 * @param	{Number}		idEvent
	 */
	addEventOnSameTime: function(idEvent) {
		var time	= this.getTime(idEvent);

		this.ext.addEvent(time);
	},



	/**
	 * Get time of an event by its position of parent container
	 *
	 * @method	getTime
	 * @param	{Number}	idEvent
	 */
	getTime: function(idEvent) {
		var mode	= this.ext.getActiveTab();
		var time	= 0;
		var event	= $('event-' + idEvent);

		if( event ) {
			if( mode === 'month' ) {
				time	= Todoyu.Time.date2Time(event.up('td').id.split('-').slice(1).join('-'));
			} else {
				var viewport= event.viewportOffset();
				var scroll	= document.body.cumulativeScrollOffset();
				var top		= viewport.top + scroll.top;
				var left	= viewport.left + scroll.left;

				time	= this.ext.CalendarBody.getTimeForPosition(left, top);
			}
		}

		return time;
	}

};