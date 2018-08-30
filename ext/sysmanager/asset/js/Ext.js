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
 * @module	Sysmanager
 */

/**
 * Sysmanager main object
 *
 * @class		sysmanager
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.sysmanager = {

	/**
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet: {},

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},



	/**
	 * Initialize sysmanager
	 *
	 * @method	init
	 */
	init: function() {
		this.PanelWidget.SysmanagerModules.init();
	},



	/**
	 * Load sysmanager module content
	 *
	 * @method	loadModule
	 * @param	{String}	module
	 * @param	{Object}	params
	 */
	loadModule: function(module, params) {
		var url		= Todoyu.getUrl('sysmanager', 'ext');
		var options	= {
			parameters: {
				action: 'module',
				module:	module
			},
			onComplete: this.onModuleLoaded.bind(this, module)
		};

		if( typeof(params) === 'object' ) {
			options.parameters = $H(options.parameters).update(params).toObject();
		}

		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Handler when module content is loaded
	 *
	 * @method	onModuleLoaded
	 * @param	{String}		module
	 * @param	{Ajax.Response}	response
	 */
	onModuleLoaded: function(module, response) {
			// Make sure the module is activated in the panel widget
		this.PanelWidget.SysmanagerModules.activate(module);

		this.updateBodyClassName(module);
	},



	/**
	 * Set body class for easy styling
	 *
	 * @method	updateBodyClassName
	 * @param	{String}	module
	 */
	updateBodyClassName: function(module) {
		var moduleClass;
		moduleClass = $w(document.body.className).detect(function(oldClass) {
			return oldClass.substr(0, 6) === 'module';
		});
		var newClass = 'module' + module.capitalize();

		document.body.replaceClassName(moduleClass, newClass);
	}

};