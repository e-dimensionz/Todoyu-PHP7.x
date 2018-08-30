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
 * @module	Daytracks
 */

/**
 * Panel widget: daytracks
 */
Todoyu.Ext.daytracks.PanelWidget.Daytracks = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:		Todoyu.Ext.daytracks,

	/**
	 * @property	timeTask
	 * @type		Number
	 */
	timeTask:	0,

	/**
	 * @property	timeTotal
	 * @type		Number
	 */
	timeTotal:	0,

	/**
	 * @property	spanTimeTask
	 * @type		Number
	 */
	spanTimeTask:	null,

	/**
	 * @property	spanTimeTotal
	 * @type		Number
	 */
	spanTimeTotal:	null,



	/**
	 * Init daytrack panelWidget
	 *
	 * @method	init
	 */
	init: function() {
		this.registerTimetracking();
		this.registerHooks();
		this.ContextMenu.init();
	},



	/**
	 * Register to timetracking callbacks
	 *
	 * @method	registerTimetracking
	 */
	registerTimetracking: function() {
		Todoyu.Ext.timetracking.addToggle('daytracks', this.onTrackingToggle.bind(this), this.onTrackingToggleUpdate.bind(this));
		Todoyu.Ext.timetracking.addTick(this.onTrackingClockUpdate.bind(this));
	},



	/**
	 * Register JS hooks of daytracks
	 *
	 * @method	registerHooks
	 */
	registerHooks: function() {
		Todoyu.Hook.add('project.task.statusUpdated', this.onTaskStatusUpdated.bind(this));
		Todoyu.Hook.add('project.quickTask.saved', this.onQuickTaskAdded.bind(this));
		Todoyu.Hook.add('project.task.removed', this.onTaskDeleted.bind(this));
	},



	/**
	 * Go to given task
	 *
	 * @method	goToTask
	 * @param	{Number}	idProject
	 * @param	{Number}	idTask
	 */
	goToTask: function(idProject, idTask) {
		if( 	(Todoyu.getArea() !== 'project' && Todoyu.Ext.project.Task.isTaskInCurrentView(idTask))
			||	(Todoyu.getArea() === 'project' && Todoyu.Ext.project.Task.isProjectOfTaskVisible(idTask))
		) {
			$('task-' + idTask).scrollToElement();
		} else {
			Todoyu.Ext.project.goToTaskInProject(idTask, idProject);
		}
	},



	/**
	 * Toggle total label (if no time was tracked before)
	 *
	 * @method	toggleTotal
	 */
	toggleTotal: function() {
		$('daytracks-daytotal').show();

		if( $('daytracks-daytotal-notracks') ) {
			$('daytracks-daytotal-notracks').hide();
		}
	},



	/**
	 * Refresh widget
	 *
	 * @method	refresh
	 */
	refresh: function() {
		var target = 'panelwidget-daytracks-content';
		var url = Todoyu.getUrl('daytracks', 'panelwidget');
		var options = {
			parameters: {
				action:	'update'
			},
			onComplete: this.onRefreshed.bind(this)
		};

		// Update dayTracks list
		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * onRefreshed dayTracks event handler
	 *
	 * @method	onRefreshed
	 * @param	{Ajax.Response}		response
	 */
	onRefreshed: function(response) {
		this.ContextMenu.attach();
	},



	/**
	 * Toggle timeTracking of given task
	 *
	 * @method	toggleTimetracking
	 * @param	{Number}	idTask
	 */
	toggleTimetracking: function(idTask) {
		Todoyu.Ext.timetracking.toggle(idTask);
	},



	/**
	 * Update task status
	 *
	 * @method	updateTaskStatus
	 * @param	{Number}		idTask
	 * @param	{String}		status
	 */
	updateTaskStatus: function(idTask, status) {
		Todoyu.Ext.project.Task.updateStatus(idTask, status);
	},



	/**
	 * Handler when task status is updated and hook is called
	 *
	 * @method	onTaskStatusUpdated
	 * @param	{Number}		idTask
	 * @param	{Number}		status
	 */
	onTaskStatusUpdated: function(idTask, status) {
		this.refresh();
	},



	/**
	 * Timetracking toggle-handler
	 *
	 * @method	onTrackingToggle
	 * @param	{Number}		idTask
	 * @param	{Boolean}		start
	 * @return	{Boolean}		Do not send any data in the tracking request
	 */
	onTrackingToggle: function(idTask, start) {
		if( start ) {
			this.toggleTotal();
		}

		return false;
	},



	/**
	 * Handler when tracking timesheet toggling updated
	 *
	 * @method	onTrackingToggleUpdate
	 * @param	{Number}			idTask
	 * @param	{Object}			data
	 * @param	{Ajax.Response}		response
	 */
	onTrackingToggleUpdate: function(idTask, data, response) {
		this.setContent(data);
	},



	/**
	 * Update content
	 *
	 * @method	setContent
	 * @param	{String}	html
	 */
	setContent: function(html) {
		$('panelwidget-daytracks-content').update(html);
		this.ContextMenu.attach();
	},



	/**
	 * Handle timetracking event: clock update
	 *
	 * @method	OnTrackingClockUpdate
	 * @param	{Number}	idTask
	 * @param	{Number}	trackedTotal
	 * @param	{Number}	trackedToday
	 * @param	{Number}	trackedCurrent
	 */
	onTrackingClockUpdate: function(idTask, trackedTotal, trackedToday, trackedCurrent) {
		var taskTimeToday	= Todoyu.Time.timeFormatSeconds(trackedToday + trackedCurrent);
			// Update current task time
		if( $('daytracks-track-' + idTask + '-time') ) {
			$('daytracks-track-' + idTask + '-time').update(taskTimeToday);
		}

		var timeElements= $('panelwidget-daytracks-content').select('ul li a span.time');
		var timeToday	= 0;

			// Sum up all task trackings
		timeElements.each(function(element){
			timeToday += Todoyu.Time.parseTimeToSeconds(element.innerHTML);
		});

		var totalTimeToday	= Todoyu.Time.timeFormatSeconds(timeToday);
		$('daytracks-daytotal-time').update(totalTimeToday);
	},



	/**
	 * Handler after quicktask has been added - refresh daytracks widget
	 *
	 * @method	onQuickTaskAdded
	 * @param	{Number}			idTask
	 * @param	{Number}			idProject
	 * @param	{Ajax.Response}		response
	 */
	onQuickTaskAdded: function(idTask, idProject, response) {
		this.refresh();
	},



	/**
	 * Handler after task was deleted
	 *
	 * @method	onTaskDeleted
	 * @param	{Number}	idTask
	 */
	onTaskDeleted: function(idTask) {
		this.refresh();
	},

	

	/**
	 * Evoke showing of timetracking history
	 *
	 * @method	showHistory
	 */
	showHistory: function() {
		this.ext.History.show();
	},



	/**
	 * Check whether task element exists within current view
	 * Wrapper for backwards compatibility
	 *
	 * @deprecated
	 * @method	isTaskInCurrentView
	 * @return	{Boolean}
	 */
	isTaskInCurrentView: function(idTask) {
		return Todoyu.Ext.project.Task.isTaskInCurrentView(idTask);
	}

};