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
 * Create a popup with select options
 */
Todoyu.DialogChoice = Class.create(Todoyu.Popup, {

	/**
	 * Option config
	 *
	 */
	config: {},

	/**
	 * Template instance
	 *
	 */
	template: null,



	/**
	 * Initialize popup
	 *
	 * @method	initialize
	 * @param	{Function}		$super
	 * @param	{Object}		[config]			Option configuration
	 * @param	{Object}		[popupOptions]
	 */
	initialize: function($super, config, popupOptions) {
		this.config				= config || {};
		popupOptions			= popupOptions || {};
		popupOptions.id			= popupOptions.id || 'dialogchoice';
		popupOptions.minWidth	= popupOptions.minWidth || 500;
		popupOptions.minHeight	= popupOptions.minHeight || 300;
		popupOptions.title		= popupOptions.title || 'Please select';

		$super(popupOptions);

		this.createContent();
		this.showCenter(true);
	},



	/**
	 * Create content for popup
	 *
	 */
	createContent: function() {
		this.initTemplate();

		var wrapper = new Element('div', {
			'class': 'wrapper'
		});

		if( this.config.description ) {
			wrapper.insert(new Element('p', {
				'class': 'description'
			}).update(this.config.description));
		}

		var options = new Element('div', {
			'class': 'choices'
		});
		wrapper.insert(options);

		this.config.options.each(function(option){
			options.insert(this.createOption(option));
		}, this);

		wrapper.on('click', 'button', this.onButtonClick.bind(this));

		this.insertElement(wrapper);
	},



	/**
	 * Handle button clicks
	 *
	 * @method	onButtonClick
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onButtonClick: function(event, element) {
		var selection	= element.id.split('-').last();

		this.onSelect(selection);
		this.close();
	},



	/**
	 * Call the callback with selection on data
	 *
	 * @method	onSelect
	 * @param	{String}	selection
	 */
	onSelect: function(selection) {
		if( this.config.onSelect ) {
			this.config.onSelect(selection, this.config.data);
		}

		this.close();
	},



	/**
	 * Create an option based on the template
	 *
	 * @method	createOption
	 * @param	{Object}	option
	 * @return	{String}
	 */
	createOption: function(option) {
		return this.template.evaluate(option);
	},



	/**
	 * Initialize template
	 */
	initTemplate: function() {
		this.template = new Template(
			'<div class="choice">' +
				'<div class="boxButton">' +
					'<button id="' + this.element.id + '-#{id}" class="button">' +
						'<span class="label">#{button}</span>' +
					'</button>' +
				'</div>' +
				'<div class="boxLabel">#{label}</div>' +
			'</div>');
	}

});