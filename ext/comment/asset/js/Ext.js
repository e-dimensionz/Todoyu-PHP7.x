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
 * Main comment object
 *
 * @class		Comment
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.comment = {

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},

	/**
	 * Initialize comment extension
	 * Add a hook to observe form display to fix email receivers field display
	 *
	 * @method	init
	 */
	init: function() {
		Todoyu.Hook.add('form.display', this.onFormDisplay.bind(this));
	},



	/**
	 * Hook, when a form is displayed
	 *
	 * @method	onFormDisplay
	 * @param	{String}	idForm
	 * @param	{String}	name
	 * @param	{String}	recordID		idTask-idComment
	 */
	onFormDisplay: function(idForm, name, recordID) {
		if( name === 'comment' ) {
			this.Edit.onFormDisplay(idForm, name, recordID)
		}
	},



	/**
	 * @method	updateFeedbackTab
	 * @param	{Number}		numFeedbacks
	 * @todo	Use core tab handling
	 */
	updateFeedbackTab: function(numFeedbacks) {
			// Count-down the feedback counter
		if( Todoyu.isInArea('portal') && Todoyu.exists('portal-tab-feedback') ) {
			var labelElement	= $('portal-tab-feedback').down('span.labeltext');

			labelElement.update(labelElement.innerHTML.replace(/\(\d\)/, '(' + numFeedbacks + ')'));
		}
	},



	/**
	 * Add new comment to given task: expand task details and open comments tab with new comment form
	 *
	 * @method	addTaskComment
	 * @param	{Number}	idTask
	 */
	addTaskComment: function(idTask) {
		Todoyu.Ext.project.Task.showDetails(idTask, 'comment', this.onTaskCommentTabLoaded.bind(this));
	},



	/**
	 * Handler when task comment tab is loaded
	 *
	 * @method	onTaskCommentTabLoaded
	 * @param	{Number}	idTask
	 * @param	{String}	tab
	 */
	onTaskCommentTabLoaded: function(idTask, tab) {
		if( !Todoyu.exists('task-' + idTask + '-commentform-0') ) {
			this.add(idTask);
		} else {
			if( !Todoyu.Ext.project.Task.isDetailsVisible(idTask) ) {
				$('task-' + idTask + '-details').toggle();
			}
		}
	},



	/**
	 * Set Label (on adding or removing comment)
	 *
	 * @method	setTabLabel
	 * @param	{Number} idTask
	 * @param	{String}	label
	 */
	setTabLabel: function(idTask, label){
		$('task-' + idTask + '-tab-comment-label').select('.labeltext').first().update(label);
	},



	/**
	 * Check whether sorting of comments of given task is desc (true) or asc (false)
	 *
	 * @method	checkSortingIsDesc
	 * @param	{Number}	idTask
	 * @return	{Boolean}
	 */
	checkSortingIsDesc: function( idTask ) {
		var elementID, isDesc;

		elementID	= 'task-' + idTask + '-comments';
		isDesc = false;

		if( elementID ) {
			isDesc	= $(elementID).hasClassName('desc');
		}

		return isDesc;
	},



	/**
	 * Add a new comment, open empty edit form
	 *
	 * @method	add
	 * @param	{Number}	idTask
	 * @param	{Number}	[idCommentQuote]		Use this comment as template
	 * @param	{Number}	[idCommentMailReply]
	 */
	add: function(idTask, idCommentQuote, idCommentMailReply) {
		idCommentQuote		= idCommentQuote || 0;
		idCommentMailReply	= idCommentMailReply || 0;

			// Clean up UI
		this.removeForms(idTask);

			// Load new comment form
		var url, options, target;
		url		= Todoyu.getUrl('comment', 'comment');
		options = {
			parameters: {
				action:		'add',
				task:		idTask,
				quote:		idCommentQuote,
				mailReply:	idCommentMailReply
			},
			insertion:	'after',
			onComplete:	this.onAdded.bind(this, idTask, idCommentQuote, idCommentMailReply)
		};
		target	= 'task-' + idTask + '-comment-commands-top';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Handler when empty edit form to add comment loaded
	 *
	 * @method	onAdded
	 * @param	{Number}			idTask
	 * @param	{Number}			idCommentQuote
	 * @param	{Number}			idCommentMailReply
	 * @param	{Ajax.Response}		response
	 */
	onAdded: function(idTask, idCommentQuote, idCommentMailReply, response) {
		$('task-' + idTask + '-comment-commands-top').scrollToElement();
	},



	/**
	 * Remove all open edit forms for comment
	 *
	 * @method	removeForms
	 * @param	{Number}		idTask
	 */
	removeForms: function(idTask) {
		$('task-' + idTask + '-tabcontent-comment').select('.commentform').invoke('remove');
	}

};