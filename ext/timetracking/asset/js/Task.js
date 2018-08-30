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
 * @module	Timetracking
 */

Todoyu.Ext.timetracking.Task = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.timetracking,



	/**
	 * Initialize timetracking: register clock callbacks
	 *
	 * @method	init
	 */
	init: function() {
		this.registerClockCallbacks();
	},



	/**
	 * Start timetracking of given task
	 *
	 * @method	start
	 * @param	{Number}	idTask
	 */
	start: function(idTask) {
		this.ext.start(idTask);
	},



	/**
	 * Stop timetracking of given task, reset timetrack button style
	 *
	 * @method	stop
	 * @param	{Number}	idTask
	 */
	stop: function(idTask) {
		this.ext.stop();
	},



	/**
	 * Register timetracking clock callbacks
	 *
	 * @method	registerClockCallbacks
	 */
	registerClockCallbacks: function() {
		this.ext.addToggle('tasktab', this.onTrackingToggle.bind(this), this.onTrackingToggleUpdate.bind(this));
		this.ext.addTick(this.onClockTick.bind(this));
	},



	/**
	 * Callback if tracking is toggled
	 *
	 * @method	onTrackingToggle
	 * @param	{Number}	idTask
	 * @param	{Boolean}	start
	 * @return	{Array}		List of tasks to update
	 */
	onTrackingToggle: function(idTask, start) {
		var info = [];
		var idTaskCurrent	= this.ext.getTaskID();

		this.ext.removeAllRunningStyles();

		if( start ) {
			this.setRunningStyle(idTask, start);

				// Update task status
			if( Todoyu.Ext.project.Task.isLoaded(idTask) ) {
				var oldStatus	= Todoyu.Ext.project.Task.getStatus(idTask);

				this.enforceTaskProgressStatusOnTrackingStart(idTask, oldStatus);

				Todoyu.Hook.exec('timetracking.tracking.toggleStart', idTask, oldStatus);
			}

			if( this.isTaskTrackingTabLoaded(idTask) ) {
				info.push(idTask);
			}

			if( idTaskCurrent !== idTask && this.isTaskTrackingTabLoaded(idTaskCurrent) ) {
				info.push(idTaskCurrent);
			}
		} else {
			if( this.isTaskTrackingTabLoaded(idTask) ) {
				info.push(idTask);
			}
		}

		return info;
	},



	/**
	 * Enforce task progress status
	 *
	 * @param	{Number}	idTask
	 * @param	{Number}	oldStatus
	 */
	enforceTaskProgressStatusOnTrackingStart: function(idTask, oldStatus) {
		if( oldStatus != 3 ) { // Not In progress
			Todoyu.Ext.project.Task.setStatus(idTask, 3); // In progress
		}
	},



	/**
	 * Update task timetracking tabs with data from tracking request
	 *
	 * @method	onTrackingToggleUpdate
	 * @param	{Number}		idTask
	 * @param	{Object}		data
	 * @param	{Ajax.Response}	response
	 */
	onTrackingToggleUpdate: function(idTask, data, response) {
		if( typeof(data) === 'object' ) {
			$H(data).each(function(pair){
				this.setTabContent(pair.key, pair.value);
			}, this);
		}
	},



	/**
	 * Callback if clock ticked (every second)
	 *
	 * @method	onClockTick
	 * @param	{Number}	idTask
	 * @param	{Number}	trackedTotal
	 * @param	{Number}	trackedToday
	 * @param	{Number}	trackedCurrent
	 */
	onClockTick: function(idTask, trackedTotal, trackedToday, trackedCurrent) {
		var el = $('task-' + idTask + '-timetrack-currentsession');
		if( el ) {
			el.update(Todoyu.Time.timeFormatSeconds(trackedCurrent));
		}
	},



	/**
	 * Check whether given task's timetracking tab is loaded
	 *
	 * @method	isTaskTrackingTabLoaded
	 * @param	{Number}	idTask
	 * @return	{Boolean}
	 */
	isTaskTrackingTabLoaded: function(idTask) {
		return Todoyu.exists('task-' + idTask + '-tabcontent-timetracking');
	},



	/**
	 * Set task style 'running', indicating visually that it is currently not / being timetracked
	 *
	 * @method	setRunningStyle
	 * @param	{Number}	idTask
	 * @param	{Boolean}	running
	 */
	setRunningStyle: function(idTask, running) {
		if( Todoyu.exists('task-' + idTask) ) {
			if( running ) {
				$('task-' + idTask).addClassName('running');
			} else {
				$('task-' + idTask).removeClassName('running');
			}
		}
	},



	/**
	 * Update timetracking tab (contains start / stop button, list of prev. tracked times, etc.) of given task.
	 *
	 * @method	updateTab
	 * @param	{Number}	idTask
	 */
	updateTab: function(idTask) {
		var target	= 'task-' + idTask + '-tabcontent-timetracking';

		if( Todoyu.exists(target) ) {
			var url		= Todoyu.getUrl('timetracking', 'tasktab');
			var options	= {
				parameters: {
					action:	'update',
					task:	idTask
				}
			};
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * @method	updateTrack
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 */
	updateTrack: function(idTask, idTrack) {
		var target	= 'task-' + idTask + '-track-' + idTrack;

		var url		= Todoyu.getUrl('timetracking', 'track');
		var options	= {
			parameters: {
				action:	'update',
				task:	idTask,
				track:	idTrack
			}
		};
		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Set new HTML content for tab content
	 *
	 * @method	setTabContent
	 * @param	{Number}	idTask
	 * @param	{String}	html
	 */
	setTabContent: function(idTask, html) {
		var target	= 'task-' + idTask + '-tabcontent-timetracking';

		if( Todoyu.exists(target) ) {
			$(target).update(html);
		}
	},



	/**
	 * Update time tracking list of given task
	 *
	 * @method	updateTrackList
	 * @param	{Number}	idTask
	 */
	updateTrackList: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			parameters: {
				action:	'tracklist',
				task:	idTask
			},
			onComplete: this.onTrackListUpdated.bind(this, idTask)
		};
		var target	= 'task-' + idTask + '-timetracks' ;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Handle track list updated
	 *
	 * @method	onTrackListUpdated
	 * @param	{Number}		idTask
	 * @param	{Ajax.Response}	response
	 */
	onTrackListUpdated: function(idTask, response) {

	},



	/**
	 * Update control box in timetracking tab of given task
	 *
	 * @method	updateTabControl
	 * @param	{Number}	idTask
	 */
	updateTabControl: function(idTask) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			parameters: {
				action:	'control',
				task:	idTask
			}
		};
		var target	= 'task-' + idTask + '-timetrack-control' ;

		if( Todoyu.exists(target) ) {
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * Get track edit form
	 *
	 * @method	editTrack
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 */
	editTrack: function(idTask, idTrack) {
		var url		= Todoyu.getUrl('timetracking', 'tasktab');
		var options	= {
			parameters: {
				action:	'edittrack',
				track:	idTrack
			},
			onComplete: this.onEditFormLoaded.bind(this, idTask, idTrack)
		};
		var target 	= 'task-' + idTask + '-track-' + idTrack;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Event handler: edit form loaded
	 *
	 * @method	onEditFormLoaded
	 * @param	{Number}		idTask
	 * @param	{Number}		idTrack
	 * @param	{Ajax.Response}	response
	 */
	onEditFormLoaded: function(idTask, idTrack, response) {
		var fieldTrackedTime	= 'timetrack-' + idTrack + '-fieldTrackedTime-workload-tracked';

		if( Todoyu.exists(fieldTrackedTime) ) {
			$(fieldTrackedTime).select();
		}
	},



	/**
	 * Save edited track
	 *
	 * @method	saveTrack
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 */
	saveTrack: function(idTask, idTrack) {
		$('timetrack-' + idTrack + '-form').request({
			parameters: {
				action: 'updatetrack',
				area:	Todoyu.getArea()
			},
			onComplete: this.onTrackSaved.bind(this, idTask, idTrack)
		});
	},



	/**
	 * Event handler: being evoked after edited track has been saved
	 *
	 * @method	onTrackSaved
	 * @param	{Number}		idTask
	 * @param	{Number}		idTrack
	 * @param	{Ajax.Response}	response
	 */
	onTrackSaved: function(idTask, idTrack, response) {
		var totalChargeableTime = response.getTodoyuHeader('chargeableTime');

		this.updateChargeableTime(idTask, totalChargeableTime);
		this.updateTrack(idTask, idTrack);
	},



	/**
	 * Update chargable time value
	 *
	 * @method	updateChargeableTime
	 * @param	{Number}	idTask
	 * @param	{Number}	time
	 */
	updateChargeableTime: function(idTask, time) {
		var valueElement		= $('task-' + idTask + '-timetrack-chargeabletime');

		if( valueElement ) {
			valueElement.update(Todoyu.Time.timeFormatSeconds(time));
		}
	},



	/**
	 * Cancel track editing
	 *
	 * @method	cancelTrackEditing
	 * @param	{Number}	idTask
	 * @param	{Number}	idTrack
	 */
	cancelTrackEditing: function(idTask, idTrack) {
		this.updateTrack(idTask, idTrack);
	},



	/**
	 * Toggle timetracks list visibility
	 *
	 * @method	toggleList
	 * @param	{Number}	idTask
	 */
	toggleList: function(idTask) {
		Todoyu.Ui.toggle('task-' + idTask + '-timetracks');
	},



	/**
	 * Scroll to given task if in current page, otherwise show in project area. Optionally close headlet before going to task
	 *
	 * @method	goToTask
	 * @param	{Number}	idProject
	 * @param	{Number}	idTask
	 * @param	{Boolean}	[closeHeadletBefore]
	 */
	goToTask: function(idProject, idTask, closeHeadletBefore) {
		closeHeadletBefore	= closeHeadletBefore || false;

		if( closeHeadletBefore !== false ) {
				// Close headlet via AJAX return here after via onComplete reference
			Todoyu.Headlets.getHeadlet('todoyutimetrackingheadlettracking').hide();
			Todoyu.Headlets.submitOpenStatus(this.goToTask.bind(this, idProject, idTask, false));
		} else {
			if( 	(Todoyu.getArea() !== 'project' && Todoyu.Ext.project.Task.isTaskInCurrentView(idTask))
				||	(Todoyu.getArea() === 'project' && Todoyu.Ext.project.Task.isProjectOfTaskVisible(idTask))
			) {
				Todoyu.Ext.project.Task.scrollTo(idTask);
			} else {
				Todoyu.Ext.project.goToTaskInProject(idTask, idProject);
			}
		}
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