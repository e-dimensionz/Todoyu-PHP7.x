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
 * Project list search widget
 */
Todoyu.Ext.project.PanelWidget.ProjectList = Class.create(Todoyu.PanelWidgetSearchList, {

	/**
	 * Initialize with search word
	 *
	 * @method	initialize
	 * @param	{Function}	$super		Parent constructor: Todoyu.PanelWidgetSearchList.initialize
	 * @param	{String}	search
	 */
	initialize: function($super, search) {
		$super({
			id:			'projectlist',
			search:		search,
			ext:		'project',
			controller:	'panelwidgetprojectlist',
			action:		'list'
		});

		this.addHooks();
	},



	/**
	 * Add various JS hooks
	 *
	 * @method	addHooks
	 */
	addHooks: function() {
			// Project save
		Todoyu.Hook.add('project.project.saved', this.onProjectSaved.bind(this));
			// Project create
		Todoyu.Hook.add('project.project.created', this.onProjectCreated.bind(this));
			// Add delete
		Todoyu.Hook.add('project.project.removed', this.onProjectDeleted.bind(this));
			// Add status filter
		Todoyu.Hook.add('project.projectstatus.changed', this.onStatusFilterChanged.bind(this));
	},



	/**
	 * Handler when clicked on item
	 *
	 * @method	onItemClick
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onItemClick: function(event, item) {
		var idProject	= item.id.split('-').last();

		Todoyu.Hook.exec('panelwidget.projectlist.onProjectClick', idProject);
	},



	/**
	 * Handler when list was updated
	 *
	 * @method	onUpdated
	 */
	onUpdated: function() {
		Todoyu.Hook.exec('project.projectlist.updated');
	},



	/**
	 * Handler being evoked after saving of projects: updates the project list panel widget
	 *
	 * @method	onProjectSaved
	 * @param	{Number}		idProject
	 */
	onProjectSaved: function(idProject) {
		this.update();
	},



	/**
	 * Handler being evoked after creation of new projects: updates the project list panel widget
	 *
	 * @method	onProjectCreated
	 * @param	{Number}		idProject
	 */
	onProjectCreated: function(idProject) {
		this.update();
	},



	/**
	 * Handler being evoked after deleting projects: updates the project list panel widget
	 *
	 * @method	onProjectDeleted
	 * @param	{Number}		idProject
	 */
	onProjectDeleted: function(idProject) {
		this.update();
	},



	/**
	 * Hook when status filter selection has changed
	 *
	 * @method	onStatusFilerChanged
	 * @param	{Array}		statues
	 */
	onStatusFilterChanged: function(statues) {
		this.update();
	}

});