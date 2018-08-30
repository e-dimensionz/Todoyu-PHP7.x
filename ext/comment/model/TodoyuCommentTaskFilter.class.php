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
 * Filters for tasks for comment stuff
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentTaskFilter extends TodoyuSearchFilterBase {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_comment_comment';



	/**
	 * Filters for unseen feedbacks
	 *
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_unseenFeedback($value, $negate = false) {
		$seenStatus	= $negate ? 1 : 0;

		$tables	= array(
			'ext_project_task',
			'ext_comment_comment',
			'ext_comment_mm_comment_feedback'
		);
		$where	= '		ext_comment_comment.deleted				= 0'
				. '	AND ext_comment_mm_comment_feedback.is_seen	= ' . $seenStatus;
		$join	= array(
			'ext_comment_comment.id_task = ext_project_task.id',
			'ext_comment_mm_comment_feedback.id_comment	= ext_comment_comment.id'
		);

		$queryParts	= array(
			'tables'=> $tables,
			'where'	=> $where,
			'join'	=> $join
		);

		return $queryParts;
	}



	/**
	 * Filters all unseen feedbacks off current user
	 *
	 *
	 * @param	Integer		$idPerson
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_unseenFeedbackCurrentPerson($idPerson, $negate = false) {
		return self::Filter_unseenFeedbackPerson(Todoyu::personid(), $negate);
	}



	/**
	 * Filter for tasks which have unseen comments (with feedback request) for a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_unseenFeedbackPerson($idPerson, $negate = false) {
		$idPerson	= Todoyu::personid($idPerson);
		$seenStatus	= $negate ? 1 : 0;

		$tables	= array(
			'ext_project_task',
			'ext_comment_comment',
			'ext_comment_mm_comment_feedback'
		);
		$where	= '		ext_comment_comment.deleted							= 0'
				. '	AND ext_comment_mm_comment_feedback.is_seen				= ' . $seenStatus
				. ' AND	ext_comment_mm_comment_feedback.id_person_feedback	= ' . $idPerson
//				. ' AND ext_project_task.id									= ext_comment_comment.id_task'
				. ' AND ext_project_task.type 								= ' . TASK_TYPE_TASK;

		$join	= array(
			'ext_comment_comment.id_task = ext_project_task.id',
			'ext_comment_mm_comment_feedback.id_comment	= ext_comment_comment.id'
		);

		$queryParts	= array(
			'tables'=> $tables,
			'where'	=> $where,
			'join'	=> $join
		);

		return $queryParts;
	}



	/**
	 * Filter condition: Tasks which have comment feedback which have not been seen by a member of a group
	 *
	 * @param	String		$groupIDs
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_unseenFeedbackRoles($groupIDs, $negate = false) {
		$queryParts	= false;
		$groupIDs	= TodoyuArray::intExplode(',', $groupIDs, true, true);

		if( sizeof($groupIDs) > 0 ) {
			$tables	= array(
				'ext_project_task',
				'ext_comment_comment',
				'ext_comment_mm_comment_feedback',
				'ext_contact_mm_person_role'
			);
			$where	= '		ext_comment_comment.deleted							= 0'
					. '	AND ext_comment_mm_comment_feedback.id_person_feedback	= ext_contact_mm_person_role.id_person'
					. '	AND ext_contact_mm_person_role.id_role					IN(' . implode(',', $groupIDs) . ')';
			$join	= array(
				'ext_comment_comment.id_task = ext_project_task.id',
				'ext_comment_mm_comment_feedback.id_comment	= ext_comment_comment.id'
			);

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: Tasks which have comments which contain the given text
	 *
	 * @param	String		$keyword
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_fulltext($keyword, $negate = false) {
		$queryParts	= false;
		$keyword	= trim($keyword);

		if( $keyword !== '' ) {
			$tables	= array(
				'ext_project_task',
				'ext_comment_comment'
			);

			$keywords	= explode(' ', $keyword);
			$fields		= array(
				'ext_comment_comment.comment'
			);
			$negator	= $negate ? 'NOT ' : '';


			$where	= $negator . TodoyuSql::buildLikeQueryPart($keywords, $fields)
					. ' AND ext_comment_comment.deleted	= 0';
			$join	= array(
				'ext_comment_comment.id_task = ext_project_task.id'
			);

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: Tasks which are written by a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Boolean		$negate
	 * @return	Array		Or FALSE
	 */
	public static function Filter_commentWrittenPerson($idPerson, $negate = false) {
		$queryParts	= false;
		$idPerson	= intval($idPerson);

		if( $idPerson !== 0 ) {
			$tables = array(
				'ext_project_task',
				'ext_comment_comment'
			);

			$condition = $negate ? ' NOT IN ' : ' IN ';

			$where	= 'ext_project_task.id ' . $condition . '(
						SELECT id_task
						FROM ext_comment_comment
						WHERE deleted = 0 AND id_person_create = ' . $idPerson
					. ')';

			$join	= array(
				'ext_comment_comment.id_task = ext_project_task.id'
			);


			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: Tasks which have comments which are written by a member of one of the roles
	 *
	 * @param	String		$roleIDs
	 * @param	Boolean		$negate
	 * @return	Array		Or FALSE
	 */
	public static function Filter_commentWrittenRoles($roleIDs, $negate = false) {
		$queryParts	= false;
		$roleIDs	= TodoyuArray::intExplode(',', $roleIDs, true, true);

		if( sizeof($roleIDs) > 0 ) {
			$tables	= array(
				'ext_project_task',
				'ext_comment_comment',
				'ext_contact_mm_person_role'
			);
			$where	= '		ext_comment_comment.deleted			= 0'
					. '	AND	ext_contact_mm_person_role.id_role IN(' . implode(',', $roleIDs) . ')';
			$join	= array(
				'ext_comment_comment.id_task = ext_project_task.id',
				'ext_comment_comment.id_person_create = ext_contact_mm_person_role.id_person'
			);

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $join
			);
		}

		return $queryParts;
	}



	/**
	 * Filter condition: comment creation date
	 *
	 * @param	String		$date
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_commentDateCreate($date, $negate = false) {
		$timestamp	= TodoyuTime::parseDate($date);
		if( $timestamp == 0 ) {
			return false;
		}

		$tables	= array(
			self::TABLE
		);
		$join	= array(
			self::TABLE . '.id_task	= ext_project_task.id'
		);
		$compare= TodoyuSearchFilterHelper::getTimeAndLogicForDate($timestamp, $negate);
		$where	=			self::TABLE . '.deleted				= 0 '
				. ' AND ' .	self::TABLE .	'.date_create' .	$compare['logic'] . ' ' . $compare['timestamp'];



		return array(
			'tables'	=> $tables,
			'where'		=> $where,
			'join'		=> $join
		);
	}



	/**
	 * Filter condition: Comment is public
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_commentIsPublic($value, $negate = false) {
		$compare= $negate ? 0 : 1;
		$where	= 'ext_comment_comment.is_public = ' . $compare;
		$join	= array(
			'ext_project_task.id = ext_comment_comment.id_task'
		);
		$tables	= array(
			'ext_project_task',
			'ext_comment_comment'
		);

		return array(
			'tables' => $tables,
			'where'	=> $where,
			'join'	=> $join
		);
	}



	/**
	 * Filter condition: Comment is public if current user is external
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array|Boolean
	 */
	public static function Filter_commentIsPublicForExternals($value, $negate = false) {
		if( TodoyuAuth::isExternal() ) {
			return self::Filter_commentIsPublic($value, $negate);
		} else {
			return false;
		}
	}



	/**
	 * Filter: Comment with feedback request from current person
	 *
	 * @param	Integer			$idPerson
	 * @param	Boolean			$negate		(Has been seen / Hasn't been seen)
	 * @return	Array|Boolean
	 */
	public function Filter_commentMyFeedbackRequestPerson($idPerson, $negate = false) {
		$idPerson	= intval($idPerson);

		$tables	= array(
			'ext_project_task',
			'ext_comment_comment',
			'ext_comment_mm_comment_feedback'
		);
		$seenState	= $negate ? 1 : 0;
		$where	= '		ext_comment_comment.deleted							= 0'
				. ' AND ext_comment_mm_comment_feedback.id_person_create	= ' . Todoyu::personid()
				. ' AND ext_comment_mm_comment_feedback.is_seen				= ' . $seenState;

		if( $idPerson !== 0 ) {
			$where .= ' AND ext_comment_mm_comment_feedback.id_person_feedback	= ' . $idPerson;
		}

		$join = array(
			'ext_comment_comment.id_task = ext_project_task.id',
			'ext_comment_mm_comment_feedback.id_comment = ext_comment_comment.id'
		);

		return array(
			'tables'	=> $tables,
			'where'		=> $where,
			'join'		=> $join
		);
	}



	/**
	 * Get query parts to sort for last comment of task
	 * Add an extra join from task to comment task. To keep the query valid, we have to remove the task table.
	 * Add order clause for not existing comments (task with no comments)
	 * Add field which get the maximum date of a comment (MAX()
	 *
	 * @param	Boolean		$desc
	 * @return array
	 */
	public static function Sorting_commentLastAdd($desc = false) {
		$sortDir = self::getSortDir($desc);

		return array(
			'tables'		=> array(
				'ext_project_task LEFT JOIN ext_comment_comment ON ext_project_task.id = ext_comment_comment.id_task'
			),
			'removeTables'	=> array(
				'ext_project_task',
				'ext_comment_comment'
			),
			'order'			=> array(
				'commentLastAdded' . $sortDir,					// Last date added by MAX() in fields
				'ISNULL(ext_comment_comment.id)' . $sortDir,	// No comments
				'ext_project_task.id' . $sortDir				// Make sure sorting is consistent
			),
			'fields'		=> array(
				'commentLastAdded'	=> 'MAX(ext_comment_comment.date_create) as commentLastAdded'
			)
		);
	}



	/**
	 * Get query parts to sort for last PUBLIC comment of task
	 *
	 * @param	Boolean		$desc
	 * @return	Array
	 */
	public static function Sorting_commentLastAddPublic($desc = false) {
		$queryParts	= self::Sorting_commentLastAdd($desc);

			// Inject public order at second position
		$extraOrders = array(
			'IF(SUM(ext_comment_comment.is_public)=0,1,0)' . self::getSortDir($desc)
		);
		array_splice($queryParts['order'], 1, 0, $extraOrders);

			// Add a IF() statement into the MAX() field to make sure only public comments are checked
		$queryParts['fields']['commentLastAdded']	= 'MAX(IF(ext_comment_comment.is_public, ext_comment_comment.date_create, 0)) as commentLastAdded';

		return $queryParts;
	}

}

?>