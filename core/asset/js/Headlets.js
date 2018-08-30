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
 * Headlet
 *
 * @namespace	Todoyu
 */
Todoyu.Headlets = {

	/**
	 * List of headlet JS objects (to call the handlers)
	 * @property	headlets
	 * @type		Object
	 */
	headlets: {},

	/**
	 *
	 * @property	openStatusTimeout
	 * @type		Function
	 */
	openStatusTimeout: null,

	/**
	 * Currently opened headlet
	 * @property	openHeadlet
	 * @type		Todoyu.Headlet
	 */
	lastOpenHeadlet: null,



	/**
	 * Initialize headlet management (observation)
	 *
	 * @method	init
	 */
	init: function() {
		if( this.areHeadletsVisible() ) {
				// Close headlets when clicked outside of the headlets (on body)
			Todoyu.Ui.addBodyClickObserver(this.onBodyClick.bind(this));
			this.detectOpenHeadlet.bind(this).delay(0.3);
		}
	},



	/**
	 * Detect the open headlet object
	 */
	detectOpenHeadlet: function() {
		this.lastOpenHeadlet = this.getOpenHeadlet();
	},



	/**
	 * Check whether headlets are present on the page
	 */
	areHeadletsVisible: function() {
		return Todoyu.exists('headlets');
	},



	/**
	 * Add a headlet object
	 *
	 * @method	add
	 * @param	{String}	name
	 * @param	{Class}		headletClass
	 */
	add: function(name, headletClass) {
		Todoyu.R[name] = this.headlets[name] = new headletClass(name);
	},



	/**
	 * Get headlet
	 *
	 * @method	getHeadlet
	 * @param	{String}	name
	 * @return	{Todoyu.Headlet}
	 */
	getHeadlet: function(name) {
		return this.headlets[name.toLowerCase()];
	},



	/**
	 * Check whether a headlet with this name exists
	 *
	 * @method	isHeadlet
	 * @param	{String}	name
	 * @return	{Boolean}
	 */
	isHeadlet: function(name) {
		return this.getHeadlet(name) !== undefined;
	},



	/**
	 * Handler when clicked on body, fired by Todoyu.Ui.onBodyClick()
	 * If clicked outside the headlets, hide all content boxes
	 *
	 * @method	onBodyClick
	 * @param	{Event}		event
	 */
	onBodyClick: function(event) {
		if( this.areHeadletsOpen() ) {
			$H(this.headlets).each(function(pair) {
				pair.value.onBodyClick();
			}, this);

			this.saveOpenStatus();
		}
	},



	/**
	 * Save open status of a headlet
	 * Setup a timeout for the save function
	 *
	 * @method	saveOpenStatus
	 */
	saveOpenStatus: function() {
			// Get open headlet
		var openHeadlet	= this.getOpenHeadlet();

			// Is a different one open?
		if( this.lastOpenHeadlet !== openHeadlet ) {
				// Clear current timeout
			window.clearTimeout(this.openStatusTimeout);

				// Last or current headlet have to be an overlay type. All others are ignored anyway
			if( this.lastOpenHeadlet && this.lastOpenHeadlet.isOverlay() || openHeadlet && openHeadlet.isOverlay() ) {
					// Start new timeout
				this.openStatusTimeout	= this.submitOpenStatus.bind(this).delay(0.5);
				this.lastOpenHeadlet	= openHeadlet;
			}
		}
	},



	/**
	 * Get currently open headlet
	 *
	 * @method	getOpenHeadlet
	 * @return	{Element|Boolean}
	 */
	getOpenHeadletElement: function() {
		var visibleOverlayContent = $('headlets').select('li > ul.content').detect(function(overlay){
			return overlay.visible();
		});

		return visibleOverlayContent ? visibleOverlayContent.up('li.headlet') : false;
	},



	/**
	 * Get open headlet object
	 *
	 * @return	{Todoyu.Headlet|Boolean}
	 */
	getOpenHeadlet: function() {
		var headletElement	= this.getOpenHeadletElement();

		if( headletElement ) {
			return this.getHeadlet(headletElement.id);
		} else {
			return false;
		}
	},



	/**
	 * Check whether any headlet is currently opened
	 *
	 * @method	areHeadletsOpen
	 * @return	{Boolean}
	 */
	areHeadletsOpen: function() {
		return !!this.getOpenHeadletElement();
	},



	/**
	 * Submit the currently open headlet
	 * False means, no headlet is open at the moment
	 *
	 * @method	submitOpenStatus
	 * @param	{Function}			onComplete		Function reference
	 */
	submitOpenStatus: function(onComplete) {
		var headlet		= this.getOpenHeadlet();
		var headletKey	= headlet && headlet.isOverlay() ? headlet.name : '';

		var url		= Todoyu.getUrl('core', 'headlet');
		options	= {
			parameters: {
				action: 'open',
				headlet:headletKey
			}
		};

		if( onComplete !== null ) {
			options.onComplete	= onComplete;
		}

		Todoyu.send(url, options);
	}

};