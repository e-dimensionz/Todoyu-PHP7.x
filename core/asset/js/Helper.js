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
 * General helper functions
 *
 * @class		Helper
 * @namespace	Todoyu
 */
Todoyu.Helper = {

	/**
	 * Convert value to Integer
	 *
	 * @method	intval
	 * @param	{String|Boolean|Number}		mixedvar
	 * @return	{Number}
	 * @deprecated
	 */
	intval: function(mixedvar) {
		return Todoyu.Number.intval(mixedvar);
	},



	/**
	 * Convert to 2-digit value (possibly add leading zero)
	 *
	 * @method	twoDigit
	 * @param	{String|Number}		number
	 * @return	{String}
	 * @deprecated
	 */
	twoDigit: function(number) {
		return Todoyu.String.twoDigit(number);
	},



	/**
	 * Get amount of lines in given string
	 *
	 * @method	countLines
	 * @param	{String}	multilineText
	 * @return	{Number}
	 * @deprecated
	 */
	countLines: function(multilineText) {
		return Todoyu.String.countLines(multilineText);
	},



	/**
	 * Toggle source of image
	 *
	 * @method	toggleImage
	 * @param	{String}		idImage
	 * @param	{String}		src1
	 * @param	{String}		src2
	 */
	toggleImage: function(idImage, src1, src2) {
		var image = $(idImage);

		if( image.src.indexOf(src1) === -1 ) {
			image.src = src1;
		} else {
			image.src = src2;
		}
	},



	/**
	 * Round with given precision
	 *
	 * @method	round
	 * @param	{Number}		value
	 * @param	{Number}	precision
	 * @return	{Number}
	 * @deprecated
	 */
	round: function(value, precision) {
		return Todoyu.Number.round(value, precision);
	},



	/**
	 * Uppercase the first character of every word in a string
	 *
	 * @method	ucwords
	 * @param	{String}	str
	 * @return	{String}
	 * @deprecated
	 */
	ucwords: function(str) {
		return Todoyu.String.ucwords(str);
	},



	/**
	 * Returns the internal translation table used by htmlspecialchars and htmlentities
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @method	get_html_translation_table
	 * @param	{String}	table
	 * @param	{String}	quote_style
	 * @deprecated
	 */
	get_html_translation_table:function(table, quote_style) {
		return Todoyu.String.get_html_translation_table(table, quote_style);
	},



	/**
	 * Convert all HTML entities to their applicable characters
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @method	html_entity_decode
	 * @param	{String}	string
	 * @param	{String}	quote_style
	 * @deprecated
	 */
	html_entity_decode:function(string, quote_style) {
		return Todoyu.String.html_entity_decode(string, quote_style);
	},



	/**
	 * Convert all applicable characters to HTML entities
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/htmlentities
	 * version: 1009.2513
	 *
	 * @method	htmlentities
	 * @param	{String}		string
	 * @param	{String}		quote_style
	 * @deprecated
	 */
	htmlentities:function(string, quote_style) {
		return Todoyu.String.htmlentities(string, quote_style);
	},



	/**
	 * Wraps buffer to selected number of characters using string break char
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @method	wordwrap
	 * @param	{String}		str
	 * @param	{Number}		int_width
	 * @param	{String}		str_break
	 * @param	{Boolean}		cut
	 * @return	{String}
	 * @deprecated
	 */
	wordwrap: function(str, int_width, str_break, cut) {
		return Todoyu.String.wordwrap(str, int_width, str_break, cut);
	},



	/**
	 * Wraps buffer to selected number of characters using string break char,
	 * while keeping HTML entities intact
	 *
	 * @method	wordwrapEntities
	 * @param	{String}		str
	 * @param	{Number}		int_width
	 * @param	{String}		str_break
	 * @param	{Boolean}		cut
	 * @return	{String}
	 * @deprecated
	 */
	wordwrapEntities: function(str, int_width, str_break, cut) {
		return Todoyu.String.wordwrapEntities(str, int_width, str_break, cut);
	},



	/**
	 * Fire event
	 *
	 * @method	fireEvent
	 * @param	{Element}		element
	 * @param	{String}		eventType e.g. 'click'
	 * @param	{Number}		x
	 * @param	{Number}		y
	 * @return	{String|Object}
	 * @deprecated
	 */
	fireEvent: function(element, eventType, x, y){
		return Todoyu.Event.fireEvent(element, eventType, x, y);
	},



	/**
	 * Replacement for default calendar close event
	 * Hide calendar and fire change event
	 *
	 * @method	onCalendarDateChanged
	 * @param	{Calendar}		calendar
	 */
	onCalendarDateChanged: function(calendar) {
			// Close calendar as in default handler
		calendar.hide();

			// Fire change event to inform all observers
		this.fireEvent(calendar.params.inputField, 'change');
	},



	/**
	 * Set element scrollTop, circumventing refresh bug in safari + chrome
	 *
	 * @method	setScrollTop
	 * @param	{Element}	element
	 * @param	{Number}	position
	 */
	setScrollTop: function(element, position) {
		element.scrollTop = position;

		if( Todoyu.Validate.isChrome() || Todoyu.Validate.isSafari() ) {
			this.onUpdateChromeSafariScrollTop(element.id, 0);
		}
	},



	/**
	 * Safari + Chrome workaround: defered window refresh to update after modification of scrollTop
	 *
	 * @method	onUpdateChromeSafariScrollTop
	 * @param	{String}	elementID
	 * @param	{Number}	step
	 */
	onUpdateChromeSafariScrollTop: function(elementID, step) {
		switch(step) {
			case 0:
			case 1:
				$(elementID).style.overflow = ( step == 0 ) ? 'scroll' : '';
				break;
			case 2:
			case 3:
				window.scrollBy(0,( step == 2 ) ? 1 : -1);
				break;
		}

		step++;
		if( step < 4 ) {
			this.onUpdateChromeSafariScrollTop.bind(this, elementID, step).defer();
		}
	},



	/**
	 * Get a key from a class with the specific prefix
	 *
	 * @method	getClassKey
	 * @param	{Element}		element
	 * @param	{String}		prefix
	 * @deprecated
	 */
	getClassKey: function(element, prefix) {
		return Todoyu.String.getClassKey(element, prefix);
	},



	/**
	 * Cron text to requested length
	 *
	 * @method	cropText
	 * @param	{String}		text
	 * @param	{Number=100}	length
	 * @param	{String=...}	append		Appendix
	 * @deprecated
	 */
	cropText: function(text, length, append) {
		return Todoyu.String.cropText(text,  length, append);
	},



	/**
	 * Clone an object using deep copy
	 * Borrowed from the highcharts prototype adapter
	 *
	 * @method	cloneObject
	 * @param	{Object}	originalObject
	 * @return	{Object}
	 */
	cloneObject: function(originalObject) {
		function doCopy(copy, original) {
			var value, key;

			for(key in original) {
				value = original[key];
				if( value && typeof value === 'object' && value.constructor !== Array && typeof value.nodeType !== 'number') {
					copy[key] = doCopy(copy[key] || {}, value); // copy
				} else {
					copy[key] = original[key];
				}
			}
			return copy;
		}

		return doCopy({}, originalObject)
	}

};