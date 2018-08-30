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
 * JS for the holiday editor (admin module)
 *
 * @namespace	Todoyu.Ext.calendar.HolidayEditor
 */

 Todoyu.Ext.calendar.HolidayEditor	= {

	/**
	 * Initialization
	 *
	 * @method	init
	 */
	init: function() {
		this.observeHolidaySelector();
	},



	/**
	 * Initialize holiday selector observer
	 *
	 * @method	observeHolidaySelector
	 */
	observeHolidaySelector: function() {
		Todoyu.PanelWidget.observe('holidayselector', this.onHolidaySelect.bind(this));
	},



	/**
	 * 'on holiday select' Event handler
	 *
	 * @method	onHolidaySelect
	 * @param	{Object}	widget
	 * @param	{Number}	value
	 */
	onHolidaySelect: function(widget, value) {
		this.loadHoliday(value);
	},



	/**
	 * Load holiday
	 *
	 * @method	loadHoliday
	 * @param	{Number}	idHoliday
	 */
	loadHoliday: function(idHoliday) {
		var url		= Todoyu.getUrl('calendar', 'calendar');
		var options	= {
			parameters: {
				holiday:idHoliday,
				action:	'edit'
			}
		};

		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Save holiday
	 *
	 * @method	save
	 * @param	{String}	form
	 * @return	{Boolean}
	 */
	save: function(form) {
		$(form).request({
			parameters: {
				action:	'save',
				area:	Todoyu.getArea()
			},
			onComplete: this.onSaved.bind(this)
		});

		return false;
	},



	/**
	 * 'on saved' Event handler
	 *
	 * @method	onSaved
	 * @param	{Ajax.Response}	response
	 */
	onSaved: function(response) {
		Todoyu.notifySuccess(response.responseText);
	}

 };