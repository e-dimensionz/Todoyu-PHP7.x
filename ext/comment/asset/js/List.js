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
 * @module	Comment
 */

/**
 *	List comments of task methods
 */
Todoyu.Ext.comment.List = {

	/**
	 * Refresh list of comments of given task, optionally toggle sorting order
	 *
	 * @method	refresh
	 * @param	{Number}	idTask
	 * @param	{Boolean}	desc
	 */
	refresh: function(idTask, desc) {
		desc	= desc !== false;

		var url, options, target;
		url		= Todoyu.getUrl('comment', 'task');
		options	= {
			parameters: {
				action:	'list',
				task:	idTask,
				desc:	desc ? 1 : 0
			},
			onComplete: this.onRefreshed.bind(this, idTask, desc)
		};
		target	= 'task-' + idTask + '-tabcontent-comment';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Check refreshed list of comments is empty hide the first of the two "add comment" buttons
	 *
	 * @method	onRefreshed
	 * @param	{Number}		idTask
	 * @param	{Boolean}		desc
	 * @param	{Ajax.Response}	response
	 */
	onRefreshed: function(idTask, desc, response) {
		this.toggleAddButtons(idTask);
		Todoyu.Ext.assets.QuickInfoAsset.install();
	},



	/**
	 * Get amount of displayed comments of given task
	 *
	 * @method	getAmountComments
	 * @param	{Number}			idTask
	 * @return	{Number}
	 */
	getAmountComments: function(idTask) {
		var commentsElement = $('task-' + idTask + '-comments');
		if( commentsElement ) {
			return commentsElement.select('.comment').size();
		} else {
			return 0;
		}
	},



	/**
	 * Check whether the given task has any shown comments
	 *
	 * @method	hasComments
	 * @param	{Number}	idTask
	 * @return	{Boolean}
	 */
	hasComments: function(idTask) {
		return this.getAmountComments(idTask) > 0;
	},



	/**
	 * Toggle comments list visibility
	 *
	 * @method	toggle
	 * @param	{Number}	idTask
	 */
	toggle: function(idTask) {
		Todoyu.Ui.toggle('task-' + idTask + '-comments');
	},



	/**
	 * Show or hide sorting buttons depending on amount of comments
	 *
	 * @param	{Number}	idTask
	 */
	toggleSortingButtons: function(idTask) {
		var action = this.getAmountComments(idTask) < 2 ? 'hide' : 'show';

		$('task-' + idTask + '-tabcontent-comment').select('button.order').invoke(action);
	},



	/**
	 * Toggle add buttons
	 * Hide first add button if there are no comments
	 *
	 * @param	{Number}	idTask
	 */
	toggleAddButtons: function(idTask) {
		var firstButton, action;
		firstButton = $('task-' + idTask).select('button.addComment').first();
		action		= this.hasComments(idTask) ? 'show' : 'hide';

		firstButton[action]();
	},



	/**
	 * Toggle sorting of comments of given task
	 *
	 * @method	toggleSorting
	 * @param	{Number}	idTask
	 */
	toggleSorting: function(idTask) {
		var list 	= $('task-' + idTask + '-comments');

		list.select('.comment').reverse().each(function(commentElement){
			list.insert(commentElement);
		});

		$('task-' + idTask + '-tabcontent-comment').select('button.order').invoke('toggleClassName', 'desc');
	}

};