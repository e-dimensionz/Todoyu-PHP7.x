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
 * @module	Project
 */

/**
 * Project filter
 *
 * @class		Filter
 * @namespace	Todoyu.Ext.project
 */
Todoyu.Ext.project.Filter = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.project,



	/**
	 * Handler when project search result has been updated
	 *
	 * @method	onProjectSearchResultsUpdated
	 */
	onProjectSearchResultsUpdated: function() {
		this.ext.ContextMenuProject.attach();
		this.ext.ContextMenuProjectInline.attach();
	},



	/**
	 * Handler when task search result has been updated
	 *
	 * @method	onTaskSearchResultsUpdated
	 */
	onTaskSearchResultsUpdated: function() {
		this.ext.ContextMenuTask.attach();
	},



	/**
	 * Handler when a person is selection with the AC
	 *
	 * @method	onProjectrolePersonAcSelect
	 * @param	{String}		name
	 * @param	{Element}		textInput
	 * @param	{Element}		listElement
	 */
	onProjectrolePersonAcSelect: function(name, textInput, listElement) {
		this.updateProjectRoleConditionValue(name);
	},



	/**
	 * Handler when the projectrole selection changed
	 *
	 * @method	onProjectroleRoleChange
	 * @param	{String}	name
	 */
	onProjectroleRoleChange: function(name) {
		this.updateProjectRoleConditionValue(name);
	},



	/**
	 * Handler when person changes. Only send update request if person field is
	 * empty and the AC handler doesn't send the request
	 *
	 * @method	onProjectrolePersonChange
	 * @param	{String}	name
	 */
	onProjectrolePersonChange: function(name) {
		if( $F('widget-autocompleter-' + name) === '' ) {
			$('widget-autocompleter-' + name + '-hidden').value = 0;
			this.updateProjectRoleConditionValue(name);
		}
	},



	/**
	 * Get selected person
	 *
	 * @method	getProjectrolePerson
	 * @param	{String}	name
	 * @return	{Number}
	 */
	getProjectrolePerson: function(name) {
		return $F('widget-autocompleter-' + name + '-hidden');
	},



	/**
	 * Get selected project roles
	 *
	 * @method	getProjectroleRoles
	 * @param	{Array}		name
	 */
	getProjectroleRoles: function(name) {
		return $F('filterwidget-select-' + name);
	},



	/**
	 * Update the condition for projectrole selector
	 * Concatenate the person and the roles to get a simple string value
	 * Format: idPerson:idRole,IdRole,idRole
	 *
	 * @method	updateProjectRoleConditionValue
	 * @param	{String}	name
	 */
	updateProjectRoleConditionValue: function(name) {
		var idPerson	= this.getProjectrolePerson(name);
		var projectRoles= this.getProjectroleRoles(name);

		if( projectRoles != null ) {
			var value		= idPerson + ':' + projectRoles.join(',');

			Todoyu.Ext.search.Filter.updateConditionValue(name, value);
		}
	}

};