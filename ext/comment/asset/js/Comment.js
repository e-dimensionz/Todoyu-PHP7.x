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
 * Comment related methods
 * @type {Object}
 */
Todoyu.Ext.comment.Comment = {

	/**
	 * @var	Ext back ref
	 */
	ext: Todoyu.Ext.comment,



	/**
	 * Toggle customer visibility of given comment
	 *
	 * @method	togglePublic
	 * @param	{Number}	idComment
	 */
	togglePublic: function(idComment) {
		var url, options;
		url		= Todoyu.getUrl('comment', 'task');
		options	= {
			parameters: {
				action:		'togglepublic',
				comment:	idComment
			},
			onComplete: this.onToggledPublic.bind(this, idComment)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler for togglePublic
	 *
	 * @method	onToggledPublic
	 * @param	{Number}			idComment
	 * @param	{Ajax.Response}		response
	 */
	onToggledPublic: function(idComment, response) {
		$('task-comment-' + idComment).toggleClassName('isPublic');
		$('comment-' + idComment + '-action-makePublic').toggleClassName('isPublic');

		var hasPublicFeedbackWarning	= this.commentHasPublicFeedbackWarning(idComment);

		if( response.hasTodoyuHeader('publicFeedbackWarning') ) {
				// Add received warning
			if( !hasPublicFeedbackWarning ) {
				this.insertPublicFeedbackWarning(idComment, response.getTodoyuHeader('publicFeedbackWarning'));
			}
		} else {
				// Remove invalid warning
			if( hasPublicFeedbackWarning ) {
				this.getCommentPublicFeedbackWarning(idComment).remove();
			}
		}
	},



	/**
	 *
	 * @param	{String}		text
	 */
	insertPublicFeedbackWarning: function(idComment, text) {
		var message = new Element('div', {
			class:	'warningMessage'
		});

		var icon = new Element('span', {
			class: 'icon'
		});

		var label = new Element('span', {
			class: 'label'
		}).update(text);

		message.appendChild(icon);
		message.appendChild(label);

		$('task-comment-' + idComment + '-involved').down('.action-bar').insert({
			before: message
		});
	},



	/**
	 * Get feedback warning element of comment if it exists
	 *
	 * @method	getCommentPublicFeedbackWarning
	 * @param	{Number}	idComment
	 */
	getCommentPublicFeedbackWarning: function(idComment) {
		return $('task-comment-' + idComment + '-involved').down('.warningMessage');
	},



	/**
	 * Check whether there is a warning about non-public task/comments being not visible
	 *
	 * @method	hasPublicFeedbackWarning
	 * @param	{Number}	idComment
	 * @return	{Boolean}
	 */
	commentHasPublicFeedbackWarning: function(idComment) {
		return !!this.getCommentPublicFeedbackWarning(idComment);
	},



	/**
	 * Get current "seen" status of given comment (for current person) and toggle it
	 *
	 * @method	toggleSeen
	 * @param	{Number}	idComment
	 * @param	{Number}	idPerson
	 */
	toggleSeen: function(idComment, idPerson) {
		var isSeen	= $('comment-' + idComment + '-seenstatus').hasClassName('isseen');

		this.setSeen(idComment, idPerson, !isSeen);
	},



	/**
	 * Toggle comment "seen" status instead of a dummy user whom the feedback was request from
	 *
	 * @method	toggleDummySeen
	 * @param	{Number}	idComment
	 * @param	{Number}	idDummyPerson
	 * @param	{String}	dummyPersonName
	 */
	toggleDummySeen: function(idComment, idDummyPerson, dummyPersonName) {
		var isSeen	= $('task-comment-' + idComment + '-involvedPerson-' + idDummyPerson).down('.icon').hasClassName('approved'),
			msgTpl	= isSeen ? '[LLL:comment.ext.overrideDummy.acknowledgeFeedback.toggle.confirm.setUnseen]' : '[LLL:comment.ext.overrideDummy.acknowledgeFeedback.toggle.confirm.setSeen]',
			msgData	= {
				dummyname: dummyPersonName
			},
			message	= msgTpl.interpolate(msgData);

		if( confirm(message) ) {
			var url, options;
			url		= Todoyu.getUrl('comment', 'comment');
			options	= {
				parameters: {
					action:			'seenbydummy',
					comment:		idComment,
					dummyperson:	idDummyPerson,
					setseen:		isSeen ? 0 : 1
				},
				onComplete: this.onToggleDummySeen.bind(this, idComment, idDummyPerson)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * @method	onToggleDummySeen
	 * @param	{Number}		idComment
	 * @param	{Number}		idDummyPerson
	 * @param	{Ajax.Response}	response
	 */
	onToggleDummySeen: function(idComment, idDummyPerson, response) {
		var starIconEl	= $('task-comment-' + idComment + '-involvedPerson-' + idDummyPerson).down('span.icon');
			// Toggle yellow/grey star icon
		if( response.getTodoyuHeader('setSeen') == 1 ) {
			starIconEl.replaceClassName('unapproved', 'approved');
		}
		if( response.getTodoyuHeader('setSeen') == 0 ) {
			starIconEl.replaceClassName('approved', 'unapproved');
		}
	},



	/**
	 * Set 'seen' status of given comment true
	 *
	 * @method	setSeenStatus
	 * @param	{Number}	idComment
	 * @param	{Number}	idPerson
	 * @param	{Boolean}	setSeen
	 */
	setSeen: function(idComment, idPerson, setSeen) {
		var url, options;
		url		= Todoyu.getUrl('comment', 'comment');
		options	= {
			parameters: {
				action:		'seen',
				comment:	idComment,
				setseen:	setSeen ? 1 : 0
			},
			onComplete: this.onSeenSet.bind(this, idComment, idPerson)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler for setSeenStatus (setting comment seen and not seen)
	 *
	 * @method	onSeenStatusSet
	 * @param	{Number}			idComment
	 * @param	{Number}			idPerson
	 * @param	{Ajax.Response}	response
	 */
	onSeenSet: function(idComment, idPerson, response) {
		var personEl, commentSeenEl, isSeen;
		personEl		= $('task-comment-' + idComment + '-involvedPerson-' + idPerson);
		commentSeenEl	= $('comment-' + idComment + '-seenstatus');
		isSeen			= commentSeenEl.hasClassName('isseen');

			// Change star grey/yellow
		commentSeenEl[isSeen ? 'removeClassName':'addClassName']('isseen');

				// Update seen/unseen mark at person name
		if( personEl ) {
			personEl.down('.feedback.icon').replaceClassName(isSeen ? 'approved':'unapproved', isSeen ? 'unapproved':'approved');
		}

		this.ext.updateFeedbackTab(response.getTodoyuHeader('feedback'));
	},



	/**
	 * Remove given comment
	 *
	 * @method	remove
	 * @param	{Number}	idComment
	 */
	remove: function(idComment) {
		if( !confirm('[LLL:comment.ext.delete.confirm]') ) {
			return false;
		}

		var url		= Todoyu.getUrl('comment', 'comment');
		var options	= {
			parameters: {
				action:		'delete',
				comment:	idComment
			},
			onComplete: this.onRemoved.bind(this, idComment)
		};

		Todoyu.send(url, options);
	},




	/**
	 * Evoked after completion of removal comment request
	 *
	 * @method	onRemoved
	 * @param	{Number}			idComment
	 * @param	{Ajax.Response}		response
	 */
	onRemoved: function(idComment, response){
		var tabLabel, idTask;
		tabLabel	= response.getTodoyuHeader('tabLabel');
		idTask		= response.getTodoyuHeader('task');

		this.ext.setTabLabel(idTask, tabLabel);

			// Fade out the removed task
		Effect.Fade($('task-comment-' + idComment), {
			duration:	0.5,
			afterFinish: function(effect) {
					// Remove element
				effect.element.remove();
					// Less than 2 comments => hide sorting buttons,
				this.ext.List.toggleSortingButtons(idTask);
					// Was last comment of task? Cleanup
				var amountComments	= $$('#task-' + idTask + '-tabcontent-comment .comment').length;
				if( amountComments == 0 ) {
					$('task-' + idTask + '-comments').remove();
					$('task-' + idTask + '-comment-commands-bottom').remove();
				}

			}.bind(this)
		});
	},



	/**
	 * Scroll to comment
	 *
	 * @method	scrollTo
	 * @param	{Number}	idComment
	 */
	scrollTo: function(idComment) {
		$('task-comment-' + idComment).scrollToElement();
	},



	/**
	 * Add a new comment with the current comment text as template
	 *
	 * @method	quote
	 * @param	{Number}	idTask
	 * @param	{Number}	idComment
	 */
	quote: function(idTask, idComment) {
		this.ext.add(idTask, idComment);
	},



	/**
	 * Quote comment and add creator as mail receiver
	 *
	 * @method	mailReply
	 * @param	{Number}	idTask
	 * @param	{Number}	idComment
	 */
	mailReply: function(idTask, idComment) {
		this.ext.add(idTask, idComment, idComment);
	}

};