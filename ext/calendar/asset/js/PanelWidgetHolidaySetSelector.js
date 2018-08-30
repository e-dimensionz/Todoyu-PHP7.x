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
 * Panel widget: holidaySet selector
 *
 * @namespace	Todoyu.Ext.calendar.PanelWidget.HolidaySetSelector
 */
Todoyu.Ext.calendar.PanelWidget.HolidaySetSelector	= {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:			Todoyu.Ext.calendar,

	/**
	 * @property	key
	 * @type		String
	 */
	key:			'panelwidget-holidaysetselector',

	/**
	 * @property	list
	 * @type		String
	 */
	list:			'panelwidget-holidaysetselector-list',



	/**
	 * Init (evoke observers installation)
	 *
	 * @method	init
	 */
	init: function() {
		this.installObservers();
	},



	/**
	 * Install observers
	 *
	 * @method	installObservers
	 */
	installObservers: function() {
		$(this.list).on('change', this.onSelectionChange.bind(this));
	},



	/**
	 * HolidaySet select event handler
	 *
	 * @method	onSelectionChange
	 * @param	{Event}		event
	 */
	onSelectionChange: function(event) {
		this.onUpdate($F(this.list).join(','));
	},



	/**
	 * Update event handler
	 *
	 * @method	onUpdate
	 * @param	{String}		value
	 */
	onUpdate: function(value) {
		this.savePrefs();

		var holidaySetIDs = $F(this.list);

		Todoyu.PanelWidget.fire(this.key, holidaySetIDs);
	},



	/**
	 * Get IDs of selected holidaySets
	 *
	 * @method	getSelectedHolidaySetIDs
	 * @return	{Number[]}
	 */
	getSelectedHolidaySetIDs: function() {
		return $F(this.list);
	},



	/**
	 * Store prefs
	 *
	 * @method	savePrefs
	 */
	savePrefs: function() {
		var holidaySetIDs	= $F(this.list).join(',');

		var url		= Todoyu.getUrl('calendar', 'preference');
		var options	= {
			parameters: {
				action:		'panelwidgetholidaysetselector',
				preference:	this.key,
				value:		holidaySetIDs
			},
			onComplete: this.onPrefsSaved.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler after prefs saved: evoke refresh
	 *
	 * @method	onPrefsSaved
	 * @param	{Ajax.Response}	response
	 */
	onPrefsSaved: function(response) {
		Todoyu.Ext.calendar.refresh();
	}

};