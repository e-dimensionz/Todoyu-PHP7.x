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
 * Manage comment feedback requests
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentFeedbackManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_comment_mm_comment_feedback';



	/**
	 * Save feedback requests of given comment
	 *
	 * @param	Integer		$idComment
	 * @param	Integer[]	$feedbackPersonIDs
	 * @return	Integer[]
	 */
	public static function saveFeedbackRequests($idComment, $feedbackPersonIDs) {
			// Get already stored unseen feedbacks
		$seenFeedbackPersonIDs	= self::getSeenFeedbacksPersonIDs($idComment);

			// Remove feedbacks that have been seen already from list, those don't need to be removed/resaved
		$unseenFeedbackPersonIDs	= array_diff($feedbackPersonIDs, $seenFeedbackPersonIDs);

			// Remove old open feedbacks from DB
		self::removeUnseenFeedbacks($idComment);

			// Add newly added feedback requests
		self::addFeedbacks($idComment, $unseenFeedbackPersonIDs);

		return $feedbackPersonIDs;
	}



	/**
	 * Get IDs of persons from whom a feedback is requested and who saw it already
	 *
	 * @param	Integer		$idComment
	 * @return	Array
	 */
	public static function getSeenFeedbacksPersonIDs($idComment) {
		$idComment	= intval($idComment);

		$requests	= self::getSeenFeedbackRequests($idComment);

		return self::extractPersonIDsFromFeedbacks($requests);
	}



	/**
	 * Get IDs of persons from whom a feedback is requested (and not yet seen)
	 *
	 * @param	Integer		$idComment
	 * @return	Array
	 */
	public static function getUnseenFeedbacksPersonIDs($idComment) {
		$idComment	= intval($idComment);
		$requests	= self::getFeedbackRequests($idComment, true);

		return self::extractPersonIDsFromFeedbacks($requests);
	}



	/**
	 * Extract person IDs from given feedbacks
	 *
	 * @param	Array	$requests
	 * @return	Array
	 */
	public static function extractPersonIDsFromFeedbacks(array $requests = array()) {
		$requests	= TodoyuArray::sortByLabel($requests, 'id_person_feedback');

		$personIDs	= array();
		foreach($requests as $request) {
			$personIDs[]	= $request['id_person_feedback'];
		}

		return array_unique($personIDs);
	}



	/**
	 * Remove all unseen feedback request from given comment
	 *
	 * @param	Integer		$idComment
	 * @return	Integer		Num affected rows
	 */
	public static function removeUnseenFeedbacks($idComment) {
		$where	=
				'		id_comment	= ' . $idComment
			.	' AND	is_seen		= 0'
		;

		return Todoyu::db()->doDelete(self::TABLE, $where);
	}



	/**
	 * Add a new feedback request
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idFeedbackPerson
	 * @return	Integer
	 */
	public static function addFeedback($idComment, $idFeedbackPerson) {
		$idComment			= intval($idComment);
		$idFeedbackPerson	= intval($idFeedbackPerson);

		$data	= array(
			'id_person_feedback'=> $idFeedbackPerson,
			'id_comment'		=> $idComment,
			'is_seen'			=> 0
		);

		$idFeedback = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('comment', 'feedback.add', array($idFeedback, $idComment, $idFeedbackPerson));

		return $idFeedback;
	}



	/**
	 * Add feedback requests for multiple persons
	 *
	 * @param	Integer		$idComment
	 * @param	Array		$personIDs
	 */
	public static function addFeedbacks($idComment, array $personIDs) {
		$idComment	= intval($idComment);
		$personIDs	= TodoyuArray::intval($personIDs, true, true);

		foreach($personIDs as $idPerson) {
			self::addFeedback($idComment, $idPerson);
		}
	}



	/**
	 * Get IDs of comments needing a feedback from the given person
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer[]
	 */
	public static function getCommentIDs($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		$field	= 'id_comment';
		$table	= self::TABLE . '		as f,
					ext_comment_comment as c,
					ext_project_task	as t';

		$where	= '	f.id_person_feedback	= ' . $idPerson
				. ' AND	f.is_seen			= 0
					AND c.id				= f.id_comment
					AND c.deleted			= 0
					AND c.id_task			!= 0
					AND t.id				= c.id_task
					AND t.type				= ' . TASK_TYPE_TASK . '
					AND t.deleted			= 0';

		$group	= 'c.id';
		$order	= 'f.date_create';

		$person = TodoyuContactPersonManager::getPerson($idPerson);
			// External persons can see only pubic comments and their feedback requests
		if( $person->isExternal() ) {
			$where .= ' AND c.is_public = 1';

				// If person can not see all projects: limit to visible ones
			if( ! $person->isAdmin() && ! TodoyuContactPersonManager::isAllowed($idPerson, 'project', 'project::seeAll') ) {
				$projectIDs	= TodoyuProjectProjectManager::getAvailableProjectsForPerson();
				$where	.= ' AND ' . TodoyuSql::buildInListQueryPart($projectIDs, 't.id_project');
			}
		}

		return TodoyuArray::flatten(Todoyu::db()->getArray($field, $table, $where, $group, $order));
	}



	/**
	 * Get number of open feedbacks
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public static function getOpenFeedbackCount($idPerson = 0 ) {
		return sizeof(self::getCommentIDs($idPerson));
	}



	/**
	 * Get task IDs which have comments which need a feedback from the person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getTaskIDs($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		$field	= '	c.id_task';
		$table	=	self::TABLE . ' f,
					ext_comment_comment c';
		$where	= '		f.id_comment		= c.id'
				. '	AND	f.id_person_feedback= ' . $idPerson
				. ' AND	f.is_seen			= 0';
		$order	= '	f.date_create';

		return Todoyu::db()->getColumn($field, $table, $where, '', $order);
	}



	/**
	 * Check whether the given comment has a feedback request from the given person
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function hasFeedbackRequest($idComment, $idPerson = 0) {
		$idComment	= intval($idComment);
		$idPerson	= Todoyu::personid($idPerson);

		$field	= self::TABLE . '.id';
		$table	= self::TABLE . ' as f,
					ext_comment_comment as c';
		$where	= '		f.id_comment			= ' . $idComment
				. ' AND	f.id_person_feedback	= ' . $idPerson
				. ' AND	f.is_seen				= 0
					AND c.id = f.id_comment
					AND c.deleted = 0
					AND c.id_task != 0';

		return Todoyu::db()->hasResult($field, $table, $where);
	}



	/**
	 * Get feedback requests, optionally only not yet seen ones
	 *
	 * @param	Integer		$idComment
	 * @param	Boolean		$onlyUnseen
	 * @return	Array
	 */
	public static function getFeedbackRequests($idComment, $onlyUnseen = false) {
		$idComment	= intval($idComment);

		$where	= 'id_comment = ' . $idComment;

		if( $onlyUnseen ) {
			$where .= ' AND is_seen = 0';
		}

		return TodoyuRecordManager::getAllRecords(self::TABLE, $where, '');
	}



	/**
	 * Get all seen feedback requests
	 *
	 * @param	Integer		$idComment
	 * @return	Array
	 */
	public static function getSeenFeedbackRequests($idComment) {
		$idComment	= intval($idComment);

		$where	= 'id_comment = ' . $idComment . ' AND is_seen = 1';

		return TodoyuRecordManager::getAllRecords(self::TABLE, $where, '');
	}



	/**
	 * Set "seen" status of given comment's feedback
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idPerson
	 * @param	Boolean		$isSeen
	 * @return	Integer
	 */
	public static function setSeenStatus($idComment, $idPerson = 0, $isSeen = true) {
		$idComment	= intval($idComment);
		$idPerson	= Todoyu::personid($idPerson);

		$table	= self::TABLE;
		$where	= '		id_comment			= ' . $idComment
				. ' AND	id_person_feedback	= ' . $idPerson;
		$data	= array(
			'date_update'	=> NOW,
			'is_seen'		=> $isSeen ? 1 : 0
		);

		return Todoyu::db()->doUpdate($table, $where, $data);
	}



	/**
	 * Set a comment's feedback request as seen
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idPerson
	 * @param	Boolean		$isSeen
	 * @return	Boolean
	 */
	public static function setAsSeen($idComment, $idPerson = 0, $isSeen = true) {
		$success	= self::setSeenStatus($idComment, $idPerson, $isSeen) > 0;

		TodoyuHookManager::callHook('comment', 'feedback.changeseen', array($idComment, $idPerson));

		return $success;
	}



	/**
	 * Set a comment's feedback request as seen
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function setAsUnseen($idComment, $idPerson = 0) {
		return self::setAsSeen($idComment, $idPerson, false);
	}



	/**
	 * Set all comments in a task as seen by a person
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idPerson
	 */
	public static function setTaskCommentsAsSeen($idTask, $idPerson = 0) {
		$idTask		= intval($idTask);
		$idPerson	= Todoyu::personid($idPerson);

		$tables	=	self::TABLE . ' f,
					ext_comment_comment c';
		$where	= '		f.id_comment		= c.id'
				. '	AND	f.id_person_feedback= ' . $idPerson
				. ' AND	c.id_task			= ' . $idTask;
		$data	= array(
			'f.is_seen'		=> 1,
			'f.date_update'	=> NOW
		);

		Todoyu::db()->doUpdate($tables, $where, $data);

		TodoyuHookManager::callHook('comment', 'task.seen', array($idTask, $idPerson));
	}



	/**
	 * Get persons whom feedback to given comment is requested from
	 *
	 * @param	Integer	$idComment
	 * @param	Mixed	[$isSeen]
	 * @return	Array[]
	 */
	public static function getFeedbackPersons($idComment, $isSeen = null) {
		$idComment	= intval($idComment);

		$fields	= '	p.id,
					p.username,
					p.email,
					p.firstname,
					p.lastname,
					p.is_dummy,
					f.is_seen';
		$tables	= '	ext_contact_person				p,
					ext_comment_mm_comment_feedback	f';
		$where	= '		f.id_comment		= ' . $idComment
				. ' AND	f.id_person_feedback= p.id
					AND	p.deleted			= 0';

		if( !is_null($isSeen) ) {
			$where .= ' AND f.is_seen = ' . ($isSeen ? 1 : 0);
		}

		$group	= '	p.id';
		$order	= '	p.lastname,
					p.firstname';
		$indexField	= 'id';

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order, '', $indexField);
	}



	/**
	 * Check whether the comment has a feedback request which is not "seen" yet
	 *
	 * @param	Integer		$idComment
	 * @return	Boolean		Open feedback request found
	 */
	public static function isCommentUnseen($idComment) {
		$idComment	= intval($idComment);
		$idPerson	= Todoyu::personid();

		$field	= 'is_seen';
		$table	= self::TABLE;
		$where	= '		id_comment			= ' . $idComment
				. ' AND	id_person_feedback	= ' . $idPerson;

		$isSeen =  Todoyu::db()->getColumn($field, $table, $where);

		return sizeof($isSeen) !== 0 && intval($isSeen[0]) === 0;
	}

}

?>