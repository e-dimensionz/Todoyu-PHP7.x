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
 * Time related helper functions
 *
 * @class		Time
 * @namespace	Todoyu
 */
Todoyu.Time = {

	/**
	 * @property	seconds
	 * @type		Object
	 */
	seconds: {
		minute:	60,
		hour:	3600,
		day:	86400,
		week:	604800,
		month:	2592000
	},



	/**
	 * Format given time, to e.g. '13:50:20'
	 *
	 * @method	timeFormat
	 * @param	{Number}		hours
	 * @param	{Number}		minutes
	 * @param	{Number}		seconds
	 * @param	{String}		[separator]
	 * @return	{String}
	 */
	timeFormat: function(hours, minutes, seconds, separator) {
		separator	= separator || ':';

		return Todoyu.String.twoDigit(hours) + separator + Todoyu.String.twoDigit(minutes) + separator + Todoyu.String.twoDigit(seconds);
	},



	/**
	 * Format given time
	 *
	 * @method	timeFormatSeconds
	 * @param	{String}	time
	 * @param	{String}	separator
	 * @return	{String}
	 */
	timeFormatSeconds: function(time, separator) {
		var timeParts = this.getTimeParts(time);

		return this.timeFormat(timeParts.hours, timeParts.minutes, timeParts.seconds, separator);
	},



	/**
	 * Format duration. Hours:minutes
	 *
	 * @method	durationFormat
	 * @param	{Number}	seconds
	 * @param	{Boolean}	leadingZero
	 * @return	{String}
	 */
	durationFormat: function(seconds, leadingZero) {
		var timeParts	= this.getTimeParts(seconds);
		var hours		= timeParts.hours;

		if( timeParts.hours < 10 && leadingZero) {
			hours = Todoyu.String.twoDigit(timeParts.hours);
		}

		return hours + ':' + Todoyu.String.twoDigit(timeParts.minutes);
	},



	/**
	 * Parse given time string to seconds
	 *
	 * @method	parseTimeToSeconds
	 * @param	{String}	timeString
	 * @return	{String}
	 */
	parseTimeToSeconds: function(timeString) {
		var parts	= timeString.stripTags().split(':');

		return Todoyu.Number.intval(parts[0]) * this.seconds.hour + (Todoyu.Number.intval(parts[1]) * this.seconds.minute) + Todoyu.Number.intval(parts[2]);
	},



	/**
	 * Get time parts of given (timestamp) time
	 *
	 * @method	getTimeParts
	 * @param	{Number}		time
	 * @return	{Object}
	 */
	getTimeParts: function(time) {
		time = Todoyu.Number.intval(time);

		var hours	= Math.floor(time / this.seconds.hour);
		var minutes	= Math.floor((time - hours * this.seconds.hour) / this.seconds.minute);
		var seconds	= time - (hours * this.seconds.hour) - (minutes * this.seconds.minute);

		return {
			hours:		hours,
			minutes:	minutes,
			seconds:	seconds
		};
	},



	/**
	 * Get result of given base timestamp shifted into future/past by given factor
	 *
	 * @method	getShiftedTime
	 * @param	{Number}		baseTime		Unit timestamp
	 * @param	{String}		type
	 * @param	{Boolean}		up
	 * @return	{Number}		Unit timestamp
	 */
	getShiftedTime: function(baseTime, type, up) {
		var date	= new Date(baseTime*1000);
		baseTime	= this.getDayStart(date);

		var factor	= up ? 1 : -1;
		var day		= 0;
		var month	= 0;

		switch( type ) {
			case 'month':
				month	= factor;
				break;
			case 'week':
				day		= factor * 7;
				break;
			case 'day':
				day		= factor;
				break;
		}

		var newDate = new Date(date.getFullYear(), date.getMonth() + month, date.getDate() + day, date.getHours(), date.getMinutes(), date.getSeconds());

		return parseInt(newDate.getTime() / 1000, 10);
	},



	/**
	 * Get timestamp at start of day
	 *
	 * @method	getDayStart
	 * @param	{Date}		date
	 * @return	{Date}
	 */
	getDayStart: function(date) {
		date.setHours(0, 0, 0, 0);

		return date;
	},



	/**
	 * Get date at start of week (being sunday or monday depending on system config)
	 *
	 * @method	getWeekStart
	 * @param	{Date}		date
	 * @return	{Date}
	 */
	getWeekStart: function(date) {
		var newDate, shiftByDays;
		date.setHours(0);
		date.setMinutes(0);
		date.setSeconds(0);

		if( date.getDay() !== Todoyu.Config.system.firstDayOfWeek ) {
				// Adjust 1st day of week from monday to sunday
			if( Todoyu.Config.system.firstDayOfWeek === 0 ) {
				shiftByDays = date.getDay();
			} else {
				shiftByDays = (date.getDay()+6) % 7;
			}
			newDate = date.addDays(-shiftByDays, true);
		} else {
			newDate = new Date(date);
		}

		return newDate;
	},



	/**
	 * Get today's date
	 *
	 * @method	getTodayDate
	 * @return	{Date}		microtime timestamp
	 */
	getTodayDate: function() {
		var date	= new Date();
		date.setHours(0, 0, 0, 0);

		return date;
	},



	/**
	 * Get current hour of day
	 *
	 * @method	getCurrentHourOfDay
	 * @return  {Number}
	 */
	getCurrentHourOfDay: function() {
		return new Date().getHours();
	},



	/**
	 * Get current minutes of hour
	 *
	 * @method	getCurrentMinutesOfHour
	 * @return  {Number}
	 */
	getCurrentMinutesOfHour: function() {
		return new Date().getMinutes();
	},



	/**
	 * Get amount of days in month
	 *
	 * @method	getDaysInMonth
	 * @param	{Number}	time
	 * @return	{Number}
	 */
	getDaysInMonth: function(time) {
		var date	= new Date(time * 1000);
		var year	= date.getFullYear();
		var month	= date.getMonth();

		return 32 - new Date(year, month, 32).getDate();
	},



	/**
	 * Get amount of days in february of given year.
	 *
	 * @method	getDaysInFebruary
	 * @param	{Number}	year
	 * @return	{Number}
	 */
	getDaysInFebruary: function(year) {
			// February has 29 days in any year evenly divisible by four, except for centurial years which are not dividable by 400
		return (year % 4 == 0) && (!(year % 100 == 0) || (year % 400 == 0)) ? 29 : 28;
	},



	/**
	 * Get date string in format YYYY-MM-DD
	 *
	 * @method	getDateString
	 * @param	{Date}		date
	 * @return	{String}
	 */
	getDateString: function(date) {
		return date.getFullYear() + '-' + Todoyu.String.twoDigit(date.getMonth() + 1) + '-' + date.getDate();
	},



	/**
	 * Get date string with time part in format YYYY-MM-DD HH:MM
	 *
	 * @method	getDateTimeString
	 * @param	{Date|Number}	date
	 * @return	{String}
	 */
	getDateTimeString: function(date) {
		if( ! (date instanceof Date) ) {
			date = new Date(date * 1000);
		}

		return date.getFullYear() + '-' + Todoyu.String.twoDigit(date.getMonth() + 1) + '-' + Todoyu.String.twoDigit(date.getDate()) + ' ' + Todoyu.String.twoDigit(date.getHours()) + ':' + Todoyu.String.twoDigit(date.getMinutes());
	},



	/**
	 * Convert date string (Y-m-d) into an timestamp
	 *
	 * @method	date2Time
	 * @param	{String}		dateString
	 * @return	{Number}
	 */
	date2Time: function(dateString) {
		var parts	= dateString.split('-');

		return Math.round((new Date(parts[0], parts[1]-1, parts[2], 0, 0, 0)).getTime() / 1000);
	},



	/**
	 * Check whether given date string contains a correct date (will not be corrected/changed when parsed)
	 *
	 * @method	isDateString
	 * @param	{String}	inputString
	 * @param	{String}	format
	 * @return	{Boolean}
	 */
	hasDateValidFormat: function(inputString, format) {
		var inputDate		= Date.parseDate(inputString, format);
		var compareString	= inputDate.print(format);

		return this.areSameDates(inputString, compareString);
	},



	/**
	 * Compare two date strings. Remove irrelavant parts to be more tollerant
	 *
	 * @param	{String}	dateString1
	 * @param	{String}	dateString2
	 */
	areSameDates: function(dateString1, dateString2) {
		var compareDate1	= dateString1.replace(/0*(\d*)/gi,"$1").replace(/0{1,2}:0{1,2}/gi, '').strip();
		var compareDate2	= dateString2.replace(/0*(\d*)/gi,"$1").replace(/0{1,2}:0{1,2}/gi, '').strip();

		compareDate1		= this.reduceYear(compareDate1);
		compareDate2		= this.reduceYear(compareDate2);

		return compareDate1 === compareDate2;
	},

	

	/**
	 * Reduce year to two digit year for better comparison
	 * 2012 => 12
	 * 12	=> 12
	 *
	 * @param	{String}	dateString
	 * @return	{String}
	 */
	reduceYear: function(dateString) {
		var dateParts	= dateString.split(/\W+/);
		var yearPart	= dateParts.find(function(datePart){
			return datePart > 1000;
		});

		if( yearPart ) {
			var smallYear = yearPart.substr(2);
			dateString = dateString.replace(yearPart, smallYear);
		}

		return dateString;
	},



	/**
	 * Get (javaScript) timestamp of start (00:00) of given day
	 *
	 * @method	getStartOfDay
	 * @param	{Date}		day
	 * @return	{Number}
	 */
	getStartOfDay: function(day) {
		var y	= day.getFullYear();
		var m	= day.getMonth();
		var d	= day.getDate();

		var dateStart	= new Date(y, m, d, 0, 0);

		return dateStart.getTime();
	},



	/**
	 * Get (javaScript) timestamp of end (23:59) of given day
	 *
	 * @method	getEndOfDay
	 * @param	{Date}		day
	 * @return	{Number}
	 */
	getEndOfDay: function(day) {
		var y	= day.getFullYear();
		var m	= day.getMonth();
		var d	= day.getDate();

		var dateStart	= new Date(y, m, d, 23, 59);

		return dateStart.getTime();
	},



	/**
	 * Get (timestamps at 00:00 of) days inside given timespan
	 *
	 * @method	getDayTimestampsInRange
	 * @param	{Date}		dateStart
	 * @param	{Date}		dateEnd
	 * @return	{Array}
	 */
	getDayTimestampsInRange: function(dateStart, dateEnd) {
		dateStart	= this.getStartOfDay(dateStart);
		dateEnd		= this.getEndOfDay(dateEnd);

		var timestamp	= dateStart;

		var days		= new Array();
		while( timestamp <= dateEnd ) {
			days.push(timestamp);
			timestamp	+= this.seconds.day * 1000;
		}

		return days;
	},



	/**
	 * Check whether timestamp is in the future
	 *
	 * @method	isTimeInFuture
	 * @param	{Number}	time
	 * @return	{Boolean}
	 */
	isTimeInFuture: function(time) {
		return this.isDateInFuture(new Date(time*1000));
	},



	/**
	 * Check whether timestamp is in the past
	 *
	 * @method	isTimeInPast
	 * @param	{Number}	time
	 * @return	{Boolean}
	 */
	isTimeInPast: function(time) {
		return !this.isTimeInFuture(time);
	},



	/**
	 * Check whether date is in the future
	 *
	 * @method	isDateInFuture
	 * @param	{Date}	date
	 * @return	{Boolean}
	 */
	isDateInFuture: function(date) {
		var dateNow		= new Date();

		return dateNow < date;
	},



	/**
	 * Check whether date is in the past
	 *
	 * @method	isDateInPast
	 * @param	{Date}	date
	 * @return	{Boolean}
	 */
	isDateInPast: function(date) {
		return !this.isDateInFuture(date);
	},



	/**
	 * Check whether date is in a past week
	 *
	 * @method	isDateInPastWeek
	 * @param	{Date}	date
	 * @return	{Boolean}
	 */
	isDateInPastWeek: function(date) {
		var compare = new Date(date).setToWeekEnd();

		return this.isDateInPast(compare);
	},



	/**
	 * Get date from ISO date string
	 * YEAR-MONTH-DAY
	 *
	 * @param	{String}	dateString
	 * @param	{Date}
	 */
	parseIsoString: function(dateString) {
		var date;

		if( Prototype.Browser.WebKit ) {
			var parts = dateString.match(/(\d+)/g);

			date = new Date(parts[0], parts[1]-1, parts[2]);
		} else {
			date = new Date(dateString);
		}

		return date;
	}

};