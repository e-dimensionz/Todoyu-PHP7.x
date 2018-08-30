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
 * List which automatically extends itself when all inputs are filled out
 */
Todoyu.AutoExtendingList = Class.create({

	/**
	 * @var	{Element}	list			List element
	 */
	list: null,

	/**
	 * @var	{String}	listTag			Tag for list (ex: UL)
	 */
	listTag: null,

	/**
	 * @var	{String}	listItemTag		Tag for list item (default: LI)
	 */
	listItemTag: null,

	/**
	 * @var	{String}	inputSelector	Selector for input elements (default: input[type=text])
	 */
	inputSelector: null,

	/**
	 * Backup copy of list element (will be restored when last element was deleted)
	 */
	element: null,



	/**
	 * Install an auto extending list
	 *
	 * @method	initialize
	 * @param	{Element}	list
	 * @param	{String}	[listItemTag]		Tag name of the list elements (default: li)
	 * @param	{String}	[inputSelector]	Selector for input elements (default: input[type=text])
	 */
	initialize: function(list, listItemTag, inputSelector) {
		this.list			= $(list);
		this.listTag		= this.list.tagName;
		this.listItemTag	= listItemTag || 'li';
		this.inputSelector	= inputSelector || 'input[type=text]';

			// Make a backup copy of a list element
		this.element	= this.list.select(this.inputSelector).last().up(this.listItemTag).clone(true);

		this.list.addClassName('autoextend');

		this.list.up('form').on('keyup', this.listTag + '.autoextend', this.onChanged.bind(this));

		this.installRemove();
	},



	/**
	 * Observe remove buttons
	 *
	 * @method	installRemove
	 */
	installRemove: function() {
		this.list.select('.remove').each(function(removeIcon) {
			this.addRemoveHandler(removeIcon);
		}, this);
	},



	/**
	 * Make all removers visible, hide the last one
	 *
	 * @method	showRemoverExceptLast
	 */
	showRemoverExceptLast: function() {
		this.list.select('.remove').invoke('show');
		this.list.select('.remove').last().hide();
	},



	/**
	 * Add remove handler to remove button
	 *
	 * @method	addRemoveHandler
	 * @param	{Element}	element
	 */
	addRemoveHandler: function(element) {
		element.on('click', this.listItemTag, this.removeHandler.bind(this));
	},



	/**
	 * Remove an item
	 *
	 * @method	removeHandler
	 * @param	{Event}		event
	 * @param	{Element}	listItem
	 */
	removeHandler: function(event, listItem){
		listItem.down('.remove').stopObserving();
		listItem.fade({
			afterFinish: this.onRemoved.bind(this, listItem),
			duration: 0.5
		});
	},



	/**
	 * Handler when item was removed (faded out)
	 *  - Remove item from DOM
	 *  - Call change handler to make sure an empty element exists
	 *
	 * @method	onRemoved
	 * @param	{Element}	listItem
	 */
	onRemoved: function(listItem) {
		listItem.remove();
		this.onChanged();
	},



	/**
	 * Event handler when an input field was changed
	 *
	 * @method	onChanged
	 * @param	{Event}		event
	 * @param	{Element}	list
	 */
	onChanged: function(event, list) {
		if( this.areAllElementsFull() ) {
			this.appendElement();
		}
		this.showRemoverExceptLast();
	},



	/**
	 * Check whether all elements are "full"
	 *
	 * @method	areAllElementsFull
	 * @return	{Boolean}
	 */
	areAllElementsFull: function() {
		return this.getInputs().all(function(input){
			return input.value.strip() !== '';
		});
	},



	/**
	 * Clone last element and append it to the list as last item
	 *
	 * @method	appendElement
	 */
	appendElement: function() {
		var clone	= this.element.clone(true);

		this.addRemoveHandler(clone.down('.remove'));

		this.list.insert({
			bottom: clone
		});
	},



	/**
	 * Get all input elements
	 *
	 * @method	getInputs
	 * @return	{Array}
	 */
	getInputs: function() {
		return this.list.select(this.inputSelector);
	}

});