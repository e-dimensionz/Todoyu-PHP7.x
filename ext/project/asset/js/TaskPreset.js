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
 *	@module		Project
 */

/**
 * Task preset
 *
 * @class		Sysmanager
 * @namespace	Todoyu.Ext.project
 */
Todoyu.Ext.project.TaskPreset = {

	/**
	 * Current task preset ID
	 * @var	{Number}	idTaskPreset
	 */
	idTaskPreset: 0,



	/**
	 * Initialize form
	 *
	 * @method	initForm
	 * @param	{Number}	idTaskPreset
	 */
	initForm: function(idTaskPreset) {
		this.idTaskPreset = idTaskPreset;

			// Disable on load
		this.initFields();
			// Add change observers
		this.addObservers();
			// Add extra save observer to enable fields
		this.addSaveObserver();
	},



	/**
	 * Get field element by type
	 *
	 * @method	getField
	 * @param	{String}	subject			person or role
	 * @param	{String}	type			assigned or owner
	 */
	getField: function(subject, type) {
		return $('record-' + this.idTaskPreset + '-field-id-' + subject + '-' + type + '-fallback');
	},



	/**
	 * Initialize fields on load
	 * Disable group selects when person is selected
	 *
	 * @method	initFields
	 */
	initFields: function() {
		if( $F(this.getField('person', 'assigned')) != 0 ) {
			this.getField('role', 'assigned').disable();
			this.getField('role', 'assigned').selectedIndex = 0;
		}
		if( $F(this.getField('person', 'owner')) != 0 ) {
			this.getField('role', 'owner').disable();
			this.getField('role', 'owner').selectedIndex = 0;
		}
	},



	/**
	 * Add observers to person fields
	 *
	 * @method	addObservers
	 */
	addObservers: function() {
		this.getField('person', 'assigned').on('change', this.onPersonChanged.bind(this, 'assigned'));
		this.getField('person', 'owner').on('change', this.onPersonChanged.bind(this, 'owner'));
	},



	/**
	 * Add extra event to save button to enable all fields (so they will be saved)
	 *
	 * @method	addSaveObserver
	 */
	addSaveObserver: function() {
		$('record-' + this.idTaskPreset + '-field-save').on('mousedown', this.onSave.bind(this));
	},



	/**
	 * Enable all fields in the form
	 *
	 * @method	onSave
	 * @param	{Event}		event
	 * @param	{Element}	button
	 */
	onSave: function(event, button) {
		$('record-' + this.idTaskPreset + '-form').select(':input:disabled').invoke('enable');
	},



	/**
	 * Handle change on person select
	 *
	 * @method	onPersonChanged
	 * @param	{String}	type		assigned or owner
	 * @param	{Event}		event		DOM event
	 * @param	{Element}	element		select element
	 */
	onPersonChanged: function(type, event, element) {
		var action	= $F(element) == 0 ? 'enable' : 'disable';

		this.getField('role', type)[action]();
		this.getField('role', type).selectedIndex = 0;
	}

};