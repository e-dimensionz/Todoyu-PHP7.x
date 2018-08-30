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

/**
 * Contact Address
 *
 * @class		Address
 * @namespace	Todoyu.Ext.contact
 */
Todoyu.Ext.contact.Address = {

	/**
	 * Sends Ajax request when the a country is selected to get its country zones
	 *
	 * @method	onChangeCountry
	 * @param	{Object}	inputField
	 * @param	{String}	referencedFieldName
	 * @param	{String}	fieldNameToReplace
	 */
	onChangeCountry: function(inputField, referencedFieldName, fieldNameToReplace) {
		var idCountry		= $F(inputField);
		var idInputFieldArr	= inputField.id.split('-').without('fulltext');
		var idFieldRegion 	= idInputFieldArr.join('-').replace(fieldNameToReplace, referencedFieldName);

		if( $(idFieldRegion) ) {
			var url 	= Todoyu.getUrl('contact', 'company');
			var options = {
				parameters: {
					action:		'regionOptions',
					country:	idCountry
				},
				onComplete: this.onUpdateCompanyAddressRecords.bind(this, idFieldRegion)
			};

			Todoyu.Ui.update(idFieldRegion, url, options);
		}
	},



	/**
	 * Fills the found options to the selector
	 * Highlights the selector for 2 seconds
	 *
	 * @method	onUpdateCompanyAddressRecords
	 * @param	{String}			idTarget
	 * @param	{Ajax.Response}		response
	 */
	onUpdateCompanyAddressRecords: function(idTarget, response) {
		new Effect.Highlight(idTarget, {
			duration: 2.0
		});
	},



	/**
	 * Form Validator for duplicated address records
	 *
	 * @param	{String}		fieldID
	 */
	checkForDuplicatedAddresses: function(fieldID) {
		var baseId = fieldID.split('-').without(fieldID.split('-').last());
		baseId = baseId.join('-');

		var street	= $F(baseId + '-street');
		var postbox	= $F(baseId + '-postbox');
		var zip		= $F(baseId + '-zip');
		var city	= $F(baseId + '-city');

		if( street && zip && city) {
			var url = Todoyu.getUrl('contact', 'ext');
			var options	= {
				parameters: {
					action: 'checkforduplicatedaddress',
					street: street,
					postbox: postbox,
					zip: zip,
					city: city
				},
				onComplete: Todoyu.Ext.contact.onCheckForDuplicatedEntries.curry(baseId + '-street')
			};

			Todoyu.send(url, options);
		}
	}
};