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
 *	Todoyu event functions
 *
 * @class		Event
 * @namespace	Todoyu
 * @type		{Object}
 */
Todoyu.Event = {

	/**
	 * Fire event
	 *
	 * @method	fireEvent
	 * @param	{Element}		element
	 * @param	{String}		eventType e.g. 'click'
	 * @return	{String|Object}
	 */
	fireEvent: function(element, eventType, x, y){
		var evt;

		if( ! document.createEvent ){
				// Dispatch for IE 8 (9 works as normal browser)
			evt = document.createEventObject();

			return element.fireEvent('on' + eventType, evt);
		} else {
				// Dispatch for firefox + others
			evt = document.createEvent('HTMLEvents');
			evt.initEvent(eventType, true, true); // event type, bubbling, cancelable

			return ! element.dispatchEvent(evt);
		}
	},



	/**
	 * Observe zoom event in browser with callback function
	 * Ported to prototype from http://mlntn.com/2008/12/11/javascript-jquery-zoom-event-plugin/
	 *
	 * @method	observeZoom
	 * @param	{Function}	callback
	 */
	observeZoom: function(callback) {
			// Observe mouse wheel
		document.on('mousewheel', function(e){
			if( e.ctrlKey ) {
				callback();
			}
		});
		document.on('DOMMouseScroll', function(e){
			if( e.ctrlKey ) {
				callback();
			}
		});

			// Observe zoom keys
		document.on('keydown', function(e) {
			switch (true) {
				case Prototype.Browser.Gecko || Prototype.Browser.IE :
					if( e.ctrlKey && (
						e.which === 187 ||
						e.which === 189 ||
						e.which === 107 ||
						e.which === 109 ||
						e.which === 96  ||
						e.which === 48
					) ) {
						callback();
					}
				break;

				case Prototype.Browser.Opera :
					if(
						e.which === 43 ||
						e.which === 45 ||
						e.which === 42 ||
						(e.ctrlKey && e.which === 48)
						) {
						callback();
					}
				break;

				case Prototype.Browser.WebKit :
					if( e.metaKey && (e.charCode === 43 || e.charCode === 45) ) {
						callback();
					}
				break;
			}
		});
	}

};