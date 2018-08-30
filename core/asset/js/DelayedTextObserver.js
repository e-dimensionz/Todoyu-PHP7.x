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
 * @module	Core
 */

/**
 * Observe a text field delayed
 * Prevents useless AJAX requests until the user has finished typing in the text field
 */
Todoyu.DelayedTextObserver = Class.create({

	/**
	 * Timeouts of delayed update events
	 * @property	timeouts
	 * @type		Object
	 */
	timeout: null,

	/**
	 * Field reference
	 */
	field:	null,

	/**
	 * Callback function
	 */
	callback: null,

	/**
	 * Delay
	 */
	delay: null,



	/**
	 * Observe an input field delayed
	 * Callback function will be called with field and value (Ex: onChanged: function(field, value) {})
	 *
	 * @method	initialize
	 * @param	{Element}	field
	 * @param	{Function}	callback		Callback. Parameters: field, fieldValue
	 * @param	{Number}	[delay]			Number of seconds to delay the request in seconds. Default: 0.5s
	 */
	initialize: function(field, callback, delay) {
		this.field		= $(field);
		this.callback	= callback;
		this.delay		= delay || 0.5;

		if( ! this.field ) {
			alert('DelayedTextObserver: unknown field to observe "' + field.toString() + '"');
			return false;
		}

		if( ! Object.isFunction(callback) ) {
			alert('The callback needs to be a valid function');
			return false;
		}

		this.install();
	},



	/**
	 * Install change handler
	 *
	 * @method	install
	 */
	install: function() {
		this.field.on('keyup', this.onChanged.bind(this));
	},



	/**
	 * Callback when the input field changes
	 * Clear older timeouts and start a new one
	 *
	 * @private
	 * @method	onChanged
	 * @param	{Event}		event
	 */
	onChanged: function(event) {
		clearTimeout(this.timeout);

		this.timeout = this.callCallback.bind(this).delay(this.delay);
	},



	/**
	 * Call the callback function with the field reference and the value
	 *
	 * @private
	 * @method	callCallback
	 */
	callCallback: function() {
		this.callback.call(null, this.field, $F(this.field).strip());
	}

});