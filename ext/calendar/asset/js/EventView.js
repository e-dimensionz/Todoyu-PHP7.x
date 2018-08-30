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
 * Functions for event view
 *
 * @namespace	Todoyu.Ext.calendar.Event.View
 */
Todoyu.Ext.calendar.Event.View	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,



	/**
	 * Open event
	 *
	 * @method	open
	 * @param	{Number}	idEvent
	 */
	open: function(idEvent) {
		this.addTab('');
		this.loadDetails(idEvent);
		this.ext.hideCalendar();
		this.show();
	},



	/**
	 * Load event details
	 *
	 * @method	loadDetails
	 * @param	{Number}	idEvent
	 */
	loadDetails: function(idEvent) {
		var url		= Todoyu.getUrl('calendar', 'event');
		var options	= {
			parameters: {
				action:	'show',
				event:	idEvent
			},
			onComplete: this.onDetailsLoaded.bind(this, idEvent)
		};
		var target	= 'calendar-view';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Handler being evoked upon completion of loading of details: set tab label
	 *
	 * @method	onDetailsLoaded
	 * @param	{Number}			idEvent
	 * @param	{Ajax.Response}		response
	 */
	onDetailsLoaded: function(idEvent, response) {
		var tabLabel	= response.getTodoyuHeader('tabLabel');

		this.setTabLabel(tabLabel);
	},



	/**
	 * If not yet there: add and activate event view tab
	 *
	 * @method	addTab
	 * @param	{String}		label
	 */
	addTab: function(label) {
		if( ! Todoyu.exists('calendar-tab-view') ) {
			var tab	= Todoyu.Tabs.build('calendar', 'view', '', label, true);

			$('calendar-tab-month').insert({
				after: tab
			});
		}

			// Delay activation, because tab handler activates add tab after this function
		Todoyu.Tabs.setActive.bind(Todoyu.Tabs).defer('calendar', 'view');
	},



	/**
	 * Remove event viewing tab
	 *
	 * @method	removeTab
	 */
	removeTab: function() {
		$('calendar-tab-view').remove();
	},



	/**
	 * Set event viewing tab label
	 *
	 * @method	setTabLabel
	 * @param	{String}		label
	 */
	setTabLabel: function(label) {
		Todoyu.Tabs.setLabel('calendar', 'view', label);
	},



	/**
	 * Hide event viewing tab
	 *
	 * @method	hide
	 */
	hide: function() {
		$('calendar-view').hide();
	},



	/**
	 * Set event viewing tab shown
	 *
	 * @method	show
	 */
	show: function() {
		$('calendar-view').show();
		Todoyu.Ui.scrollToTop();
	},



	/**
	 * Edit event, close detail view
	 *
	 * @method	edit
	 * @param	{Number}	idEvent
	 */
	edit: function(idEvent) {
		this.ext.Event.edit(idEvent);
	},



	/**
	 * Check whether event viewing tab exists in DOM
	 *
	 * @method	isActive
	 * @return	{Boolean}
	 */
	isActive: function() {
		return Todoyu.exists('calendar-tab-view');
	},



	/**
	 * Cancel detail view and go back to calendar
	 *
	 */
	cancelView: function() {
		this.close();
		this.ext.show();
	},



	/**
	 * Close event viewing tab and update calendar view
	 *
	 * @method	close
	 */
	close: function() {
		this.removeTab();
		this.hide();
		this.cleanView();
	},



	/**
	 * Remove content from view panel
	 *
	 */
	cleanView: function() {
		$('calendar-view').update('');
	}

};