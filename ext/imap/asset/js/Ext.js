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
 * @module	Imap
 */

/**
 * Main imap object
 *
 * @class		imap
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.imap = {

	/**
	 * Port for SSL
	 */
	PORT_SSL: 993,

	/**
	 * Port for Start TLS
	 */
	PORT_STARTTLS: 143,

	/**
	 * Default port without encryption
	 */
	PORT_DEFAULT: 143,



	/**
	 * Initialization
	 *
	 * @method	init
	 */
	init: function() {

	},


	/**
	 * Initialize account record form
	 *
	 */
	initAccountForm: function() {
		this.observeEncryptionCheckboxes();
	},



	/**
	 * Observe the two encryption checkboxes to exclude each other
	 *
	 */
	observeEncryptionCheckboxes: function() {
		var idAccount	= $('content-body').down('.formRecord').id.split('-')[1];
		var useStartTls	= $('record-' + idAccount + '-field-use-starttls');
		var useSsl		= $('record-' + idAccount + '-field-use-ssl');
		var fieldPort	= $('record-' + idAccount + '-field-port');

		useStartTls.on('change', this.onEncryptionChanged.bind(this, useSsl, fieldPort));
		useSsl.on('change', this.onEncryptionChanged.bind(this, useStartTls, fieldPort));
	},



	/**
	 * Disable the other field if the changed field is selected now
	 *
	 * @param	{Element}	otherField
	 * @param	{Event}		event
	 * @param	{Element}	field
	 */
	onEncryptionChanged: function(otherField, fieldPort, event, field) {
		var type = field.id.endsWith('ssl') ? 'ssl' : 'starttls';

		if( field.checked ) {
			if( otherField.checked ) {
				otherField.checked = false;
			}
			fieldPort.value = type === 'ssl' ? this.PORT_SSL : this.PORT_STARTTLS;
		} else {
			if( type === 'ssl' ) {
				fieldPort.value = this.PORT_DEFAULT;
			}
		}
	}

};