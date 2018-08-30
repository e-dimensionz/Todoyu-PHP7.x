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
 *	Task bookmarks panelwidget
 *
 * @module		Bookmark
 * @class		TaskBookmarks
 * @namespace	Todoyu.Ext.bookmark.PanelWidget
 */
Todoyu.Ext.bookmark.PanelWidget.TaskBookmarks = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:		Todoyu.Ext.bookmark,



	/**
	 * @property	key
	 * @type		String
	 */
	key:		'taskbookmarks',

	sortable:	null,



	/**
	 * Initialize task bookmarks panel widget
	 *
	 * @method	init
	 */
	init: function() {
		this.registerTimetracking();
		this.registerHooks();

		this.initExtra();
	},



	/**
	 * Additional initialization: contextmenu, sortables
	 *
	 * @method	initExtra
	 */
	initExtra: function() {
		this.ContextMenu.attach();
		this.initSortable();
	},



	/**
	 * Register JS hooks of task bookmarks
	 *
	 * @method	registerHooks
	 */
	registerHooks: function() {
		Todoyu.Hook.add('project.task.statusUpdated', this.onTaskStatusUpdated.bind(this));
	},



	/**
	 * Register to time tracking callbacks
	 *
	 * @method	registerTimetracking
	 */
	registerTimetracking: function() {
		if( Todoyu.Ext.timetracking ) {
			Todoyu.Ext.timetracking.addToggle('bookmarks', this.onTrackingToggle.bind(this), this.onTrackingToggleUpdate.bind(this));
		}
	},



	/**
	 * Callback if time tracking is toggled
	 *
	 * @method	onTrackingToggle
	 * @param	{Number}	idTask
	 * @param	{Boolean}	start
	 */
	onTrackingToggle: function(idTask, start) {
		return false;
	},



	/**
	 * Update bookmark panel widget with data from tracking request
	 *
	 * @method	onTrackingToggleUpdate
	 * @param	{Number}		idTask
	 * @param	{String}		data		New html content
	 * @param	{Ajax.Response}	response
	 */
	onTrackingToggleUpdate: function(idTask, data, response) {
		this.setContent(data);
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
	 * Start task time tracking
	 *
	 * @method	startTask
	 * @param	{Number}		idTask
	 */
	startTask: function(idTask) {
		Todoyu.Ext.timetracking.start(idTask);
	},



	/**
	 * Stop task time tracking
	 *
	 * @method	stopTask
	 * @param	{Number}		idTask
	 */
	stopTask: function(idTask) {
		Todoyu.Ext.timetracking.stop();
	},



	/**
	 * Refresh the widget content
	 *
	 * @method	refresh
	 */
	refresh: function() {
		if( ! this.isVisible() ) {
			return false;
		}

		var url	= Todoyu.getUrl('bookmark', 'refresh');	// ext, action
		var options = {
			parameters: {
				action: 'update'
			},
			onComplete: this.onRefreshed.bind(this)
		};

		this.ContextMenu.detach();
		this.disableSortable();

		Todoyu.Ui.update('panelwidget-taskbookmarks-content', url, options);
	},



	/**
	 * onRefreshed task bookmarks event handler
	 *
	 * @method	onRefreshed
	 * @param	{Ajax.Response}		response
	 */
	onRefreshed: function(response) {
		this.initExtra();
	},



	/**
	 * Set task bookmarks widget content, re-init associated extras
	 *
	 * @method	setContent
	 * @param	{String}	html
	 */
	setContent: function(html) {
		$('panelwidget-taskbookmarks-content').update(html);
		this.initExtra();
	},



	/**
	 * Show given task within its project
	 *
	 * @method	showTaskInProject
	 * @param	{Number}		idTask
	 */
	showTaskInProject: function(idTask) {
		Todoyu.goTo('project', 'ext', {task:idTask}, 'task-' + idTask);
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
	 * Remove given task bookmark from favorites
	 *
	 * @method	removeTask
	 * @param	{Number}		idTask
	 */
	removeTask: function(idTask) {
		this.ext.Task.remove(idTask);
	},



	/**
	 * Rename bookmark
	 *
	 * @method	renameBookmark
	 * @param	{Number}	idTask
	 */
	renameBookmark: function(idTask) {
		var titleElem	= $('taskbookmarks-task-' + idTask).down('.title');
		var currentElem	= titleElem.up('li').down('.currentLabel');
		var title		= currentElem.innerHTML.strip();
		var displayLabel;

		var newTitle= prompt('[LLL:bookmark.ext.bookmark.renamePrompt]', title);

		if( newTitle !== null ) {
			newTitle	= newTitle.stripScripts().stripTags().strip();
			displayLabel= newTitle || titleElem.title;

			titleElem.update(displayLabel.substr(0, 40));
			currentElem.update(displayLabel);

			this.saveRename(idTask, newTitle);
		}
	},



	/**
	 * Save new bookmark label
	 *
	 * @method	saveBookmarkLabel
	 * @param	{Number}	idTask
	 * @param	{String}	newLabel
	 */
	saveRename: function(idTask, newLabel) {
		this.ext.rename('task', idTask, newLabel, this.onRenamed.bind(this))
	},



	/**
	 * Handle renamed for panelwidget
	 *
	 * @method	onRenamed
	 * @param	{String}		type
	 * @param	{Number}		idItem
	 * @param	{String}		newLabel
	 * @param	{Ajax.Response}	response
	 */
	onRenamed: function(type, idItem, newLabel, response) {

	},



	/**
	 * Fix event odd after a task was removed
	 *
	 * @method	fixEvenOdd
	 */
	fixEvenOdd: function() {
		var items = $('taskbookmarks-listitems').select('li.listItem');

		if( items ) {
			items.invoke('removeClassName', 'even');
			items.invoke('removeClassName', 'odd');

			items.each(function(item, index){
				item.addClassName(index%2?'odd':'event');
			});
		}
	},



	/**
	 * Initialize bookmark sortables
	 * Remark: element id's of sortable items MUST separate element and item identifier by underscore for sortable to work!
	 *
	 * @method	initSortable
	 */
	initSortable: function() {
		this.disableSortable();

			// Define options for all sortables
		var options	= {
			handle:		'handle',
			onUpdate:	this.onSortableUpdate.bind(this),
			format:		/^[^_\-](?:[A-Za-z0-9\-\_]*)[-](.*)$/
		};

		var list	= $('panelwidget-taskbookmarks-content').down('ul');

		if( list ) {
			Sortable.create(list, options);
		}
	},



	/**
	 * Disable bookmark sortability
	 *
	 * @method	disableSortable
	 */
	disableSortable: function() {
		var list	= $('panelwidget-taskbookmarks-content').down('ul');

		if( list ) {
			Sortable.destroy(list);
		}
	},



	/**
	 * Handler after update of filterSet sortables
	 *
	 * @method	onSortableUpdate
	 * @param	{Element}	listElement
	 */
	onSortableUpdate: function(listElement) {
		var items	= Sortable.sequence(listElement);
		this.saveBookmarksOrder('task', items);
		this.fixEvenOdd();
	},



	/**
	 * Save order of filterSet items (conditions)
	 *
	 * @method	saveBookmarksOrder
	 * @param	{String}	type
	 * @param	{Array}		items
	 */
	saveBookmarksOrder: function(type, items) {
		var action		= 'bookmarksOrder';
		var value	= Object.toJSON({
			type:	type,
			items:	items
		});
		var idItem	= 0;

		this.ext.Preference.save(action, value, idItem);
	},



	/**
	 * Check whether bookmarks widget is loaded
	 *
	 * @method	isVisible
	 * @return	{Boolean}
	 */
	isVisible: function() {
		return Todoyu.PanelWidget.isLoaded('TaskBookmarks');
	},



	/**
	 *
	 */
	initQuickSearch: function() {
		var input = $('bookmark-task-quicksearch');
		if( input ) {
			input.stopObserving();
			input.on('keydown', this.handleSearchKeyInput.bind(this));
		}
	},



	/**
	 *
	 * @param	{Event}		event
	 * @param	{Element}	element
	 * @returns {boolean}
	 */
	handleSearchKeyInput: function(event, element) {
		if( event.keyCode === Event.KEY_RETURN ) {
			var searchValue = element.getValue();
			var searchValueArr = searchValue.split('.');

			Todoyu.Ext.project.quickSearch(searchValueArr[0], searchValueArr[1]);
		}

		return false;
	}
};