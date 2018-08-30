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
 * Loader box
 * Shows a message and a spinner to show that an action is in progress
 *
 * @class		LoaderBox
 * @namespace	Todoyu
 */
Todoyu.LoaderBox = Class.create({

	/**
	 * Name of the box
	 */
	name: '',

	/**
	 * Config
	 */
	config: {
		block:	true,
		show:	false
	},



	/**
	 * Loader box element
	 * @property	box
	 * @type		Element
	 */
	box: null,

	/**
	 * Screen blocker element
	 * @property	screenBlock
	 * @type		Element
	 */
	screenBlock: null,



	/**
	 * Initialize
	 *
	 * @method	initialize
	 * @param	{String}	name
	 * @param	{Object}	config
	 */
	initialize: function(name, config) {
		this.name = name;

		if( config ) {
			this.config	= $H(this.config).merge(config).toObject();
		}

		this.build();

		if( this.config.title ) {
			this.setTitle(this.config.title);
		}

		if( this.config.text ) {
			this.setText(this.config.text);
		}

		if( this.config.show ) {
			this.show();
		} else {
			this.hide();
		}
	},



	/**
	 * Remove HTML of the box
	 *
	 * @method	destroy
	 */
	destroy: function() {
		if( this.box ) {
			this.hide();
			this.remove();
		}
	},



	/**
	 * Remove elements
	 *
	 * @method	remove
	 */
	remove: function() {
		this.box.remove();
		this.screenBlock.remove();
	},



	/**
	 * Set text
	 *
	 * @method	setText
	 * @param	{String}	text
	 */
	setText: function(text) {
		this.box.down('.content').update(text);
	},



	/**
	 * Update content with a new element
	 * Like text, but inserts a HTML element
	 *
	 * @method	update
	 * @param 	{Element}	element
	 */
	update: function(element) {
		this.setText('');
		this.box.down('.content').insert(element);
	},



	/**
	 * Set box title
	 *
	 * @method	setTitle
	 * @param	{String}	title
	 */
	setTitle: function(title) {
		this.box.down('.title').update(title);
	},



	/**
	 * Show the loader box with a message
	 *
	 * @method	show
	 * @param	{String}	text
	 * @param	{Boolean}	block
	 */
	show: function(text, block) {
		if( text ) {
			this.setText(text);
		}

		if( this.config.block || block ) {
			this.showBlock();
		}

		this.center();
		this.box.show();
	},



	/**
	 * Hide the loader box
	 *
	 * @method	hide
	 */
	hide: function() {
		this.box.hide();
		this.hideBlock();
	},



	/**
	 * Build HTML structure
	 *
	 * @method	build
	 */
	build: function() {
		this.buildBox();
		this.buildScreenBlock();
	},



	/**
	 * Build the loader box with its sub elements
	 *
	 * @method	buildBox
	 */
	buildBox: function() {
		if( ! this.box ) {
			this.box = new Element('div',{
				id:		'loader-box-' + this.name,
				style:	'display:none',
				'class':'loaderBox'
			});

			this.box.insert(new Element('div', {
				'class': 'title'
			}).update('[LLL:core.global.loaderBox.title]'));

			this.box.insert(new Element('img', {
				'class': 	'spinner',
				src: 		'core/asset/img/ajax-loader-large.gif'
			}));

			this.box.insert(new Element('div', {
				'class': 'content'
			}));

			document.body.insert(this.box);
		}
	},



	/**
	 * Build screen blocker
	 *
	 * @method	buildScreenBlock
	 */
	buildScreenBlock: function() {
		this.screenBlock	= new Element('div', {
			id:	'loader-box-screen-block'
		});

		document.body.insert(this.screenBlock);
	},



	/**
	 * Center the loader box on the screen
	 *
	 * @method	_center
	 */
	center: function() {
		Todoyu.Ui.centerElement(this.box);
	},



	/**
	 * Show the screen blocker
	 *
	 * @method	_showScreenBlock
	 */
	showBlock: function() {
		this.screenBlock.show();
	},



	/**
	 * Hide blocker
	 *
	 * @method	hideBlock
	 */
	hideBlock: function() {
		this.screenBlock.hide();
	}

});