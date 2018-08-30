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
 * Main project object
 *
 * @class		project
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.project = {

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},

	/**
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet: {},



	/**
	 *
	 */
	altPressed: false,



	/**
	 * Initialization
	 *
	 * @method	init
	 */
	init: function() {
		this.registerHooks();
	},



	/**
	 * Register callbacks to JS hooks
	 *
	 * @method	registerHooks
	 */
	registerHooks: function() {
		Todoyu.Hook.add('project.project.created', this.Project.Edit.onProjectCreated.bind(this.Project.Edit));
		Todoyu.Hook.add('core.contextmenu', this.ContextMenuProjectInline.onContextMenu.bind(this.ContextMenuProjectInline));
//		Todoyu.Hook.add('project.task.saved', this.Task.onProjectTaskAdded(response);

			// Register area specific callbacks
		if( Todoyu.isInArea('project') ) {
			Todoyu.Hook.add('panelwidget.projectlist.onProjectClick', this.ProjectTaskTree.onPanelwidgetProjectlistProjectClick.bind(this.ProjectTaskTree));
		}
	},



	/**
	 * To be called from other areas (e.g portal) to jump to a specific task within its project,
	 *	to be shown inside the project area
	 *
	 * @method	goToTaskInProject
	 * @param	{Number}	idTask
	 * @param	{Number}	idProject
	 * @param	{Boolean}	newWindow
	 * @param	{String}	windowName
	 */
	goToTaskInProject: function(idTask, idProject, newWindow, windowName) {
			// URL + hash are already set but project/task not available? reload
		var params	= {
			project:	idProject,
			task:		idTask
		};
		if( 	Todoyu.isCurrentLocationHref('project', 'ext', params, 'task-' + idTask)
			&& (!Todoyu.Tabs.hasTab('project', idProject) || !this.Task.isProjectOfTaskVisible(idTask) )
		) {
			document.location.reload();
		} else {
			newWindow	= newWindow ? newWindow : false;
			windowName	= windowName ? windowName : '';

			params = {
				task: idTask
			};
			if( ! Object.isUndefined(idProject) ) {
				params.project = idProject;
			}

			Todoyu.goTo('project', 'ext', params, 'task-' + idTask, newWindow, windowName);
		}
	},



	/**
	 * Open given task for editing in it's project in project area
	 *
	 * @method	editTaskInProject
	 * @param	{Number}	idTask
	 */
	editTaskInProject: function(idTask, idProject, windowName) {
		windowName	= windowName ? windowName : '';

		var params = {
			task: idTask,
			edit: idTask
		};
		if( ! Object.isUndefined(idProject) ) {
			params.project = idProject;
		}

		Todoyu.goTo('project', 'ext', params, 'task-' + idTask, true, windowName);
	},



	/**
	 * @method	goToTaskInProjectByTasknumber
	 * @deprecated
	 * @param	{Number}	taskNumber
	 */
	goToTaskInProjectByTasknumber: function(taskNumber) {
		this.goToTaskInProjectByTaskNumber(taskNumber);
	},

	/**
	 * Go to a task in project view, if you have only the full tasknumber (no task ID)
	 * Gets the task ID by AJAX and redirects the browser
	 *
	 * @method	goToTaskInProjectByTasknumber
	 * @param	{String}	taskNumber
	 */
	goToTaskInProjectByTaskNumber: function(taskNumber) {
		var url		= Todoyu.getUrl('project', 'task');
		var options	= {
			parameters: {
				action:		'number2id',
				tasknumber: taskNumber
			},
			onComplete: this.onGoToTaskInProjectByTasknumber.bind(this, taskNumber)
		};

		Todoyu.send(url, options);
	},



	/**
	 * @method	onGoToTaskInProjectByTasknumber
	 * @deprecated
	 * @param	{String}		taskNumber
	 * @param	{Ajax.Response}	response
	 */
	onGoToTaskInProjectByTasknumber: function(taskNumber, response) {
		this.onGoToTaskInProjectByTaskNumber(taskNumber, response);
	},

	/**
	 * Handler for task IDs request
	 * responseText is the task ID
	 *
	 * @method	onGoToTaskInProjectByTaskNumber
	 * @param	{String}		taskNumber
	 * @param	{Ajax.Response}	response
	 */
	onGoToTaskInProjectByTaskNumber: function(taskNumber, response) {
		var idTask	= parseInt(response.responseText, 10);

		this.goToTaskInProject(idTask);
	},



	/**
	 * Toggle task tree of given project
	 *
	 * @method	toggleTaskTree
	 * @param	{Number}	idProject
	 */
	toggleTaskTree: function(idProject) {
		this.TaskTree.toggle();

		Todoyu.Helper.toggleImage(
			'project-' + idProject + '-tasktreetoggle-image',
			'asset/img/toggle_plus.png',
			'asset/img/toggle_minus.png'
		);
	},



	/**
	 * Event handler: 'onTreeUpdate'
	 *
	 * @method	onTreeUpdate
	 * @param	{Ajax.Response}		response
	 */
	onTreeUpdate: function(response) {
		this.attachContextMenu();

		if( response.getHeader('Todoyu-hash') ) {
			window.location.hash = response.getHeader('Todoyu-hash');
		}
	},



	/**
	 * Attach project and task context menus
	 *
	 * @method	attachContextMenu
	 */
	attachContextMenu: function() {
		this.ContextMenuProject.attach();
		this.ContextMenuTask.attach();
		this.ContextMenuProjectInline.attach();
	},



	/**
	 * Save project pref
	 *
	 * @method	savePref
	 * @param	{String}	preference
	 * @param	{String}	value
	 * @param	{Number}	idItem
	 * @param	{String}	onComplete
	 */
	savePref: function(preference, value, idItem, onComplete) {
		Todoyu.Pref.save('project', preference, value, idItem, onComplete);
	},



	/**
	 * Open popup with quick create for project
	 *
	 * @method	openProjectPopup
	 */
	openProjectPopup: function() {
		Todoyu.Headlets.getHeadlet('todoyuheadletquickcreate').openTypePopup('project','project')
	},



	/**
	 * Get status of an element
	 *
	 * @method	getStatusOfElement
	 * @param	{Element}	element
	 * @return	{String}
	 */
	getStatusOfElement: function(element) {
		element			= $(element);
		var statusIndex	= 0;

		if( element ) {
			var statusClass	= element.getClassNames().grep(/.*Status(\d)/).first();

			if( statusClass ) {
				statusIndex		= statusClass.split('Status').last();
			}
		}

		return statusIndex;
	},



	/**
	 * Set status for an element
	 * Element has to contain a class which has the format Status4
	 * Status class can also be prefixed: bcStatus4
	 *
	 * @method	setStatusOfElement
	 * @param	{Element|String}	element
	 * @param	{Number}			newStatus
	 */
	setStatusOfElement: function(element, newStatus) {
		element			= $(element);

		if( element ) {
			var oldStatus	= this.getStatusOfElement(element);

			if( oldStatus ) {
				element.className	= element.className.replace('Status' + oldStatus, 'Status' + newStatus);
			}
		}
	},



	/**
	 *
	 * @param	{Number}	idProject
	 * @param	{Number}	taskNumber
	 */
	quickSearch: function(idProject, taskNumber) {
		if ( idProject && taskNumber && !isNaN(parseInt(idProject)) && !isNaN(parseInt(taskNumber)) ) {
			var url = Todoyu.getUrl('project', 'task');
			var options = {
				parameters: {
					action: 'number2id',
					tasknumber: idProject + '.' + taskNumber
				},
				onComplete: this.goToTaskInProjectQuickSearch.bind(this, idProject)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 *
	 * @param [Number}	idProject
	 * @param response
	 */
	goToTaskInProjectQuickSearch: function(idProject, response) {
		var idTask = response.responseText;

		if( idTask != 0) {
			Todoyu.Ext.project.goToTaskInProject(idTask, idProject, false, '');
		}
	}
};