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
 * Todoyu main container. All other JS containers are (sub-)nodes of this container
 *
 * @class	Todoyu
 * @static
 */
var Todoyu = {

	/**
	 * System name
	 * @property	name
	 * @type		String
	 */
	name: 'Todoyu',

	/**
	 * Copyright owner
	 * @property	copyright
	 * @type		String
	 */
	copyright: 'snowflake productions GmbH, Zurich/Switzerland',

	/**
	 * Log level
	 * @property	logLevel
	 * @type		Number
	 */
	logLevel:	0,

	/**
	 * Store the format of all available jsCalendars
	 *
	 * @property	JsCalFormat
	 * @type		Object
	 */
	JsCalFormat: {},

	/**
	 * Extension container
	 *
	 * @property	Ext
	 * @type		Object
	 */
	Ext: {},

	/**
	 * Core headlets container
	 *
	 * @property	CoreHeadlets
	 * @type		Object
	 */
	CoreHeadlets: {},

	/**
	 * Registry
	 *
	 * @property	R
	 * @type		Object
	 */
	R: {},

	/**
	 * System config. Injected on page load
	 *
	 * @type	Object
	 */
	Config: {},



	/**
	 * Initialize todoyu object
	 *
	 * @method	init
	 */
	init: function() {
		this.loadConfig();
		this.Ajax.Responders.init();
		this.Headlets.init();
		this.Navi.init();
		this.QuickInfo.init();
		this.Ui.fixAnchorPosition();
		this.Ui.observeBody();
		this.Ui.observeScroll();
		this.Ui.observeResize();
		this.initExtensions();
		this.initConsole();
	},



	/**
	 * Import todoyuTempConfig from window namespace into todoyu
	 *
	 */
	loadConfig: function() {
		if( window.todoyuTempConfig ) {
			this.Config = window.todoyuTempConfig;
			window.todoyuTempConfig = null;
		}
	},



	/**
	 * Init fake console if it doesn't exist
	 */
	initConsole: function() {
		if( !window.console ) {
			window.console = {
				log:	function(msg){},
				info:	function(msg){},
				warn:	function(msg){}
			};
		}
	},



	/**
	 * Initialize all extensions
	 * Call the init() function of all extensions in their main container if it exists
	 *
	 * @method	initExtensions
	 */
	initExtensions: function() {
		$H(this.Ext).each(function(pair){
			if( typeof(pair.value.init) === 'function' ) {
				try {
					pair.value.init();
				} catch(exception) {
					this.log(exception);
				}
			}
		}, this);
	},



	/**
	 * Build request url with extension and controller
	 *
	 * @method	getUrl
	 * @param	{String}		extKey
	 * @param	{String}		[controller]
	 * @param	{Object}		[params]
	 * @param	{String}		[hash]
	 * @return	{String}
	 */
	getUrl: function(extKey, controller, params, hash) {
		var url	= 'index.php?ext=' + extKey;

		if( controller ) {
			url = url + '&controller=' + controller;
		}

		if( typeof params === 'object' ) {
			if( Object.keys(params).size() > 0 ) {
				url += '&' + Object.toQueryString(params);
			}
		}

		if( hash ) {
			url += '#' + hash;
		}

		return url;
	},



	/**
	 * Check whether the current browser URL is the same as the redirection URL of the given todoyu parameters
	 *
	 * @method	isCurrentLocationHref
	 * @param	{String}	extKey
	 * @param	{String}	[controller]
	 * @param	{Object}	[params]
	 * @param	{String}	[hash]
	 * @return	{Boolean}
	 */
	isCurrentLocationHref: function(extKey, controller, params, hash) {
		var url	= this.getUrl(extKey, controller, params);

			// Check for each query part separately as the alphabetical order does not matter
		var queryParts		= url.split('&');
		var anyPartMissing	= queryParts.any(function(part) {
			return document.location.href.indexOf(part) == -1;
		});

		if( anyPartMissing ) {
			return false;
		}

		if( Object.isString(hash) ) {
			return document.location.href.endsWith('#' + hash);
		}

		return true;
	},



	/**
	 * Redirect to another page
	 *
	 * @method	goTo
	 * @param	{String}	extKey
	 * @param	{String}	[controller]
	 * @param	{Object}	[params]
	 * @param	{String}	[hash]
	 * @param	{Boolean}	[newWindow]
	 * @param	{String}	[windowName]
	 */
	goTo: function(extKey, controller, params, hash, newWindow, windowName) {
		newWindow	= newWindow ? newWindow : false;
		var url		=  this.getUrl(extKey, controller, params);

		if( hash ) {
			this.goToHashURL(url, hash, newWindow, windowName);
		} else {
			if( newWindow === false ) {
				location.href = url;
			} else {
				window.open(url, windowName);
			}
		}
	},



	/**
	 * Go to an URL with a hash
	 * If the URL itself is identical, just scroll to the element
	 *
	 * @method	goToHashURL
	 * @param	{String}	url
	 * @param	{String}	hash
	 * @param	{Boolean}	newWindow
	 * @param	{String}	windowName
	 */
	goToHashURL: function(url, hash, newWindow, windowName) {
		newWindow		= newWindow ? newWindow : false;
		var searchPart	= url.substr(url.indexOf('?'));

		if( location.search === searchPart && Todoyu.exists(hash) ) {
			if( $(hash).getHeight() > 0 ) {
				$(hash).scrollToElement();
				return;
			}
		}

			// Fallback
		if( newWindow === false ) {
			location.href =  url + '#' + hash;
		} else {
			windowName	= windowName ? windowName : '';

			window.open(url + '#' + hash, windowName);
		}
	},



	/**
	 * Send AJAX request
	 *
	 * @method	send
	 * @param	{String}		url
	 * @param	{Object}			options
	 */
	send: function(url, options) {
		options = Todoyu.Ajax.getDefaultOptions(options);

		return new Ajax.Request(url, options);
	},



	/**
	 * Check whether an element exists
	 *
	 * @method	exists
	 * @param	{Element|String}		element		Element or its ID
	 */
	exists: function(element) {
		if( Object.isElement(element) ) {
			return true;
		}

		return document.getElementById(element) !== null;
	},



	/**
	 * Get current area
	 *
	 * @method	getArea
	 * @return	{String|Boolean}
	 */
	getArea: function() {
		var area = false;

		if( document.body ) {
			area	= document.body.id.split('-').last();
		} else {
			area	= document.location.href.toQueryParams().ext;
		}

		return area;
	},


	/**
	 * Check whether given area is currently open
	 *
	 * @method	isInArea
	 * @param	{String}	areaKey
	 * @return  {Boolean}
	 */
	isInArea: function(areaKey) {
		return areaKey === this.getArea();
	},



	/**
	 * Show error notification
	 *
	 * @method	notifyError
	 * @param	{String}		message
	 * @param	{String}		[identifier]
	 */
	notifyError: function(message, identifier) {
		Todoyu.Notification.notifyError(message, identifier);
	},



	/**
	 * Show success notification
	 *
	 * @method	notifySuccess
	 * @param	{String}		message
	 * @param	{String}		[identifier]
	 * @param	{Number}		[delay]
	 */
	notifySuccess: function(message, identifier, delay) {
		Todoyu.Notification.notifySuccess(message, false, delay, identifier);
	},



	/**
	 * Show info notification
	 *
	 * @method	notifyInfo
	 * @param	{String}		message
	 * @param	{Number}		[delay]
	 * @param	{String}		[identifier]
	 */
	notifyInfo: function(message, delay, identifier) {
		Todoyu.Notification.notifyInfo(message, false, delay, identifier);
	},



	/**
	 * Check whether the current user is logged in- guess for logged-in if headlets are shown
	 *
	 * @method	isLoggedIn
	 * @return	{Boolean}
	 */
	isLoggedIn: function() {
		return this.exists('headlets');
	},



	/**
	 * Call a user function in string format with given arguments
	 * @example	Todoyu.calluserFunction('Todoyu.notifySuccess', 'This is a message', 5);
	 *
	 * @method	callUserFunction
	 * @param	{String}		functionName
	 * @return	{String|Number|Array|Object}
	 */
	callUserFunction: function(functionName /*, args */) {
		var args	= $A(arguments).slice(1);
		var func	= this.getFunction(functionName);

		return func.apply(window, args);
	},



	/**
	 * Call a function reference, if it's one. Otherwise just ignore the call
	 * The first argument is the function, all other arguments will be handed down to this function
	 * The debug output is just for development
	 *
	 * @method	callIfExists
	 * @param	{Function}	functionReference	Function
	 * @param	{Object}	context				Context which is this in function
	 */
	callIfExists: function(functionReference, context /*, args */) {
		var args = $A(arguments).slice(2);

		if( typeof functionReference === 'function' ) {
			functionReference.apply(context, args);
		} else {
			//Todoyu.log('Todoyu.callIfExists() was executed with a non-function. This can be an error (not sure). Params: ' + Object.inspect(args), 1);
		}
	},



	/**
	 * Get a function reference from a function string
	 * Ex: 'Todoyu.Ext.project.edit'
	 *
	 * @method	getFunctionFromString
	 * @param	{String}		functionName
	 * @param	{Boolean}		bind
	 * @return	{Function}
	 */
	getFunctionFromString: function(functionName, bind) {
		var context	= this.getContext(functionName);
		var func	= functionName.split(".").last();
		var funcRef	= context[func];

		if( bind ) {
			funcRef = funcRef.bind(context);
		}

		return funcRef;
	},



	/**
	 * Get context of a function string
	 *
	 * @method	getContext
	 * @param	{String}		functionName
	 * @return	{Object}
	 */
	getContext: function(functionName) {
		var namespaces	= functionName.split("."),
			context		= window,
			i			= 0;
		namespaces.pop();

		for(i=0; i < namespaces.length; i++) {
			context = context[namespaces[i]];

			if( context === undefined ) {
				alert("Function: " + functionName + " not found!");
				return false;
			}
		}

		return context;
	},



	/**
	 * Get function from string or just return the function
	 *
	 * @method	getFunction
	 * @param	{String|Function}	func
	 */
	getFunction: function(func) {
		switch( typeof func ) {
			case 'string':
				return this.getFunctionFromString(func, true);

			case 'function':
				return func;
		}

		return Prototype.emptyFunction;
	},



	/**
	 * Todoyu log. Check level and if console exists
	 *
	 * @method	log
	 * @param	{Object|String|Array|Number|Element}		element
	 * @param	{Number}									[level]
	 * @param	{String}									[title]
	 */
	log: function(element, level, title) {
		if( level === undefined || (Object.isNumber(level) && level >= this.logLevel) ) {
			if( window.console !== undefined ) {
				if( title !== undefined ) {
					window.console.log('Log: ' + title);
				}
				window.console.log(element);
//				console.error('error');
			}
		}
	}

};