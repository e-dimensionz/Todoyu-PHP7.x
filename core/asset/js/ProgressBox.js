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
 * Progress box
 * Show loader box with progress bar and message
 *
 * Options:
 *  - See Todoyu.LoaderBox
 *  - total: total count of iterations
 *  - onCancel: Handler when user clicks on cancel button (which only exists if this property is set)
 *  - onComplete: Handler when iterations are completed
 */
Todoyu.ProgressBox = Class.create(Todoyu.LoaderBox, {

	/**
	 * Progress counter
	 */
	current: 0,



	/**
	 * Initialize progress box
	 *
	 * @method	initialize
	 * @param	{Function}	$super		Parent constructor: Todoyu.LoaderBox.initialize
	 * @param	{String}	name
	 * @param	{Object}	config
	 */
	initialize: function($super, name, config) {
		$super(name, config);

		this.buildProgress();

		if( this.config.message ) {
			this.setProgressMessage(this.config.message);
		}
	},



	/**
	 * Build progress box elements
	 *
	 * @method	buildProgress
	 */
	buildProgress: function() {
		var wrap 	= new Element('div');
		var label	= new Element('div', {
			'class': 'message'
		});
		var bar		= new Element('div', {
			'class': 'bar'
		});
		var progress= new Element('div', {
			'class': 'progress'
		}).setStyle({
			width: 0
	 	});

		bar.insert(progress);
		wrap.insert(label);
		wrap.insert(bar);

		if( this.config.onCancel ) {
			var button	= Todoyu.Ui.buildButton('progressbox-cancel', 'cancelProgress', '[LLL:gantt.ext.progress.cancel]', this.onCancel.bind(this));

			wrap.insert(button);
		}

		this.update(wrap);
		this.box.addClassName('progressBox');
	},



	/**
	 * Update progress
	 * - Set message
	 * - Move progress bar
	 *
	 * @method	updateProgress
	 * @param	{Number}	count
	 * @param	{String}	message
	 */
	updateProgress: function(count, message) {
		this.current	= count;

		this.setBarWidth(count);
		this.setProgressMessage(message);

		if( this.current >= this.config.total ) {
			this.onComplete();
		}
	},



	/**
	 * Set width of bar with new counter
	 *
	 * @method	setBarWidth
	 * @param	{Number}	count
	 */
	setBarWidth: function(count) {
		var barWidth	= this.box.down('.bar').getWidth();
		var progWidth	= (count/this.config.total)*barWidth;

		this.box.down('.progress').setStyle({
			width: progWidth + 'px'
		});
	},



	/**
	 * Set progress message
	 *
	 * @method	setProgressMessage
	 * @param	{String}	message
	 */
	setProgressMessage: function(message) {
		this.box.down('.message').update(message);
	},



	/**
	 * Handler when cancel clicked
	 *
	 * @method	onCancel
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onCancel: function(event, element) {
		this.destroy();

		if( this.config.onCancel ) {
			this.config.onCancel(this.current);
		}
	},



	/**
	 * Handler when progress reached total count
	 *
	 * @method	onComplete
	 */
	onComplete: function() {
		this.destroy();

		if( this.config.onComplete ) {
			this.config.onComplete(this.current);
		}
	}

});