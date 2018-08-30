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
 * List scroll loader
 * Load more list elements when scrolled near the bottom of the list
 *
 * @type	{Object}
 */
Todoyu.ListScrollLoader = Class.create({

	/**
	 * @var	{Element}		Outer scroll box
	 */
	box: null,

	/**
	 * @var	{Element}		Table
	 */
	table: null,

	/**
	 * @var	{Element}		Loading info box
	 */
	loader: null,

	/**
	 * Options
	 */
	options: {
		preload: 	100,
		pageSize: 	20,
		checkDelay:	0.5
	},

	/**
	 * Cache for static values
	 */
	cache: {},

	/**
	 * Update timeout
	 */
	timeout: null,

	/**
	 * Message displayed while loading more records
	 */
	loadingMessage: '[LLL:core.global.loadingMore]',



	/**
	 * Initialize
	 *
	 * @param	{Element|String}	box
	 * @param	{Element|String}	table
	 * @param options
	 */
	initialize: function(box, table, options) {
		this.box	= $(box);
		this.table	= $(table);
		this.options= $H(this.options).merge(options).toObject();

		this.init();
	},



	/**
	 * Init
	 *
	 */
	init: function() {
			// Observe box for scrolling
		this.box.on('scroll', this.box.tagName, this.onScroll.bind(this));
			// Setup cache
		this.initCache();
	},



	/**
	 * Initialize cache
	 *
	 */
	initCache: function() {
		this.cache = {
			tableHeight:	this.table.getHeight(),	// Height of the table => grows
			boxHeight:		this.box.getHeight(),	// Height of the box => static
			lastLoadScroll:	0,						// Last scroll position. Prevent check when all loaded or scrolling up
			offset:			0						// List offset => grows per loading
		};
	},



	/**
	 * Handle list scroll
	 * Start timeout for handling
	 *
	 * @param	{Event}		event
	 * @param	{Element}	box
	 */
	onScroll: function(event, box) {
		clearTimeout(this.timeout);

		this.timeout = this.checkExtending.bind(this).delay(this.options.checkDelay);
	},


	
	/**
	 * Check whether more list items should be loaded
	 *
	 */
	checkExtending: function() {

		if( this.box.scrollTop + this.cache.boxHeight + this.options.preload >= this.cache.tableHeight ) {
			if( this.box.scrollTop > this.cache.lastLoadScroll ) {
				this.cache.lastLoadScroll = this.box.scrollTop;
				this.loadMore();
			}
		}
	},



	/**
	 * Load more list items
	 *
	 */
	loadMore: function() {
		var url		= this.getRequestUrl();
		var options	= this.getRequestOptions();

		this.cache.offset += this.options.pageSize;

		options.parameters.offset	= this.cache.offset;
		options.onComplete			= (options.onComplete || Prototype.emptyFunction).wrap(this.onMoreLoaded.bind(this));

		this.showLoader();

		Todoyu.send(url, options);
	},



	/**
	 * Insert the loaded list items
	 *
	 * @param	{Function}			proceed		Proceed function
	 * @param	{Ajax.Response}		response
	 */
	onMoreLoaded: function(proceed, response) {
		var updated = false;

		this.hideLoader();

		if( response.responseText ) {
			updated = true;

			this.table.insert(response.responseText);
		}

			// Call original onComplete handler
		proceed(response, updated, this.cache.offset);
	},



	/**
	 * Show the loader
	 */
	showLoader: function() {
		this.getLoader().appear();
	},



	/**
	 * Hide the loader
	 *
	 */
	hideLoader: function() {
		this.getLoader().fade();
	},



	/**
	 * Get loader element
	 *
	 * @return	{Element}
	 */
	getLoader: function() {
		if( !this.loader ) {
			this.loader = new Element('div', {
				id:			this.box.id + '-loader',
				className: 'scrollLoaderInfo'
			});
			this.loader.update(this.loadingMessage);
			this.box.insert(this.loader);
		}

		return this.loader;
	},



	/**
	 * Get request url
	 *
	 * @abstract
	 * @return	{String}
	 */
	getRequestUrl: function() {
		alert('OVERRIDE: getRequestUrl');
		return '';
	},



	/**
	 * Get request options
	 *
	 * @abstract
	 * @return	{Object}
	 */
	getRequestOptions: function() {
		alert('OVERRIDE: getRequestOptions');
		return {};
	}

});