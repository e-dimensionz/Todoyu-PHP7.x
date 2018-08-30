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
 * @module	Portal
 */

/**
 * Portal main object
 *
 * @class		portal
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.portal = {

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget:	{},

	/**
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet:		{},



	/**
	 * Initialize portal
	 *
	 * @method	init
	 */
	init: function() {
		if( Todoyu.isInArea('portal') ) {
			Todoyu.Hook.add('project.quickTask.saved', this.onQuickTaskSaved.bind(this)); 
		}
	},



	/**
	 * Check whether current area is portal
	 *
	 * @method	isInPortalArea
	 * @return	{Boolean}
	 */
	isInPortalArea: function() {
		return Todoyu.isInArea('portal');
	},



	/**
	 * Show task
	 * If task is currently displayed in portal area:	jump to it
	 * If task is currently not displayed in portal area: jump to it in project area
	 *
	 * @method	showTask
	 * @param	{Number}	idTask
	 * @param	{Number}	idProject
	 */
	showTask: function(idTask, idProject) {
		var task	= 'task-' + idTask;

		if( Todoyu.Ui.isVisible(task) ) {
			$(task).scrollToElement();
			Todoyu.Ui.twinkle(task);
		} else {
			Todoyu.Ext.project.goToTaskInProject(idTask, idProject);
		}
	},



	/**
	 * Open sub task containers of given task
	 *
	 * @method	expandSubtaskContainers
	 * @param	{Number}	idTask
	 */
	expandSubtaskContainers: function(idTask) {
		var task		= $('task-' + idTask);
		var container	= task.up('.subtasks');
		var idParts, idCTask;

		while( container ) {
			idParts	= container.id.split('-');
			idCTask	= idParts[1];

			Todoyu.Ext.project.TaskTree.openSubtasks(idCTask);

			container = $('task-' + idCTask).up('.subtasks');
		}
	},



	/**
	 * Handler when quick task has been saved: Update the current portal task list
	 *
	 * @method	onQuickTaskSaved
	 * @param	{Number}			idTask
	 * @param	{Number}			idProject
	 * @param	{Ajax.Response}		response
	 */
	onQuickTaskSaved: function(idTask, idProject, response) {
		var activeTabKey	= this.Tab.getActiveTab();

		if( activeTabKey != 'appointment' ) {
				// Reload tab content
			this.Tab.showTab(activeTabKey, false);
		} 
	},



	/**
	 * Save portal preferences
	 *
	 * @method	savePref
	 * @param	{String}	action
	 * @param	{String}	value
	 * @param	{Number}	idItem
	 * @param	{String}	onComplete
	 */
	savePref: function(action, value, idItem, onComplete) {
		Todoyu.Pref.save('portal', action, value, idItem, onComplete);
	},



	/**
	 * Update amount of result items in filter set list
	 * Only update if only one filterset is selected
	 *
	 * @param	{Number}	idFilterset
	 * @param	{Number}	numResults
	 */
	updateResultCounterOfActiveFiltersetInList: function(idFilterset, numResults) {
		this.PanelWidget.FilterPresetList.updateFiltersetResultCounter(idFilterset, numResults);
	}

};