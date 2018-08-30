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
 * Base class for status selectors in panelwidgets
 *
 * @class		PanelWidgetStatusSelector
 * @namespace	Todoyu
 * @constructor
 */
Todoyu.PanelWidgetStatusSelector = Class.create({

	/**
	 * @property	list
	 * @type		Element
	 */
	list: null,

	/**
	 * Initialize panel widget
	 *
	 * @method	initialize
	 * @param	{String}	list		element ID
	 */
	initialize: function(list) {
		this.list = $(list);

		this._observeList();
	},



	/**
	 * Install (selection change) event observer for the PanelWidget
	 *
	 * @private
	 * @method	_observeList
	 */
	_observeList: function() {
		this.list.on('change', this._onChange.bind(this));
	},



	/**
	 * Handle list selection change
	 *
	 * @private
	 * @method	_onChange
	 * @param	{Event}		event
	 */
	_onChange: function(event) {
		var runDefault = true;

		if( this.onChange ) {
			runDefault = this.onChange(event);
		}

		if( runDefault ) {
			this._defaultOnChange(event);
		}
	},



	/**
	 * Default selection change handler: select all if no option selected
	 *
	 * @private
	 * @method	_defaultOnChange
	 * @param	{Event}		event
	 */
	_defaultOnChange: function(event) {
		if( ! this.isAnyStatusSelected() ) {
			this.selectAll();
		}
	},



	/**
	 * OnChange handler
	 *
	 * @method	onChange
	 * @param	{Event}		event
	 * @return	{Boolean}
	 */
	onChange: function(event) {
		return true;
	},



	/**
	 * Get form value of the panel widget (selected statuses)
	 *
	 * @method	getValue
	 * @return	{Array}
	 */
	getValue: function() {
		return this.getSelectedStatuses();
	},



	/**
	 * Get selected statuses of panel widget
	 *
	 * @method	getSelectedStatuses
	 * @return	{Array}
	 */
	getSelectedStatuses: function() {
		return $F(this.list);
	},



	/**
	 * Get amount of selected statuses
	 *
	 * @method	getNumSelected
	 * @return	{Number}
	 */
	getNumSelected: function() {
		return this.getValue().length;
	},



	/**
	 * Check if any status' checkbox is checked
	 *
	 * @method	isAnyStatusSelected
	 * @return	{Boolean}
	 */
	isAnyStatusSelected: function() {
		return this.getNumSelected() > 0;
	},



	/**
	 * Select all statuses
	 *
	 * @method	selectAll
	 */
	selectAll: function() {
		$(this.list).childElements().invoke('writeAttribute', 'selected', true);
	},



	/**
	 * Evoke update of given panel widget
	 *
	 * @method	fireUpdate
	 * @param	{String}	key
	 */
	fireUpdate: function(key) {
		Todoyu.PanelWidget.fire(key, this.getValue());
	}

});