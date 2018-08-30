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
 * Template class to render the context menu JSON object into HTML with prototype template
 */
Todoyu.ContextMenu.Template = {

	/**
	 * Template objects
	 *
	 * @property	template
	 * @type		Object
	 */
	template: {
		item:		null,
		submenu:	null
	},



	/**
	 * HTML patterns for the templates
	 *
	 * @property	html
	 * @type		Object
	 */
	html: {
		item:		'<li class="#{key}" id="contextmenu-#{key}" onmouseover="Todoyu.ContextMenu.submenu(\'#{key}\', true)" onmouseout="Todoyu.ContextMenu.submenu(\'#{key}\', false)"><a onclick="#{jsAction}" href="javascript:void(0)" class="#{class}">#{label}</a>#{submenu}</li>',
		submenu:	'<ul class="context-submenu" id="contextmenu-#{parentKey}-submenu">#{submenu}</ul>'
	},



	/**
	 * The render functions appends each item to this variable to build the menu
	 *
	 * @property	code
	 * @type		String
	 * @private
 	 */

	code: '',



	/**
	 * Render a json object into the context menu HTML code
	 *
	 * @method	render
	 * @param	{Object}	json
	 * @return	{String}
	 */
	render: function(json) {
		this.init();

			// Render each menu item
		json.each(function(item){
				// If the item has a submenu, replace the key with the rendered code
				// Prevent with negative array check that all the functions of an array are iterated
			if( typeof item.submenu === 'object' && ! Object.isArray(item.submenu) ) {
				//Todoyu.log(item.submenu.length);
				//Todoyu.log(Object.isArray(item.submenu));
				item.submenu = this.renderSubmenu(item);
			}
				// Append rendered item
			this.code += this.template.item.evaluate(item);
		}.bind(this));

		return this.code;
	},



	/**
	 * Render sub menu of an item
	 *
	 * @method	renderSubmenu
	 * @param	{Object}		parentItem
	 * @return	{String}
	 */
	renderSubmenu: function(parentItem) {
		var items = '';

			// Transform the submenu object into a hash for iterating over it
		$H(parentItem.submenu).each(function(pair){
			items += this.template.item.evaluate(pair.value);
		}.bind(this));

		return this.template.submenu.evaluate({
			'parentKey':	parentItem.key,
			'submenu':		items
		});
	},



	/**
	 * Initialize template objects and clean code variable
	 *
	 * @method	init
	 */
	init: function() {
		this.code = '';

		if( this.template.item === null ) {
			this.template.item 		= new Template(this.html.item);
			this.template.submenu 	= new Template(this.html.submenu);
		}
	}

};