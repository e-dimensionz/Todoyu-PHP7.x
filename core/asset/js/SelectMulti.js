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
 * Handle multi-select filters
 */
Todoyu.SelectMulti = Class.create({

	/**
	 * Item list for display
	 *
	 * @var	{Todoyu.ItemList}	itemList
	 */
	itemList: null,

	/**
	 * Field list for IDs
	 *
	 * @var	{Todoyu.FieldList}	fieldList
	 */
	fieldList: null,

	/**
	 * Select field
	 *
	 * @var	{Element}	field
	 */
	field: null,

	/**
	 * Event handler callbacks
	 *
	 * @var	{Object}	callbacks
	 */
	callbacks: {},

	/**
	 * Timeout to fire change of selection
	 *
	 * @var	{Function}	fireChangeTimeout
	 */
	fireChangeTimeout: null,

	/**
	 * Last selected elements
	 *
	 * @var	{Array}		lastSelection
	 */
	lastSelection: null,



	/**
	 * Initialize multi select object
	 *
	 * @method	initialize
	 * @param	{Element|String}	field
	 * @param	{Function}			callbackAdd
	 * @param	{Function}			callbackRemove
	 */
	initialize: function(field, callbackAdd, callbackRemove) {
		this.field = $(field);

		this.callbacks = {
			onAdd:		callbackAdd,
			onRemove:	callbackRemove
		};

			// After click on options
		this.field.on('mouseup', 'option', this.onSelect.bind(this));
			// When mouse leaves field and mouseup occurs outside
		this.field.on('mouseout', 'select', this.onMouseOut.bind(this));
		this.field.stopObserving('change');

		var idItemList		= $(field).id + '-itemlist';
		var idFieldList		= $(field).id + '-value';

			// List with labels of selected elements
		this.itemList	= new Todoyu.ItemList(idItemList, {
			onRemove: this.onItemListRemove.bind(this)
		});

			// Hidden field with IDs of selected elements
		this.fieldList	= new Todoyu.FieldList(idFieldList);
		this.lastSelection = $F(this.field);
	},



	/**
	 * Handler when an item was selected
	 *
	 * @method	onSelect
	 * @param	{Event}		event
	 * @param	{Element}	option
	 */
	onSelect: function(event, option) {
		this.fireChange();
	},



	/**
	 * Handler when mouse leaves select
	 * Used when user selects a range of values but the mouseup event occurs outside of the element
	 *
	 * @method	onMouseOut
	 * @param	{Event}		event
	 * @param	{Element}	select
	 */
	onMouseOut: function(event, select) {
			// Clear other timeout
		clearTimeout(this.fireChangeTimeout);

			// Get currently selected items
		var selectedItems	= $F(this.field);

			// Only handle if any element is selected
		if( selectedItems.size() > 0 ) {
				// Check whether there are new elements selected
			var diff = Array.prototype.without.apply(selectedItems, this.lastSelection);

			if( diff.size() > 0 ) {
				this.fireChangeTimeout = this.fireChange.bind(this).delay(0.3);
			}
		}
	},



	/**
	 * Handle selection change
	 *
	 * @method	fireChange
	 */
	fireChange: function() {
			// Clear registered timeouts
		clearTimeout(this.fireChangeTimeout);
			// Save last selection
		this.lastSelection = $F(this.field);

		var items	= Todoyu.Form.getSelectedItems(this.field);

			// Add selected items to test list and hidden field
		$H(items).each(function(pair){
			this.itemList.add(pair.key, pair.value);
			this.fieldList.add(pair.key);
		}, this);

			// Select all selected options
		Todoyu.Form.selectOptions(this.field, this.fieldList.getItems());

		this.callbacks.onAdd.call(this, this, items);
	},



	/**
	 * Handler when an item was removed from the list
	 *
	 * @method	onItemListRemove
	 * @param	{Element}		listElement
	 * @param	{String|Number}	idItem
	 */
	onItemListRemove: function(listElement, idItem) {
//		this.itemList.remove(idItem);
		this.fieldList.remove(idItem);

		this.callbacks.onRemove.call(this, this, idItem);
	}

});