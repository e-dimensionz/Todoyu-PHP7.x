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
 * Autocompleter
 *
 * @class		Autocomplete
 * @namespace	Todoyu
 */
Todoyu.Autocomplete = {

	/**
	 * Autocompleter references
	 * @property	AC
	 * @type		Object
	 */
	AC: {},



	/**
	 * Initialize autocompleter
	 *
	 * @method	install
	 * @param	{Number}		idElement		ID of the element whichs value will be set by autocomplete
	 * @param	{Object}		options			Custom options
	 */
	install: function(idElement, options) {
		var inputField		= idElement + '-fulltext';
		var suggestDiv		= idElement + '-suggest';

			// Setup request
		var url		= Todoyu.getUrl('core', 'autocomplete');

			// Create autocompleter
		this.AC[idElement] = new Todoyu.Autocompleter(inputField, suggestDiv, url, options);
	},



	/**
	 * Get autocompleter
	 *
	 * @method	getAC
	 * @param	{String}	name
	 */
	getAC: function(name) {
		return this.AC[name];
	}

};