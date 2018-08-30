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
 * @module	Firststeps
 */

/**
 * Main firststeps object
 *
 * @class		firststeps
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.firststeps = {

	/**
	 * Initialize extension
	 *
	 * @method	init
	 */
	init: function() {

	},



	/**
	 * Open wizard
	 *
	 * @method	openWizard
	 */
	openWizard: function() {
		this.Wizard.open();
	},



	/**
	 * @method	disableWizardAndClose
	 */
	disableWizardAndClose: function() {
		this.disableWizard();
		this.Wizard.close();
	},



	/**
	 * @method	disableWizard
	 */
	disableWizard: function() {
		var url		= Todoyu.getUrl('firststeps', 'ext');
		var options	= {
			parameters: {
				action: 'saveDisable'
			}
		};

		Todoyu.send(url, options);
	}

};