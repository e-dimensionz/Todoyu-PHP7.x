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
 * Headlet base class
 */
Todoyu.Headlet = Class.create({

	/**
	 * @var	{String}	name		Name/ID of the headlet
	 */
	name: '',



	/**
	 * Constructor: Install basics for all headlet types
	 *
	 * @method	initialize
	 * @param	{String}	name
	 */
	initialize: function(name) {
		this.name = name.toLowerCase();

		this.observeButton();

		if( this.isMenu() ) {
			this.observeMenu();
		}
		if( this.isOverlay() ) {
			this.observeOverlay();
		}
	},



	/**
	 * Observe the button/icon
	 *
	 * @method	observeButton
	 */
	observeButton: function() {
		var button = this.getButton();

		if( button ) {
			button.on('click', 'a', this._handleButtonClick.bind(this));
		}
	},



	/**
	 * Observe the menu list
	 *
	 * @method	observeMenu
	 */
	observeMenu: function() {
		this.getContent().on('click', 'li', this._handleMenuClick.bind(this));
	},



	/**
	 * Observe the overlay box
	 *
	 * @method	observeOverlay
	 */
	observeOverlay: function() {
		this.getContent().on('click', this._handleOverlayClick.bind(this));
	},



	/**
	 * Internal handler when clicked on the button
	 *
	 * @method	_handleButtonClick
	 * @param	{Event}		event
	 * @param	{Element}	buttonElement
	 */
	_handleButtonClick: function(event, buttonElement) {
		event.stop();

		this.hideOthers();

		if( this.isActive() ) {
			this.setAllInactive();
		} else {
			if( this.getType() !== 'button' ) {
				this.setActive();
			}
		}

		this.onButtonClick(event);
	},



	/**
	 * Default button click handler
	 * Toggles the overlay/menu if available
	 *
	 * @method	onButtonClick
	 * @param	{Event}		event
	 */
	onButtonClick: function(event) {
		this.toggle();

		this.saveOpenStatus();
	},



	/**
	 * Internal handler when clicked on a menu entry
	 *
	 * @method	_handleMenuClick
	 * @param	{Event}		event
	 * @param	{Element}	menuItem
	 */
	_handleMenuClick: function(event, menuItem) {
		var idParts	= menuItem.id.split('-');
		var ext		= idParts[1];
		var type	= idParts[2];

		this.onMenuClick(ext, type, event, menuItem);
	},



	/**
	 * Default handler when clicked on a menu item
	 *
	 * @method	onMenuClick
	 * @param	{String}	ext
	 * @param	{String}	type
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onMenuClick: function(ext, type, event, item) {

	},



	/**
	 * Internal handler when clicked on the overlay box
	 * Prevent body click event which closes all overlays
	 *
	 * @method	_handleOverlayClick
	 * @param	{Event}	event
	 */
	_handleOverlayClick: function(event) {
		event.stop();
	},




	/**
	 * Get the label of a menu entry
	 *
	 * @method	getMenuItemLabel
	 * @param	{String}	ext
	 * @param	{String}	type
	 * @return	{String}
	 */
	getMenuItemLabel: function(ext, type) {
		return this.getContent().down('li a.todoyuheadletquickcreate-item-' + ext + '-' + type).innerHTML;
	},




	/**
	 * Get button element
	 *
	 * @method	getButton
	 * @return	{Element}
	 */
	getButton: function() {
		return $(this.name + '-button');
	},




	/**
	 * Get content element
	 *
	 * @method	getContent
	 * @return	{Element}
	 */
	getContent: function() {
		return $(this.name + '-content');
	},



	/**
	 * Check if content is visible
	 *
	 * @method	isVisible
	 * @return	{Boolean}
	 */
	isVisible: function() {
		return this.hasContent() && this.getContent().visible();
	},



	/**
	 * Check whether content overlay is open
	 *
	 */
	isOpen: function() {
		return this.isVisible()
	},



	/**
	 * Show content of headlet
	 *
	 * @method	show
	 */
	show: function() {
		this.hideOthers();
		this.setActive();

		if( this.hasContent() ) {
			this.getContent().show();
			this.onContentShow();
		}
	},



	/**
	 * Handle show content
	 *
	 */
	onContentShow: function() {
		// noop
	},



	/**
	 * Hide content of headlet
	 *
	 * @method	hide
	 */
	hide: function() {
		if( this.hasContent() ) {
			$(this.name + '-content').hide();
		}
		this.setInactive();
	},



	/**
	 * Toggle content of headlet
	 *
	 * @method	toggle
	 */
	toggle: function() {
		this.isVisible() ? this.hide() : this.show();
	},



	/**
	 * Hide all other headlet contents
	 *
	 * @method	hideOthers
	 */
	hideOthers: function() {
		this.hideAll(this.name);
	},



	/**
	 * Hide all headlet contents
	 *
	 * @method	hideAll
	 */
	hideAll: function(except) {
			// Call hide function for all headlets
		$H(Todoyu.Headlets.headlets).each(function(pair){
			if( pair.key !== except ) {
				pair.value.hide();
			}
		});
	},



	/**
	 * Check whether the headlet has a content element
	 *
	 * @method	hasContent
	 * @return	{Boolean}
	 */
	hasContent: function() {
		return Todoyu.exists(this.name + '-content');
	},



	/**
	 * Get headlet type
	 *
	 * @method	getType
	 * @return	{String}
	 */
	getType: function() {
		var button = this.getButton();

		if( !button ) return '';

		var classNames	= $w(button.className);
		var typeClass	= classNames.detect(function(className){
			return className.indexOf('headletType') !== -1;
		});

		return typeClass.replace('headletType', '').toLowerCase();
	},



	/**
	 * Check whether the headlet has type menu
	 *
	 * @method	isMenu
	 * @return	{Boolean}
	 */
	isMenu: function() {
		return this.getType() === 'menu';
	},



	/**
	 * Check whether the headlet has type overlay
	 *
	 * @method	isOverlay
	 * @return	{Boolean}
	 */
	isOverlay: function() {
		return this.getType() === 'overlay';
	},



	/**
	 * Set a headlet active
	 *
	 * @method	setActive
	 */
	setActive: function() {
		this.setAllInactive();

		$(this.name).addClassName('active');
	},



	/**
	 * Check whether a headlet is active
	 *
	 * @method	isActive
	 */
	isActive: function() {
		return $(this.name).hasClassName('active');
	},



	/**
	 * Set headlet inactive
	 *
	 * @method	setInactive
	 */
	setInactive: function() {
		$(this.name).removeClassName('active');
	},



	/**
	 * Set all headlets inactive
	 *
	 * @method	setAllInactive
	 */
	setAllInactive: function() {
		if( $('headlets') ) {
			$('headlets').select('.headlet').invoke('removeClassName', 'active');
		}
	},



	/**
	 * Default handler when clicked on the body (this means an event was not stopped)
	 *
	 * @method	onBodyClick
	 */
	onBodyClick: function() {
		if( this.isOpen() ) {
			this.hide();
		}
	},




	/**
	 * Save open status of all headlets
	 *
	 * @method	saveOpenStatus
	 */
	saveOpenStatus: function() {
		Todoyu.Headlets.saveOpenStatus();
	}


});