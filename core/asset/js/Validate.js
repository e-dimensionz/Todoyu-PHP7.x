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
 * Various validation and verification functions
 *
 * @class		Validate
 * @namespace	Todoyu
 */
Todoyu.Validate = {

	/**
	 * Check whether given string is empty (based on http://phpjs.org/functions/empty)
	 *
	 * @method	isEmpty
	 * @param	{String|Number|Object}	mixed_var
	 * @return	{Boolean}
	 */
	isEmpty: function(mixed_var) {
		if( typeof mixed_var == 'object' ) {
			return this.IsObjectEmpty(mixed_var);
		}

		if( mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || typeof mixed_var === 'undefined' ) {
			return true;
		}

		return mixed_var.strip() === '';
	},



	/**
	 * Check whether given object is empty (based on http://phpjs.org/functions/empty)
	 *
	 * @method	IsObjectEmpty
	 * @param	{Object}	obj
	 * @return	{Boolean}
	 */
	IsObjectEmpty: function(obj) {
		for(var key in obj) {
			return false;
		}

		return true;
	},



	/**
	 * Check given string to only contain given acceptable characters
	 *
	 * @method	isOnlyAllowedChars
	 * @param	{String}	value
	 * @param	{String}	chars
	 */
	isOnlyAllowedChars: function(value, chars) {
		for(var i= 0; i < value.length; i++) {
			if( chars.indexOf(value.charAt(i)) == -1 ) {
				return false;
			}
		}

		return true;
	},



	/**
	 * Check whether client is given browser (e.g. 'chrome', 'safari')
	 *
	 * @method	isNavigatorUserAgent
	 * @param	{String}	browserName
	 * @return	{Boolean}
	 */
	isNavigatorUserAgent: function(browserName) {
		browserName	= browserName.toLowerCase();

		return navigator.userAgent.toLowerCase().indexOf(browserName) > -1;
	},



	/**
	 * Check whether used client browser is google chrome
	 *
	 * @method	isChrome
	 * @return	{Boolean}
	 */
	isChrome: function() {
		return this.isNavigatorUserAgent('chrome');
	},



	/**
	 * Check whether used client browser is apple safari
	 *
	 * @method	isSafari
	 * @return	{Boolean}
	 */
	isSafari: function() {
		return this.isNavigatorUserAgent('safari');
	}

};