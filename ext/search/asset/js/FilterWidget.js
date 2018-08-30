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
 * Filter widget in search area
 *
 * @module	Search
 */
Todoyu.Ext.search.FilterWidget = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:		Todoyu.Ext.search,

	/**
	 * Timeouts of widgets
	 *
	 * @property	timeout
	 * @type		Object
	 */
	timeout:	{},



	/**
	 * Handler when text inside a text-widget is changed: update search results
	 * The update is delayed, so no every key will force a result update
	 *
	 * @method	onTextEntered
	 * @param	{Element}	input	The textinput
	 */
	onTextEntered: function(input) {
			// Get widget value and name
		var name	= $(input).up('div.filterWidget').id;
		var value	= $F(input);

			// Clear existing timeout of previous inputs
		if( this.timeout[name] ) {
			window.clearTimeout(this.timeout[name]);
			delete this.timeout[name];
		}

			// Update filter condition
		this.ext.Filter.setConditionValue(name, value);

			// Create new timeout to update results (can be cleared by new inputs)
		this.timeout[name] = this.ext.Filter.updateResults.bind(this.ext.Filter, this.ext.Filter.getActiveTab(), 0).delay(0.4);
	}

};