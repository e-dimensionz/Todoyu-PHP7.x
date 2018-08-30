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
 * @module	Core
 */

/**
 * General AJAX helper functions
 *
 * @namespace	Todoyu
 * @class		Ajax
 */
Todoyu.Ajax = {

	/**
	 * Handler when request failed (not status code 200)
	 *
	 * @method	onFailure
	 * @param	{Ajax.Response}		response
	 */
	onFailure: function(response) {
		Todoyu.log('Request was not successful');
	},



	/**
	 * Check if a no access header has been sent.
	 * Cancel execution and show error message if so
	 *
	 * @method	checkNoAccessHeader
	 * @param	{Ajax.Response}		response
	 */
	checkNoAccessHeader: function(response) {
		if( response.hasNoAccess() ) {
				// Delete onComplete handler to prevent processing an empty response
			delete response.request.options.onComplete;
			var missingRight = response.getTodoyuHeader('noAccess-right');
			Todoyu.notifyError('[LLL:core.global.noAccess.errorMessage] (' + missingRight + ')');
			Todoyu.Hook.exec('core.noaccess', response, missingRight);
		}
	},



	/**
	 * Check whether not logged in header was sent
	 *
	 * @method	checkNotLoggedInHeader
	 * @param	{Ajax.Response}		response
	 */
	checkNotLoggedInHeader: function(response) {
		if( response.isNotLoggedIn() ) {
				// Delete onComplete handler to prevent processing an empty response
			response.request.options.backupOnComplete = response.request.options.onComplete;
			delete response.request.options.onComplete;
			Todoyu.notifyError('[LLL:core.global.notLoggedIn.errorMessage]');
			Todoyu.Hook.exec('core.notloggedin', response);
		}
	},



	/**
	 * Check if a PHP error header has been sent
	 * Cancel execution and show error message if so
	 *
	 * @method	checkPhpErrorHeader
	 * @param	{Ajax.Response}		response
	 */
	checkPhpErrorHeader: function(response) {
		if( response.hasPhpError() ) {
			delete response.request.options.onComplete;
			Todoyu.notifyError(response.getPhpError());
			Todoyu.log(response.getPhpError());
			Todoyu.Hook.exec('core.phperror', response);
		}
	},



	/**
	 * Set default options
	 *
	 * @method	getDefaultOptions
	 * @param	{Object}	options
	 * @return	{Object}
	 */
	getDefaultOptions: function(options) {
		if( options === undefined ) {
			options = {};
		}

		if( options.evalScripts === undefined ) {
			options.evalScripts = true;
		}

		if( options.parameters === undefined ) {
			options.parameters = {};
		}

		if( options.parameters.area === undefined ) {
			options.parameters.area = Todoyu.getArea();
		}

		if( options.onFailure === undefined ) {
			options.onFailure = this.onFailure.bind(this);
		}

		return options;
	},



	/**
	 * Start spinner when headlet is preset
	 *
	 */
	startSpinner: function() {
		if( Todoyu.Headlets.isHeadlet('todoyuheadletajaxloader') ) {
			Todoyu.Headlets.getHeadlet('todoyuheadletajaxloader').active();
		}
	},



	/**
	 * Stop spinner when headlet is present and no more requests are running
	 *
	 */
	stopSpinner: function() {
		if( Ajax.activeRequestCount < 1 ) {
			if( Todoyu.Headlets.isHeadlet('todoyuheadletajaxloader') ) {
				Todoyu.Headlets.getHeadlet('todoyuheadletajaxloader').inactive();
			}
		}
	}

};