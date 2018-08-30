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
 * @module	Search
 */

Todoyu.Ext.search.Preference = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.search,



	/**
	 * Save given preference of search extension
	 *
	 * @method	save
	 * @param	{String}	action
	 * @param	{String}	value
	 * @param	{String}	idItem
	 * @param	{Function}	onComplete
	 */
	save: function(action, value, idItem, onComplete) {
		Todoyu.Pref.save('search', action, value, idItem, onComplete);
	},



	/**
	 * Save preference: current active tab
	 *
	 * @method	saveActiveTab
	 * @param	{String}	tab
	 */
	saveActiveTab: function(tab) {
		var action = 'saveActiveTab';
		this.sendAction(action, tab);
	},



	/**
	 * Save preference: current filter set
	 *
	 * @method	saveCurrentFilter
	 */
	saveCurrentFilter: function() {
		var action	= 'saveCurrentFilterSet';
		var currentFilterSet = Todoyu.Ext.search.Filter.FilterID;

		this.sendAction(action, currentFilterSet);
	},



	/**
	 * Save preference: collapsed-state of given element
	 *
	 * @method	saveToggling
	 * @param	{String}	elementID
	 * @param	{Boolean}	elementDisplay
	 */
	saveToggling: function(elementID, elementDisplay) {
		var action = 'saveToggleStatus';

		var value = Object.toJSON({
			elementID:		elementID,
			elementDisplay:	elementDisplay
		});

		this.sendAction(action, value);
	},



	/**
	 * Save preference: current order of search filters
	 *
	 * @method	saveOrder
	 * @param	{String}	value
	 */
	saveOrder: function(value) {
		var action = 'saveOrder';
		this.sendAction(action, value);
	},



	/**
	 * Wrapper method to evoke given search action with given value
	 *
	 * @method	sendAction
	 * @param	{String}	action
	 * @param	{String}	value
	 */
	sendAction: function(action, value) {
		var url = Todoyu.getUrl('search', 'preference');
		var options = {
			parameters: {
				action:	action,
				value:	value
			}
		};

		Todoyu.send(url, options);
	}

};