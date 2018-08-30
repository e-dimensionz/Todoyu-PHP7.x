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
 * @module
 */

/**
 * Todoyu popup
 *
 * @namespace	Todoyu
 * @class
 * @see			http://prototype-window.xilinus.com/documentation.html
 */
Todoyu.Popup = Class.create(Window, {

	/**
	 * Closing status
	 */
	isClosing:		false,

	/**
	 * Close was forced by code
	 */
	forcedClose:	false,

	/**
	 * ESC key event handler for popup closing
	 */
	escBodyHandler:	null,



	/**
	 * Default todoyu options for window
	 * @var	{Object}
	 */
	todoyuOptions: {
		className:			'dialog',
		resizable:			true,
		closable:			true,
		minimizable:		false,
		maximizable:		false,
		draggable:			false,
		zIndex:				2000,
		recenterAuto:		false,
		hideEffect:			Element.hide,
		showEffect:			Element.show,
		effectOptions:		null,
		destroyOnClose:		true,
		requestOptions:		{}
	},



	/**
	 * Constructor
	 * Handle contentUrl, content and element parameter
	 *
	 * @constructor
	 * @param	{Function}	$super		Window.initialize
	 * @param	{Object}	options
	 */
	initialize: function($super, options) {
		this.todoyuOptions.closeCallback	= this.closeCallback.bind(this);
		options = $H(this.todoyuOptions).merge(options).toObject();

		if( ! options.requestOptions.parameters ) {
			options.requestOptions.parameters	= {};
		}

		if( ! options.requestOptions.parameters.area ) {
			options.requestOptions.parameters.area = Todoyu.getArea();
		}

		$super(options);

		if( this.options.contentUrl ) {
			this.addOnCompleteWrap();
			this.setAjaxContent(this.options.contentUrl, this.options.requestOptions, false, false);
		} else if( this.options.content ) {
			this.setHTMLContent(this.options.content, true);
		} else if( this.options.element ) {
			this.insertElement(this.options.element);
		}

		Todoyu.Popups.setPopup(this.options.id, this);
		this.installObserver();
	},



	/**
	 * Add internal onComplete wrapper to give popup instance as second parameter
	 *
	 * @method	addOnCompleteWrap
	 */
	addOnCompleteWrap: function() {
			// Assert that request options exists
		this.options.requestOptions				= this.options.requestOptions || {};
			// Assert that onComplete exists
		this.options.requestOptions.onComplete	= this.options.requestOptions.onComplete || Prototype.emptyFunction;
			// Wrap onComplete with internal onComplete
		this.options.requestOptions.onComplete	= this.options.requestOptions.onComplete.wrap(this.onComplete.bind(this));
	},



	/**
	 * Internal onComplete handler
	 *
	 * @method	onComplete
	 * @param	{Function}		originalOnComplete
	 * @param	{Ajax.Response}	response
	 */
	onComplete: function(originalOnComplete, response) {
		originalOnComplete(response, this);
	},



	/**
	 * Get ID of the popup
	 *
	 * @method	getPopupID
	 * @return	{String}
	 */
	getPopupID: function() {
		return this.options.id;
	},



	/**
	 * Install observers
	 *
	 * @method	installObserver
	 */
	installObserver: function() {
		this.escBodyHandler = document.on('keyup', this.onEscUp.bind(this));

		Windows.addObserver({
			onDestroy: this.onDestroy.bind(this)
		});
	},



	/**
	 * Custom keyup handler - close last opened popup on [ESC] key up
	 *
	 * @method	onEscUp
	 * @param	{Event}		event
	 */
	onEscUp: function(event) {
		if( event.keyCode == 27  ) {
			this.close();
			this.escBodyHandler.stop();
		}
	},



	/**
	 * Insert a content element from DOM
	 *
	 * @method	insertElement
	 * @param	{Element}	element
	 */
	insertElement: function(element) {
		this.setContent(element, true, true);
	},



	/**
	 * Set html content. Evaluate scripts
	 *
	 * @method	setHTMLContent
	 * @param	{Function}	$super			Window.setHTMLContent
	 * @param	{String}	html
	 * @param	{Boolean}	evalScripts
	 */
	setHTMLContent: function($super, html, evalScripts) {
		$super(html);

		if( evalScripts !== false ) {
			html.evalScripts();
		}
	},



	/**
	 * Destroy handler
	 *
	 * @method	onDestroy
	 * @param	{String}		eventName
	 * @param	{Todoyu.Popup}	popup
	 */
	onDestroy: function(eventName, popup) {
		Todoyu.Popups.onDestroy(popup);
	},



	/**
	 * Wrapper for close method
	 * Prevent close callback loops
	 *
	 * @method	close
	 * @param	{Function}	[$super]	Parent close function: Window.close
	 * @param	{Boolean}	[forced]	Close was forced by code, not by an event of the window
	 */
	close: function($super, forced) {
		if( forced ) {
			this.forcedClose = true;
		}

		if( ! this.isClosing ) {
			this.isClosing = true;

			Todoyu.Ui.closeRTE(this.content);

			$super();
		}
	},



	/**
	 * Close callback
	 *
	 * @method	closeCallback
	 * @param	{Todoyu.Popup}		popup
	 * @return	Boolean				True destroys the window
	 */
	closeCallback: function(popup) {
		if( ! this.forcedClose ) {
			var button	= this.content.down('button.cancelButton');
			if( button ) {
				this.isClosing = true;
				Todoyu.Event.fireEvent(button, 'click');
			}
		}

		return true;
	}

});