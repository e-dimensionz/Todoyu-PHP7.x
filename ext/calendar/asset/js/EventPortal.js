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
 * Functions for events in portal
 *
 * @namespace	Todoyu.Ext.calendar.EventPortal
 */
Todoyu.Ext.calendar.EventPortal	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.calendar,



	/**
	 * Toggle details of listed event entry (in listing of e.g portal's events tab). Used for eventslist only
	 *
	 * @method	toggleDetails
	 * @param	{Number}		idEvent
	 */
	toggleDetails: function(idEvent) {
		$('event-static-' + idEvent).toggleClassName('expanded');

			// If detail is not loaded yet, send request
		if( this.isDetailsLoaded(idEvent)) {
			$('event-' + idEvent + '-details').toggle();

			this.saveEventExpandedStatus(idEvent, $('event-' + idEvent + '-details').visible());
		} else {
			this.loadDetails(idEvent);
		}

		this.setAcknowledgeStatus(idEvent);
	},



	/**
	 * Load event details ("expands" the item with loaded details)
	 *
	 * @method	loadDetails
	 * @param	{Number}		idEvent
	 */
	loadDetails: function(idEvent) {
		var url		= Todoyu.getUrl('calendar', 'portal');
		var options	= {
			parameters: {
				action: 'detail',
				event:	idEvent
			},
			onComplete: this.onDetailsLoaded.bind(idEvent)
		};
		var target	= 'event-' + idEvent + '-header';

		Todoyu.Ui.append(target, url, options);
	},



	/**
	 * Handler when event details have been loaded
	 *
	 * @method	onDetailsLoaded
	 * @param	{Number}			idEvent
	 * @param	{Ajax.Response}		response
	 */
	onDetailsLoaded: function(idEvent, response) {

	},



	/**
	 * Check whether event details are loaded
	 *
	 * @method	isDetailsLoaded
	 * @param	{Number}		idEvent
	 * @return	{Boolean}
	 */
	isDetailsLoaded: function(idEvent) {
		return Todoyu.exists('event-' + idEvent + '-details');
	},



	/**
	 * Save event details
	 *
	 * @method	saveEventExpandedStatus
	 * @param	{Number}		idEvent
	 * @param	{Boolean}		expanded
	 */
	saveEventExpandedStatus: function(idEvent, expanded) {
		var value	= expanded ? 1 : 0;
		this.ext.savePref('portalEventExpanded', value, idEvent);
	},



	/**
	 * Set event acknowledged
	 *
	 * @method	acknowledgeEvent
	 * @param	{Number}		idEvent
	 */
	acknowledgeEvent: function(idEvent) {
		var url	= Todoyu.getUrl('calendar', 'event');

		var options	= {
			parameters: {
				action:	'acknowledge',
				event:	idEvent
			},
			onComplete: this.onAcknowledged.bind(this, idEvent)
		};

		this.setAcknowledgeStatus(idEvent);

		Todoyu.send(url, options);
	},



	/**
	 * 'On acknowledged' event handler
	 *
	 * @method	onAcknowledged
	 * @param	{Number}			idEvent
	 * @param	{Ajax.Response}		response
	 */
	onAcknowledged: function(idEvent, response) {

	},



	/**
	 * Set acknowledge status for event in portal
	 *
	 * @method	setAcknowledgeStatus
	 * @param	{Number}	idEvent
	 */
	setAcknowledgeStatus: function(idEvent) {
		$('acknowledge-' + idEvent).removeClassName('not');
	},



	/**
	 * Reduce the count of appointments in the tab label
	 *
	 * @method	reduceAppointmentCounter
	 */
	reduceAppointmentCounter: function() {
		var numResults	= Todoyu.Ext.portal.Tab.getNumResults('appointment');

		Todoyu.Ext.portal.Tab.updateNumResults('appointment', numResults-1);
	},



	/**
	 * Jump to calendar event edit view
	 *
	 * @method	edit
	 * @param	{Number}	idEvent
	 */
	edit: function(idEvent) {
		this.ext.Event.jumpToEventEditView(idEvent);
	}

};