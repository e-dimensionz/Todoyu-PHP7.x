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

Todoyu.Ext.project.QuickCreateTask = {

	/**
	 * Evoked upon opening of event quick create wizard popUp
	 *
	 * @method	onPopupOpened
	 */
	onPopupOpened: function() {
		Todoyu.Hook.exec('project.task.formLoaded', 0, {
			quickCreate: true
		});
	},



	/**
	 * Update quickcreate task form (inside popup) with given project preselected
	 *
	 * @method	updateForm
	 * @param	{Number}	idProject
	 */
	updateForm: function(idProject) {
		var url		= Todoyu.getUrl('project', 'Quickcreatetask');
		var options	= {
			parameters: {
				action:		'popup',
				project:	idProject,
				update:		1,
				data:		this.getFormData(false)
			},
			onComplete:	this.onFormUpdated.bind(this)
		};

		Todoyu.Ui.update('quickcreate_content', url, options);
	},



	/**
	 * Get quick create task form data
	 *
	 * @method	getFormData
	 * @param	{Boolean}	hash		Get as hash
	 * @return	{String|Object}
	 */
	getFormData: function(hash) {
		hash = hash === true;

		return $('quickcreate_content').down('form').serialize(hash);
	},



	/**
	 * Call registered quickcreate task event handler (onPopupOpened)
	 *
	 * @method	onFormUpdated
	 */
	onFormUpdated: function() {
		Todoyu.callUserFunction('Todoyu.Ext.project.QuickCreateTask.onPopupOpened');
	},



	/**
	 * Save task
	 *
	 * @method	save
	 * @param	{String}		form
	 */
	save: function(form) {
		Todoyu.Ui.closeRTE(form);

		$(form).request({
			parameters: {
				action:	'save',
				area:	Todoyu.getArea()
			},
			onComplete: this.onSaved.bind(this)
		});
	},



	/**
	 * Evoked after edited task having been saved. Handles display of success / failure message and refresh of saved task / failed form.
	 *
	 * @method	onSaved
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(response) {
		var idProject	= response.getTodoyuHeader('idProject');
		var idTask		= response.getTodoyuHeader('idTask');
		var idTaskOld	= response.getTodoyuHeader('idTaskOld');

		var notificationIdentifier	= 'project.task.saved';

			// Save resulted in error?
		if( response.hasTodoyuError() ) {
				// Update task edit form with form remarks, display failure notification
			Todoyu.Headlets.getHeadlet('todoyuheadletquickcreate').updatePopupContent(response.responseText);
			Todoyu.Hook.exec('project.task.formLoaded', 0, {
				quickCreate: true
			});
			Todoyu.notifyError('[LLL:project.task.save.error]', notificationIdentifier);
		} else {
				// Saving went ok
			Todoyu.Hook.exec('project.task.saved', idTask);

			if( Todoyu.isInArea('project') ) {
					// Task tree of project which the new task belongs to is displayed?
				if( idProject == Todoyu.Ext.project.ProjectTaskTree.getActiveProjectID() ) {
						// Refresh
					Todoyu.Ext.project.TaskTree.update();
				}
			}

			Todoyu.Popups.close('quickcreate');
			Todoyu.notifySuccess('[LLL:project.task.save.success]', notificationIdentifier);
		}
	}

};