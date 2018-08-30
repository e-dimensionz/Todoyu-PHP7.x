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
 * @module	Search
 */

/**
 * @class		ActionPanel
 * @namespace	Todoyu.Ext.search
 */
Todoyu.Ext.search.ActionPanel = {

	/**
	 * @property	filter
	 * @type		Object
	 */
	filter: Todoyu.Ext.search.Filter,



	/**
	 * Evoke results CSV export
	 *
	 * @method	exportResults
	 * @param	{String}	name
	 */
	exportResults: function(name) {
		if( Todoyu.Ext.search.Filter.Conditions.size() > 0 ) {
			var conditions	= this.filter.Conditions.getAll(true);
			var conjunction	= this.filter.getConjunction();

			var options = {
				action:			'export',
				tab:			this.filter.getActiveTab(),
				exportname:		name,
				conditions:		conditions,
				conjunction:	conjunction
			};
			this.sendExportPostRequest('actionpanel', options);
		} else {
			alert('[LLL:search.ext.export.error.saveEmpty]');
		}
	},



	/**
	 *
	 * @method	sendExportPostRequest
	 * @param	{String}	controller
	 * @param	{Object}	options
	 */
	sendExportPostRequest: function(controller, options) {
		var url =  Todoyu.getUrl('search', controller);
		var form= new Element('form', {method: 'post', action: url});

		$H(options).each(function(form, pair){
			form.appendChild(new Element('input', {type: 'hidden', name: pair.key, value: pair.value}));
		}.bind(this, form));

		$$('body')[0].appendChild(form);
		form.submit();
		form.remove();
	}

};
