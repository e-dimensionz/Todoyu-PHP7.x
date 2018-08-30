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
 * Context menu
 *
 * @class		ContextMenu
 * @namespace	Todoyu
 */
Todoyu.ContextMenu = {

	/**
	 * Callback for body click events
	 */
	bodyClickObserver: null,

	/**
	 * Currently visible sub menu (for the delay workaround)
	 */
	visibleSubmenu: null,

	/**
	 * Timeout callback for sub menu (for the delay workaround)
	 */
	hideSubmenuDelay: null,



	/**
	 * Get context menu element
	 *
	 * @returns	{Element}
	 */
	getMenu: function() {
		return $('contextmenu');
	},



	/**
	 * Attach contextmenu to a group of elements
	 * Automatically prevents double context menus by removing registered ones before adding the new one
	 *
	 * @method	attach
	 * @param	{String}		name		Name of the context menu type (php callbacks are registered for this type)
	 * @param	{String}		selector	CSS selector expression
	 * @param	{Function}		callback	Callback function to find element if on the observed DomElement
	 */
	attach: function(name, selector, callback) {
		this.detach(selector);

		$$(selector).each(function(element){
			element.on('contextmenu', this.load.bind(this, name, callback, element));
		}, this);
	},



	/**
	 * Detach context menu from all elements which match to the selector
	 *
	 * @method	detach
	 * @param	{String}	selector		CSS Selector
	 */
	detach: function(selector) {
		var elements	= $$(selector);

		elements.each(function(element) {
			element.stopObserving('contextmenu');
		});
	},



	/**
	 * Load context menu items (JSON) over AJAX
	 *
	 * @private
	 * @method	load
	 * @param	{String}		type				Name of the context menu type
	 * @param	{Function}		callback			Callback function to parse ID from element
	 * @param	{Element}		observedElement		Observed element
	 * @param	{Event}			event				Click event object
	 * @return	{Boolean}
	 */
	load: function(type, callback, observedElement, event) {
			// Stop click event to prevent browsers context menu
		event.stop();

		var elementKey	= callback(observedElement, event);

		var url		= Todoyu.getUrl('core', 'contextmenu');
		var options	= {
			parameters: {
				action:			'get',
				contextmenu:	type,
				element:		elementKey
			}
		};

		this.showMenu(url, options, event, type, elementKey);

		return false;
	},



	/**
	 * Request, render and display context menu
	 *
	 * @private
	 * @method	showMenu
	 * @param	{String}	url
	 * @param	{Array}		options
	 * @param	{Event}		event
	 * @param	{String}	type
	 * @param	{String}	elementKey
	 */
	showMenu: function(url, options, event, type, elementKey) {
			// Wrap to onComplete function to call renderMenu right before the defined onComplete function
		options.onComplete = (options.onComplete || Prototype.emptyFunction).wrap(function(proceed, transport, json) {
				// Build menu HTML from json
			this.buildMenuFromJSON(transport.responseJSON);
				// Set menu dimensions based on the event location and the items
			this.setMenuDimensions(event, type, elementKey);
				// Call defined onComplete function
			proceed(transport, json, type, elementKey);
		 }.bind(this));

		Todoyu.send(url, options);
	},



	/**
	 * Render item context menu from given JSON (using JS template)
	 *
	 * @private
	 * @method	buildMenuFromJSON
	 * @param	{Object}		menuJSON
	 */
	buildMenuFromJSON: function(menuJSON) {
		var menu = this.Template.render(menuJSON);

		this.updateMenuContainer(menu);

		Todoyu.QuickInfo.disable();
	},



	/**
	 * Set menu dimensions (display position) and show the menu
	 *
	 * @private
	 * @method	setMenuDimensions
	 * @param	{Event}		event			Event object
	 * @param	{String}	type			Menu type
	 * @param	{String}	elementKey		Key of the element in context (record id or combined string)
	 */
	setMenuDimensions: function(event, type, elementKey) {
			// Fetch menu dimension data
		var menuElement	= this.getMenu(),
			menuHeight	= menuElement.getHeight(),
			menuWidth	= menuElement.getWidth(),
			scrollOffset= document.viewport.getScrollOffsets(),
			mouseLeft	= event.pointerX(),
			mouseTop	= event.pointerY(),
			menuLeft	= mouseLeft - scrollOffset.left + menuWidth > document.viewport.getWidth() ? mouseLeft - menuWidth : mouseLeft,
			menuTop		= mouseTop - scrollOffset.top + menuHeight > document.viewport.getHeight() ? mouseTop - menuHeight : mouseTop;

			// Set position of the menu
		menuElement.setStyle({
			position:	'absolute',
			display:	'block',
			left:		menuLeft + 'px',
			top:		menuTop + 'px'
		});

			// Observe outside clicks
		this.bodyClickObserver = document.body.on('click', this.hide.bind(this));
			// Observe context-menu-clicks on context menu
		menuElement.on('contextmenu', this.preventContextMenu);

		Todoyu.Hook.exec('core.contextmenu', type, elementKey, menuLeft, menuTop);
	},



	/**
	 * Update context menu container with given HTML
	 *
	 * @method	updateMenuContainer
	 * @param	{String}		menuHTML
	 */
	updateMenuContainer: function(menuHTML) {
		this.getMenu().update(menuHTML);
	},



	/**
	 * Prevent showing of context menu: stop event.
	 *
	 * @method	preventContextMenu
	 * @param	{Event}			event
	 * @return	{Boolean}
	 */
	preventContextMenu: function(event) {
		event.stop();
		return false;
	},



	/**
	 * Hide context menu
	 *
	 * @method	hide
	 */
	hide: function() {
		var menu = this.getMenu();

		if( menu && this.bodyClickObserver ) {
				// Stop body click observer
			this.bodyClickObserver.stop();

			menu.hide();

			Todoyu.QuickInfo.enable();
		}
	},



	/**
	 * Show or hide given item's sub menu (at calculated position)
	 * Note: The whole hide callback stuff is only necessary for firefox (5) on linux
	 *
	 * @method	submenu
	 * @param	{String}		key			Key of the item, a submenu should be displayed
	 * @param	{Boolean}		show		Show or hide?
	 * @param	{Boolean}		noDelay		Don't delay hide (was delayed on the first execution)
	 * @return	{Boolean}
	 */
	submenu: function(key, show, noDelay) {
		show	= show !== false;
		noDelay	= noDelay === true;

		var menuElement	= this.getMenu(),
			menuItem	= $(menuElement.id + '-' + key),
			subMenuItem	= $(menuElement.id + '-' + key + '-submenu');

		if( !menuElement || !menuItem || !subMenuItem ) {
			return false;
		}

			// Already show another submenu, hide last visible
		if( show && this.visibleSubmenu && this.visibleSubmenu !== key ) {
			this.submenu(this.visibleSubmenu, false, true); // hide
		}

			// Hide request for current submenu. Prevent closing while navigating in the submenu, so start delayed call
		if( !show && !noDelay && key === this.visibleSubmenu ) {
			clearTimeout(this.hideSubmenuDelay);
			this.hideSubmenuDelay = this.submenu.bind(this, key, false, true).delay(0.1);
			return true;
		}

			// Show submenu, cancel delayed hide callback
		if( show && key === this.visibleSubmenu ) {
			clearTimeout(this.hideSubmenuDelay);
		}


		if( show ) {
			var menuOffset		= menuElement.viewportOffset(),
				itemOffset		= menuItem.viewportOffset(),
				subMenuHeight	= subMenuItem.getHeight(),
				subMenuWidth	= subMenuItem.getWidth(),
				subMenuLeft		= menuItem.getWidth() - 5,
				subMenuTop		= itemOffset.top - menuOffset.top + 5;

				// Fix top position
			if( subMenuHeight + itemOffset.top > document.viewport.getHeight() ) {
				subMenuTop	= subMenuTop - subMenuHeight + 20;
			} else {
				subMenuTop	+= 5;
			}

				// Fix left position
			if( subMenuWidth + itemOffset.left + menuItem.getWidth() > document.viewport.getWidth() ) {
				subMenuLeft	= subMenuLeft - subMenuWidth - menuItem.getWidth()  + 10;
			} else {
				subMenuLeft	-= 5;
			}

			subMenuItem.setStyle({
				display:'block',
				left:	subMenuLeft + 'px',
				top:	subMenuTop + 'px'
			});

				// Set current visible context menu
			this.visibleSubmenu = key;
		} else {
				// Cancel hide callback
			if( this.visibleSubmenu === key ) {
				clearTimeout(this.hideSubmenuDelay);
			}
			this.visibleSubmenu = null;
			subMenuItem.hide();
		}

		return true;
	}

};