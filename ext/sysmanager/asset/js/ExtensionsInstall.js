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
 * Extension installation
 *
 * @module	Sysmanager
 */
Todoyu.Ext.sysmanager.Extensions.Install = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.sysmanager,



	/**
	 * Show list of not installed extensions
	 *
	 * @method	showList
	 */
	showList: function() {
		this.ext.Extensions.showTab('', 'installed');
	},



	/**
	 * Install an extension
	 *
	 * @method	install
	 * @param	{String}	ext
	 */
	install: function(ext, noRegister) {
		if( confirm('[LLL:sysmanager.extension.install.confirm]') ) {
			var url		= Todoyu.getUrl('sysmanager', 'extensions');
			var options	= {
				parameters: {
					action: 	'install',
					extension:	ext,
					register:	noRegister ? 0 : 1
				},
				onComplete: this.onInstalled.bind(this, ext)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler when extension installation has finished (successfully or not)
	 *
	 * @method	onInstalled
	 * @param	{String}			extKey
	 * @param	{Ajax.Response}		response
	 */
	onInstalled: function(extKey, response) {
		var registrationRequired = response.getTodoyuHeader('registrationRequired') == 1;

			// Registration required? The installation process was canceled
		if( registrationRequired ) {
			this.showLicenseDialogForImportOfCommercialExtension(extKey);
			return;
		}

			// Installation process completed
		var extTitle	= response.getTodoyuHeader('extTitle');

		if( response.hasTodoyuError() ) {
			Todoyu.notifyInfo('Installation of Extension failed: ' + extTitle + ' (' + extKey + ')');
			var problems	= response.getTodoyuHeader('installProblems');

			this.showInstallationProblems(extKey, problems, extTitle);
		} else {
				// Installation succeeded, notify and update screen
			Todoyu.notifySuccess('[LLL:sysmanager.extension.installed.notify] ' + extTitle, 'sysmanager.install.extension');
			this.showUpdateDialog(extKey);
		}
	},



	/**
	 * Show problems which prevent an installation
	 *
	 * @method	showInstallationProblems
	 * @param	{Object}	problems
	 * @todo	take locale strings from XML
	 */
	showInstallationProblems: function(extKey, problems, extTitle) {
			// Show core warning
		if( problems.core !== false ) {
			Todoyu.notifyError('[LLL:sysmanager.extension.install.error.core] [LLL:sysmanager.extension.install.error.core.atLeastVersion]' + ' ' + problems.core, 'sysmanager.install.coreversion');

		}

			// Show conflict warnings
		if( $A(problems.conflicts).size() > 0 ) {
			Todoyu.notifyError(extTitle + ' extension conflicts with: ' + $A(problems.conflicts).join(', '), 'sysmanager.install.problems');
		}

			// Show dependency warnings
		if( ! Object.isArray(problems.depends) ) {
			var dependencies= [];
			var msg 		= extTitle + ' extension depends on: ';
			$H(problems.depends).each(function(pair){
				dependencies.push(pair.key + ': ' + pair.value);
			});
			msg += dependencies.join(', ');
			Todoyu.notifyError(msg);
		}
	},



	/**
	 * Show dialog to register a commercial extension which is already imported locally
	 *
	 * @method	showLicenseDialogForImportOfCommercialExtension
	 * @param	{String}	ext
	 */
	showLicenseDialogForImportOfCommercialExtension: function(ext) {
		this.ext.Repository.Search.showExtensionInstallDialog(ext, true);
	},



	/**
	 * License the imported extension. If this was successful, install it
	 * like a normal free extension
	 *
	 * @method	installAndLicenseImportedExtension
	 * @param	{String}	ext
	 */
	installAndLicenseImportedExtension: function(ext) {
		var url		= Todoyu.getUrl('sysmanager', 'extensions');
		var options	= {
			parameters: {
				action: 	'licenseImportedExtension',
				extension:	ext
			},
			onComplete: this.onImportedExtensionLicensed.bind(this, ext)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when commercial extension was licensed.
	 * Close dialog and call normal installer, but prevent extra check
	 *
	 * @method	onImportedExtensionLicensed
	 * @param	{String}		ext
	 * @param	{Ajax.Response}	response
	 */
	onImportedExtensionLicensed: function(ext, response) {
		var licensed = response.getTodoyuHeader('licensed');

		if( licensed ) {
			this.ext.Repository.closeDialog();
			this.install(ext, true);
		} else {
			alert("ERROR");
		}
	},



	/**
	 * Show update dialog for an extension
	 *
	 * @method	showUpdate
	 * @param	{String}	ext
	 */
	showUpdateDialog: function(ext) {
		var url		= Todoyu.getUrl('sysmanager', 'extensions');
			var options	= {
				parameters: {
					action: 	'dialogUpdate',
					extension:	ext
				},
				onComplete: this.onUpdateShowed.bind(this, ext)
			};

			Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Handler after extension installation failure screen has been shown
	 *
	 * @method	onInstallationFailedShowed
	 * @param	{String}			ext
	 * @param	{Ajax.Response}		response
	 */
	onInstallationFailedShowed: function(ext, response) {

	},



	/**
	 * Handler when update dialog for an extension is displayed
	 *
	 * @method	onUpdateShowed
	 * @param	{String}			ext
	 * @param	{Ajax.Response}		response
	 */
	onUpdateShowed: function(ext, response) {

	},



	/**
	 * Uninstall an extension
	 *
	 * @method	uninstall
	 * @param	{String}		ext
	 */
	uninstall: function(ext) {
		if( confirm('[LLL:sysmanager.extension.uninstall.confirm]') ) {
			var url		= Todoyu.getUrl('sysmanager', 'extensions');
			var options	= {
				parameters: {
					action:		'uninstall',
					extension:	ext
				},
				onComplete: this.onUninstalled.bind(this, ext)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler when extension is uninstalled
	 *
	 * @method	onUninstalled
	 * @param	{String}		extKey
	 * @param	{Ajax.Response}	response
	 */
	onUninstalled: function(extKey, response) {
		var notificationIdentifier	= 'sysmanager.extension.uninstalled';

		if( response.hasTodoyuError() ) {
			var info	= response.getTodoyuHeader('info');

			Todoyu.notifyError('[LLL:sysmanager.extension.uninstall.error.general]: ' + info, notificationIdentifier);
		} else {
			var extName	= response.getTodoyuHeader('extTitle');

			Todoyu.notifySuccess('[LLL:sysmanager.extension.uninstall.ok]: ' + extName, notificationIdentifier);

			Todoyu.Ui.setContentBody(response.responseText);
		}
	}

};