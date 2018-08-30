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
 * Functions to handle projects
 *
 * @class		Project
 * @namespace	Todoyu.Ext.project
 */
Todoyu.Ext.project.Project = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.project,



	/**
	 * Get DOM element of header of project with given ID
	 *
	 * @method	getHeader
	 * @param	{Number}		idProject
	 * @return	{Element}
	 */
	getHeader: function(idProject) {
		return $('project-' + idProject + '-header');
	},



	/**
	 * Edit given project
	 *
	 * @method	edit
	 * @param	{Number}	idProject
	 */
	edit: function(idProject){
		if( Todoyu.getArea() != 'project' ) {
			Todoyu.goTo('project', 'ext', {
				action: 'edit',
				project: idProject
			});
		}

		this.hideDetails(idProject);
		this.ext.TaskTree.hide(idProject);

		this.Edit.createFormWrapDivs(idProject);
		this.Edit.loadForm(idProject);
	},



	/**
	 * Delete given project
	 *
	 * @method	remove
	 * @param	{Number}	idProject
	 */
	remove: function(idProject) {
		if( confirm('[LLL:project.ext.js.removeProject]') ) {
			var url		= Todoyu.getUrl('project', 'project');
			var options	= {
				parameters: {
					action:		'remove',
					project:	idProject
				},
				onComplete: this.onRemoved.bind(this, idProject)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handle completion event after project having been deleted. Remove project from project task tree and remove project tab.
	 *
	 * @method	onRemoved
	 * @param	{Number}		idProject
	 */
	onRemoved: function(idProject) {
		Todoyu.notifySuccess('[LLL:project.ext.js.project.deleted]');

		if( Todoyu.isInArea('project') ) {
			this.ext.ProjectTaskTree.removeProject(idProject);
			this.ext.ProjectTaskTree.openFirstTab();
			this.removeProjectSubNaviItem(idProject);
		} else {
			$('project-' + idProject).fade();
		}

		Todoyu.Hook.exec('project.project.removed', idProject);
	},



	/**
	 * Remove a project from the project tab (dropdown menu) sub navigation
	 *
	 * @method	removeProjectSubNaviItem
	 * @param	{Number}	idProject
	 */
	removeProjectSubNaviItem: function(idProject) {
		var subnavi	= $('navi-main-list').down('li.itemProject').down('ul');

		if( ! Object.isUndefined(subnavi) ) {
			var item	= subnavi.down('li.itemProject' + idProject);

			if( ! Object.isUndefined(item) ) {
				item.remove();
			}
		}
	},



	/**
	 * Toggle display of project details
	 *
	 * @method	toggleDetails
	 * @param	{Number}	idProject
	 */
	toggleDetails: function(idProject) {
		var detailDiv	= $('project-' + idProject + '-details');

		if( ! detailDiv.visible() ) {
			if( detailDiv.empty() ) {
				var url		= Todoyu.getUrl('project', 'project');
				var options	= {
					parameters: {
						action:		'details',
						project:	idProject
					}
				};
				Todoyu.Ui.update(detailDiv, url, options);
			}
			this.setExpandedStyle(idProject, true);
			detailDiv.show();
		} else {
			this.setExpandedStyle(idProject, false);
			detailDiv.hide();
		}

		Todoyu.Hook.exec('project.project.detailsToggled', idProject);
		this.saveDetailsExpanded(idProject, detailDiv.visible());
	},



	/**
	 * Set project style expanded/ collapsed
	 *
	 * @method	setExpandedStyle
	 * @param	{Number}	idProject
	 * @param	{Boolean}	isExpanded
	 */
	setExpandedStyle: function(idProject, isExpanded) {
		var project = $('project-' + idProject);

		if( isExpanded ) {
			project.addClassName('expanded');
		} else {
			project.removeClassName('expanded');
		}
	},



	/**
	 * Save state of project details being expanded
	 *
	 * @method	saveDetailsExpanded
	 * @param	{Number}	idProject
	 * @param	{Boolean}	expanded
	 */
	saveDetailsExpanded: function(idProject, expanded) {
		Todoyu.Pref.save('project', 'detailsexpanded', expanded ? 1 : 0, idProject, 0);
	},



	/**
	 * Hide details of given project
	 *
	 * @method	hideDetails
	 * @param	{Number}	idProject
	 */
	hideDetails: function(idProject) {
		Todoyu.Ui.hide('project-' + idProject + '-details');
	},



	/**
	 * Show project details
	 *
	 * @method	showDetails
	 * @param	{Number}	idProject
	 */
	showDetails: function(idProject) {
		Todoyu.Ui.show('project-' + idProject + '-details');
	},



	/**
	 * Add task to given project
	 *
	 * @method	addTask
	 * @param	{Number}		idProject
	 */
	addTask: function(idProject) {
		if( Todoyu.isInArea('project')  ) {
			this.ext.Task.addTaskToProject(idProject);
		} else {
			Todoyu.goTo('project', 'ext', {
				action: 	'addtask',
				project:	idProject
			});
		}
	},



	/**
	 * Add new container to given project
	 *
	 * @method	addContainer
	 * @param	{Number}		idProject
	 */
	addContainer: function(idProject) {
		if( Todoyu.isInArea('project') ) {
			this.ext.Task.addContainerToProject(idProject);
		} else {
			Todoyu.goTo('project', 'ext', {
				action: 	'addcontainer',
				project: 	idProject
			});
		}
	},



	/**
	 * Refresh given project display
	 *
	 * @method	refresh
	 * @param	{Number}		idProject
	 */
	refresh: function(idProject) {
		var url		= Todoyu.getUrl('project', 'project');
		var options	= {
			parameters: {
				action:		'details',
				project:	idProject
			}
		};
		var target	= 'project-' + idProject + '-details';

		if( Todoyu.exists(target) ) {
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * Change status of given project to given status
	 *
	 * @method	updateStatus
	 * @param	{Number}		idProject
	 * @param	{Number}		status
	 */
	updateStatus: function(idProject, status) {
		var url	= Todoyu.getUrl('project', 'project');
		var options	= {
			parameters: {
				action:		'setstatus',
				project:	idProject,
				status:		status
			},
			onComplete:	this.onStatusUpdated.bind(this, idProject, status)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when project status has been updated: refresh project display and set status
	 *
	 * @method	onStatusUpdated
	 * @param	{Number}			idProject
	 * @param	{Number}			status
	 * @param	{Ajax.Response}		response
	 */
	onStatusUpdated: function(idProject, status, response) {
		this.refresh(idProject);
		this.setStatus(idProject, status);

		Todoyu.Hook.exec('project.project.saved', idProject);
	},



	/**
	 * Get current status of given project
	 *
	 * @method	getStatus
	 * @param	{Number}	idProject
	 */
	getStatus: function(idProject) {
		var project		= $('project-' + idProject);
		var statusBar	= project.down('div.projectstatus') || project.down('span.headLabel');

		return this.ext.getStatusOfElement(statusBar);
	},



	/**
	 * Set project status
	 *
	 * @method	setStatus
	 * @param	{Number}		idProject
	 * @param	{Number}		status
	 */
	setStatus: function(idProject, status) {
		var project		= $('project-' + idProject);
		var statusBar	= project.down('div.projectstatus') || project.down('span.headLabel');

		this.ext.setStatusOfElement(statusBar, status);
	},



	/**
	 * Paste a task into given project
	 *
	 * @method	pasteTask
	 * @param	{Number}		idProject
	 */
	pasteTask: function(idProject) {
		var url		= Todoyu.getUrl('project', 'project');
		var options	= {
			parameters: {
				action:	'pasteInProject',
				project:idProject
			},
			onComplete: this.onTaskPasted.bind(this, idProject)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when task is pasted into project: insert/move task item
	 *
	 * @method	onTaskPasted
	 * @param	{Number}			idProject
	 * @param	{Ajax.Response}		response
	 */
	onTaskPasted: function(idProject, response) {
		var idTaskNew		= response.getTodoyuHeader('idTask');
		var clipboardMode	= response.getTodoyuHeader('clipboardMode');


			// If task was cut, remove old element
		if( clipboardMode === 'cut' ) {
			this.ext.Task.removeTaskElement(idTaskNew);
		}

		var taskContainer	= $('project-' + idProject + '-tasks');

		if( taskContainer.down('.lostTasks') ) {
				// Insert before lost tasks
			taskContainer.down('.lostTasks').insert({
				before: response.responseText
			});
		} else {
				// Insert as last item
			taskContainer.insert({
				bottom: response.responseText
			});
		}

			// Attach context menu to all tasks (so the pasted ones get one too)
		this.ext.ContextMenuTask.attach();
			// Highlight the new pasted task
		this.ext.Task.highlight(idTaskNew);
		this.ext.Task.highlightSubTasks(idTaskNew);
	},



	/**
	 * Toggle project info box content
	 *
	 * @param	{String}	boxKey
	 */
	toggleInfoBox: function(boxKey) {
		var header	= $('project-infobox-' + boxKey + '-header');
		var content	= $('project-infobox-' + boxKey + '-content');
		var closed	= content.visible();

		content.toggle();
		header.toggleClassName('closed');
	}

};
