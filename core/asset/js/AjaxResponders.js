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
 * Todoyu specific Ajax.Responders to prototype AJAX handling
 * This responders are called for every AJAX request of prototype
 *
 * @class		Responders
 * @namespace	Todoyu.Ajax
 * @static
 */
Todoyu.Ajax.Responders = {

	/**
	 * Hooks called when request is completed
	 *
	 * @property	completeHooks
	 * @type		Array
	 */
	completeHooks: [],



	/**
	 * Register the used AJAX responders
	 *
	 * @method	init
	 */
	init: function() {
		Ajax.Responders.register({
			onCreate:		this.onCreate.bind(this),
			onComplete:		this.onComplete.bind(this),
			onException:	this.onException.bind(this)
		});

		this.addOnCompleteHook(Todoyu.Ajax.checkNotLoggedInHeader.bind(Todoyu.Ajax));
		this.addOnCompleteHook(Todoyu.Ajax.checkNoAccessHeader.bind(Todoyu.Ajax));
		this.addOnCompleteHook(Todoyu.Ajax.checkPhpErrorHeader.bind(Todoyu.Ajax));
		this.addOnCompleteHook(Todoyu.Notification.checkNoteHeader.bind(Todoyu.Notification));
	},



	/**
	 * Add a new hook function which will be called when request is completed
	 *
	 * @method	addOnCompleteHook
	 * @param	{Function}		hook
	 */
	addOnCompleteHook: function(hook) {
		this.completeHooks.push(hook);
	},



	/**
	 * Call all registered hook functions
	 * They receive the response object as only parameter
	 *
	 * @method	callOnCompleteHooks
	 * @param	{Ajax.Response}		response
	 */
	callOnCompleteHooks: function(response) {
		this.completeHooks.each(function(response, func){
			func(response);
		}.bind(this, response));
	},



	/**
	 * Extend the prototype 'respondToReadyState' handler
	 * Delete the onComplete handler if no access flag is set in header
	 *
	 * @method	onCreate
	 * @param	{Ajax.Request}	request
	 */
	onCreate: function(request, xhr) {
		Todoyu.Ajax.startSpinner();

		var originalMethod 	= request.respondToReadyState,
			that			= this;

		request.respondToReadyState = function(readyState) {
			var state	= Ajax.Request.Events[readyState],
				response= new Ajax.Response(this);

				// Call onComplete hooks
			if( state === 'Complete' ) {
				if( that.hasPhpFatalError(response) ) {
					that.handlePhpFatalError(response);
				}

				Todoyu.Ajax.Responders.callOnCompleteHooks(response);
			}

			originalMethod.call(response.request, readyState);
		};
	},



	/**
	 * Check whether response contains fatal php error message
	 *
	 * @param	{Ajax.Response}		response
	 * @returns	{Boolean}
	 */
	hasPhpFatalError: function(response) {
		if( response.responseText ) {
			if( response.responseText.indexOf("class='xdebug-error") !== -1 ) { // error with xdebug
				if( response.responseText.indexOf('Call Stack') !== -1 && response.responseText.indexOf("<font size='1'>") !== -1) { // second check to prevent false positives
					return true;
				}
			}
			if( response.responseText.indexOf('<b>Parse error</b>:') !== -1 ) { // php error (no xdebug)
				if( response.responseText.indexOf('<br />') !== -1 ) {
					return true;
				}
			}
		}

		return false;
	},



	/**
	 * Handle php fatal error
	 *
	 * @param	{Ajax.Response}		response
	 */
	handlePhpFatalError: function(response) {
		var message	= '[LLL:core.global.fatalErrorMessage]';

		Todoyu.notifyError(message, 'phpFatalError');
		Todoyu.log(message);

		Todoyu.Ajax.stopSpinner();
	},



	/**
	 * Handler when a request is completed
	 *
	 * @method	onComplete
	 * @param	{Ajax.Response}		response
	 */
	onComplete: function(response) {
			// Check for hash header and scroll to element
		this.scrollToElement(response);

			// If no more requests are running, stop spinner
		Todoyu.Ajax.stopSpinner();
	},



	/**
	 * Handler when connection to server fails
	 *
	 * @method	onException
	 * @param	{Ajax.Response}	response
	 * @param	{Object}		exception
	 */
	onException: function(response, exception) {
		//Todoyu.notifyError('[LLL:core.ajax.requestFailed]', 0);
		//alert('[LLL:core.ajax.requestFailed]');

		Todoyu.log(response);
		Todoyu.log(exception);
	},



	/**
	 * Check whether hash header has been sent and scroll to it
	 *
	 * @method	scrollToElement
	 * @param	{Ajax.Response}		response
	 */
	scrollToElement: function(response) {
		var hash = response.getHeader('Todoyu-Hash'); // Do not use getTodoyuHeader(), it fails...

		if( hash !== null && Todoyu.exists(hash) ) {
			$(hash).scrollToElement();
		}
	}

};