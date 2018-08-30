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
 * Extension import/upload
 *
 * @module	Sysmanager
 */
Todoyu.Ext.sysmanager.Extensions.Import = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.sysmanager,



	/**
	 * Show for for extension upload
	 *
	 * @method	showImportDialog
	 */
	showImportDialog: function() {
		var url		= Todoyu.getUrl('sysmanager', 'extensions');
		var options	= {
			parameters: {
				action: 'dialogImport'
			},
			onComplete: this.onImportShowed.bind(this)
		};

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Show extensions list
	 *
	 * @method	showList
	 */
	showList: function() {
		this.ext.Extensions.showTab('', 'imported');
	},



	/**
	 * Handler when extension upload form is showed
	 *
	 * @method	onImportShowed
	 * @param	{Ajax.Response}		response
	 */
	onImportShowed: function(response) {

	},



	/**
	 * Start extension upload (uses iframe to submit the file upload)
	 *
	 * @method	startUpload
	 */
	startUpload: function() {
		if( $F('importExtension-field-file') !== '' ) {
			Todoyu.Form.submitToIFrame('importExtension-form', 'import');
		} else {
			alert('[LLL:sysmanager.extension.upload.noArchiveSelected]');
		}
	},



	/**
	 * Handler when upload is finished
	 * Function is called from the iFrame which submitted the file
	 *
	 * @method	uploadFinished
	 * @param	{String}	ext
	 * @param	{Boolean}	success
	 * @param	{String}	message
	 */
	importFinished: function(ext, success, message) {
		var notificationIdentifier	= 'sysmanager.extensions.import.finished';

		if( success === true ) {
			Todoyu.notifySuccess('[LLL:sysmanager.extension.upload.ok]: ' + ext, notificationIdentifier);

			this.ext.Extensions.Install.showList();
		} else {
			Todoyu.notifyError('[LLL:sysmanager.extension.upload.error]: ' + ext + ' (' + message + ')', notificationIdentifier);
		}
	},



	/**
	 * Handler when extension import has failed
	 *
	 * @method	{importFailed}
	 * @param	{String}		message
	 */
	importFailed: function(message) {
		Todoyu.notifyError('[LLL:sysmanager.extension.upload.error]: ' + message, 'sysmanager.extensions.import.finished');
	}

};