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
 *	Todoyu number functions
 *
 * @class		Number
 * @namespace	Todoyu
 * @type		{Object}
 */
Todoyu.Number = {

	/**
	 * Convert value to Integer
	 *
	 * @method	intval
	 * @param	{String|Boolean|Number}		mixedvar
	 * @return	{Number}
	 */
	intval: function(mixedvar) {
		var type = typeof(mixedvar);
		var temp;

		switch(type) {
			case 'boolean':
				return mixedvar ? 1 : 0;

			case 'string':
				temp = parseInt(mixedvar, 10);
				return isNaN(temp) ? 0 : temp;

			case 'number':
				return Math.floor(mixedvar);

			default:
				return 0;
		}
	},



	/**
	 * Round with given precision
	 *
	 * @method	round
	 * @param	{Number}		value
	 * @param	{Number}	precision
	 * @return	{Number}
	 */
	round: function(value, precision) {
		value		= parseFloat(value);
		precision	= this.intval(precision);
		var factor	= Math.pow(10, precision);

		return Math.round((value*factor))/factor;
	},



	/**
	 * Check for Numeric input
	 *
	 * @param	{String|Boolean|Number|Object}		value
	 * @return	{Boolean}
	 */
	isNumeric: function(value) {
		return isFinite(value);
	}

};