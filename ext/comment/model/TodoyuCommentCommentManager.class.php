<?php
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
 * Manage task comments
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentCommentManager {

	/**
	 * @var	String		Default database table
	 */
	const TABLE = 'ext_comment_comment';

	/**
	 * @var	String
	 */
	const TABLE_FEEDBACK = 'ext_comment_mm_comment_feedback';



	/**
	 * Get a comment
	 *
	 * @param	Integer		$idComment
	 * @return	TodoyuCommentComment
	 */
	public static function getComment($idComment) {
		return TodoyuRecordManager::getRecord('TodoyuCommentComment', $idComment);
	}



	/**
	 * Get comment form
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idTask
	 * @param	Array		$formData
	 * @param	Array		$formParams
	 * @return	TodoyuForm
	 */
	public static function getCommentForm($idComment, $idTask, array $formData = array(), array $formParams = array()) {
		$xmlPath		= 'ext/comment/config/form/comment.xml';
		$formParams['task'] = $idTask;

		$form	= TodoyuFormManager::getForm($xmlPath, $idComment, $formParams, $formData);

		if( sizeof($formData) ) {
			$form->setFormData($formData);
		}

		return $form;
	}



	/**
	 * Get form with data for comment add
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idCommentQuote
	 * @param	Integer		$idCommentMailReply
	 * @param	Array		$formParams
	 * @return	TodoyuForm
	 */
	public static function getAddForm($idTask, $idCommentQuote, $idCommentMailReply, array $formParams = array()) {
		$idTask				= intval($idTask);
		$idCommentQuote		= intval($idCommentQuote);
		$idCommentMailReply	= intval($idCommentMailReply);
		$formParams['task'] = $idTask;

		if( $idCommentQuote ) {
			$formParams['quote'] = $idCommentQuote;
		}
		if( $idCommentMailReply ) {
			$formParams['mailReply'] = $idCommentMailReply;
		}

		$form	= self::getCommentForm(0, $idTask, array(), $formParams);

		$idFeedbackPerson	= TodoyuCommentCommentManager::getOpenFeedbackRequestPersonID($idTask);
		$data	= array(
			'id'		=> 0,
			'id_task'	=> $idTask,
			'feedback'	=> array($idFeedbackPerson)
		);

			// Quote comment
		if( $idCommentQuote !== 0 ) {
			$commentQuote = TodoyuCommentCommentManager::getComment($idCommentQuote);
			$data['comment']= $commentQuote->getCommentQuotedText();
		}

			// mail reply comment (quote + mail receiver)
		if( $idCommentMailReply !== 0 ) {
			$mailReplyComment = self::getComment($idCommentMailReply);
			$data['email_receivers'][] = $mailReplyComment->getPersonCreate()->getMailReceiver()->getTuple();;
		}

		$xmlPath= 'ext/comment/config/form/comment.xml';
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, 0, $formParams);

		$form->setFormData($data);
		$form->setRecordID($idTask . '-0');

		return $form;
	}



	/**
	 * Get form with data for comment edit
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idComment
	 * @param	Array		$formParams
	 * @return	TodoyuForm
	 */
	public static function getEditForm($idTask, $idComment, array $formParams = array()) {
		$idTask			= intval($idTask);
		$idComment		= intval($idComment);
		$form			= TodoyuCommentCommentManager::getCommentForm($idComment, $idTask, array(), $formParams);

				// Edit comment
		$comment	= TodoyuCommentCommentManager::getComment($idComment);
		$data		= $comment->getTemplateData(true);

		$xmlPath= 'ext/comment/config/form/comment.xml';
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idComment, array(
			'task'	=> $idTask
		));

		$form->setFormData($data);
		$form->setRecordID($idTask . '-' . $idComment);

		return $form;
	}



	/**
	 * Get task ID the given comment belongs to
	 *
	 * @param	Integer		$idComment
	 * @return	Integer
	 */
	public static function getTaskID($idComment) {
		$idComment	= intval($idComment);

		return self::getComment($idComment)->getTaskID();
	}



	/**
	 * Filter HTML tags inside comment text to keep only allowable ones
	 *
	 * @param	String		$text
	 * @return	String
	 */
	public static function filterHtmlTags($text) {
		return strip_tags($text, Todoyu::$CONFIG['EXT']['comment']['allowedTags']);
	}



	/**
	 * Save comment.
	 * Also sends comment mails if any email receivers given
	 *
	 * @param	Array		$data
	 * @return	Array		Data about comment saving: id, feedback, email, emailOk
	 */
	public static function saveComment(array $data) {
		$idComment	= intval($data['id']);
		$idTask		= intval($data['id_task']);
		$task		= TodoyuCommentTaskManager::getTask($idTask);
		$xmlPath	= 'ext/comment/config/form/comment.xml';
		$isNew		= false;

		$result		= array(
			'id'		=> $idComment,
			'feedback'	=> array(),
			'email'		=> false
		);

		if( $idComment === 0 ) {
			$idComment = self::addComment(array(
				'id_task'	=> $idTask
			));
			$result['id']	= $idComment;
			$isNew			= true;
		} else {
			$data['id_person_update'] = TodoyuAuth::getPersonID();
		}

			// Apply fallback
		$data	= $task->applyCommentFallback($data);

			// Clean html tags
		$data['comment'] = self::filterHtmlTags($data['comment']);

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idComment);

			// Extract feedback data
		$personFeedbackIDs	= array_unique(TodoyuArray::intExplode(',', $data['feedback'], true, true));

			// Get email receivers: tuples like 'type:ID' or just person IDs, which defaults type to 'contactperson'
		$receiverTuples		= array_unique($data['email_receivers']);

		$assets				= $data['assets'];

			// Remove special handled fields
		unset($data['email_receivers']);
		unset($data['feedback']);
		unset($data['assets']);

			// Update comment in database
		self::updateComment($idComment, $data);

			// Clear record cache
		self::removeFromCache($idComment);

			// Set all comments in task as seen
		TodoyuCommentFeedbackManager::setTaskCommentsAsSeen($idTask);

			// Add feedback requests
		$result['feedback'] = TodoyuCommentFeedbackManager::saveFeedbackRequests($idComment, $personFeedbackIDs);

			// Save new uploaded assets
		if( sizeof($assets) ) {
			TodoyuCommentAssetManager::saveAssets($data['id'], $idComment, $idTask, $assets);
		}

			// Clear record cache
		self::removeFromCache($idComment);

			// Call saved hook
		TodoyuHookManager::callHook('comment', 'comment.save', array($idComment, $isNew));

			// Send emails
		if( sizeof($receiverTuples) ) {
			$result['email'] = TodoyuCommentMailer::sendEmails($idComment, $receiverTuples);
		}

		return $result;
	}



	/**
	 * Add comment
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addComment(array $data = array()) {
		$idComment = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('comment', 'comment.add', array($idComment));

		return $idComment;
	}



	/**
	 * Update a comment
	 *
	 * @param	Integer		$idComment
	 * @param	Array		$data
	 */
	public static function updateComment($idComment, array $data) {
		TodoyuRecordManager::updateRecord(self::TABLE, $idComment, $data);

		TodoyuHookManager::callHook('comment', 'comment.update', array($idComment, $data));
	}



	/**
	 * Delete a comment
	 *
	 * @param	Integer		$idComment
	 */
	public static function deleteComment($idComment) {
		TodoyuRecordManager::deleteRecord(self::TABLE, $idComment);

		TodoyuHookManager::callHook('comment', 'comment.delete', array($idComment));
	}



	/**
	 * Remove comment from cache
	 *
	 * @param	Integer		$idComment
	 */
	public static function removeFromCache($idComment) {
			// Clear record cache
		TodoyuRecordManager::removeRecordCache('TodoyuCommentComment', $idComment);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idComment);
	}



	/**
	 * Get all comments of a task ordered by creation date
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public static function getTaskComments($idTask, $desc = false) {
		$idTask	= intval($idTask);

		$where	= 'id_task = ' . $idTask . ' AND deleted = 0';
		$order	= 'date_create ' . ($desc ? 'DESC' : 'ASC');

		return TodoyuRecordManager::getAllRecords(self::TABLE, $where, $order);
	}



	/**
	 * Get the IDs of all comments of a task
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$desc
	 * @return	Integer[]
	 */
	public static function getTaskCommentIDs($idTask, $desc = true) {
		$idTask	= intval($idTask);
		$sortDir= $desc ? 'DESC' : 'ASC';

		$fields	= 'id';
		$table	= self::TABLE;
		$where	= 'id_task = ' . $idTask . ' AND deleted = 0';
		$order	= 'date_create ' . $sortDir;

			// Limit comment it own and public if person can't see ALL comments
		if( ! Todoyu::allowed('comment', 'comment:seeAll') ) {
			$where .= ' AND	(
							id_person_create	= ' . Todoyu::personid() . ' OR
							is_public		= 1
						)';
		}

		return Todoyu::db()->getColumn($fields, $table, $where, '', $order);
	}



	/**
	 * Get the number of comments of a task
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getNumberOfTaskComments($idTask) {
		return sizeof(self::getTaskCommentIDs($idTask));
	}



	/**
	 * Change comments public flag
	 *
	 * @param	Integer		$idComment
	 * @param	Boolean		$public
	 */
	public static function setPublic($idComment, $public = true) {
		$idComment	= intval($idComment);
		$data		= array(
			'is_public' => ($public ? 1 : 0)
		);

		self::updateComment($idComment, $data);
	}



	/**
	 * Check whether a person is the create of a comment
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isCreator($idComment, $idPerson = 0) {
		$idComment	= intval($idComment);
		$idPerson	= Todoyu::personid($idPerson);

		$fields	= 'id';
		$table	= self::TABLE;
		$where	= 'id = ' . $idComment . ' AND id_person_create = ' . $idPerson;

		return Todoyu::db()->hasResult($fields, $table, $where);
	}



	/**
	 * Get details of persons which could receive a comment email
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$taskMembers
	 * @param	Boolean		$projectMembers
	 * @param	Boolean		$allInternals
	 * @return	Array
	 */
	public static function getEmailReceiverIDs($idTask, $taskMembers = false, $projectMembers = false, $allInternals = false) {
		$idTask		= intval($idTask);
		$personIDs	= array();

			// Add task Persons
		if( $taskMembers ) {
			$taskPersonIDs	= TodoyuProjectTaskManager::getTaskPersonIDs($idTask);

			foreach($taskPersonIDs as $idPerson) {
				$person	= TodoyuContactPersonManager::getPerson($idPerson);
				$email	= $person->getEmail(true);

				if( $email !== false ) {
					$personIDs[] = $idPerson;
				}
			}
		}



			// Add project Persons
		if( $projectMembers ) {
			$idProject			= TodoyuProjectTaskManager::getProjectID($idTask);
			$projectPersonIDs	= TodoyuProjectProjectManager::getProjectPersonIDs($idProject);

			foreach($projectPersonIDs as $idPerson) {
				$person	= TodoyuContactPersonManager::getPerson($idPerson);
				$email	= $person->getEmail(true);

				if( $email !== false ) {
					$personIDs[] = $idPerson;
				}
			}
		}


			// Add internal Persons
		if( $allInternals ) {
			$internalPersonIDs= TodoyuContactPersonManager::getInternalPersonIDs();

			foreach($internalPersonIDs as $idPerson) {
				$person	= TodoyuContactPersonManager::getPerson($idPerson);
				$email	= $person->getEmail(true);

				if( $email !== false ) {
					$personIDs[] = $idPerson;
				}
			}
		}

		return array_unique($personIDs);
	}



	/**
	 * Toggle comment public flag
	 *
	 * @param	Integer		$idComment
	 * @return	Integer
	 */
	public static function togglePublic($idComment) {
		$idComment	= intval($idComment);

		return Todoyu::db()->doBooleanInvert(self::TABLE, $idComment, 'is_public');
	}



	/**
	 * Get items for the task context menu
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$items
	 * @return	Array
	 */
	public static function getTaskContextMenuItems($idTask, array $items) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);
		$allowed= array();

		if( $task->isTask() && ! $task->isLocked() ) {
			$ownItems	=& Todoyu::$CONFIG['EXT']['comment']['ContextMenu']['Task'];

			if( isset($items['add']) ) {
				$allowed['add']['submenu']['add-comment'] = $ownItems['add']['submenu']['add-comment'];
			}
		}

		return array_merge_recursive($items, $allowed);
	}



	/**
	 * Get IDs of tasks with a requested feedback from current person
	 *
	 * @return	Array
	 */
	public static function getFeedbackTaskIDs() {
		return self::getFeedbackTaskFilter()->getTaskIDs('ext_comment_comment.date_create');
	}



	/**
	 * Get feedback task filter
	 *
	 * @return	TodoyuProjectTaskFilter
	 */
	public static function getFeedbackTaskFilter() {
		$conditions	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['comment']['feedbackTabFilters']);

		return new TodoyuProjectTaskFilter($conditions);
	}



	/**
	 * Get ID of the person which requested a feedback from the current user and the feedback is open
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getOpenFeedbackRequestPersonID($idTask) {
		$idTask	= intval($idTask);

		$field	= '	fb.id_person_create';
		$tables	=		self::TABLE_FEEDBACK . ' fb'
				. ',' . self::TABLE . ' co';
		$where	= '		fb.id_comment			= co.id'
				. ' AND co.id_task				= ' . $idTask
				. ' AND	fb.id_person_feedback	= ' . TodoyuAuth::getPersonID()
				. ' AND	fb.is_seen				= 0';
		$order	= '	fb.date_create DESC';
		$limit	= 1;
		$resField	= 'id_person_create';

		$idPerson	= Todoyu::db()->getFieldValue($field, $tables, $where, '', $order, $limit, $resField);

		return intval($idPerson);
	}



	/**
	 * Link comment IDs in given text
	 *
	 * @param	String		$text
	 * @return	String
	 */
	public static function linkCommentIDsInText($text) {
		if( Todoyu::allowed('project', 'general:area') ) {
			$pattern= '/(^|[^\w\.=#]+)(c(\d+))([^\w\.]+|$)/';
			$text	= preg_replace_callback($pattern, array('TodoyuCommentCommentManager', 'callbackLinkCommentsInText'), $text);
		}

		return $text;
	}



	/**
	 * Repalce comment text with link version
	 *
	 * @param	Array		$matches
	 * @return	String
	 */
	private static function callbackLinkCommentsInText(array $matches) {
		$idComment	= intval($matches[3]);
		$comment	= self::getComment($idComment);
		$idTask		= $comment->getTaskID();
		$task		= $comment->getTask();
		$person		= $comment->getPersonCreate()->getFullName();
		$date		= TodoyuTime::format($comment->getDateCreate(), 'D2MshortTime');
		$title		= $date . ' | ' . $person . ' | ' . $task->getTitleWithTaskNumber();

		return $matches[1] . '<a href="index.php?ext=project&task=' . $idTask . '&tab=comment#task-comment-' . $idComment . '" title="' . $title . '">' . $matches[2] . '</a>' . $matches[4];
	}



	/**
	 * Prefix every paragraph with a ">"
	 *
	 * @param	String		$commentHtml
	 * @param	String		$prefix
	 * @return	String
	 */
	public static function getPrefixedResponseLines($commentHtml, $prefix = COMMENT_QUOTE_PREFIX) {
			// Quote paragraphs
		$pattern	= '/(<p[^>]*?>)(.*?)(<\/p>)/is';
		$replace	= '\1' . $prefix . '\2\3';
		$commentHtml= preg_replace($pattern, $replace, $commentHtml);

			// Quote line breaks
		$pattern	= '/<br[^>]*?>/is';
		$replace	= '\0' . $prefix;
		$commentHtml= preg_replace($pattern, $replace, $commentHtml);

			// First line not a paragraph
		if( substr($commentHtml, 0, 2) !== '<p' ) {
			$commentHtml = $prefix . $commentHtml;
		}

		$commentHtml = str_replace('<pre>' , '<pre>' . $prefix, $commentHtml);

		return $commentHtml;
	}

}

?>