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

Todoyu.Ext.sysmanager.ExtConf = {

	/**
	 * On save handler
	 *
	 * @method	onSave
	 * @param	{String}	form
	 */
	onSave: function(form) {
		Todoyu.Ui.saveRTE(form);

		$(form).request({
			parameters: {
				action:	'save',
				area:	Todoyu.getArea()
			},
			onComplete: this.onSaved.bind(this)
		});

		return false;
	},



	/**
	 * On saved handler
	 *
	 * @method	onSaved
	 * @param	{Array}	response
	 */
	onSaved: function(response) {
		var notificationIdentifier	= 'sysmanager.extconf.saved';

		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('[LLL:sysmanager.ext.extconf.savingFailed]', notificationIdentifier);

			$('config-form').replace(response.responseText);
		} else {
			Todoyu.notifySuccess('[LLL:sysmanager.ext.extconf.saved]', notificationIdentifier);
		}
	}

};