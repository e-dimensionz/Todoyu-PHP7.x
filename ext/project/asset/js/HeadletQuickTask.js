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

Todoyu.Ext.project.Headlet.QuickTask = Class.create(Todoyu.Headlet, {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.project,



	/**
	 * Initialize quicktask headlet
	 *
	 * @method	init
	 * @param	{Function}		$super		Parent constructor: Todoyu.Headlet.initialize
	 * @param	{String}		name
	 */
	initialize: function($super, name) {
		$super(name);

		Todoyu.Hook.add('project.quickTask.saved', this.onQuickTaskSaved.bind(this));
	},



	/**
	 * Handle button click: evoke add quicktask
	 *
	 * @method	onButtonClick
	 * @param	{Event}		event
	 */
	onButtonClick: function(event) {
		this.ext.QuickTask.openPopup();
	},



	/**
	 * Handler when quick task has been saved: Update the task list, if task has been added to the active project
	 *
	 * @method	onQuickTaskSaved
	 * @param	{Number}			idTask
	 * @param	{Number}			idProject
	 * @param	{Ajax.Response}		response
	 */
	onQuickTaskSaved: function(idTask, idProject, response) {
		if( Todoyu.isInArea('project') ) {
			if( idProject == this.ext.ProjectTaskTree.getActiveProjectID() ) {
				this.ext.TaskTree.update();
			}
		}
	}

});