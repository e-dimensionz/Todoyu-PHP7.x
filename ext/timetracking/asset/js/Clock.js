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
 * @module	Timetracking
 */

/**
 * Clock functions to display the current tracked time
 *
 * @class		Clock
 * @namespace	Todoyu.Ext.timetracking
 */
Todoyu.Ext.timetracking.Clock = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.timetracking,

	/**
	 * Periodical executer
	 *
	 * @property	pe
	 * @type		PeriodicalExecuter
	 */
	pe:		null,



	/**
	 * Start loop which refreshes the clock every second
	 * Prevents making multiple instances (the clock would run faster every time!)
	 *
	 * @method	start
	 */
	start: function() {
		if( ! this.isRunning() ) {
			this.pe = new PeriodicalExecuter(this.onClockTick.bind(this), 1);
		}
	},



	/**
	 * Stop clock refreshing
	 *
	 * @method	stop
	 */
	stop: function() {
		if( this.isRunning() ) {
			this.pe.stop();
			this.pe = null;
		}
	},



	/**
	 * Check whether clock (periodical execution update) is running
	 *
	 * @method	isRunning
	 * @return	{Boolean}
	 */
	isRunning: function() {
		return this.pe !== null;
	},



	/**
	 * Handler for clock tick: evoke timetracking clock ticking handler
	 *
	 * @method	onClockTick
	 * @param	{Object}	periodicalExecuter
	 */
	onClockTick: function(periodicalExecuter) {
		this.ext.onClockTick();
	},



	/**
	 * Show a new clock in a display area. Can be initialized with a start time
	 *
	 * @method	showClock
	 * @param	{String}		idDisplayArea
	 * @param	{Number}		startTime
	 */
	showClock: function(idDisplayArea, startTime) {
		this.addDisplayArea(idDisplayArea);

		if( ! this.isRunning() ) {
			if( typeof startTime === 'number' ) {
				this.setTime(startTime);
			}
			this.start();
		}
	},



	/**
	 * Get current tracked task
	 *
	 * @method	getTask
	 */
	getTask: function() {
		return Todoyu.Ext.timetracking.getTask();
	},



	/**
	 * Get currently tracked time
	 *
	 * @method	getTime
	 * @return	{Number}
	 */
	getTime: function() {
		return this.ext.getTrackedCurrent();
	},



	/**
	 * Register given callback
	 *
	 * @method	addCallback
	 * @param	{Function}	callback
	 */
	addCallback: function(callback) {
		this.callbacks.push(callback);
	},



	/**
	 * Call registered callback functions
	 *
	 * @method	callCallbacks
	 */
	callCallbacks: function() {
		this.callbacks.each(function(callback) {
			callback(this.getTask(), this.getTime());
		}.bind(this));
	},



	/**
	 * Add a new display area to the list of updated elements
	 *
	 * @method	addDisplayArea
	 * @param	{String}		idDisplayArea
	 */
	addDisplayArea: function(idDisplayArea) {
		this.displayAreas.push(idDisplayArea);
		this.displayAreas.uniq();
	},



	/**
	 * Call updater function for all registered display areas
	 *
	 * @method	refreshAreas
	 * @param	{PeriodicalExecuter}	pe
	 */
	refreshAreas: function(pe) {
		this.displayAreas.each(function(idDisplayArea) {
			this.updateDisplayArea(idDisplayArea, this.getTime());
		}.bind(this));
	},



	/**
	 * Update a display area with the current time
	 *
	 * @method	updateDisplayArea
	 * @param	{String}		idDisplayArea
	 * @param	{Number}		seconds
	 */
	updateDisplayArea: function(idDisplayArea, seconds) {
		var timeString = Todoyu.Helper.timestampFormat(seconds, ':');

		$(idDisplayArea).update(timeString);
	},



	/**
	 * Get an array with the keys hours, minutes and seconds of the current time
	 *
	 * @method	getTimeParts
	 * @return	{Object}
	 */
	getTimeParts: function() {
		var hours	= Math.floor(this.getTime() / Todoyu.Time.seconds.hour);
		var minutes	= this.getTime() - (hours * Todoyu.Time.seconds.hour);

		return {
			hours: 		hours,
			minutes:	Math.floor(minutes / Todoyu.Time.seconds.minute),
			seconds:	minutes - (Math.floor(minutes / Todoyu.Time.seconds.minute) * Todoyu.Time.seconds.minute)
		};
	}

};