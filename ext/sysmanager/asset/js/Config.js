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
 * Sysmanager config area
 *
 * @class		Config
 * @namespace	Todoyu.Ext.sysmanager
 */
Todoyu.Ext.sysmanager.Config = {

	/**
	 * Click handler for sysmanager tabs
	 *
	 * @method	onTabClick
	 * @param	{Event}		event
	 * @param	{String}	tab
	 */
	onTabClick: function(event, tab) {
		var url		= Todoyu.getUrl('sysmanager', 'config');
		var options	= {
			parameters: {
				action:	'update',
				tab:	tab
			},
			onComplete: this.onTabLoaded.bind(this, tab)
		};

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Handler when tab was loaded
	 *
	 * @method	onTabLoaded
	 * @param	{String}		tab
	 * @param	{Ajax.Response}	response
	 */
	onTabLoaded: function(tab, response) {

	},



	/**
	 * Save system configuration form
	 *
	 * @method	saveSystemConfig
	 * @param	{Form}	form
	 */
	saveSystemConfig: function(form) {
		$(form).request({
			parameters: {
				action: 'saveSystemConfig',
				area:	Todoyu.getArea()
			},
			onComplete: this.onSystemConfigSaved.bind(this)
		});
	},



	/**
	 * Handler when system config was saved
	 *
	 * @method	onSystemConfigSaved
	 * @param	{Ajax.Response}	response
	 */
	onSystemConfigSaved: function(response) {
		var notificationIdentifier	= 'sysmanager.systemconfig.saved';

		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('[LLL:sysmanager.ext.config.tab.systemconfig.failed]', notificationIdentifier);
		} else {
			Todoyu.notifySuccess('[LLL:sysmanager.ext.config.tab.systemconfig.saved]', notificationIdentifier);
		}

		Todoyu.Ui.setContentBody(response.responseText);
	},



	/**
	 * Save password strength settings
	 *
	 * @method	savePasswordStrength
	 * @param	{Element}	form
	 */
	savePasswordStrength: function(form) {
		$(form).request({
			parameters: {
				action: 'savePasswordStrength',
				area:	Todoyu.getArea()
			},
			onComplete: this.onPasswordStrengthSaved.bind(this)
		});
	},



	/**
	 * Handler when system password strength settings have been saved - notify success
	 *
	 * @method	onPasswordStrengthSaved
	 * @param	{Ajax.Response}	response
	 */
	onPasswordStrengthSaved: function(response) {
		Todoyu.notifySuccess('[LLL:sysmanager.ext.config.tab.passwordstrength.saved]');
	},



	/**
	 * Save repository configuration (into config\settings.php)
	 *
	 * @method	saveRepositoryConfig
	 * @param	{String}				form		form element ID
	 */
	saveRepositoryConfig: function(form) {
		$(form).request({
			parameters: {
				action: 'saveRepositoryConfig',
				area:	Todoyu.getArea()
			},
			onComplete: this.onRepositoryConfigSaved.bind(this)
		});
	},



	/**
	 * Handler when repository config has been saved: notify success
	 *
	 * @method	onRepositoryConfigSaved
	 * @param	{Ajax.Response}				response
	 */
	onRepositoryConfigSaved: function(response) {
		Todoyu.notifySuccess('[LLL:sysmanager.ext.config.tab.repository.saved]');
	}

};