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

Todoyu.Ext.project.TaskTree = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.project,

	sortable: null,



	/**
	 * Init project task tree
	 *
	 * @method	init
	 */
	init: function() {
		this.installObservers();
		this.initSortable();
	},



	/**
	 * Initialize sortable tree
	 *
	 * @method	initSortable
	 */
	initSortable: function() {
		var idProject		= this.getProjectID();
		var taskContainer	= $('project-' + idProject + '-tasks');

		if( taskContainer ) {
			this.sortable = new this.Sortable(taskContainer, {
				onChange: this.onSortingChange.bind(this, idProject)
			});
		}
	},



	/**
	 * Reload drag'n'drop sorting (reinitialize)
	 *
	 * @method	reloadSortable
	 */
	reloadSortable: function() {
		if( ! this.sortable ) {
			this.initSortable();
		} else {
			this.sortable.reload();
		}
	},



	/**
	 * Handler when sorting was changed with drag'n'drop
	 *
	 * @method	onSortingChange
	 * @param	{Number}	idProject
	 * @param	{Number}	idTaskDragged
	 * @param	{Number}	idTaskDrop
	 * @param	{String}	position
	 */
	onSortingChange: function(idProject, idTaskDragged, idTaskDrop, position) {
		this.removeEmptySubTaskContainers();

		var url		= Todoyu.getUrl('project', 'task');
		var options	= {
			parameters: {
				action: 	'dragdrop',
				project: 	idProject,
				taskDrag:	idTaskDragged,
				taskDrop:	idTaskDrop,
				position:	position
			},
			onComplete: this.onSortingSaved.bind(this, idProject, idTaskDragged, idTaskDrop, position)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when new sorting order was saved
	 *
	 * @method	onSortingSaved
	 * @param	{Number}		idProject
	 * @param	{Number}		idTaskDragged
	 * @param	{Number}		idTaskDrop
	 * @param	{String}		position
	 * @param	{Ajax.Response}	response
	 */
	onSortingSaved: function( idProject, idTaskDragged, idTaskDrop, position, response) {
		Todoyu.notifySuccess('[LLL:project.task.dragndrop.saved]', 'taskDragDrop');
	},



	/**
	 * Remove all empty sub task containers
	 *
	 * @method	removeEmptySubTaskContainers
	 */
	removeEmptySubTaskContainers: function() {
		$$('div.subtasks').each(function(container){
			if( container.empty() ) {
				container.remove();
			}
		});
	},



	/**
	 * Install observers
	 *
	 * @method	installObservers
	 */
	installObservers: function() {
		Todoyu.PanelWidget.observe('taskstatusfilter', this.onStatusFilterUpdate.bind(this));
	},



	/**
	 * Get tree's DOM element ID
	 *
	 * @method	tree
	 * @param	{Number}	idProject
	 */
	tree: function(idProject) {
		return $('project-' + idProject + '-tasks');
	},



	/**
	 * Toggle display of task tree of given project
	 *
	 * @method	toggle
	 * @param	{Number}	 idProject
	 */
	toggle: function(idProject) {
		if( this.tree(idProject) ) {
			this.tree(idProject).toggle();
		}
	},



	/**
	 * Hide task tree of given project
	 *
	 * @method	hide
	 * @param	{Number}	idProject
	 */
	hide: function(idProject) {
		var taskTree = this.tree(idProject);

		if( taskTree ) {
			taskTree.hide();
		}
	},



	/**
	 * Update task tree with a new filter configuration
	 *
	 * @method	updated
	 * @param	{Number}	idProject
	 * @param	{String}	filterName
	 * @param	{String}	filterValue
	 */
	update: function(idProject, filterName, filterValue) {
		if( Object.isUndefined(idProject) ) {
			idProject = this.ext.ProjectTaskTree.getActiveProjectID();
		}

		var url		= Todoyu.getUrl('project', 'tasktree');
		var options	= {
			parameters: {
				action:		'update',
				project:	idProject
			},
			onComplete: this.onUpdated.bind(this)
		};
		var target	= 'project-' + idProject + '-tasks';

		if( typeof(filterName) !== 'undefined' ) {
			options.parameters["filter[name]"] = filterName;
			options.parameters["filter[value]"] = filterValue;
		}

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Evoked after task tree updating has been completed. Adds the context menu.
	 *
	 * @method	onUpdated
	 * @param	{Ajax.Response}		response
	 */
	onUpdated: function(response) {
		this.addContextMenu();
		this.reloadSortable();
	},



	/**
	 * Evoked upon update of status filter: evokes update of given project's tree
	 *
	 * @method	onStatusFilterUpdate
	 * @param	{String}	widgetName
	 * @param	{Array}	params
	 */
	onStatusFilterUpdate: function(widgetName, params) {
		var idProject 	= this.getProjectID();
		var filterValue	= params.join(',');

		this.update(idProject, 'status', filterValue);
	},



	/**
	 * Get ID of currently active task tree project
	 *
	 * @method	getProjectID
	 * @return	{Number}
	 */
	getProjectID: function() {
		return this.ext.ProjectTaskTree.getActiveProjectID();
	},



	/**
	 * Toggle display of sub tasks and save resulting display state of given given task inside the task tree(, load sub tasks if toggled to be shown and not loaded yet)
	 *
	 * @method	toggleSubTasks
	 * @param	{Number}	idTask
	 * @param	{Function}	callback		Will get the task ID as parameter
	 */
	toggleSubTasks: function(idTask, callback) {
		if( !this.ext.Task.hasSubTasks(idTask) ) {
			this.onSubTasksToggled(idTask, callback);
			return;
		}

			// Load sub tasks if they are not already loaded
		if( this.areSubTasksLoaded(idTask) ) {
			var container = this.ext.Task.getSubTasksContainer(idTask);
			container.toggle();
			this.saveSubTaskOpenStatus(idTask, container.visible());
			this.onSubTasksToggled(idTask, callback);
		} else {
			this.loadSubTasks(idTask, this.onSubTasksToggled.bind(this, idTask, callback));
		}
	},



	/**
	 * Handler when sub tasks are toggled
	 *
	 * @method	onSubTasksToggled
	 * @param	{Number}		idTask
	 * @param	{Function}		callback
	 * @param	{Number}		[idTaskDummy]
	 * @param	{Ajax.Response}	[response]
	 */
	onSubTasksToggled: function(idTask, callback, idTaskDummy, response) {
		if( callback ) {
			callback(idTask);
		}
		this.reloadSortable();

		if( this.ext.Task.hasSubTasks(idTask) ) {
			var isVisible = this.ext.Task.getSubTasksContainer(idTask).visible();

			this.setSubTaskTriggerExpanded(idTask, isVisible);
		}
	},



	/**
	 * Expand sub tasks of given task in task tree
	 *
	 * @method	expandSubTasks
	 * @param	{Number}	idTask
	 */
	expandSubTasks: function(idTask, callback) {
		if( !this.areSubTasksVisible(idTask) ) {
			this.toggleSubTasks(idTask, callback);
		}
	},



	/**
	 * Toggle expand-option icon of given task
	 *
	 * @method	toggleSubTasksTriggerIcon
	 * @param	{Number}	idTask
	 */
	toggleSubTasksTriggerIcon: function(idTask) {
		if( this.ext.Task.hasSubTasksContainer(idTask) ) {
			var areSubtasksVisible = this.ext.Task.getSubTasksContainer(idTask).visible();
			this.setSubTaskTriggerExpanded(idTask, areSubtasksVisible);
		} else {
			var subtaskExpandTrigger = this.ext.Task.getSubTasksExpandTrigger(idTask);
			if( subtaskExpandTrigger ) {
				subtaskExpandTrigger.removeClassName('expandable');
			}
		}
	},




	/**
	 * @method	setSubtaskTriggerExpanded
	 * @deprecated
	 * @param	{Number}	idTask
	 * @param	{Boolean}	isExpanded
	 */
	setSubtaskTriggerExpanded: function(idTask, isExpanded) {
		this.setSubTaskTriggerExpanded(idTask, isExpanded);
	},

	/**
	 * Set sub task trigger to a specific state
	 *
	 * @method	setSubtaskTriggerExpanded
	 * @param	{Number}	idTask
	 * @param	{Boolean}	isExpanded
	 */
	setSubTaskTriggerExpanded: function(idTask, isExpanded) {
		var trigger	= this.ext.Task.getSubTasksExpandTrigger(idTask);
		var method	= isExpanded !== false ? 'addClassName' : 'removeClassName';

			// Show icon
		trigger.addClassName('expandable');
			// Set icon style
		trigger[method]('expanded');
	},



	/**
	 * Check whether sub tasks of given task are loaded (DOM elements exist)
	 *
	 * @method	areSubTasksLoaded
	 * @param	{Number}	idTask
	 * @return	{Boolean}
	 */
	areSubTasksLoaded: function(idTask) {
		return Todoyu.exists('task-' + idTask + '-subtasks');
	},



	/**
	 * Check whether sub tasks of given task are set visible currently
	 *
	 * @method	areSubTasksVisible
	 * @param	{Number} idTask
	 * @return	{Boolean}
	 */
	areSubTasksVisible: function(idTask) {
		var subTaskContainer = $('task-' + idTask + '-subtasks');

		return subTaskContainer && subTaskContainer.visible();
	},



	/**
	 * Load sub tasks
	 *
	 * @method	loadSubTasks
	 * @param	{Number}		idTask
	 * @param	{Function}	callback
	 */
	loadSubTasks: function(idTask, callback) {
		var url		= Todoyu.getUrl('project', 'task');
		var options	= {
			parameters: {
				action:	'subtasks',
				task:	idTask,
				show:	0
			},
			onComplete: this.onSubTasksLoaded.bind(this, idTask, callback)
		};
		var target	= 'task-' + idTask;

		Todoyu.Ui.insert(target, url, options);
	},



	/**
	 * Handler when sub tasks are loaded
	 *
	 * @method	onSubTasksLoaded
	 * @param	{Number}			idTask
	 * @param	{Function}		callback
	 * @param	{Ajax.Response}	response
	 */
	onSubTasksLoaded: function(idTask, callback, response) {
		this.ext.Task.addContextMenu(idTask);
		this.reloadSortable();

		if( typeof callback === 'function' ) {
			callback(idTask, response);
		}
	},



	/**
	 * Save task tree sub tasks being opened status pref
	 *
	 * @method	saveSubTaskOpenStatus
	 * @param	{Number}	idTask
	 * @param	{Boolean}	isOpen
	 */
	saveSubTaskOpenStatus: function(idTask, isOpen) {
		Todoyu.Pref.save('project', 'subtasks', isOpen ? 1 : 0, idTask);
	},



	/**
	 * Evoke (Re-)Adding of task tree (tasks') context menu
	 *
	 * @method	addContextMenu
	 */
	addContextMenu: function() {
		Todoyu.Ext.project.ContextMenuTask.attach();
	},



	/**
	 * Load sub tasks
	 *
	 * @method	loadSubTasks
	 * @param	{Number}		idTask
	 * @param	{Boolean}		[expand]
	 */
	reloadTask: function(idTask, expand) {
		expand	= expand || false;

		var url		= Todoyu.getUrl('project', 'task');
		var options	= {
			parameters: {
				action:	'get',
				task:	idTask,
				expand:	expand ? 1:0
			},
			onComplete: this.onReloadedTask.bind(this, idTask)
		};

		var target	= 'task-' + idTask;

		Todoyu.Ui.replace(target, url, options);
	},



	/**
	 * Handler when sub tasks are loaded
	 *
	 * @method	onReloadedTask
	 * @param	{Number}			idTask
	 * @param	{Ajax.Response}		response
	 */
	onReloadedTask: function(idTask, response) {
		this.onSubTasksLoaded(idTask);
	}

};