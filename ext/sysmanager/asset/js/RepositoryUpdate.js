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
 * Repository Update
 *
 * @class		Update
 * @namespace	Todoyu.Ext.sysmanager.Repository
 */
Todoyu.Ext.sysmanager.Repository.Update = {

	/**
	 * @var	{Object}	Extension
	 */
	ext: Todoyu.Ext.sysmanager,

	/**
	 * @var	{Object}	Repository functions
	 */
	repo: Todoyu.Ext.sysmanager.Repository,



	/**
	 * Initialize
	 *
	 * @method	init
	 */
	init: function() {
		this.repo.installWarningsObservers();
	},



	/**
	 * Reload list with updates
	 *
	 * @method	refreshUpdateList
	 * @param	{Function}	[onComplete]
	 */
	refreshUpdateList: function(onComplete) {
		var url		= this.repo.getUrl();
		var options	= {
			parameters: {
				action:	'refreshUpdateList'
			},
			onComplete:	onComplete || Prototype.emptyFunction
		};

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Show dialog for core update
	 *
	 * @method	showCoreUpdateDialog
	 */
	showCoreUpdateDialog: function() {
		var url		= this.repo.getUrl();
		var options	= {
			parameters: {
				action:	'coreUpdateDialog'
			}
		};

		this.repo.dialog = Todoyu.Popups.open('coreUpdate', 'Core Update', 600, url, options);
	},



	/**
	 * Show dialog for extension update
	 *
	 * @method	showExtensionUpdateDialog
	 * @param	{String}	extkey
	 */
	showExtensionUpdateDialog: function(extkey) {
		this.repo.showExtensionDialog(extkey, 'updateDialog', '[LLL:sysmanager.repository.extension.update.install.dialogtitle]');
	},



	/**
	 * Install extension update from tER
	 *
	 * @method	installExtensionUpdate
	 * @param	{String}	extkey
	 */
	installExtensionUpdate: function(extkey) {
		if( confirm('[LLL:sysmanager.repository.extension.update.confirm]') ) {
			var url		= this.repo.getUrl();
			var options	= {
				parameters: {
					action:		'installExtensionUpdate',
					extkey:		extkey
				},
				onComplete: this.onExtensionUpdateInstalled.bind(this, extkey)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Callback after extension update has been installed
	 *
	 * @method	onExtensionUpdateInstalled
	 * @param	{String}	extkey
	 */
	onExtensionUpdateInstalled: function(extkey, response) {
		var notificationIdentifier	= 'sysmanager.repository.extension.updateinstalled';

		if( response.hasTodoyuError() ) {
			var error	= response.getTodoyuErrorMessage();
			Todoyu.notifyError(error, notificationIdentifier);
		} else {
			Todoyu.notifySuccess('[LLL:sysmanager.repository.extension.update.success]', notificationIdentifier, 10);
			this.repo.dialog.close();
			this.refreshUpdateList();
		}
	},



	/**
	 * Install update of todoyu core from given URL
	 *
	 * @method	installCoreUpdate
	 */
	installCoreUpdate: function() {
		if( confirm('[LLL:sysmanager.repository.core.update.confirm]') ) {
			var url		= this.repo.getUrl();
			var options	= {
				parameters: {
					action:		'installCoreUpdate'
				},
				onComplete: this.onCoreUpdateInstalled.bind(this)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Callback after todoyu core update has been installed
	 *
	 * @method	onCoreUpdateInstalled
	 * @param	{Ajax.Response}		response
	 */
	onCoreUpdateInstalled: function(response) {
		var notificationIdentifier	= 'sysmanager.repositoryupdate.coreupdate.installed';

		if( response.hasTodoyuError() ) {
			var error	= response.getTodoyuErrorMessage();
			Todoyu.notifyError(error, notificationIdentifier);
		} else {
			Todoyu.notifySuccess('[LLL:sysmanager.repository.core.update.success]', notificationIdentifier, 10);
			this.repo.dialog.close();

			new Todoyu.LoaderBox('update', {
				block: 	true,
				text: 	'[LLL:sysmanager.repository.core.core.update.reload]',
				show:	true
			});

			setTimeout(location.reload, 1000);
		}
	},



	/**
	 * Show tER extension update details in new window
	 *
	 * @method	showExtensionUpdateDetails
	 * @param	{String}	terLink
	 */
	showExtensionUpdateDetails: function(terLink) {
		this.repo.showExtensionInTER(terLink);
	}

};