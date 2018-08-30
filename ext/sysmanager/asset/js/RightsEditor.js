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
 * @module	Sysmanager
 */

/**
 * System rights mask manager
 *
 * @class		RightsEditor
 * @namespace	Todoyu.Ext.sysmanager
 */
Todoyu.Ext.sysmanager.RightsEditor = {

	/**
	 * Requireds and dependents
	 *
	 * @property	require
	 * @type		Object
	 */
	require: {},

	/**
	 * True if unsaved changes are in the form
	 *
	 * @property	dirty
	 * @type		Boolean
	 */
	dirty: false,

	/**
	 * Last form values (for dirty check)
	 *
	 * @property	lastFormValues
	 * @type		Object
	 */
	lastFormValues:{},



	/**
	 * Init
	 *
	 * @method	init
	 */
	init: function() {
			// Observe form changes
		this.observeForm();

		if( this.getRoles().size() === 0 ) {
				// Check role selector
			this.selectAllRoles();
			this.onFormChange();
		}

			// Store the current values as last values
		this.storeLastFormValues();
	},



	/**
	 * Init matrix with dependencies and install observers
	 *
	 * @method	initMatrix
	 * @param	{JSON}	require
	 */
	initMatrix: function(require) {
			// If no dependecies given, use empty object. Empty array doesn't work with $H()
		require	= Object.isArray(require) ? {} : require;

			// Set required mapping
		this.require = $H(require);

			// Disable all dependents whose required right is not set
		this.initDependents();

			// Observe rights checkboxes for change
		this.observeRightsForm();

			// Are there rights to be edited? hide disable save button otherwise
		var noRights	= $$('td.roleRight').size() === 0;
		$$('button.save').each(function(element) {
			$(element).disabled = noRights;
		});
	},



	/**
	 * Observe roles and extension selectors
	 *
	 * @method	observeForm
	 */
	observeForm: function() {
		$('rightseditor-field-roles').on('change', this.onRoleChange.bind(this));
		$('rightseditor-field-extension').on('change', this.onExtensionChange.bind(this));
	},



	/**
	 * Handler when form changes
	 * Called when roles or extension changes
	 *
	 * @method	onFormChange
	 */
	onFormChange: function() {
		if( this.dirty ) {
			if( confirm('[LLL:sysmanager.ext.rights.dirtyChanges]') ) {
				var roles	= this.getRoles();
				var ext		= this.getExtension();
				this.applyLastFormValues();
				this.saveRights(this.onDirtyChangesSaved.bind(this, roles, ext));
				return;
			} else {
				this.dirty = false;
			}
		}

		this.updateMatrix();
	},



	/**
	 * Store the parameters or the current values of the elements as last values
	 *
	 * @method	storeLastFormValues
	 * @param	{Array}		[roles]
	 * @param	{String}	[ext]
	 */
	storeLastFormValues: function(roles, ext) {
		this.lastFormValues = {
			roles:	roles || this.getRoles(),
			ext:	ext || this.getExtension()
		};
	},



	/**
	 * Apply the last form values
	 *
	 * @method	applyLastFormValues
	 */
	applyLastFormValues: function() {
		this.applyFormValues(this.lastFormValues.roles, this.lastFormValues.ext);
	},



	/**
	 * Apply for values
	 *
	 * @method	applyFormValues
	 * @param	{Array}		roles
	 * @param	{String}	ext
	 */
	applyFormValues: function(roles, ext) {
		Todoyu.Form.selectOptions('rightseditor-field-roles', roles);
		Todoyu.Form.selectOptions('rightseditor-field-extension', ext);
	},



	/**
	 * After dirty changes have been saved, update matrix as requests before
	 *
	 * @method	onDirtyChangesSaved
	 * @param	{Array}			roles
	 * @param	{String}		ext
	 * @param	{Ajax.Response}	response
	 */
	onDirtyChangesSaved: function(roles, ext, response) {
			// Apply the form values (before the dirty save)
		this.applyFormValues(roles, ext);
			// Store the current values as 'last values'
		this.storeLastFormValues();
			// Update the matrix with the new values
		this.updateMatrix();
	},




	/**
	 * Handler when the role selection changes
	 * Select all roles if none is selected. Prevents empty matrix
	 * Called before the form change. So we can update the selection just before the form is submitted
	 *
	 * @method	onRoleChange
	 * @param	{Event}		event
	 */
	onRoleChange: function(event) {
		this.preventEmptyRoleSelection();
		this.onFormChange();
	},



	/**
	 * Prevent that no roles are selected
	 * If no role is selected, select all
	 *
	 * @method	preventEmptyRoleSelection
	 */
	preventEmptyRoleSelection: function() {
		if( this.getRoles().size() === 0 ) {
			this.selectAllRoles();
		}
	},



	/**
	 * Select all role options in role selector
	 *
	 * @method	selectAllRoles
	 */
	selectAllRoles: function() {
		$('rightseditor-field-roles').select('option').invoke('writeAttribute', 'selected', true);

	},



	/**
	 * Handler when extension is changed
	 *
	 * @method	onExtensionChange
	 * @param	{Event}		event
	 */
	onExtensionChange: function(event) {
		this.onFormChange();
	},



	/**
	 * Update the whole rights editor
	 *
	 * @method	updateEditor
	 */
	updateEditor: function() {
		Todoyu.Ui.updatePage('sysmanager', 'ext');
	},



	/**
	 * Update matrix
	 *
	 * @method	updateMatrix
	 */
	updateMatrix: function() {
		$('rightseditor-form').request({
			parameters: {
				action:	'matrix',
				area:	Todoyu.getArea()
			},
			onComplete: this.onMatrixUpdated.bind(this)
		});
	},



	/**
	 * On editor updated handler
	 *
	 * @method	onMatrixUpdated
	 * @param	{Array}	response
	 */
	onMatrixUpdated: function(response) {
		$('rightsmatrix').update(response.responseText);
	},



	/**
	 * Save rights over AJAX, no reload
	 *
	 * @method	saveRights
	 * @param	{Function}		callback
	 */
	saveRights: function(callback) {
		$('rightsmatix-form').request({
			parameters: {
				action:		'save',
				extension:	this.getExtension(),
				roles:		this.getRoles().join(','),
				area:		Todoyu.getArea()
			},
			onComplete:	this.onRightsSaved.bind(this, callback)
		});
	},



	/**
	 * On saved handler
	 *
	 * @method	onRightsSaved
	 * @param	{Array}	response
	 */
	onRightsSaved: function(callback, response) {
		this.dirty = false;
		Todoyu.notifySuccess('[LLL:sysmanager.ext.rights.saved]', 'sysmanager.rights.save');
		Todoyu.callIfExists(callback, this, response);
	},



	/**
	 * Handler when group selection has changed
	 *
	 * @method	onRolesChange
	 * @param	{Event}		event
	 */
	onRolesChange: function(event) {
		this.updateEditor();
	},



	/**
	 * Check dependencies for all rights
	 *
	 * @method	initDependents
	 */
	initDependents: function() {
		var roles	= this.getRoles();

		this.require.each(function(roles, require){
			roles.each(function(require, idRole){
				this.checkDependents(require.key, idRole);
			}.bind(this, require));
		}.bind(this, roles));
	},



	/**
	 * Observe rights checkboxes
	 *
	 * @method	observeRightsForm
	 */
	observeRightsForm: function() {
		$('rightsmatix-form').on('change', 'input', this.onRightChange.bind(this));
	},



	/**
	 * Handler when a right is changed
	 *
	 * @method	onRightChange
	 * @param	{Event}		event
	 */
	onRightChange: function(event, element) {
			// Set dirty flag for unsaved changes
		this.dirty = true;

		var idParts	= element.id.split('-');
		var right	= idParts.slice(0,-1).join(':'); // Remove role ID and join section and right
		var idRole	= idParts.last();

		this.checkDependents(right, idRole);
	},



	/**
	 * Get checkbox element
	 *
	 * @method	checkbox
	 * @param	{String}		right
	 * @param	{Number}		idRole
	 */
	checkbox: function(right, idRole) {
		return $(right.replace(/:/, '-') + '-' + idRole);
	},



	/**
	 * Get all rights which are required for the right
	 *
	 * @method	getRequireds
	 * @param	{String}		right
	 */
	getRequireds: function(right) {
		return this.require.get(right);
	},



	/**
	 * Get all dependent rights of a right
	 *
	 * @method	getDependents
	 * @param	{String}		right
	 */
	getDependents: function(right) {
		var dependents = [];

		this.require.each(function(rightRequire){
			if( rightRequire.value.include(right) ) {
				dependents.push(rightRequire.key);
			}
		});

		return dependents;
	},



	/**
	 * Check/uncheck right checkbox
	 *
	 * @method	checkRight
	 * @param	{String}		right
	 * @param	{Number}		idRole
	 * @param	{Boolean}		check
	 */
	checkRight: function(right, idRole, check) {
		this.checkbox(right, idRole).checked = check;
	},



	/**
	 * Check if a right checkbox is checked
	 *
	 * @method	isRightChecked
	 * @param	{String}		right
	 * @param	{Number}		idRole
	 */
	isRightChecked: function(right, idRole) {
		return this.checkbox(right, idRole).checked;
	},



	/**
	 * Enable/disable a right checkbox
	 *
	 * @method	enableRight
	 * @param	{String}		right
	 * @param	{Number}		idRole
	 * @param	{Boolean}		enable
	 */
	enableRight: function(right, idRole, enable) {
		this.checkbox(right, idRole).disabled = enable === false;
	},



	/**
	 * Check whether a rights checkbox is enabled
	 *
	 * @method	isRightEnabled
	 * @param	{String}		right
	 * @param	{Number}		idRole
	 */
	isRightEnabled: function(right, idRole) {
		return this.checkbox(right, idRole).disabled !== true;
	},



	/**
	 * Check if a right is active (active = enabled and checked)
	 *
	 * @method	isRightActive
	 * @param	{String}		right
	 * @param	{Number}		idRole
	 */
	isRightActive: function(right, idRole) {
		return this.isRightEnabled(right, idRole) && this.isRightChecked(right, idRole);
	},



	/**
	 * Activate a right. Enabled and checked
	 *
	 * @method	activateRight
	 * @param	{String}		right
	 * @param	{Number}		idGroup
	 * @param	{Boolean}		active
	 */
	activateRight: function(right, idGroup, active) {
		this.enableRight(right, idGroup);
		this.checkRight(right, idGroup);
	},



	/**
	 * Check if all required rights for a right are currently active
	 *
	 * @method	allRequiredsActive
	 * @param	{String}		right
	 * @param	{Number}		idGroup
	 */
	allRequiredsActive: function(right, idGroup) {
		var requireds = this.getRequireds(right);

		return requireds.all(function(idGroup, reqRight){
			return this.isRightActive(reqRight, idGroup);
		}.bind(this, idGroup));
	},



	/**
	 * Check dependent rights of a right
	 * Enable or disable them by dependencies
	 *
	 * @method	checkDependents
	 * @param	{String}		right
	 */
	checkDependents: function(right, idRole) {
		var dependents	= this.getDependents(right);

			// Check if right is active
		var active = this.isRightActive(right, idRole);
			// Loop over all rights which depend on this right
		dependents.each(function(active, idRole, depRight){
				// If right is active, activate dependent
			if( active ) {
					// Only activate right if all other required parents are active too
				if( this.allRequiredsActive(depRight, idRole) ) {
					this.enableRight(depRight, idRole, true);
				}
			} else {
					// Disable right because required parent is not active
				this.enableRight(depRight, idRole, false);
			}
		}.bind(this, active, idRole));
	},



	/**
	 * Get selected roles
	 *
	 * @method	getRoles
	 * @return	{Array}
	 */
	getRoles: function() {
		return $F('rightseditor-field-roles') || [];
	},



	/**
	 * Get currently selected extension's key
	 *
	 * @method	getExtension
	 * @return	{String}
	 */
	getExtension: function() {
		return $F('rightseditor-field-extension');
	},



	/**
	 * Toggle right
	 *
	 * @method	toggleRight
	 * @param	{String}	right
	 */
	toggleRight: function(right) {
		var checkboxes	= $('right-' + right.replace(/:/, '-')).select('input').findAll(function(input){
			return input.disabled === false;
		});

			// Toggle the checkboxes
		this.toggleCheckboxes(checkboxes);
			// Recheck all rights
		this.initDependents();
	},



	/**
	 * Toggle group
	 *
	 * @method	toggleRoleRights
	 * @param	{Number}		idRole
	 */
	toggleRoleRights: function(idRole) {
			// Get role rights checkboxes
		var checkboxes= $('rightsmatix-form').select('input[id$=-' + idRole + ']');
			// Toggle the checkboxes
		this.toggleCheckboxes(checkboxes);
			// Recheck all rights
		this.initDependents();
	},



	/**
	 * Toggle checkboxes
	 *
	 * @method	toggleCheckboxes
	 * @param	{Array}	checkboxes
	 */
	toggleCheckboxes: function(checkboxes) {
		this.allOn	= checkboxes.all(function(checkbox){
			return checkbox.checked
		});

			// Check or uncheck all
		checkboxes.each(function(checkbox) {
			checkbox.checked = ! this.allOn;
		}.bind(this));
	}

};