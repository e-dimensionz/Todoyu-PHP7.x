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
 * Item list
 * UL with LI items which have an ID and a label
 */
Todoyu.ItemList = Class.create({

	/**
	 * List
	 */
	list: null,

	/**
	 * Options
	 */
	options: {},



	/**
	 * Initialize
	 *
	 * @method	initialize
	 * @param	{Element|String}	list
	 * @param	{Object}			[options]
	 */
	initialize: function(list, options) {
		this.list 		= $(list);
		this.options	= options || {};

			// Set emptyFunction if no callback defined
		this.options.onRemove	= this.options.onRemove || Prototype.emptyFunction;
		this.options.onAdd		= this.options.onAdd || Prototype.emptyFunction;

			// Observe list for remove clicks
		this.list.on('click', 'span.remove', this.remove.bind(this));
	},



	/**
	 * Handler to remove an item
	 *
	 * @method	remove
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	remove: function(event, item) {
		item	= item.up('li');

		Effect.SlideUp(item, {
			afterFinish:	item.remove.bind(item),
			duration:		0.3
		});

		this.options.onRemove.call(this, this.list, item.id);
	},



	/**
	 * Add a new item
	 *
	 * @method	add
	 * @param	{Number}	idItem
	 * @param	{String}	label
	 */
	add: function(idItem, label) {
		if( ! this.include(idItem) ) {
			var li	= new Element('li', {
				id: idItem
			});
			li.update(label);

			li.insert(new Element('span', {
				className: 'remove'
			}));

			this.list.insert(li);

			this.options.onAdd.call(this, this.list, idItem, label);
		}
	},



	/**
	 * Get item IDs as array
	 *
	 * @method	getItems
	 * @return	{Array}
	 */
	getItems: function() {
		return this.list.select('li').collect(function(li){
			return li.id;
		});
	},



	/**
	 * Check whether the item list already contains the item (with same id)
	 *
	 * @method	include
	 * @param	{String|Number}		idItem
	 */
	include: function(idItem) {
		return this.getItems().include(idItem);
	}

});