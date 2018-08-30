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
 * JS - validation for form-elements
 *
 * @class		FormValidator
 * @namespace	Todoyu
 */
Todoyu.FormValidator = {

	/**
	 * @property	validators
	 * @type		Object
	 */
	validators: {},

	/**
	 * Reference to form helper
	 *
	 * @property	ext
	 * @type		Object
	 */
	form: Todoyu.Form,



	/**
	 * @method	initField
	 * @param	{String}		fieldID
	 */
	initField: function(fieldID) {
		if($(fieldID)) {
			this.initValidators(fieldID);
			this.initObserver(fieldID);
		}
	},



	/**
	 * @method	addValidator
	 * @param	{String}	fieldID
	 * @param	{Function}	validatorMethod
	 */
	addValidator: function(fieldID, validatorMethod) {
		this.validators[fieldID].validatorMethods.push(validatorMethod);
	},



	/**
	 * @method	validate
	 * @param	{String}	fieldID
	 */
	validate: function(fieldID) {
		this.validators[fieldID].hasError = false;
		this.validators[fieldID].validatorMethods.each(function(element) {
			if(this[element]) {
				this[element](fieldID);
			} else {
				Todoyu.callUserFunction(element, fieldID);
			}
		}.bind(this));
	},



	/**
	 * @method	initValidators
	 * @param	{String}		fieldID
	 */
	initValidators:function (fieldID) {
		if ( !this.validators[fieldID] ) {
			this.validators[fieldID] = {
				validatorMethods: [],
				observer: null,
				hasError: false,
				msg: ''
			}
		}
	},



	/**
	 * Stop all validation events of object
	 *
	 * @method	initObserver
	 * @param	{String}		fieldID
	 */
	initObserver: function(fieldID) {
		if( this.validators[fieldID].observer !== null) {
			this.validators[fieldID].observer.stop();
		}

		this.validators[fieldID].observer = $(fieldID).on('change', this.validate.bind(this, fieldID));
	},



	/**
	 * @method	isNumeric
	 * @param	{String}	fieldID
	 */
	isNumeric: function(fieldID) {
		var value = $(fieldID).getValue();

		var error = this.getError(fieldID, !Todoyu.Number.isNumeric(value));

		this.form.setFieldErrorStatus($(fieldID), error);

		if( error ) {
			this.addErrorMessage(fieldID, '[LLL:core.form.field.hasError]');
		}
	},



	/**
	 * @method	isNotZero
	 * @param	{String}		fieldID
	 */
	isNotZero: function(fieldID) {
		var error = this.getError(fieldID, !(Todoyu.Number.intval($(fieldID).getValue()) > 0));

		this.form.setFieldErrorStatus($(fieldID), error);

		if( error ) {
			this.addErrorMessage(fieldID, '[LLL:core.form.field.hasError]');
		}
	},



	/**
	 * @method	getError
	 * @param	{String}		fieldID
	 * @param	{Boolean}		errorFromValidation
	 */
	getError: function(fieldID, errorFromValidation) {
		var error = this.validators[fieldID].hasError;

		error = error || errorFromValidation;

		this.validators[fieldID].hasError = error;

		return error;
	},



	/**
	 * @method	addErrorMessage
	 * @param	{String}		fieldID
	 * @param	{String}		msg
	 */
	addErrorMessage: function(fieldID, msg) {
		this.addMessageElement(fieldID, 'errorMessage', msg);
	},



	/**
	 * @method	addWarningMessage
	 * @param	{String}		fieldID
	 * @param	{String}		msg
	 * @param	{Number}		positionRight
	 */
	addWarningMessage: function(fieldID, msg, positionRight) {
		var element = this.addMessageElement(fieldID, 'warningMessage', msg);

		if(positionRight) {
			this.positionRight(element);
		}
	},



	/**
	 * @method	removeWarningMessage
	 * @param	{String}	fieldID
	 */
	removeWarningMessage: function(fieldID) {
		var element = this.getMessageElement(fieldID, 'warningMessage');

		if( element ) {
			element.remove();
		}
	},



	/**
	 * Relative positioning of Warning Message
	 *
	 * @method	positionRight
	 * @param	{Object}	element
	 */
	positionRight: function(element) {
		element.style.position	= 'absolute';
		element.style.zIndex	= 1;

		var input = element.previous(':input');
		var offset = element.positionedOffset();

		element.style.left = (offset.left + input.getWidth() + 20) + 'px';
		element.style.top = (offset.top - input.getHeight()) + 'px';
	},



	/**
	 * Add Html element containing the error/warning Message
	 *
	 * @method	addMessageElement
	 * @param	{String}		fieldID
	 * @param	{String}	htmlClassName
	 * @param	{String}	msg
	 */
	addMessageElement: function(fieldID, htmlClassName, msg) {
		var errorElement = this.getMessageElement(fieldID, htmlClassName);

		if( errorElement ) {
			errorElement.remove();
		}

		var element = new Element('div');
		element.addClassName(htmlClassName);
		element.innerHTML = msg;

		$(fieldID).up('.fElement').down('.clear').insert({before: element});

		return element;
	},



	/**
	 *
	 * @method	getErrorElement
	 * @param	{String}	fieldID
	 * @param	{String}	htmlClassName
	 * @returns	{*|HTMLElement}
	 */
	getMessageElement: function (fieldID, htmlClassName) {
		return $(fieldID).up('.fElement').down('.' + htmlClassName);
	}
};