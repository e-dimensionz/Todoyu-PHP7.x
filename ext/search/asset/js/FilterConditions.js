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
 * @module	Search
 */

Todoyu.Ext.search.Filter.Conditions = {

	conditions: {},



	/**
	 * Add condition
	 *
	 * @method	add
	 * @param	{String}	name
	 * @param	{String}	type
	 * @param	{String}	condition
	 * @param	{String}	value
	 * @param	{Boolean}	negate
	 */
	add: function(name, type, condition, value, negate) {
		var conditionName = condition + '-' + name;

		this.conditions[conditionName] = {
			name:		conditionName,
			type:		type,
			condition:	condition,
			value:		value,
			negate:		!!negate
		};
	},



	/**
	 * Update value of given filter condition to given value 
	 *
	 * @method	updateValue
	 * @param	{String}	conditionName
	 * @param	{String}	value
	 */
	updateValue: function(conditionName, value) {
		this.conditions[conditionName].value = value;
	},



	/**
	 * Update negation of given search filter condition
	 *
	 * @method	updateNegation
	 * @param	{String}	conditionName
	 * @param	{Boolean}	negate
	 */
	updateNegation: function(conditionName, negate) {
		this.conditions[conditionName].negate = negate === true;
	},



	/**
	 * Check whether given search filter condition is currently negated
	 *
	 * @method	isNegated
	 * @param	{String}	conditionName
	 * @return	{Boolean}
	 */
	isNegated: function(conditionName) {
		return this.conditions[conditionName].negate === true;
	},



	/**
	 * Toggle negation flag of given condition
	 *
	 * @method	toggleNegated
	 * @param	{String}	conditionName
	 */
	toggleNegated: function(conditionName) {
		this.conditions[conditionName].negate = ! this.conditions[conditionName].negate;
	},



	/**
	 * Remove given condition from current search filter conditions
	 *
	 * @method	remove
	 * @param	{String}	conditionName
	 */
	remove: function(conditionName) {
		delete this.conditions[conditionName];
	},



	/**
	 * Clear current search filter conditions
	 *
	 * @method	clear
	 */
	clear: function() {
		this.conditions = {};
	},



	/**
	 * Get all current search filter conditions, optionally as JSON
	 *
	 * @method	getAll
	 * @param	{Boolean}	asJSON
	 * @return	{Mixed}
	 */
	getAll: function(asJSON) {
		if( asJSON ) {
			return Object.toJSON(this.conditions);
		} else {
			return this.conditions;
		}
	},



	/**
	 * Get amount of current set search filter conditions
	 *
	 * @method	size
	 * @return	{Number}
	 */
	size: function() {
		return Object.keys(this.conditions).size();
	},



	/**
	 * Check whether current view has conditions
	 *
	 * @return	{Boolean}
	 */
	hasConditions: function() {
		return this.size() !== 0;
	}

};