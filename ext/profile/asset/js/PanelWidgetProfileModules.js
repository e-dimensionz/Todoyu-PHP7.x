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
 *	Panel widget JS: Profile Modules
 */
Todoyu.Ext.profile.PanelWidget.ProfileModules = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:				Todoyu.Ext.profile,

	/**
	 * @property	list
	 * @type		Element
	 */
	list: null,



	/**
	 * Initialize profile modules widget
	 *
	 * @method	init
	 */
	init: function() {
		this.list	= $('panelwidget-profilemodules-content').down('ul');

		this.activateFirstIfNonActive();
	},



	/**
	 * Activate first tab if no tab is selected
	 *
	 * @method	activateFirstIfNonActive
	 */
	activateFirstIfNonActive: function() {
		if( this.list.down('li.active') ) {
			this.setActiveByElement(this.list.down('li'));
		}
	},



	/**
	 * Activate given module element
	 *
	 * @method	setActiveByElement
	 * @param	{Element}	moduleElement
	 */
	setActiveByElement: function(moduleElement) {
		moduleElement.addClassName('active');
	},



	/**
	 * Get element of given module name
	 *
	 * @method	getModuleByName
	 * @param	{String}	moduleName
	 * @return	{Element}
	 */
	getModuleByName: function(moduleName) {
		return this.list.down('li.' + moduleName);
	},



	/**
	 * Activate module element (by given module name)
	 *
	 * @method	setActiveByName
	 * @param	{String}	moduleName
	 */
	setActiveByName: function(moduleName) {
		var moduleElement	= this.getModuleByName(moduleName);

		if( Todoyu.exists(moduleElement) ) {
			this.setActiveByElement(moduleElement);
		}
	},



	/**
	 * Deactivate module element of given name
	 *
	 * @method	deactivateByName
	 * @param	{String}	moduleName
	 */
	deactivateByName: function(moduleName) {
		var moduleElement	= this.getModuleByName(moduleName);

		this.deactivateAll(moduleElement);
	},



	/**
	 * Deactivate given module element
	 *
	 * @method	deactivateByElement
	 * @param	{Element}	module
	 */
	deactivateByElement: function(module) {
		if( Todoyu.exists(module) ) {
			module.removeClassName('active');
		}
	},



	/**
	 * Get all module elements
	 *
	 * @method	getAllModules
	 * @return	{Element[]}
	 */
	getAllModules: function() {
		return $$('#' + this.list.id + ' li');
	},



	/**
	 * Get currently active module
	 *
	 * @method	getActive
	 * @return	{Element}
	 */
	getActive: function() {
		return this.list.down('li.active');
	},



	/**
	 * Get name of the active module
	 *
	 * @method	getActiveKey
	 * @return	{String}
	 */
	getActiveName: function() {
		var active = this.getActive();

		if( active ) {
			return active.getClassNames().toString().replace(' active', '');
		} else {
			return null;
		}
	},



	/**
	 * Set all modules deactivate
	 *
	 * @method	deactivateAll
	 */
	deactivateAll: function() {
		var allModules	= this.getAllModules();

		allModules.each(function(item) {
			this.deactivateByElement(item);
		}.bind(this));
	},



	/**
	 * Handler when module has been clicked in widget
	 *
	 * @method	onClickModule
	 * @param	{String}	moduleName
	 */
	onClickModule: function(moduleName) {
		this.deactivateAll();
		this.setActiveByName(moduleName);

		this.ext.loadModule(moduleName);
	}

};