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
 * Methods for handling record items in forms
 *
 * @class		FormRecords
 * @namespace	Todoyu
 */
Todoyu.FormRecords = Class.create({

	/**
	 *
	 */
	type: null,

	/**
	 * Config
	 */
	config: {},

	/**
	 * Form element base ID for html elements
	 */
	baseID: '',

	/**
	 * Search field element
	 */
	searchField: null,

	/**
	 * Results container element
	 */
	results: null,

	/**
	 * Selection container element
	 */
	selection: null,

	/**
	 * Storage field (select) element
	 */
	storageField: null,

	/**
	 * Ignored input keys
	 */
	ignoreKeyInputs: [
		Event.KEY_RETURN,
		32, // Space,
		16, // Shift
		17, // Ctrl,
		18, // Alt
		93, // Contextmenu
		20 // Caps lock
	],

	/**
	 * Last submitted search text.
	 * Prevent needless updates
	 */
	lastText: null,

	/**
	 * Timeout for a search request
	 * Allows typing without firing too much requests
	 * @var	{Function}	timeoutSearch
	 */
	timeoutSearch: null,

	/**
	 * Timeout for clearing the search results
	 */
	timeoutClear: null,

	/**
	 * Max length of the label
	 */
	maxLabelLength: 49,



	/**
	 * Initialize
	 *
	 * @method	initialize
	 * @param	{String}	type
	 * @param	{String}	baseID
	 * @param	{Object}	[config]
	 */
	initialize: function(type, baseID, config) {
		this.type	= type;
		this.baseID	= baseID;
		this.config	= config || {};

			// Fetch element instances
		this.searchField	= $(baseID + '-search');
		this.results		= $(baseID + '-results');
		this.selection		= $(baseID + '-selection');
		this.storageField	= $(baseID + '-storage');

		this.init();
	},



	/**
	 * Observe elements
	 *
	 * @method	init
	 */
	init: function() {
		this.searchField.on('keyup', this.onSearchKeyUp.bind(this));
		this.searchField.on('blur', this.onSearchFieldBlur.bind(this));
		this.results.on('click', 'li', this.onResultItemSelect.bind(this));
		this.selection.on('click', 'span.remove', this.onSelectedItemRemove.bind(this));
	},



	/**
	 * Handle key input event in search field
	 *
	 * @method	onSearchKeyUp
	 * @param	{Event}		event
	 */
	onSearchKeyUp: function(event) {
		var hot;

		event.stop();

			// Normal keys
		if( !this.ignoreKeyInputs.include(event.keyCode) ) {
			if( this.lastText !== this.getSearchText() ) {
				this.stopDelayedClear();
				this.startDelayedSearch();
			}
		}

			// Special keys
		switch( event.keyCode ) {
			case Event.KEY_RETURN:
				hot	= this.results.down('li.hot');
				if( hot ) {
					this.onResultItemSelect(event, hot);
					this.markFirstAsHot();
				}
				break;

			case Event.KEY_ESC:
				this.clearResults();
				break;

			case Event.KEY_DOWN:
				hot = this.results.down('.hot');
				if( hot ) {
					var next = hot.next('li');
					if( next ) {
						hot.removeClassName('hot');
						next.addClassName('hot');
					}
				}
				break;

			case Event.KEY_UP:
				hot = this.results.down('.hot');
				if( hot ) {
					var previous = hot.previous('li');
					if( previous ) {
						hot.removeClassName('hot');
						previous.addClassName('hot');
					}
				}
				break;
		}
	},



	/**
	 * Handle blur event of search field
	 *
	 * @method	onSearchFieldBlur
	 * @param	{Event}		event
	 */
	onSearchFieldBlur: function(event) {
		this.startDelayedClear();
	},



	/**
	 * Start delayed clear
	 * The delay is required, because a direct click on the result list
	 * causes a blur too. In this case, we stop the timeout right after the click
	 *
	 * @method	startDelayedClear
	 */
	startDelayedClear: function() {
		this.stopDelayedClear();

		this.timeoutClear = this.clear.bind(this).delay(0.3);
	},

	

	/**
	 * Stop the clear timeout
	 *
	 * @method	stopDelayedClear
	 */
	stopDelayedClear: function() {
		if( this.timeoutClear ) {
			clearTimeout(this.timeoutClear);
		}
	},



	/**
	 * Mark first item in result list as hot
	 * Hot means, when the user presses return,
	 * this item will be added to the selection
	 *
	 * @method	markFirstAsHot
	 */
	markFirstAsHot: function() {
		this.results.select('li').invoke('removeClassName', 'hot');

		var first	= this.results.down('li');

		if( first ) {
			first.addClassName('hot');
		}
	},



	/**
	 * Handle select of result item
	 * Add it the the selection list
	 *
	 * @method	onResultItemSelect
	 * @param	{Event}		event
	 * @param	{Element}	resultItem
	 */
	onResultItemSelect: function(event, resultItem) {
		this.stopDelayedClear();

		var id			= resultItem.id.split('-').last();
		var label		= resultItem.down('.label').innerHTML.strip();
		var title		= resultItem.down('.label').title;
		var className	= resultItem.getClassNames().without('hot').join(' ').strip();

		this.addSelectedItem(id, label, title, className);
		resultItem.remove();

		this.markFirstAsHot();

		if( this.isResultListEmpty() ) {
			this.searchField.select();
		} else {
			this.searchField.focus();
		}
	},



	/**
	 * Handle remove of selection item
	 * Delete it from list
	 *
	 * @method	onSelectItemRemove
	 * @param	{Event}		event
	 * @param	{Element}	removeIcon
	 */
	onSelectedItemRemove: function(event, removeIcon) {
		var idItem	= removeIcon.up('li').id.split('-').last();

			// remove icon to prevent a double click
		removeIcon.remove();

		this.removeSelectedItem(idItem);
	},



	/**
	 * Check whether result list is empty
	 *
	 * @method	isResultListEmpty
	 * @return	{Boolean}
	 */
	isResultListEmpty: function() {
		return this.results.select('li').size() === 0;
	},



	/**
	 * Start a new timeout for search
	 *
	 * @method	startDelayedSearch
	 */
	startDelayedSearch: function() {
		clearTimeout(this.timeoutSearch);
		this.timeoutSearch = this.search.bind(this).delay(0.3);
	},



	/**
	 * Send search request
	 *
	 * @method	search
	 */
	search: function() {
		var url		= Todoyu.getUrl('core', 'records');
		var options	= {
			parameters: {
				action:	'list',
				type:	this.type,
				search:	this.getSearchText(),
				ignore: this.getSelectedItemIDs().join(',')
			},
			onComplete:	this.onSearchResponse.bind(this)
		};

		if( this.config.params ) {
			options.parameters.params = Object.toJSON(this.config.params);
		}

		this.lastText = this.getSearchText();

		Todoyu.send(url, options);
	},



	/**
	 * Handle search response
	 * The response has to be in JSON format and contain a list of objects
	 * Format: [{id:1,label:'test'},{...}]
	 *
	 * @method	onSearchResponse
	 * @param	{Ajax.Response}		response
	 */
	onSearchResponse: function(response) {
		this.clearResults();

		if( response.responseJSON ) {
			response.responseJSON.each(function(resultItem){
				if( !this.isItemInSelection(resultItem.id) ) {
					this.addResultItem(resultItem.id, resultItem.label, resultItem.className);
				} else {
					// Notify about matching items in list?
				}
			}, this);
		}

		this.markFirstAsHot();

		// Check for empty result container?
	},



	/**
	 * Clear results container
	 *
	 * @method	clearResults
	 */
	clearResults: function() {
		this.results.update('');
	},



	/**
	 * Search search results and search field
	 *
	 * @method	clear
	 */
	clear: function() {
		this.clearResults();
		this.searchField.value = '';
	},



	/**
	 * Add a new item the the results container
	 *
	 * @method	addResultItem
	 * @param	{String}	id
	 * @param	{String}	label
	 * @param	{String}	[className]
	 */
	addResultItem: function(id, label, className) {
		className	= className || '';

		this.results.insert(this.buildResultItem(id, label, className));
	},



	/**
	 * Search search text
	 *
	 * @method	getSearchText
	 * @return	{String}
	 */
	getSearchText: function() {
		return this.searchField.value.strip();
	},



	/**
	 * Get selected item IDs
	 *
	 * @method	getSelectedItemIDs
	 * @return	{Array}
	 */
	getSelectedItemIDs: function() {
		return $F(this.storageField) || [];
	},



	/**
	 * Add a new item to the selection list
	 *
	 * @method	addSelectedItem
	 * @param	{String}	id
	 * @param	{String}	label
	 * @param	{String}	[title]
	 * @param	{String}	[className]
	 */
	addSelectedItem: function(id, label, title, className) {
		this.selection.insert(this.buildSelectedItem(id, label, title, className));
		this.addStorageValue(id, label);
	},



	/**
	 * Remove an item from the selection list
	 *
	 * @method	removeSelectedItem
	 * @param	{String}	id
	 */
	removeSelectedItem: function(id) {
		this.removeSelectedElement(id);
		this.removeStorageValue(id);
	},



	/**
	 * Remove element node from the selection list
	 *
	 * @method	removeSelectedElement
	 * @param	{String}	id
	 */
	removeSelectedElement: function(id) {
		var selectedItem	= $(this.baseID + '-selection-' + id);

		if( selectedItem ) {
			new Effect.SlideUp(selectedItem, {
				duration: 0.3,
				afterFinish: function() {
					selectedItem.remove();
				}
			});
		}
	},



	/**
	 * Build a result item element node
	 *
	 * @method	buildResultItem
	 * @param	{String}	id
	 * @param	{String}	label
	 * @param	{String}	[className]
	 */
	buildResultItem: function(id, label, className) {
		className	= className || '';

		var item = new Element('li', {
			id:		this.baseID + '-results-' + id,
			class:	className
		});
		var iconEl	= new Element('span', {
			class: 'icon recordIcon'
		});
		var labelEl	= new Element('span', {
			class: 'label'
		});

		if( label.length > this.maxLabelLength ) {
			labelEl.title = label;
			label = Todoyu.String.cropText(label, 45);
		}

		labelEl.update(label);

		item.insert(iconEl);
		item.insert(labelEl);

		return item;
	},



	/**
	 * Build item for selection list
	 *
	 * @method	buildSelectedItem
	 * @param	{String}	id
	 * @param	{String}	label
	 * @param	{String}	[title]
	 * @param	{String}	[className]
	 */
	buildSelectedItem: function(id, label, title, className) {
		var item = new Element('li', {
			id:	this.baseID + '-selection-' + id
		});
		var iconEl	= new Element('span', {
			className: 'icon recordIcon'
		});
		var labelEl	= new Element('span', {
			className: 'label'
		});
		var removeEl	= new Element('span', {
			className: 'icon remove'
		});

		if( title ) {
			labelEl.title = title;
		}
		if( className ) {
			item.addClassName(className);
		}

		labelEl.update(label);

		item.insert(iconEl);
		item.insert(labelEl);
		item.insert(removeEl);

		return item;
	},



	/**
	 * Check whether item is already in the selection list
	 *
	 * @method	isItemInSelection
	 * @param	{String}	id
	 * @return	{Boolean}
	 */
	isItemInSelection: function(id) {
		return this.getStorageOption(id) !== false;
	},



	/**
	 * Add an item to the hidden select element for later form submit
	 *
	 * @method	addStorageValue
	 * @param	{String}	id
	 * @param	{String}	[label]
	 */
	addStorageValue: function(id, label) {
		label	= label || '';

		if( ! this.getStorageOption(id) ) {
			this.storageField.options[this.storageField.length] = new Option(label, id, true, true);
			Todoyu.Event.fireEvent(this.storageField, 'change');
		}
	},



	/**
	 * Remove item from select element
	 *
	 * @method	removeStorageValue
	 * @param	{String}	id
	 */
	removeStorageValue: function(id) {
		var option	= this.getStorageOption(id);

		if( option ) {
			option.remove();
			Todoyu.Event.fireEvent(this.storageField, 'change');
		}
	},



	/**
	 * Get option element for item. Or false if not in storage list
	 *
	 * @method	getStorageOption
	 * @param	{String}	id
	 * @return	{Element|Boolean}
	 */
	getStorageOption: function(id) {
		return this.storageField.select('option').detect(function(option){
			return option.value == id;
		}) || false;
	}

});