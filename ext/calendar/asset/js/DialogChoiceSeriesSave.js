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
 * Popup to decide if the whole series or just a single event should be edited
 */
Todoyu.Ext.calendar.DialogChoiceSeriesSave = Class.create(Todoyu.DialogChoice, {

	/**
	 * Initialize popup
	 *
	 * @method	initialize
	 * @param	{Function}	$super
	 * @param	{Function}	onSelect		Callback for selection. Args: selection and data
	 * @param	{Object}	[data]			Additional data to give to the callback function as second parameter
	 */
	initialize: function($super, onSelect, data) {
		var options		= {
			title: '[LLL:calendar.series.dialog.save.title]'
		};
		var config = {
			description: '[LLL:calendar.series.dialog.save.desc]',
			options: [
				{
					id:		'all',
					button:	'[LLL:calendar.series.dialog.save.all.button]',
					label:	'[LLL:calendar.series.dialog.save.all.label]'
				},
				{
					id:		'future',
					button:	'[LLL:calendar.series.dialog.save.future.button]',
					label:	'[LLL:calendar.series.dialog.save.future.label]'
				}
			],
			onSelect: onSelect,
			data: data || {}
		};

		$super(config, options);
	}

});