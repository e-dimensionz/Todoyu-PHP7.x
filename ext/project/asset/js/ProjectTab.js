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

Todoyu.Ext.project.Project.Tab = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.project,



	/**
	 * Handle onSelect event of tab: show affected tab which the event occured on
	 *
	 * @method	onSelect
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 */
	onSelect: function(idProject, tabKey) {
		this.show(idProject, tabKey);
	},



	/**
	 * Show given tab of given project
	 *
	 * @method	show
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{Function}	onComplete
	 */
	show: function(idProject, tabKey, onComplete) {
		var tabContainer = this.buildTabID(idProject, tabKey);

		if( ! Todoyu.exists(tabContainer) ) {
			this.createTabContainer(idProject, tabKey);
			this.load(idProject, tabKey, onComplete);
		} else {
			this.saveSelection(idProject, tabKey);
			Todoyu.callIfExists(onComplete, this, idProject, tabKey);
		}

		this.activate(idProject, tabKey);
	},



	/**
	 * Load given tab of given project
	 *
	 * @method	load
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{Function}	onComplete
	 */
	load: function(idProject, tabKey, onComplete) {
		this.showAjaxLoader(idProject);
		var url 	= Todoyu.getUrl('project', 'project');
		var options	= {
			parameters: {
				action:		'tabload',
				project:	idProject,
				tab:		tabKey
			},
			onComplete:	this.onLoaded.bind(this, idProject, tabKey, onComplete)
		};

		var tabDiv	= this.buildTabID(idProject, tabKey);
		Todoyu.Ui.update(tabDiv, url, options);
	},



	/**
	 * Handler when tab is loaded
	 *
	 * @method	onLoaded
	 * @param	{Number}		idProject
	 * @param	{String}		tabKey
	 * @param	{Function}		onComplete
	 */
	onLoaded: function(idProject, tabKey, onComplete) {
		this.activate(idProject, tabKey);
		Todoyu.callIfExists(onComplete, this, idProject, tabKey);

		Todoyu.Hook.exec('project.projectTab.onLoaded', idProject);
		this.hideAjaxLoader(idProject);
	},



	/**
	 * Check if a tab of a project is already loaded
	 *
	 * @todo	check: still in use? remove?
	 * @method	isLoaded
	 * @param	{Number}		idProject
	 * @param	{String}		tabKey
	 */
	isLoaded: function(idProject, tabKey) {
		return Todoyu.exists('project-' + idProject + '-tabcontent-' + tabKey);
	},



	/**
	 * Create tab container to given project.
	 *
	 * @method	createTabContainer
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 */
	createTabContainer: function(idProject, tabKey) {
		$('project-' + idProject + '-tabcontent').insert({
			top: new Element(
				'div', {
					id:			this.buildTabID(idProject, tabKey),
					className:	'tab projectDetails' + tabKey.capitalize()
				}
			)
		});
	},



	/**
	 * Render element ID of given tab of given project
	 *
	 * @method	buildTabID
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @return	{String}
	 */
	buildTabID: function(idProject, tabKey) {
		return 'project-' + idProject + '-tabcontent-' + tabKey;
	},



	/**
	 * Activate given tab of given project: hide other tabs, activate tab head, set tab content visible
	 *
	 * @method	activate
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 */
	activate: function(idProject, tabKey) {
		this.hideAll(idProject);
		this.setVisible(idProject, tabKey);
		this.setActive(idProject, tabKey);
	},



	setActive: function(idProject, tabName) {
		var list	= $(idProject + '-tabs');

		if( list ) {
			var tab = list.down('div.tabkey-' + tabName);

			if( tab ) {
				list.select('div').invoke('removeClassName', 'active');
				tab.addClassName('active');
				return;
			}
		}
	},



	/**
	 * Save given project's selected (given) tab
	 *
	 * @method	saveSelection
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 */
	saveSelection: function(idProject, tabKey) {
		var url = Todoyu.getUrl('project', 'project');
		var options	= {
			parameters: {
				action:		'tabselected',
				idProject:	idProject,
				tab:		tabKey
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Hide all tabs of given project
	 *
	 * @method	hideAll
	 * @param	{Number}	idProject
	 */
	hideAll: function(idProject) {
		this.getContainer(idProject).select('.tab').invoke('hide');
	},




	/**
	 * Set given tab of given project visible
	 *
	 * @method	setVisible
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 */
	setVisible: function(idProject, tabKey) {
		$(this.buildTabID(idProject, tabKey)).show();
	},



	/**
	 * Get tabs container element of given project
	 *
	 * @method	getContainer
	 * @param	{Number}	idProject
	 * @return	{Element}
	 */
	getContainer: function(idProject) {
		return $('project-' + idProject + '-tabcontainer');
	},



	/**
	 * Get tab head ID of given tab of given project
	 *
	 * @method	getHeadID
	 * @param	{Number}	idProject
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @return	{String}
	 */
	getHeadID: function(idProject, tabKey) {
		return 'project-' + idProject + '-tabhead-' + tabKey;
	},



	/**
	 * Extract tabKey (e.g 'timetracking' / 'comment' / 'assets') out of item ID
	 *
	 * @method	getKeyFromID
	 * @param	{Number}	idItem
	 * @return	{String}
	 */
	getKeyFromID: function(idItem) {
		return idItem.split('-').last();
	},



	showAjaxLoader: function(idProject) {
		var ajaxLoader = $('project-' + idProject + '-ajaxloader');
		ajaxLoader.show();
	},



	hideAjaxLoader: function(idProject) {
		var ajaxLoader = $('project-' + idProject + '-ajaxloader');
		ajaxLoader.hide();
	}

};