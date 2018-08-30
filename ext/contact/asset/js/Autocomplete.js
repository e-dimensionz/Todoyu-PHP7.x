/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * @module	Contact
 */

Todoyu.Ext.contact.Autocomplete = {

	/**
	 * Handler when parent task field is autocompleted
	 *
	 * @method	onRegionAutocomplete
	 * @param	{Ajax.Response}			response
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onRegionAutocomplete: function(response, autocompleter) {
		if( response.getTodoyuHeader('acElements') == 0 ) {
			Todoyu.notifyInfo('[LLL:contact.ext.ac.region.notFoundInfo]');
		}
	},



	/**
	 * Handler when person field is autocompleted
	 *
	 * @method	onPersonAutocomplete
	 * @param	{Ajax.Response}			response
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onPersonAutocomplete: function(response, autocompleter) {
		if( response.isEmptyAcResult() ) {
			Todoyu.notifyInfo('[LLL:contact.ext.ac.person.notFoundInfo]');
			return false;
		}
	},



	/**
	 * Handler when company field is autocompleted
	 *
	 * @method	onCompanyAutocomplete
	 * @param	{Ajax.Response}			response
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onCompanyAutocomplete: function(response, autocompleter) {
		if( response.isEmptyAcResult() ) {
			Todoyu.notifyInfo('[LLL:contact.ext.ac.company.notFoundInfo]');
			return false;
		}
	}

};