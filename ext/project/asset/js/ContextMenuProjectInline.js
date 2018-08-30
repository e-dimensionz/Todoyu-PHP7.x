/****************************************************************************
 * todoyu is published under the BSD License:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * @module	Project
 */

/**
 *
 * @class		ContextMenuProjectInline
 * @namespace	Todoyu.Ext.project
 */
Todoyu.Ext.project.ContextMenuProjectInline = {

	type: 'ProjectInline',



	/**
	 * @method	attach
	 */
	attach: function() {
		var selector	= '.contextmenuProjectInline';
		this.detach(selector);

		$$(selector).each(function(element){
			element.on('click', Todoyu.ContextMenu.load.bind(Todoyu.ContextMenu, this.type, this.getID.bind(this), element));
			element.on('contextmenu', Todoyu.ContextMenu.load.bind(Todoyu.ContextMenu, this.type, this.getID.bind(this), element));
		//	element.on('mouseover', Todoyu.ContextMenu.load.bind(Todoyu.ContextMenu, this.type, this.getID.bind(this), element));
		}, this);
	},



	/**
	 * @method	detach
	 * @param	{String}		selector
	 */
	detach: function(selector) {
		var elements	= $$(selector);

		elements.each(function(element) {
			element.stopObserving('click');
			element.stopObserving('contextmenu');
	//		element.stopObserving('mouseover');
		});
	},



	/**
	 * @method	getID
	 * @param	{Element}	element
	 * @param	{Event}		event
	 * @return {String}
	 */
	getID: function(element, event) {
		return Todoyu.Ext.project.ContextMenuProject.getID(element, event);
	},


	/**
	 * @method	onContextMenu
	 * @param	{String}		type
	 * @param	{String}		elementKey
	 * @param	{Number}		left
	 * @param	{Number}		top
	 */
	onContextMenu: function(type, elementKey, left, top) {
		if( type == this.type) {
			var selector = $('menu-' + elementKey);

			var dimension	= selector.viewportOffset();
			var scrollOffset = Element.cumulativeScrollOffset(selector);

			$('contextmenu').style.top = (dimension.top + scrollOffset.top + (selector.getHeight() / 2) ) + 'px';
			$('contextmenu').style.left = (dimension.left + scrollOffset.left) + 'px';
		}
	}
};