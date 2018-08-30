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
 * System logo upload
 */
Todoyu.Ext.sysmanager.Config.Logo = {

	/**
	 * Save logo form. Start upload over an iframe
	 *
	 * @method	onFileSelectionChange
	 * @param	{Element}	form
	 */
	onFileSelectionChange: function(form) {
		Todoyu.Form.addIFrame('logo');

		$(form).writeAttribute('target', 'upload-iframe-logo');

		$(form).submit();
	},



	/**
	 * Handler when upload is finished
	 *
	 * @method	onUploadFinished
	 * @param	{Boolean}	success
	 */
	onUploadFinished: function(success) {
		var notificationIdentifier	= 'sysmanager.config.logo.upload';

		if( success ) {
			Todoyu.notifySuccess('[LLL:sysmanager.ext.config.logo.upload.ok]', notificationIdentifier);
			setTimeout("document.location.reload()", 2000);
		} else {
			Todoyu.notifyError('[LLL:sysmanager.ext.config.logo.upload.error]', notificationIdentifier);
		}
	}

};