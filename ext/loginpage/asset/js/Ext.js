/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * @module	Loginpage
 */

/**
 * Loginpage main object
 *
 * @class		loginpage
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.loginpage = {

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},

	/**
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet: {},

	/**
	 * ID of username field element
	 *
	 * @property	fieldUsername
	 * @type		String
	 */
	fieldUsername:	'login-field-username',

	/**
	 * ID of password field element
	 *
	 * @property	fieldPassword
	 * @type		String
	 */
	fieldPassword:	'login-field-password',


	fieldSubmit: 'login-field-submit',

	/**
	 * ID of remain field
	 *
	 * @property	fieldRemain
	 * @type		String
	 */
	fieldRemain:	'login-field-loginremain',

	/**
	 * ID of status element
	 *
	 * @property	elStatus
	 * @type		String
	 */
	elStatus:		'formElement-login-field-status-inputbox',

	/**
	 * ID of element to contain status of "forgot password" option status
	 *
	 * @property	elStatus
	 * @type		String
	 */
	forgotPasswordElStatus:		'formElement-forgotpassword-field-status-inputbox',

	/**
	 * @property	popup
	 * @type		Todoyu.Popup
	 */
	popup: null,

	/**
	 * @property	oldRequest
	 * @type		Ajax.Response
	 */
	oldRequest: null,



	/**
	 * Init loginpage ext JavaScript
	 *
	 * @method	init
	 */
	init: function() {
		if( this.hasLoginForm() ) {
			this.initLoginpage();
		}

		this.registerHooks();
		this.preloadImages();
	},



	/**
	 * Check whethere fields of login form are in DOM
	 *
	 * @method	hasLoginForm
	 * @return	{Boolean}
	 */
	hasLoginForm: function() {
		return Todoyu.exists(this.fieldUsername) && Todoyu.exists(this.fieldPassword);
	},



	/**
	 * Initialize login page handlers
	 *
	 */
	initLoginpage: function() {
		this.observeForm();
		this.observeUsernameField();
		this.observePasswordField();
		this.focusField();
		this.disableToggleSave();
	},



	/**
	 * Preload images to make sure they are already loaded on success login redirect
	 *
	 */
	preloadImages: function() {
		var imgNotify = new Image();
		imgNotify.src = 'core/asset/img/icons.png';

		var imgLoading = new Image();
		imgLoading.src = 'core/asset/img/ajax-loader_success.gif';
	},



	/**
	 * Focus username input if empty, otherwise: focus password input
	 *
	 * @method	focusField
	 */
	focusField: function() {
		if( $F(this.fieldUsername) === '' ) {
			$(this.fieldUsername).focus();
		} else {
			$(this.fieldPassword).focus();
		}
	},



	/**
	 * Override panelwidget save function, to prevent access denied message because the user isn't logged in
	 *
	 * @method	disableToggleSave
	 */
	disableToggleSave: function() {
		Todoyu.PanelWidget.saveToggleStatus = Prototype.emptyFunction;
	},



	/**
	 * Install form onSubmit-observer
	 *
	 * @method	observeForm
	 */
	observeForm: function() {
		$('login-form').on('submit', this.onFormSubmit.bind(this));
	},



	/**
	 * Observe the username field for keyPress event
	 *
	 * @method	observeUsernameField
	 */
	observeUsernameField: function() {
		$(this.fieldUsername).on('keypress', this.detectCapsLock.bind(this));
	},



	/**
	 * Observe the password field for changes
	 *
	 * @method	observePasswordField
	 */
	observePasswordField: function() {
		$(this.fieldPassword).on('keypress', this.detectCapsLock.bind(this));
		$(this.fieldPassword).on('keyup', this.onPasswordEnter.bind(this));
		$(this.fieldPassword).on('change', this.onPasswordEnter.bind(this));
	},



	/**
	 * Register hooks
	 *
	 * @method	registerHooks
	 */
	registerHooks: function() {
		Todoyu.Hook.add('core.notloggedin', this.onLoggedOutAuto.bind(this));
	},



	/**
	 * Detect whether capsLock is active and warn if
	 *
	 * @method	detectCapsLock
	 * @param	{Event}		event
	 */
	detectCapsLock: function(event) {
		if( Todoyu.Ui.isCapsLock(event) ) {
			Todoyu.Notification.notify(Todoyu.Notification.INFO, '[LLL:loginpage.ext.warning.capslock]', false, 3, 'capslock');
		}
	},



	/**
	 * When password field input changes, generate hashed password, if form is submitted normally (no AJAX)
	 *
	 * @method	onPasswordEnter
	 * @param	{Event}		event
	 */
	onPasswordEnter: function(event) {
		$('login-field-passhash').value = this.getHashedPassword();
	},



	/**
	 * onSubmit event handler: stop event and evoke form submission
	 *
	 * @method	onFormSubmit
	 * @param	{Event}		event
	 * @return	{Boolean}
	 */
	onFormSubmit: function(event) {
		event.stop();

		this.submitForm();

		return false;
	},



	/**
	 * Evoke login form submission as Todoyu post-request, params taken from form fields
	 *
	 * @method	submitForm
	 */
	submitForm: function() {
		if( this.checkFieldsNotEmpty() ) {
			this.onLoginRequest();

			var url		= Todoyu.getUrl('loginpage', 'ext');
			var	options	= {
				parameters: {
					action:		'login',
					username:	$F(this.fieldUsername),
					passhash:	this.getHashedPassword(),
					remain:		this.isRemainLoginChecked()
				},
				onComplete:	this.onLoginResponse.bind(this)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Get MD5 hash of entered password
	 *
	 * @method	getHashedPassword
	 * @return	String
	 */
	getHashedPassword: function() {
		return hex_md5($F(this.fieldPassword));
	},



	/**
	 * Check whether field 'remember me on this computer' is checked
	 *
	 * @method	isRemainLoginChecked
	 * @return	{Boolean}
	 */
	isRemainLoginChecked: function() {
		return $(this.fieldRemain).checked === true;
	},



	/**
	 * Check whether all (required) fields of the login form are filled (username, password), if not filled: focus empty field
	 *
	 * @method	checkFieldsNotEmpty
	 * @return	{Boolean}
	 */
	checkFieldsNotEmpty: function() {
		if( $F(this.fieldUsername).strip() === '' ) {
			alert('[LLL:loginpage.ext.error.enterUsername]');
			$(this.fieldUsername).value = '';
			$(this.fieldUsername).focus();
			return false;
		}
		if( $F(this.fieldPassword).strip() === '' ) {
			alert('[LLL:loginpage.ext.error.enterPassword]');
			$(this.fieldPassword).focus();
			return false;
		}

		return true;
	},



	/**
	 * Handle login request: disable login form fields, display verification progress message
	 *
	 * @method	onLoginRequest
	 */
	onLoginRequest: function() {
		this.toggleLoginFields(false);
		this.displayVerifying();
	},



	/**
	 * Enable / disable fields of login form
	 *
	 * @method	toggleLoginFields
	 * @param	{Boolean}	active
	 */
	toggleLoginFields: function(active) {
		var method = active ? 'enable' : 'disable';

		$(this.fieldUsername)[method]();
		$(this.fieldPassword)[method]();
		$(this.fieldRemain)[method]();
		$(this.fieldSubmit)[method]();
	},



	/**
	 * Handle login request, evoked from onComplete of login form submission
	 *
	 * @method	onLoginResponse
	 * @param	{Ajax.Response}		response
	 */
	onLoginResponse: function(response){
		var status	= response.responseJSON;

		if( status.success ) {
			this.displayLoginSuccess();
			this.reloadAfterLogin.bind(this).delay(0.2);
		} else {
			this.toggleLoginFields(true);
			this.displayLoginError(status.message);
			$(this.fieldPassword).select();
		}
	},



	/**
	 * Reload page after login
	 *
	 * @method	reloadAfterLogin
	 */
	reloadAfterLogin: function() {
		location.reload();
	},



	/**
	 * Display status message when verifying received login data
	 *
	 * @method	displayVerifying
	 */
	displayVerifying: function() {
		$(this.elStatus).update('<img src="core/asset/img/ajax-loader.gif" />[LLL:loginpage.ext.form.status.verifyingLoginData]');
		$(this.elStatus).addClassName('notification');
	},



	/**
	 * Display status message of successful login
	 *
	 * @method	displayLoginSuccess
	 */
	displayLoginSuccess: function() {
		$(this.elStatus).update('<img src="core/asset/img/ajax-loader_success.gif" /><span class="icon"></span>[LLL:loginpage.ext.form.status.loginOk]');
		$(this.elStatus).removeClassName('failure');
		$(this.elStatus).addClassName('success');
	},



	/**
	 * Display status message of login error
	 *
	 * @method	displayLoginError
	 * @param	{String}	message
	 */
	displayLoginError: function(message) {
		$(this.elStatus).update('<span class="icon"></span>' + message);
		$(this.elStatus).addClassName('failure');
	},



	/**
	 * Display status message when verifying received login data
	 *
	 * @method	displayForgotPasswordVerifying
	 */
	displayForgotPasswordVerifying: function() {
		$(this.forgotPasswordElStatus).update('<img src="core/asset/img/ajax-loader.gif" />[LLL:loginpage.ext.form.status.verifyingLoginData]');
		$(this.forgotPasswordElStatus).addClassName('notification');
	},



	/**
	 * Display status message of successful login
	 *
	 * @method	displayForgotPasswordSuccess
	 */
	displayForgotPasswordSuccess: function() {
		$(this.elStatus).update('<span class="icon"></span>[LLL:loginpage.ext.forgotpassword.form.field.notification.success]');
		$(this.elStatus).addClassName('notification');
		$(this.elStatus).removeClassName('failure');
		$(this.elStatus).addClassName('success');
	},



	/**
	 * Display status message of forgot password error
	 *
	 * @method	displayForgotPasswordError
	 * @param	{String}	message
	 */
	displayForgotPasswordError: function(message) {
		$(this.forgotPasswordElStatus).update('<span class="icon"></span>' + message);
		$(this.forgotPasswordElStatus).addClassName('notification');
		$(this.forgotPasswordElStatus).addClassName('failure');
	},



	/**
	 * Log out current person
	 *
	 * @method	logout
	 */
	logout: function() {
		var url		= Todoyu.getUrl('loginpage', 'ext');
		var options	= {
			parameters: {
				action:	'logout'
			},
			onComplete:	this.onLoggedOut.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handle logging out: clears all params and reloads loginpage
	 *
	 * @method	onLoggedOut
	 * @param	{Ajax.Response}		response
	 */
	onLoggedOut: function(response) {
			// Remove all parameters from url and reload
		location.search = '';
	},



	/**
	 * @method	loadForgotPasswordForm
	 */
	loadForgotPasswordForm: function() {
		var url		= Todoyu.getUrl('loginpage', 'ext');
		var options	= {
			parameters: {
				action:		'loadForgotPasswordForm',
				username:	$F(this.fieldUsername)
			},
			onComplete:	this.onForgotPasswordFormLoaded.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when forget-password form has been loaded - set content from response
	 *
	 * @method	onForgotPasswordFormLoaded
	 * @param	{Ajax.Response}		response
	 */
	onForgotPasswordFormLoaded: function(response) {
		$('login-form').replace(response.responseText);
	},



	/**
	 * @method	cancelForgotPasswordForm
	 * @param	{Element}	form
	 */
	cancelForgotPasswordForm: function(form) {
		var url		= Todoyu.getUrl('loginpage', 'ext');
		var options	= {
			parameters: {
				action:		'loadLoginForm'
			},
			onComplete:	this.onCancelForgotPasswordForm.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when forget-password form has been loaded - set content from response
	 *
	 * @method	onForgotPasswordFormLoaded
	 * @param	{Ajax.Response}		response
	 */
	onCancelForgotPasswordForm: function(response) {
		var formID	= Todoyu.exists('forgotpassword-form') ? 'forgotpassword-form' : 'relogin-popup-form';

		$(formID).replace(response.responseText);

		this.init();
	},



	/**
	 * @method	submitForgotPasswordForm
	 * @param	{Element}	form
	 */
	submitForgotPasswordForm: function(form) {
		this.displayForgotPasswordVerifying();

		$(form).request({
			parameters: {
				action:	'forgotPassword',
				area:	Todoyu.getArea()
			},
			onComplete: this.onForgotPasswordResponse.bind(this)
		});
	},



	/**
	 * @method	onForgotPasswordResponse
	 * @param	{Ajax.Response}		response
	 */
	onForgotPasswordResponse: function(response) {
		var status	= response.responseJSON;

		if( response.hasTodoyuError() ) {
			$('forgotpassword-form').replace(status.form);

			if( status.message != null ) {
				this.displayForgotPasswordError(status.message);
			}
		} else {
			$('forgotpassword-form').replace(status.form);

			this.displayForgotPasswordSuccess();
			this.init();
		}
	},



	/**
	 * This method is called by the onLoggedOut hook. Sends the request to load the relogin form
	 *
	 * @method	onLoggedOutAuto
	 * @param	{Ajax.Response}	response
	 */
	onLoggedOutAuto: function(response) {
		var url		= Todoyu.getUrl('loginpage', 'ext');

		var options = {
			parameters: {
					action: 'reloginPopup'
			},
			onComplete: this.onLoggedOutFormLoaded.bind(this)
		};

		var idPopup	= 'reLoginPopup';
		var width	= 540;

		this.oldRequest = response.request;

		if( ! this.popup || ! this.popup.isVisible() ) {
			this.popup = Todoyu.Popups.open(idPopup, '[LLL:loginpage.ext.loginexpired.title]', width, url, options);
		}
	},



	/**
	 * Handle the loaded re-login form
	 *
	 * @method	onLoggedOutFormLoaded
	 */
	onLoggedOutFormLoaded: function() {
		$('login-form').on('submit', this.onReLoginFormSubmit.bind(this));
		this.observePasswordField();
		this.focusField();
		this.disableToggleSave();
	},



	/**
	 * Handler to re-submit login form
	 *
	 * @method	onReLoginFormSubmit
	 * @param	{Event}		event
	 */
	onReLoginFormSubmit: function(event) {
		if( this.checkFieldsNotEmpty() ) {
			this.onLoginRequest();

			var url		= Todoyu.getUrl('loginpage', 'ext');
			var	options	= {
				parameters: {
					action:	'login',
					username:	$F(this.fieldUsername),
					passhash:	this.getHashedPassword(),
					remain:		this.isRemainLoginChecked()
				},
				onComplete:	this.onReLoginResponse.bind(this)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handle the re-login request.
	 *
	 * If login was successful:
	 * 	- close the popup
	 * 	- fade the Notifications
	 * 	- resend the request
	 *
	 * If login was not successful
	 * 	- re-enable the form fields
	 * 	- display the error message in the form
	 * 	- preselect the password field
	 *
	 * @method	onReLoginResponse
	 * @param	{Ajax.Response}	response
	 */
	onReLoginResponse: function(response) {
		var status	= response.responseJSON;

		Todoyu.Hook.exec('loginpage.relogin.onreloginresponse', response);

		if( status.success ) {
			Todoyu.Popups.close('reLoginPopup');
			Todoyu.Notification.fadeAllNotes();
			this.popup = null;
			this.oldRequest.options.onComplete = this.oldRequest.options.backupOnComplete;
			Todoyu.send(this.oldRequest.url, this.oldRequest.options);
		} else {
			this.toggleLoginFields(true);
			this.displayLoginError(status.message);
			$(this.fieldPassword).select();
		}
	},



	/**
	 * checks if cookies are enabled in the browser
	 *
	 * @method	checkCookie
	 */
	checkCookie: function() {
		var cookieEnabled=(navigator.cookieEnabled)? true : false;

		if (typeof navigator.cookieEnabled=="undefined" && ! cookieEnabled){
			document.cookie="cookiecheck";
			cookieEnabled	= document.cookie.indexOf("cookiecheck") != -1;
		}

		if( !cookieEnabled ) {
			var url = Todoyu.getUrl('loginpage', 'ext');

			var options = {
				parameters: {
					action: 'nocookie'
				},
				onComplete: this.onCookieCheckComplete.bind(this)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * if cookies are disabled show the warning - message.
	 * Otherwise hide the whole form - field to prevent displaying an empty div
	 *
	 * @method	onCookieCheckComplete
	 * @param	{Ajax.Response}		response
	 */
	onCookieCheckComplete: function(response) {
		if( response.hasTodoyuError() ) {
			if( $('formElement-login-field-javascript') ) {
				$('formElement-login-field-javascript').select('.commenttext')[0].insert('<div id="loginform-cookiecheck">' + response.responseText + '</div>');
			}
		} else {
			$('formElement-login-field-javascript').hide();
		}
	}

};