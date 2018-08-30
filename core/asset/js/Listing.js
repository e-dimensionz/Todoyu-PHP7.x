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
 * Listing
 *
 * @class		Listing
 * @namespace	Todoyu
 */
Todoyu.Listing = {

	/**
	 * @property	config
	 * @type		Object
	 */
	config: {},



	/**
	 * Initialize
	 *
	 * @method	init
	 * @param	{String}		name
	 * @param	{String}		update
	 * @param	{Number}		size
	 * @param	{Number}		offset
	 * @param	{Number}		total
	 */
	init: function(name, update, size, offset, total) {
		var url	= update.split('/');

		this.config[name] = {
			name:	name,
			size:	size,
			offset:	offset,
			total:	total,
			url: {
				ext:		url[0],
				controller: url[1],
				action:		url[2]
			}
		};
	},



	/**
	 * Evoke getting more list results
	 *
	 * @method	more
	 * @param	{String}	name
	 * @param	{Number}	pagenum
	 * @param	{Object}	[listParams]		List function parameters
	 */
	more: function(name, pagenum, listParams) {
		listParams	= listParams || {};

		var newOffset = this.config[name].offset + this.config[name].size;
		if( newOffset < this.config[name].total ) {
			this.extend(name, newOffset, pagenum, listParams);
		}
	},



	/**
	 * Fetch more results and extend the amount of entries listed
	 *
	 * @method	extend
	 * @param	{String}		name
	 * @param	{Number}		offset
	 * @param	{Number}		pagenum
	 * @param	{Object}		[listParams]		List function parameters, e.g. search-word config
	 */
	extend: function(name, offset, pagenum, listParams) {
		listParams	= listParams || {};

		var url		= Todoyu.getUrl(this.config[name].url.ext, this.config[name].url.controller);
		var options	= {
			parameters: {
				action:		this.config[name].url.action,
				name:		name,
				listParams:	Object.toJSON(listParams),
				offset:		offset
			},
			onComplete: this.onExtended.bind(this, name, offset)
		};

		$('extendlisting').remove();

		var target	= 'paging-' + name + '-table-' + pagenum;

		Todoyu.Ui.append(target, url, options);
	},



	/**
	 * Handler being evoked upon completion of extending displayed entries
	 *
	 * @method	onExtended
	 * @param	{String}			name
	 * @param	{Number}			offset
	 * @param	{Ajax.Response}		response
	 */
	onExtended: function(name, offset, response) {
		Todoyu.Hook.exec('core.listing.extended', name, offset, response, this);
	}

};