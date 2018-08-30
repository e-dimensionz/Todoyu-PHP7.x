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
 * QuickInfo
 *
 * @class		QuickInfo
 * @namespace	Todoyu
 */
Todoyu.QuickInfo	= {

	/**
	 * ID of the quickinfo popup
	 *
	 * @property	popupID
	 * @type		String
	 */
	popupID:	'quickinfo',

	/**
	 * Content cache with time info
	 * @property	cache
	 * @type		Object
	 */
	cache:	{},

	/**
	 * Default cache time for an element (seconds)
	 *
	 * @property	defaultCacheTime
	 * @type		Number
	 */
	defaultCacheTime:	60,

	/**
	 * Custom cache time per element
	 *
	 * @see			this.setCacheTime()
	 * @property	customCacheTime
	 * @type		Object
	 */
	customCacheTime:	{},

	/**
	 * Template object to convert JSON into HTML
	 *
	 * @property	template
	 * @type		Template
	 */
	template:	null,

	/**
	 * Flag if loading is in progress (prevents multiple loading requests)
	 *
	 * @property	loading
	 * @type		Boolean
	 */
	loading:	false,

	/**
	 * Flag if quickinfo is currently hidden
	 *
	 * @property	hidden
	 * @type		Boolean
	 */
	hidden:	false,



	/**
	 * Callback for delayed hide
	 *
	 * @property	delayedHide
	 * @type		Function
	 */
	delayedHide:	null,

	/**
	 * Delay time for hiding quickinfo
	 *
	 * @property	delayedHideTime
	 * @type		Number
	 */
	delayedHideTime:	0.4,

	/**
	 * Callback for delayed show
	 *
	 * @property	delayedShow
	 * @type		Function
	 */
	delayedShow:	null,

	/**
	 * Delay time for showing quickinfo
	 *
	 * @property	delayedShowTime
	 * @type		Number
	 */
	delayedShowTime:	0.6,

	/**
	 * Active element (DOM element)
	 *
	 * @property	active
	 * @type		Element
	 */
	active:	null,

	/**
	 * Trigger to deactivate quickInfo, e.g. while dragging elements
	 *
	 * @property	disabled
	 * @type		Boolean
	 */
	disabled:	false,



	/**
	 * Init quickinfo
	 *
	 * @method	init
	 */
	init: function() {
			// Insert HTML element into document
		this.insertQuickInfoElement(this.popupID);

			// Activate showing of quickinfos
		this.enable();

			// Observe document for clicks to close the quickinfo
		document.body.on('click', this.onBodyClick.bind(this));
	},



	/**
	 * Enable showing of quickinfos
	 *
	 * @method	enable
	 */
	enable: function() {
		this.disabled	= false;
	},



	/**
	 * Disable showing of quickinfos
	 *
	 * @method	disable
	 */
	disable: function() {
		this.hide(true);

		this.disabled	= true;
	},



	/**
	 * Install quickinfo on elements matching the selector
	 *
	 * @method	install
	 * @param	{String}	name
	 * @param	{String}	selector
	 * @param	{Function}	idCallback
	 */
	install: function(name, selector, idCallback) {
		this.uninstall(selector);

		$$(selector).each(function(element) {
			element.on('mouseover', this.onMouseOver.bind(this, name, idCallback, element));
			element.on('mouseout', this.onMouseOut.bind(this, name, idCallback, element));
		},this);
	},



	/**
	 * Handler when an observer element is hovered
	 *
	 * @method	onMouseOver
	 * @param	{String}	name
	 * @param	{Function}	idCallback
	 * @param	{Element}	element
	 * @param	{Event}		event
	 */
	onMouseOver: function(name, idCallback, element, event) {
			// Hide active element if another one should be displayed
		if( !this.disabled ) {
			if( this.active !== null && this.active !== element) {
				this.hide(true);
			}

			if( ! this.isVisible() ) {
				this.show(event, name, idCallback, element);
			}

			Todoyu.Hook.exec('core.quickinfo.mouseover', event, name, element);
		}
	},



	/**
	 * Handler when an observed element is left with the mouse
	 *
	 * @method	onMouseOut
	 * @param	{String}	name
	 * @param	{Function}	callback
	 * @param	{Element}	element
	 * @param	{Event}		event
	 */
	onMouseOut: function(name, callback, element, event) {
		this.stopDelayedCallbacks();

		if( this.isVisible() ) {
			this.hide(false);
		}

		Todoyu.Hook.exec('core.quickinfo.mouseout', event, name, element);
	},



	/**
	 * Handler for <body> click
	 *
	 * @method	onBodyClick
	 * @param	{Event}	event
	 */
	onBodyClick: function(event) {
		this.hide(true);
	},



	/**
	 * Uninstall quickinfo from elements which match the selector
	 *
	 * @method	uninstall
	 * @param	{String}	selector
	 */
	uninstall: function(selector) {
		$$(selector).each(function(element) {
			element.stopObserving('mouseover');
			element.stopObserving('mouseout');
		});
	},



	/**
	 * Insert quick info elements container
	 *
	 * @method	insertQuickInfoElement
	 */
	insertQuickInfoElement: function() {
		if( ! Todoyu.exists( this.popupID) ) {
			var quickInfo  = new Element('div', {
				'id':	this.popupID
			}).hide();

			$(document.body).insert(quickInfo);

				// Observe quickinfo for mouse events
			$(this.popupID).on('mouseover', this.onInfoOver.bind(this));
			$(this.popupID).on('mouseout', this.onInfoOut.bind(this));
		}
	},



	/**
	 * Handler when moving the mouse on the quickinfo
	 * Cancel delayed hiding
	 *
	 * @method	onInfoOver
	 * @param	{Event}		event
	 */
	onInfoOver: function(event) {
		this.stopDelayedCallbacks();
	},



	/**
	 * Handler when moving the mouse off the quickinfo
	 * Start delayed hiding
	 *
	 * @method	onInfoOut
	 * @param	{Event}		event
	 */
	onInfoOut: function(event) {
		this.hide();
	},



	/**
	 * Update quick info element style to given position and set it visible
	 *
	 * @method	show
	 * @param	{Event}			event
	 * @param	{String}		name
	 * @param	{Function}		idCallback
	 * @param	{String}		observedElement
	 */
	show: function(event, name, idCallback, observedElement, show) {
		event.stop();
		this.stopDelayedCallbacks();

		if( show !== true ) {
			this.delayedShow = this.show.bind(this, event, name, idCallback, observedElement, true).delay(this.delayedShowTime);
			return;
		}

		var elementKey	= idCallback(observedElement, event);

		var cacheID	= name + elementKey;
		this.hidden	= false;

		if( this.loading === true ) {
			return false;
		}

		this.loading = true;

		if( this.isCached(cacheID) ) {
				// Show cached
			this.display(name, elementKey, event.pointerX(), event.pointerY(), observedElement);
			this.loading = false;
		} else {
				// Have it be loaded and shown after
			this.loadQuickInfo(name, elementKey, idCallback, event, observedElement);
		}
	},



	/**
	 * Display quickinfo which is in cache
	 *
	 * @method	display
	 * @param	{String}	name
	 * @param	{String}	elementKey
	 * @param	{Number}	pointerX
	 * @param	{Number}	pointerY
	 * @param	{Element}	observedElement
	 */
	display: function(name, elementKey, pointerX, pointerY, observedElement) {
		this.active	= observedElement;

		this.updatePopup(this.getFromCache(name + elementKey));

		this.showPopUp(pointerX, pointerY);
	},



	/**
	 * Set cache time for a type
	 *
	 * @method	setCacheTime
	 * @param	{String}		type
	 * @param	{Number}		time		Cache time in seconds
	 */
	setCacheTime: function(type, time) {
		this.customCacheTime[type] = time;
	},



	/**
	 * Get cache time for an element type. Gets time until cache is valid
	 *
	 * @method	getCacheTime
	 * @param	{String}		type
	 */
	getCacheTime: function(type) {
		return (new Date()).getTime() + (this.customCacheTime[type] !== undefined ? parseInt(this.customCacheTime[type], 10) : this.defaultCacheTime)*1000;
	},



	/**
	 * Show quickinfo tooltip
	 *
	 * @method	showPopUp
	 * @param	{Number}		x
	 * @param	{Number}		y
	 */
	showPopUp: function(x, y) {
			// Check hide-flag (prevent lapse due to running request while mouseOut happened)
		if( ! this.hidden ) {
			x += 8;
			y -= 12;

			var popupWidth	= $(this.popupID).getDimensions().width;

			if( x + 20 > window.innerWidth - popupWidth) {
				x = x - popupWidth - 20;
			}

			$(this.popupID).setStyle({
				'top':	y + 'px',
				'left':	x + 'px'
			}).show();
		}
	},



	/**
	 * Hide quick-info tooltip
	 * If hide is not true, the function calls itself delayed with the true hide flag
	 *
	 * @method	hide
	 * @param	{Boolean}	hide		True: hide now, False: hide delayed
	 */
	hide: function(hide) {
		this.stopDelayedCallbacks();

		if( hide === true ) {
				// Hide quick info element
			if( $(this.popupID) ) {
				$(this.popupID).hide();

					// Hide-flag: comprehend overlapping of mouseOut and running show request
				this.hidden	= true;
				this.active	= null;
			}
		} else {
				// Schedule delayed hiding
			this.delayedHide = this.hide.bind(this, true).delay(this.delayedHideTime);
		}
	},



	/**
	 * Stop timeout which call hide for quickinfo
	 *
	 * @method	stopDelayedHide
	 */
	stopDelayedCallbacks: function() {
		clearTimeout(this.delayedHide);
		clearTimeout(this.delayedShow);
	},




	/**
	 * Evoke loading of quickinfo tooltip content
	 *
	 * @method	loadQuickInfo
	 * @param	{String}	name			'event' / 'holiday' / 'person', etc.
	 * @param	{String}	elementKey
	 * @param	{Function}	callback
	 * @param	{Event}		event
	 * @param	{Element}	observedElement
	 */
	loadQuickInfo: function(name, elementKey, callback, event, observedElement) {
		var url		= Todoyu.getUrl('core', 'quickinfo');
		var options	= {
			parameters: {
				action:		'get',
				'quickinfo':	name,
				'element':		elementKey
			},
			onComplete: this.onQuickInfoLoaded.bind(this, name, elementKey, event, observedElement)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Show quickinfo after loaded via AJAX. Add to cache with custom cache time
	 *
	 * @method	onQuickInfoLoaded
	 * @param	{String}			name			Type of element
	 * @param	{String}			elementKey		Key of element (mostly element ID)
	 * @param	{Event}				event			Hover event
	 * @param	{Ajax.Response}		response		Ajax response
	 * @param	{Ajax.Response}	response
	 */
	onQuickInfoLoaded: function(name, elementKey, event, observedElement, response) {
		var cacheKey= name + elementKey;
		var content	= this.buildQuickInfo(response.responseJSON);
		var time	= this.getCacheTime(name);

		this.addToCache(cacheKey, content, time);

		this.loading= false;

		if( ! this.hidden ) {
			this.display(name, elementKey, event.pointerX(), event.pointerY(), observedElement);
		}
	},



	/**
	 * Render quick info tooltip HTML from JSON data
	 *
	 * @method	buildQuickInfo
	 * @param	{Object}		json
	 * @return	{String}
	 */
	buildQuickInfo: function(json) {
		if( this.template === null ) {
			this.template = new Template('<dt class="#{class}Icon #{key}Icon">&nbsp;</dt><dd class="#{class}Label #{key}Label">#{label}&nbsp;</dd>');
		}

		var content	= '';
		json.each(function(item){
				// Ensure maximum word length not to break layout: add wordwrap. But only if string doesn't contain html
//			if( item.label.indexOf('<') === -1 ) {
//				item.label	= Todoyu.String.wordwrapEntities(item.label, 16, ' ', true);
//			}

				// Add template row with item
			content += this.template.evaluate(item);
		}.bind(this));

		return '<dl>' + content.replace("\n", '<br />') + '</dl>';
	},



	/**
	 * Update popUp content
	 *
	 * @method	updatePopup
	 * @param	{String}	content
	 */
	updatePopup: function(content) {
		$(this.popupID).update(content);
	},



	/**
	 * Check whether the quickinfo is currently visible
	 *
	 * @method	isVisible
	 * @return	{Boolean}
	 */
	isVisible: function() {
		return $(this.popupID) && $(this.popupID).visible();
	},



	/**
	 * Add quickInfo content to cache
	 *
	 * @method	addToCache
	 * @param	{String}		cacheID		ID of the cached element
	 * @param	{String}		content		cached content
	 * @param	{Number}		time		cache time
	 */
	addToCache: function(cacheID, content, time) {
		this.cache[cacheID] = {
			time:	time,
			content:content
		};
	},



	/**
	 * Get quickinfo content from cache
	 *
	 * @method	getFromCache
	 * @param	{String}		cacheID
	 * @return	{String}		Or false
	 */
	getFromCache: function(cacheID) {
		return this.isCached(cacheID) ? this.cache[cacheID].content : false;
	},



	/**
	 * Remove item of given ID from cache
	 *
	 * @method	removeFromCache
	 * @param	{String}	cacheID
	 */
	removeFromCache: function(cacheID) {
		if( this.cache[cacheID] ) {
			delete this.cache[cacheID];
		}
	},



	/**
	 * Check whether item with given ID is cached
	 *
	 * @method	isCached
	 * @return	{Boolean}
	 */
	isCached: function(cacheID) {
		return typeof(this.cache[cacheID]) === 'object' && this.cache[cacheID].time > (new Date()).getTime();
	},



	/**
	 * Wrapper for backwards compatibility
	 *
	 * @deprecated
	 */
	activate: function() {
		this.enable();
	},



	/**
	 * Wrapper for backwards compatibility
	 *
	 * @deprecated
	 */
	deactivate: function() {
		this.disable();
	}

};