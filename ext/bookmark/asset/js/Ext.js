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
 * Bookmark main object
 *
 * @module		Bookmark
 * @class		bookmark
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.bookmark = {

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
	 * Add bookmark
	 *
	 * @method	add
	 * @param	{String}	type
	 * @param	{Number}	idItem
	 * @param	{String}	onComplete
	 */
	add: function(type, idItem, onComplete) {
		var url		= Todoyu.getUrl('bookmark', 'bookmark');
		var options = {
			parameters: {
				action:	'add',
				type:	type,
				item:	idItem
			},
			onComplete: this.onAdded.bind(this, type, idItem, onComplete)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handle bookmark added
	 *
	 * @method	onAdded
	 * @param	{String}		type
	 * @param	{Number}		idItem
	 * @param	{Function}		onComplete
	 * @param	{Ajax.Response}	response
	 */
	onAdded: function(type, idItem, onComplete, response) {
		Todoyu.notifySuccess('[LLL:bookmark.ext.bookmark.added]');

		this.refreshPanelWidget();

		if( onComplete ) {
			onComplete(type, idItem, response);
		}
	},



	/**
	 * Remove bookmark
	 *
	 * @method	remove
	 * @param	{String}	type
	 * @param	{Number}	idItem
	 * @param	{String}	onComplete
	 */
	remove: function(type, idItem, onComplete) {
		var url		= Todoyu.getUrl('bookmark', 'bookmark');
		var options = {
			parameters: {
				action:	'remove',
				type:	type,
				item:	idItem
			},
			onComplete: this.onRemoved.bind(this, type, idItem, onComplete)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handle bookmark removed
	 *
	 * @method	onRemoved
	 * @param	{String}		type
	 * @param	{Number}		idItem
	 * @param	{Function}		onComplete
	 * @param	{Ajax.Response}	response
	 */
	onRemoved: function(type, idItem, onComplete, response) {
		Todoyu.notifySuccess('[LLL:bookmark.ext.bookmark.removed]');

		this.refreshPanelWidget();

		if( onComplete ) {
			onComplete(type, idItem, response);
		}
	},



	/**
	 * Rename bookmark
	 *
	 * @method	rename
	 * @param	{String}	type
	 * @param	{Number}	idItem
	 * @param	{String}	newLabel
	 * @param	{Function}	onComplete
	 */
	rename: function(type, idItem, newLabel, onComplete) {
		var url		= Todoyu.getUrl('bookmark', 'bookmark');
		var options	= {
			parameters: {
				action:	'rename',
				type:	type,
				item:	idItem,
				label:	newLabel
			},
			onComplete: this.onRenamed.bind(this, type, idItem, newLabel, onComplete)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handle bookmark renamed
	 *
	 * @method	onRenamed
	 * @param	{String}			type
	 * @param	{Number}			idItem
	 * @param	{String}			newLabel
	 * @param	{Function}			onComplete
	 * @param	{Ajax.Response}		response
	 */
	onRenamed: function(type, idItem, newLabel, onComplete, response) {
		Todoyu.notifySuccess('[LLL:bookmark.ext.bookmark.renamed]');

		if( onComplete ) {
			onComplete(type, idItem, newLabel, response);
		}
	},



	/**
	 * Start time tracking of given task in the bookmark box
	 *
	 * @method	start
	 * @param	{Number}	idTask
	 */
	start: function(idTask) {
		if( idTask > 0 ) {
			Todoyu.Ext.timetracking.Task.start(idTask);
		}
	},



	/*
	 * Stop time tracking of given task in the bookmark box
	 *
	 * @method	stop
	 * @param	{Number}	idTask
	 */
	stop: function(idTask) {
		if( idTask > 0 ) {
			Todoyu.Ext.timetracking.Task.stop(idTask);
		}
	},



	/**
	 * Refresh bookmarks panel widget
	 *
	 * @method	refreshPanelWidget
	 */
	refreshPanelWidget: function() {
		if( Todoyu.PanelWidget.isLoaded('TaskBookmarks') ) {
			this.PanelWidget.TaskBookmarks.refresh();
		}
	}

};