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
 * @module	Profile
 */

/**
 * Profile main object
 *
 * @class		profile
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.profile = {

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},

	/**
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet: {},



	/**
	 * Load given profile module
	 *
	 * @method	loadModule
	 * @param	{String}	module
	 */
	loadModule: function(module) {
		var url		= Todoyu.getUrl('profile', 'ext');
		var options	= {
			parameters: {
				action: 'module',
				module: module
			},
			onComplete: this.onModuleLoaded.bind(this, module)
		};

		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Handler to be called after profile module has been loaded
	 *
	 * @method	onModuleLoaded
	 * @param	{String}			module
	 * @param	{Ajax.Response}		response
	 */
	onModuleLoaded: function(module, response) {

	},



	/**
	 * Set body to given HTML content
	 *
	 * @method	setContent
	 * @param	{String}	content
	 */
	setContent: function(content) {
		Todoyu.Ui.setContentBody(content);
	},



	/**
	 * Remove class names and messages of form errors form profile form
	 *
	 * Different to other forms, the profile stays open after being saved,
	 * therefor (when validating the form) error messages must be removed after successful saving.
	 *
	 * @method	removeFormErrors
	 */
	removeFormErrors: function() {
		var body	= $('content-body');

			// Remove error class names
		body.select('.error').each(function(element){
			element.removeClassName('error');
		});
			// Remove error messages
		body.select('.errorMessage').each(function(element){
			element.remove();
		});
	}

};