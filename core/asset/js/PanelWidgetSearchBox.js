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
 * Simple search box panel widget
 *
 * @type	{Object}
 */
Todoyu.PanelWidget.SearchBox = Class.create({

	/**
	 * Config
	 */
	config: {
		searchDelay: 0.5
	},

	/**
	 * Text input
	 */
	textInput: null,

	/**
	 * Clear button
	 */
	clearButton: null,

	/**
	 * Form
	 */
	form: null,

	/**
	 * Search timeout
	 */
	searchTimeout: null,

	/**
	 * Keys which are ignored on entering
	 * No request will be fired
	 */
	ignoreKeyInputs: [
		Event.KEY_RETURN,
		32 // Space
	],



	/**
	 * Initialize
	 *
	 * @param config
	 */
	initialize: function(config) {
		this.config	= $H(this.config).merge(config||{}).toObject();

		var idTextInput		= this.config.idTextInput 	? this.config.idTextInput 	: 'panelwidget-' +  this.config.id + '-searchword';
		var idClearButton	= this.config.idClearButton	? this.config.idClearButton : 'panelwidget-' +  this.config.id + '-clear';
		var idForm			= this.config.idForm		? this.config.idForm 		: 'panelwidget-' +  this.config.id + '-form';

		this.textInput	= $(idTextInput);
		this.clearButton= $(idClearButton);
		this.form		= $(idForm);

		this.installObservers();
		this.toggleClearButton();
	},



	/**
	 * Install observers
	 *
	 */
	installObservers: function() {
		this.textInput.on('keyup', this.onTextInputKeyUp.bind(this));
		this.clearButton.on('click', this.onClearButtonClick.bind(this));
		this.form.on('submit', this.onFormSubmit.bind(this));
	},



	/**
	 * Handle key up event in text input
	 * Ignore space and return and start delayed search
	 *
	 * @param	{Event}		event
	 */
	onTextInputKeyUp: function(event) {
		this.toggleClearButton();


		if( !this.ignoreKeyInputs.include(event.keyCode) ) {
			this.startTimeout();
		}
	},



	/**
	 * Handle clear button click
	 *
	 * @param	{Event}		event
	 */
	onClearButtonClick: function(event) {
		this.clear();
	},



	/**
	 * Handle form submit by pressing return
	 *
	 * @param	{Event}		event
	 */
	onFormSubmit: function(event) {
		event.stop();
		this.search();
	},



	/**
	 * Clear current timeout if set
	 *
	 * @method	clearTimeout
	 */
	clearTimeout: function() {
		if( this.searchTimeout ) {
			window.clearTimeout(this.searchTimeout);
			this.searchTimeout = null;
		}
	},



	/**
	 * Start new search timeout
	 * Clear old timeout
	 */
	startTimeout: function() {
		this.clearTimeout();

		this.searchTimeout = this.search.bind(this).delay(this.config.searchDelay);
	},



	/**
	 * Clear input field
	 *
	 * @method	clear
	 */
	clear: function() {
		this.textInput.clear();
		this.toggleClearButton();
		this.search();
	},



	/**
	 * Execute search request
	 *
	 * @method	search
	 */
	search: function() {
		console.log('No search method implemented in derived object');
	},



	/**
	 * Get current search value
	 *
	 * @method	getSearchText
	 * @return	{String}
	 */
	getSearchText: function() {
		return $F(this.textInput).strip();
	},



	/**
	 * Toggle clear button. Only visible if search text entered
	 *
	 * @method	toggleClearButton
	 */
	toggleClearButton: function() {
		if( this.getSearchText() === '' ) {
			this.clearButton.hide();
		} else {
			this.clearButton.show();
		}
	}

});