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

/**
 * Timetracking headlet
 *
 * @class		Timetracking
 * @namespace	Todoyu.Ext.timetracking.Headlet
 */
Todoyu.Ext.timetracking.Headlet.Timetracking = Class.create(Todoyu.Headlet, {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.timetracking,

	/**
	 * @property	info
	 * @type		unknown
	 */
	info: null,

	/**
	 * @property	barClasses
	 * @type		Object
	 */
	barClasses: [{
			value: 100,
			color: 'red'
		},{
			value: 90,
			color: 'orange'
		},{
			value: 80,
			color: 'yellow'
		},{
			value: 0,
			color: '#cadb98'
		}
	],



	/**
	 * Initialize timetracking headlet (register timetracking).
	 *
	 * @method	initialize
	 * @param	{Function}	$super		Parent constructor: Todoyu.Headlet.initialize
	 * @param	{String}	name
	 */
	initialize: function($super, name) {
		$super(name);

			// Listen to task status changes
		Todoyu.Hook.add('project.task.statusUpdated', this.onTaskStatusChange.bind(this));

			// Register timetracking
		this.ext.addToggle('trackheadlet', this.onTrackingToggle.bind(this), this.onTrackingToggleUpdate.bind(this));
		this.ext.addTick(this.onClockTick.bind(this));
	},



	/**
	 * Hook called when a task status changes
	 * Change status of headlet task if loaded
	 *
	 * @method	onTaskStatusChange
	 * @param	{Number}	idTask
	 * @param	{Number}	status
	 */
	onTaskStatusChange: function(idTask, status) {
		var task	= $('todoyutimetrackingheadlettracking-task-' + idTask);

		if( task ) {
			Todoyu.Ext.project.setStatusOfElement(task.down('.headLabel'), status);
		}
	},



	/**
	 * Handler when clicked on button
	 *
	 * @method	onButtonClick
	 * @param	{Function}	$super		Todoyu.Headlet.onButtonClick
	 * @param	{Event}		event
	 */
	onButtonClick: function($super, event) {
		$super(event);
	},



	/**
	 * Handler when clicked on content
	 *
	 * @method	onContentClick
	 * @param	{Event}		event
	 */
	onContentClick: function(event) {
		this.setActive();
	},



	/**
	 * Hide timetracking headlet, save display state
	 *
	 * @method	hide
	 * @param	{Function}	$super		Todoyu.Headlet.hide
	 */
	hide: function($super) {
		$super();
	},



	/**
	 * Callback for timetracking toggling
	 *
	 * @method	onTrackingToggle
	 * @param	{Number}	idTask
	 * @param	{Boolean}	start
	 * @return	{Boolean}	No data to transmit. Just render new headlet content
	 */
	onTrackingToggle: function(idTask, start) {
		this.setTrackingStatus(start);

		return false;
	},



	/**
	 * Update timetracking headlet with data from tracking request
	 *
	 * @method	onTrackingToggleUpdate
	 * @param	{Number}		idTask
	 * @param	{String}		data		New HTML content
	 * @param	{Ajax.Response}	response
	 */
	onTrackingToggleUpdate: function(idTask, data, response) {
		this.setContent(data);
	},



	/**
	 * Handle update event of clock inside timetracking headlet
	 *
	 * @method	onClockTick
	 * @param	{Number}	idTask
	 * @param	{Number}	trackedTotal
	 * @param	{Number}	trackedToday
	 * @param	{Number}	trackedCurrent
	 */
	onClockTick: function(idTask, trackedTotal, trackedToday, trackedCurrent) {
		this.updateTime(trackedCurrent);
		this.updatePercent();
	},



	/**
	 * Set tracking status for button
	 *
	 * @method	setTrackingStatus
	 * @param	{Boolean}		tracking
	 */
	setTrackingStatus: function(tracking) {
		this.getButton()[tracking?'addClassName':'removeClassName']('tracking');
	},



	/**
	 * Update displayed tracked time count inside headlet
	 *
	 * @method	updateTime
	 * @param	{Number}  	time
	 */
	updateTime: function(time) {
		var divCurrentTime = $(this.name + '-tracking');

		if( divCurrentTime ) {
			divCurrentTime.update(Todoyu.Time.timeFormatSeconds(time));
		}
	},



	/**
	 * Update (used amount of estimated task workload in) percent inside headlet
	 *
	 * @method	updatePercent
	 */
	updatePercent: function() {
		var idPercent = this.name + '-percent';

		if( this.isVisible() && Todoyu.exists(idPercent) ) {
			var percent	= this.ext.getPercentOfTime();

				// Update numeric percentage info
			$(idPercent).update(percent + '%');

				// Update progressive percentage bar
			var progressSpan = $(this.name + '-progress');
			if( percent > 100 ) {
				percent = 100;
			}
			progressSpan.setStyle({
				width:	percent + '%'
			});
				// Update sub elements of bar
			this.barClasses.detect(function(barClass){
				if( percent >= barClass.value ) {
					progressSpan.setStyle({
						backgroundColor: barClass.color
					});
					return true;
				}
			}, true);
		}
	},



	/**
	 * Update timetracking headlet. Evokes reRendering of the headlet.
	 *
	 * @method	updateContent
	 */
	updateContent: function() {
		var url		= Todoyu.getUrl('timetracking', 'headlet');
		var options	= {
			parameters: {
				action:	'update'
			},
			onComplete:	this.onContentUpdated.bind(this)
		};
		var target	= this.getContent();

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Set timetracking headlet content
	 *
	 * @method	setContent
	 * @param	{String}	html
	 */
	setContent: function(html) {
		this.getContent().update(html);
	},



	/**
	 * Handler when content is updated
	 *
	 * @method	onContentUpdated
	 * @param	{Ajax.Response}		response
	 */
	onContentUpdated: function(response) {

	},



	/**
	 * Stop timetracking of given task
	 *
	 * @method	stopTask
	 * @param	{Number}	idTask
	 */
	stopTask: function(idTask) {
		this.ext.stop(idTask);
	},



	/**
	 * Start timetracking of given task
	 *
	 * @method	startTask
	 * @param	{Number}	idTask
	 */
	startTask: function(idTask) {
		this.ext.start(idTask);
	},



	/**
	 * Scroll to given task if in current page, otherwise show in project area
	 *
	 * @method	goToTask
	 * @param	{Number}	idProject
	 * @param	{Number}	idTask
	 */
	goToTask: function(idProject, idTask) {
		this.ext.goToTask(idProject, idTask);
	},



	/**
	 * Check whether tracking is active
	 *
	 * @return	{Boolean}
	 */
	isTrackingActive: function() {
		return this.ext.isTracking();
	},



	/**
	 * Keep headlet open when tracking is active
	 *
	 * @method	onBodyClick
	 */
	onBodyClick: function($super) {
		if( !this.isTrackingActive() ) {
			$super();
		}
	}

});