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
 * Panelwidget which lists all sysmanager modules
 *
 * @class		SysmanagerModules
 * @namespace	Todoyu.Ext.sysmanager.PanelWidget
 */
Todoyu.Ext.sysmanager.PanelWidget.SysmanagerModules = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.sysmanager,

	/**
	 * @property	list
	 * @type		Element
	 */
	list: null,



	/**
	 * Initialize sysmanager modules widget
	 *
	 * @method	init
	 */
	init: function() {
		this.list = $('sysmanager-modules');
	},



	/**
	 * Load and activate given module
	 *
	 * @method	module
	 * @param	{String}	module
	 */
	module: function(module) {
		this.ext.loadModule(module);
		this.activate(module);
	},



	/**
	 * Activate given module
	 *
	 * @method	activate
	 * @param	{String}	module
	 */
	activate: function(module) {
		var current = this.getActive();

		if( current ) {
			current.removeClassName('active');
		}

		this.setActive(module);
	},



	/**
	 * Get currently activated module option
	 *
	 * @method	getActive
	 * @return	Element
	 */
	getActive: function() {
		return this.list.down('li.active');
	},



	/**
	 * Set given module option active
	 *
	 * @method	setActive
	 * @param	{String}	module
	 */
	setActive: function(module) {
		this.list.down('li.' + module).addClassName('active');
	}

};