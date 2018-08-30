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
 * Timepicker helper for time inputs
 *
 * @class		TimePicker
 * @namespace	Todoyu
 * @constructor
 */
Todoyu.TimePicker = Class.create({

	/**
	 * @property	hour
	 * @type		Number
	 */
	hour:	0,

	/**
	 * @property	minute
	 * @type		Number
	 */
	minute:	0,

	/**
	 * Input element
	 * @property	element
	 * @type		Element
	 */
	element: null,

	/**
	 * Picker div
	 * @property	picker
	 * @type		Element
	 */
	picker:		null,

	/**
	 * Container hour
	 * @property	divHour
	 * @type		Element
	 */
	divHour:	null,

	/**
	 * Container minute
	 * @property	divMinute
	 * @type		Element
	 */
	divMinute:	null,

	/**
	 * Callback for body clicks
	 */
	bodyClickObserver:	null,



	/**
	 * Configuration
	 * @property	config
	 * @type		Object
	 */
	config: {
		height:			22,
		rangeHour:		[0,99],
		stepHour:		1,
		rangeMinute:	[0,55],
		stepMinute:		5
	},



	/**
	 * Constructor
	 *
	 * @method	initialize
	 * @param	{String}		idElement
	 * @param	{Object}		[config]
	 */
	initialize: function(idElement, config) {
		this.element= $(idElement);
		this.config	= $H(this.config).merge(config || {}).toObject();

		var dur = this._readDuration();

		if( dur.min % 5 !== 0 ) {
			this.config.stepMinute = 1;
			this.config.rangeMinute = [0,59];
		} else {
			this.config.stepMinute = 5;
			this.config.rangeMinute = [0,55];
		}

		this._build();
		this._observePicker();

		this.setHour(dur.hour);
		this.setMinute(dur.min);

		this.updateElement();

		this.show();
	},



	/**
	 * Show picker near the element
	 *
	 * @method	show
	 */
	show: function() {
		this._setPosition();
		this.picker.show();

			// Install outside clicks observer
		this.bodyClickObserver	= document.body.on('click', '', this._onBodyClick.bind(this));
	},



	/**
	 * Hide the picker
	 *
	 * @method	hide
	 */
	hide: function() {
		this.picker.hide();

		if( this.bodyClickObserver ) {
			this.bodyClickObserver.stop();
		}
	},



	/**
	 * Set hour and update scroll
	 *
	 * @method	setHour
	 * @param	{Number}		hour
	 */
	setHour: function(hour) {
		this.hour = this._keepInRange(hour, this.config.rangeHour);

		this.updateScroll();
	},



	/**
	 * Set minute and update scroll
	 *
	 * @method	setMinute
	 * @param	{Number}		minute
	 */
	setMinute: function(minute) {
		this.minute = this._keepInRange(minute, this.config.rangeMinute);
		this.updateScroll();
	},



	/**
	 * Update scroll of minute and hour
	 *
	 * @method	updateScroll
	 */
	updateScroll: function() {
		var newHourPos = this.hour * this.config.height * -1 + 2 * this.config.height - (2 * this.config.height);
		new Effect.Move(this.divHour, {
			'y': newHourPos,
			'mode': 'absolute',
			'duration': 0.3
		});

		var newMinPos = (this.minute/this.config.stepMinute) * this.config.height * -1 + 2 * this.config.height - (2 * this.config.height);

		new Effect.Move(this.divMinute, {
			'x': 25,
			'y': newMinPos,
			'mode': 'absolute',
			'duration': 0.3
		});

		this.updateElement.bind(this).delay(0.2);
	},



	/**
	 * Update current selection in element
	 *
	 * @method	updateElement
	 */
	updateElement: function() {
		this.element.value = this.hour + ':' + Todoyu.String.twoDigit(this.minute);
	},



	/**
	 * Set picker position near the element
	 *
	 * @private
	 * @method	_setPosition
	 */
	_setPosition: function() {
		var elOffset= this.element.cumulativeOffset();
		var elDim	= this.element.getDimensions();
		var dpHeight= this.picker.getHeight();
		var left	= elOffset.left + elDim.width + 1;
		var top		= elOffset.top + (elDim.height/2) - (dpHeight/2);

		this.picker.setStyle({
			'display':	'block',
			'left':		left + 'px',
			'top':		top + 'px'
		});
	},



	/**
	 * Make element id, prefixed with element id
	 *
	 * @private
	 * @method	_makeID
	 * @param	{String}		name
	 */
	_makeID: function(name) {
		return this.element.id + '-' + name;
	},



	/**
	 * Build picker HTML
	 *
	 * @private
	 * @method	_build
	 */
	_build: function() {
		this._remove();

		this.picker = new Element('div', {
			'id': this._makeID('durationpicker'),
			'class': 'dpPicker'
		});

		this.divHour = new Element('div', {
			'id': this._makeID('durationpicker-hour'),
			'class': 'dpHour dpCol'
		});

		this.divMinute = new Element('div', {
			'id': this._makeID('durationpicker-minute'),
			'class': 'dpMinute dpCol'
		});

		this.picker.insert(new Element('div', {
			'id': this._makeID('durationpicker-mask'),
			'class': 'dpMask'
		}));

		this._insertHours();
		this._insertMinute();

		this.picker.insert(this.divHour);
		this.picker.insert(this.divMinute);

		this.hide();

		$(document.body).insert(this.picker);
	},



	/**
	 * Insert hour elements
	 *
	 * @private
	 * @method	_insertHours
	 */
	_insertHours: function() {
		for(var i = this.config.rangeHour[0]; i <= this.config.rangeHour[1]; i += this.config.stepHour) {
			this.divHour.insert(new Element('div').update(i));
		}
	},



	/**
	 * Insert minute elements
	 *
	 * @private
	 * @method	_insertMinute
	 */
	_insertMinute: function() {
		for(var i = this.config.rangeMinute[0]; i <= this.config.rangeMinute[1]; i += this.config.stepMinute) {
			this.divMinute.insert(new Element('div').update(Todoyu.String.twoDigit(i)));
		}
	},



	/**
	 * Remove picker from document
	 *
	 * @private
	 * @method	_remove
	 */
	_remove: function() {
		var idPicker = this._makeID('durationpicker');

		if( Todoyu.exists(idPicker) ) {
			$(idPicker).remove();
		}
	},



	/**
	 * Observe picker for click and wheel turning
	 * Observe element for clicks
	 *
	 * @private
	 * @method	_observePicker
	 */
	_observePicker: function() {
		var wheelEventName	= Prototype.Browser.Gecko ? 'DOMMouseScroll' : 'mousewheel';

		this.picker.on('click', this._onSelection.bind(this));
		this.divHour.on(wheelEventName, this._onHourScroll.bind(this));
		this.divMinute.on(wheelEventName, this._onMinuteScroll.bind(this));

			// Observe outside clicks
//		this.element.on('click', this._onElementClick.bind(this));
	},






	/**
	 * Event handler for picker click
	 *
	 * @private
	 * @method	_onSelection
	 * @param	{Event}		event
	 */
	_onSelection: function(event) {
		var column	= event.findElement('div.dpCol');
		var delay	= 0;

		if( column !== event.element() ) {
			var type = column.id.split('-').last();
			var value= Todoyu.Number.intval(event.element().innerHTML);

			if( type == 'hour' ) {
				this.setHour(value);
			} else {
				this.setMinute(value);
			}

			delay = 0.5;
		}

		this.updateElement.bind(this).delay(delay);
		this.hide.bind(this).delay(delay);
	},



	/**
	 * Handler for body click events
	 *
	 * @method	_onBodyClick
	 * @param	{Event}			event
	 * @param	{Element}		element
	 */
	_onBodyClick: function(event, element) {
			// Ignore clicks on this element's duration picker icon
		if ( element.siblings().indexOf(this.element) === -1 ) {
			this.hide();
		}
	},



	/**
	 * Event handler for hour scroll
	 *
	 * @private
	 * @method	_onHourScroll
	 * @param	{Event}		event
	 */
	_onHourScroll: function(event) {
		Event.stop(event);

		var hour = this.hour - Event.wheel(event) * this.config.stepHour;

		this.setHour(hour);
	},



	/**
	 * Event handler for minute scroll
	 *
	 * @private
	 * @method	_onMinuteScroll
	 * @param	{Event}		event
	 */
	_onMinuteScroll: function(event) {
		Event.stop(event);

		var minute = this.minute - Event.wheel(event) * this.config.stepMinute;

		this.setMinute(minute);
	},



	/**
	 * Make sure the value stays in the range.
	 *
	 * @private
	 * @method	_keepInRange
	 * @param	{Number}		value
	 * @param	{Array}		range		Bottom and top range
	 */
	_keepInRange: function(value, range) {
		if( value < range[0] ) {
			value = range[0];
		}

		if( value > range[1] ) {
			value = range[1];
		}

		return value;
	},



	/**
	 * Read duration from element
	 *
	 * @method	_readDuration
	 */
	_readDuration: function() {
		var value = $F(this.element);
		var dur	= {
			'hour': 0,
			'min': 0
		};

		if( value !== '' ) {
			if( value.indexOf(':') === -1 ) {
				dur.hour = Todoyu.Number.intval(value);
			} else {
				var parts	= value.split(':');

				if( parts.size() === 2 ) {
					dur.hour = Todoyu.Number.intval(parts[0]);
					dur.min = Todoyu.Number.intval(parts[1]);
				}
			}
		}

		return dur;
	}

});