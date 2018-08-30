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
 * Wizard with multiple steps in a popup
 * Only one wizard can be open at the time
 */
Todoyu.Wizard = {

	/**
	 * Active wizard info
	 */
	wizard: null,



	/**
	 * Open a wizard
	 *
	 * @method	open
	 * @param	{String}	wizardName
	 * @param	{Function}	[onLoadCallback]
	 */
	open: function(wizardName, onLoadCallback) {
		onLoadCallback	= onLoadCallback || Prototype.emptyFunction;

		var url		= Todoyu.getUrl('core', 'wizard');
		var options	= {
			parameters: {
				action: 'load',
				wizard: wizardName
			},
			onComplete: this.onOpened.bind(this)
		};

		this.wizard = {
			name: 		wizardName,
			popup: 		Todoyu.Popups.open('wizard' + wizardName, 'Wizard', 900, url, options),
			callback: 	onLoadCallback
		};
	},



	/**
	 * Handler when wizard was opened
	 *
	 * @method	onOpened
	 * @param	{Ajax.Response}	response
	 */
	onOpened: function(response) {
		this.onConfigUpdate(response);
	},



	/**
	 * Go one step back in wizard
	 *
	 * @method	back
	 */
	back: function() {
		this.submit('back');
	},



	/**
	 * Go to next step in wizard
	 *
	 * @method	next
	 */
	next: function() {
		this.submit('next');
	},



	/**
	 * Submit the wizard form. Set direction if provided
	 *
	 * @method	submit
	 * @param	{String}	[direction]
	 * @param	{String}	[callback]
	 */
	submit: function(direction, callback) {
		if( typeof direction === 'string' ) {
			this.setDirection(direction);
		}

		callback	= callback || Prototype.emptyFunction;

		Todoyu.Ui.closeRTE('wizard-form');

		this.getForm().request({
			onComplete: this.onSubmitted.bind(this, callback),
			area:	Todoyu.getArea()
		});
	},



	/**
	 * Handler when form was submitted
	 *
	 * @method	onSubmitted
	 * @param	{Ajax.Response}	response
	 */
	onSubmitted: function(callback, response) {
		$('wizard').replace(response.responseText);
		this.onConfigUpdate(response);
		callback(response);
	},



	/**
	 * Handler when wizard was loaded (opened or submitted)
	 *
	 * @method	onLoaded
	 * @param	{Ajax.Response}	response
	 */
	onConfigUpdate: function(response) {
		this.wizard.popup.setTitle(response.getTodoyuHeader('label'));
		this.wizard.callback(response, this.wizard);
	},



	/**
	 * Set direction for next step
	 *
	 * @method	setDirection
	 * @param	{String}	direction
	 */
	setDirection: function(direction) {
		$('wizard-direction').value = direction;
	},



	/**
	 * Go to a wizard step
	 *
	 * @method	goToStep
	 * @param	{String}	step
	 */
	goToStep: function(step) {
		this.submit(step);
	},



	/**
	 * Get current step
	 *
	 * @method	getStepName
	 */
	getStepName: function() {
		return $F('wizard-step');
	},



	/**
	 * Get name of the wizard
	 *
	 * @method	getWizardName
	 */
	getWizardName: function() {
		return this.wizard.name;
	},



	/**
	 * Get form element of the wizard
	 *
	 * @method	getForm
	 */
	getForm: function() {
		return $('wizard-form');
	},



	/**
	 * Set no save mode. Wizard just goes to requested direction without validation or saving data
	 *
	 * @method	setNoSave
	 * @param	{Boolean}	value
	 */
	setNoSave: function(value) {
		$('wizard-nosave').value = value === false ? 0 : 1;
	},



	/**
	 * Close wizard
	 *
	 * @method	close
	 * @param	{Boolean}	[noConfirm]
	 */
	close: function(noConfirm) {
		noConfirm	= noConfirm === true;

		if( noConfirm || confirm('[LLL:core.global.wizard.close.confirm]') ) {
			Todoyu.Popups.close('wizard' + this.wizard.name);
		}
	}

};