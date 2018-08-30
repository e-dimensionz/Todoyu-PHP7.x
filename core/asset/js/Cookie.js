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
 * Cookie
 *
 * @namespace	Todoyu
 * @class		Cookie
 */
Todoyu.Cookie = {

	data: {},

	options: {
		expires:	1,
		domain:		"",
		path:		"",
		secure:		false
	},



	/**
	 * Initialize
	 *
	 * @method	init
	 * @param	{Object}	options
	 * @param	{Object}	[data]
	 */
	init: function(options, data) {
		data	= data || false;

		Todoyu.Cookie.options = Object.extend(Todoyu.Cookie.options, options || {});

		var payload = Todoyu.Cookie.retrieve();

		if( ! data && payload ) {
			Todoyu.Cookie.data = payload.evalJSON();
		} else {
			Todoyu.Cookie.data = data || {};
		}
		Todoyu.Cookie.store();
	},



	/**
	 * Get attribute with given key from cookie data
	 *
	 * @method	getData
	 * @param	{String}	key
	 * @return	{String}
	 */
	getData: function(key) {
		return Todoyu.Cookie.data[key];
	},



	/**
	 * Store given attribute in cookie data
	 *
	 * @method	setData
	 * @param	{String}	key
	 */
	setData: function(key, value) {
		Todoyu.Cookie.data[key] = value;
		Todoyu.Cookie.store();
	},



	/**
	 * Remove given attribute from cookie data
	 *
	 * @method	removeData
	 * @param	{String}	key
	 */
	removeData: function(key) {
		delete Todoyu.Cookie.data[key];
		Todoyu.Cookie.store();
	},



	/**
	 * Retrieve cookie
	 *
	 * @method	retrieve
	 * @return	{String}
	 */
	retrieve: function() {
		var start = document.cookie.indexOf(Todoyu.Cookie.options.name + "=");

		if( start == -1 ) {
			return null;
		}

		if( Todoyu.Cookie.options.name != document.cookie.substr(start, Todoyu.Cookie.options.name.length) ) {
			return null;
		}

		var len = start + Todoyu.Cookie.options.name.length + 1;
		var end = document.cookie.indexOf(';', len);

		if( end == -1 ) {
			end = document.cookie.length;
		}

		return unescape(document.cookie.substring(len, end));
	},



	/**
	 * @method	store
	 */
	store: function() {
		var expires = '';

		if( Todoyu.Cookie.options.expires ) {
			var today = new Date();
			expires = Todoyu.Cookie.options.expires * 86400000;
			expires = ';expires=' + new Date(today.getTime() + expires);
		}

		document.cookie = Todoyu.Cookie.options.name + '=' + escape(Object.toJSON(Todoyu.Cookie.data)) + Todoyu.Cookie.getOptions() + expires;
	},



	/**
	 * @method	erase
	 */
	erase: function() {
		document.cookie = Todoyu.Cookie.options.name + '=' + Todoyu.Cookie.getOptions() + ';expires=Thu, 01-Jan-1970 00:00:01 GMT';
	},



	/**
	 * @method	getOptions
	 */
	getOptions: function() {
		return (Todoyu.Cookie.options.path ? ';path=' + Todoyu.Cookie.options.path : '') + (Todoyu.Cookie.options.domain ? ';domain=' + Todoyu.Cookie.options.domain : '') + (Todoyu.Cookie.options.secure ? ';secure' : '');
	}

};