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
 *	Extend element prototype
 */
Element.addMethods({

	/**
	 * Replace a class name on an element
	 *
	 * @method	relaceClassName
	 * @param	{Element}	element
	 * @param	{String}	className
	 * @param	{String}	replacement
	 */
	replaceClassName: function(element, className, replacement){
		if (!(element = $(element))) {return;}
		return element.removeClassName(className).addClassName(replacement);
	},



	/**
	 * Get class names of an element
	 *
	 * @getClassNames
	 * @param	{Element}	element
	 */
	getClassNames: function(element) {
		return $w(element.className);
	},



	/**
	 * Scroll to an element but consider the fixed header
	 *
	 * @method	scrollToElement
	 * @param	{Element}	element
	 */
	scrollToElement: function(element) {
		Todoyu.Ui.scrollToElement(element);

		return element;
	},



	/**
	 * Convert node to HTML string
	 *
	 * @method	toHTML
	 * @param	{Element}	element
	 * @return	{String}
	 */
	toHTML: function(element) {
		var dummy = new Element('div');
		dummy.insert(element);

		return dummy.innerHTML;
	}

});


/**
 * Extend AJAX response prototype
 */
Ajax.Response.addMethods({
	/**
	 * Get todoyu style http headers (prefixed by 'Todoyu-')
	 *
	 * @method	getTodoyuHeader
	 * @param	{String}		name
	 */
	getTodoyuHeader: function(name) {
		var header = this.getHeader('Todoyu-' + name);

		return header === null ? header : header.isJSON() ? header.evalJSON() : header;
	},



	/**
	 * Check whether a todoyu header was sent
	 *
	 * @method	hasTodoyuHeader
	 * @param	{String}	name
	 */
	hasTodoyuHeader: function(name) {
		return this.getTodoyuHeader(name) !== null;
	},



	/**
	 * Check whether todoyu error was sent
	 *
	 * @return	{Boolean}
	 */
	hasTodoyuError: function() {
		return this.getTodoyuHeader('error') == 1;
	},


	/**
	 * Get todoyu error message
	 *
	 * @return	{String}
	 */
	getTodoyuErrorMessage: function() {
		return this.getTodoyuHeader('errorMessage');
	},



	/**
	 * Check whether no access header was sent
	 *
	 * @return	{Boolean}
	 */
	hasNoAccess: function() {
		return this.getTodoyuHeader('noAccess') == 1;
	},



	/**
	 * Check whether notLoggedIn header was sent
	 *
	 * @return	{Boolean}
	 */
	isNotLoggedIn: function() {
		return this.getTodoyuHeader('notLoggedIn') == 1;
	},



	/**
	 * Check whether a PHP error header was sent
	 *
	 * @return	{Boolean}
	 */
	hasPhpError: function() {
		return this.getPhpError() !== null;
	},



	/**
	 * Get the PHP error header
	 *
	 * @return	{String}
	 */
	getPhpError: function() {
		return this.getTodoyuHeader('Php-Error');
	},



	/**
	 * Get number of AC result items
	 *
	 * @return	{Number}
	 */
	getNumAcElements: function() {
		return Todoyu.Number.intval(this.getTodoyuHeader('acElements'));
	},



	/**
	 * Check whether the result is an empty autocompleter result
	 *
	 * @return	{Boolean}
	 */
	isEmptyAcResult: function() {
		return this.getNumAcElements() === 0;
	}
});



/**
 * Add days to a date
 *
 * @method	addDays
 * @param	{Number}	days			Amount of day
 * @param	{Boolean}	[newDate]		Create a new date instead updating this one
 * @return	{Date}
 */
Date.prototype.addDays = function(days, newDate) {
	var date = newDate ? new Date(this) : this;

	date.setDate(this.getDate() + days);

	return date;
};



/**
 * Check whether current date is today
 *
 * @return	{Boolean}
 */
Date.prototype.isToday = function() {
	var today = new Date();

	return this.getFullYear() === today.getFullYear() && this.getMonth() === today.getMonth() && this.getDate() === today.getDate();
};



/**
 * Set date to week start (monday morning)
 *
 * @retrun	{Date}
 */
Date.prototype.setToWeekStart = function() {
	var day		= this.getDay();
	var shift	= (day+6)%7;

	this.addDays(-shift, false);
	this.setHours(0, 0, 0);

	return this;
};



/**
 * Set date to week end (sunday night)
 *
 * @return	{Date}
 */
Date.prototype.setToWeekEnd = function() {
	var day = this.getDay();

	this.addDays(7-day, false);
	this.setHours(23, 59, 59);

	return this;
};



/**
 * Add round method to number
 *
 * @method	round
 * @param	{Number}	[precision]
 */
Number.prototype.round = function(precision) {
	var factor	= Math.pow(10, precision || 0);
	return Math.round(factor * this)/factor;
};



/**
 * Array.sum()
 */
Array.prototype.sum = function(){
	for(var i=0,sum=0;i<this.length;sum+=this[i++]){}
	return sum;
};



/**
 * Array Remove - By John Resig (MIT Licensed)
 *
 * @param	{Number}	from
 * @param	{Number}	to
 * @returns {Number}
 */
Array.prototype.remove = function(from, to) {
	var rest = this.slice((to || from) + 1 || this.length);
	this.length = from < 0 ? this.length + from : from;
	return this.push.apply(this, rest);
};



/*
 * Original: http://adomas.org/javascript-mouse-wheel/
 * prototype extension by "Frank Monnerjahn" <themonnie@gmail.com>
 */
Object.extend(Event, {
	wheel: function (event){
		var delta = 0;
		if (!event) {
			event = window.event;
		}
		if( event.wheelDelta ) {
			delta = event.wheelDelta/120;
			if( window.opera ) {
				delta = -delta;
			}
		} else if (event.detail) { delta = -event.detail/3;	}
		return Math.round(delta); //Safari Round
	}
});