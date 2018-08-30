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
 * Panel widget: event type selector
 *
 * @namespace	Todoyu.Ext.calendar.PanelWidget.EventTypeSelector
 */
Todoyu.Ext.calendar.PanelWidget.EventTypeSelector	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.calendar,

	/**
	 * @property	list
	 * @type		Element
	 */
	list:	null,



	/**
	 * Init event type selector panel widget
	 *
	 * @method	init
	 */
	init: function() {
		this.list	= $('panelwidget-eventtypeselector-list');

		this.installObservers();
	},



	/**
	 * Install observers
	 *
	 * @method	installObservers
	 */
	installObservers: function() {
		this.list.on('change', this.onSelectionChange.bind(this));
	},



	/**
	 * Event type select event handler
	 *
	 * @method	onSelectionChange
	 * @param	{Event}		event
	 */
	onSelectionChange: function(event) {
		this.onUpdate();
	},



	/**
	 * Update event handler
	 *
	 * @method	onUpdate
	 */
	onUpdate: function() {
		this.savePrefs();
	},



	/**
	 * Select all event types
	 *
	 * @method	selectAllEventTypes
	 * @param	{Boolean}		select
	 * @todo	remove param 'select'?
	 */
	selectAllEventTypes: function(select) {
		var selected	= select === true;

		this.list.select('option').invoke('writeAttribute', 'selected', selected);
	},



	/**
	 * Get IDs of selected event types
	 *
	 * @method	getSelectedEventTypes
	 * @return	{String[]}
	 */
	getSelectedEventTypes: function() {
		return $F(this.list);
	},



	/**
	 * Get amount of selected event types
	 *
	 * @method	getNumberOfSelectedEventTypes
	 * @return	{Number}
	 */
	getNumberOfSelectedEventTypes: function() {
		return this.getSelectedEventTypes().size();
	},



	/**
	 * Check if any type is currently selected
	 *
	 * @method	isAnyEventTypeSelected
	 * @return	{Boolean}
	 */
	isAnyEventTypeSelected: function() {
		return this.getNumberOfSelectedEventTypes() > 0;
	},



	/**
	 * Store prefs
	 *
	 * @method	savePrefs
	 */
	savePrefs: function() {
		var pref	= this.getSelectedEventTypes().join(',');

		Todoyu.Pref.save('calendar', 'panelwidgeteventtypeselector', pref, 0, this.onPrefsSaved.bind(this));
	},



	/**
	 * Handler after prefs have been saved: send update info
	 *
	 * @method	onPrefsSaved
	 * @param	{Ajax.Response}	response
	 */
	onPrefsSaved: function(response) {
		Todoyu.PanelWidget.fire('eventtypeselector', this.getSelectedEventTypes());
	}

};