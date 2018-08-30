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


if( ! TodoyuInstaller ) {
	var TodoyuInstaller = {};
}


/**
 * Todoyu installer namespace
 *
 */
TodoyuInstaller = {

	/**
	 * Focus first form field
	 *
	 * @method	focusFirstFormField
	 * @param	{Element}	form
	 */
	focusFirstFormField: function(form) {
		if( form ) {
			var field = form.select('input[type!=hidden]', 'select', 'textarea', 'button').first();
			if( field && field.visible() ) {
				field.focus();
			}
		}
	},



	/**
	 * Hide the next button
	 *
	 * @method	hideButton
	 */
	hideButton: function() {
		$('next').hide();
	},



	/**
	 * Show the next button
	 *
	 * @method	showButton
	 */
	showButton: function() {
		$('next').show();
	},



	/**
	 * Submit locale selection
	 *
	 * @method	selectLocale
	 * @param	{String}	locale
	 */
	selectLocale: function(locale) {
		$('locale').value = locale;
		$$('form').first().submit()
	},


	/**
	 * Disable given text box if selected value == 0
	 *
	 * @method	disableTextBox
	 * @param	{Element}	selector
	 */
	disableTextBox: function(selector)	{
		textbox = document.getElementById('database_new');
		textbox.disabled = selector.options[selector.selectedIndex].value === '0';
	},



	/**
	 * Check database selection / declaration of new database to be created
	 *
	 * @method	checkDbSelect
	 */
	checkDbSelect: function() {
		var newDbName	= $F('database_new').strip();

		if( newDbName !== '' ) {
				// New DB name specified? deactivate selector
			$('database').selectedIndex	= 0;
			$('database').disabled	= true;

				// Make sure there's no existing DB with that name
			$('error-newnameTaken').hide();
			$$('button').first().show();
			$$('#database option').each(function(dbOption){
				if ( dbOption.value == newDbName) {
					$('error-newnameTaken').show();
					$('next').hide();
				}
			});
		} else {
			$('database').disabled	= false;
		}
	},




	/**
	 * Ensure password and it's repetition are identical
	 *
	 * @method	validateAdminAccountData
	 */
	validateAdminAccountData: function() {
		var companyOk	= this.validateCompanyName();
		var firstnameOk	= this.validateFirstname();
		var lastnameOk	= this.validateLastname();
		var passwordOk	= this.validatePassword();

		var submitButton= $$('button')[0];

		if ( companyOk && firstnameOk && lastnameOk && passwordOk ) {
			submitButton.show();
		} else {
			submitButton.hide();
		}
	},


	/**
	 * Set CSS class of field label red / default depending on given validation result
	 *
	 * @method	updateLabelValidationClass
	 * @param	{String}		labelField
	 * @param	{Boolean}		isOk
	 */
	updateLabelValidationClass: function(labelField, isOk) {
		if ( isOk ) {
			$(labelField).removeClassName('redLabel');
			$(labelField).removeClassName('redLabel');
		} else {
			$(labelField).addClassName('redLabel');
			$(labelField).addClassName('redLabel');
		}
	},



	/**
	 * Validate input for company name
	 *
	 * @method	validateCompanyName
	 * @return	{Boolean}
	 */
	validateCompanyName: function() {
		var isOk	= $F('company').replace(' ', '').length > 0;
		this.updateLabelValidationClass('labelCompany', isOk);

		return isOk;
	},



	/**
	 * Validate input for firstname
	 *
	 * @method	validateFirstname
	 * @return  {Boolean}
	 */
	validateFirstname: function() {
		var isOk	= $F('firstname').replace(' ', '').length > 0;
		this.updateLabelValidationClass('labelFirstname', isOk);

		return isOk;
	},



	/**
	 * Validate input for lastname
	 *
	 * @method	validateLastname
	 * @return	{Boolean}
	 */
	validateLastname: function() {
		var isOk	= $F('lastname').replace(' ', '').length > 0;
		this.updateLabelValidationClass('labelLastname', isOk);

		return isOk;
	},



	/**
	 * Validate password to be long enough and both repetitions being identic
	 *
	 * @method	validatePassword
	 * @return	{Boolean}
	 */
	validatePassword: function() {
		var areIdentic	= $F('password') == $F('password_confirm');
		var longEnough	= $F('password').length >= 5;

		var isOk	= areIdentic && longEnough;

		this.updateLabelValidationClass('labelPassword',		isOk);
		this.updateLabelValidationClass('labelPasswordConfirm',	isOk);

		return isOk;
	}

};