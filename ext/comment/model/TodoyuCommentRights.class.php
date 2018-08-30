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
 * Comment rights functions
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentRights {

	/**
	 * Deny access
	 * Shortcut for comment
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('comment', $right);
	}



	/**
	 * Check whether person is allowed to edit a comment
	 * Check whether person has editing rights and whether person can edit a status
	 *
	 * @param	Integer		$idComment
	 * @return	Boolean
	 */
	public static function isEditAllowed($idComment) {
		$idComment	= intval($idComment);
		$comment	= TodoyuCommentCommentManager::getComment($idComment);
		$idTask		= $comment->getTaskID();

		return self::isEditInTaskAllowed($idTask, $comment->isCurrentPersonCreator());
	}



	/**
	 * Check whether person can edit comments in this task
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$isCreator
	 * @return	Boolean
	 */
	public static function isEditInTaskAllowed($idTask, $isCreator = false) {
		$idTask	= intval($idTask);

		if( TodoyuProjectTaskRights::isSeeAllowed($idTask) ) {
			if( Todoyu::allowed('comment', 'comment:editAll') ) {
				return true;
			}

			if( Todoyu::allowed('comment', 'comment:editOwn') && $isCreator) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Check whether adding comments in given task is allowed
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isAddInTaskAllowed($idTask) {
		$idTask	= intval($idTask);

		return TodoyuProjectTaskRights::isSeeAllowed($idTask) && Todoyu::allowed('comment', 'general:use');
	}



	/**
	 * Check whether a person can see the comment
	 *
	 * @param	Integer		$idComment
	 * @return	Boolean
	 */
	public static function isSeeAllowed($idComment) {
		$idComment	= intval($idComment);
		$comment	= TodoyuCommentCommentManager::getComment($idComment);
		$idTask		= $comment->getTaskID();

		if( TodoyuProjectTaskRights::isSeeAllowed($idTask) ) {
			if( Todoyu::allowed('comment', 'comment:seeAll')) {
				return true;
			} else {
				$idPerson	= Todoyu::personid();
				return $comment->isCurrentPersonCreator() || array_key_exists( $idPerson, $comment->getFeedbackPersonsData() ) || $comment->isPublic();
			}
		}

		return false;
	}



	/**
	 * Check whether person can delete the given comment
	 *
	 * @param	Integer	$idComment
	 * @return	Boolean
	 */
	public static function isDeleteAllowed( $idComment ) {
		$idComment	= intval($idComment);
		$comment	= TodoyuCommentCommentManager::getComment($idComment);
		$idTask		= $comment->getTaskID();

		if( TodoyuProjectTaskRights::isSeeAllowed($idTask) && !TodoyuProjectTaskManager::isLocked($idTask)) {
			if( Todoyu::allowed('comment', 'comment:deleteAll') ) {
				return true;
			}

			if( Todoyu::allowed('comment', 'comment:deleteOwn') && $comment->isCurrentPersonCreator() ) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Restrict access to persons which are allowed to add comments in this task
	 *
	 * @todo	state deny message more precisely
	 * @param	Integer		$idComment
	 */
	public static function restrictEdit($idComment) {
		if( ! self::isEditAllowed($idComment) ) {
			self::deny('comment:edit');
		}
	}



	/**
	 * Restrict access to persons who are allowed to edit comments in
	 *
	 * @param	Integer		$idTask
	 * @todo	state deny message more precisely
	 */
	public static function restrictEditInTask($idTask) {
		if( ! self::isEditInTaskAllowed($idTask) ) {
			self::deny('comment:edit');
		}
	}



	/**
	 * Restrict access to persons who are allowed to add comments in this task
	 *
	 * @param	Integer		$idTask
	 * @todo	state deny message more precisely
	 */
	public static function restrictAddInTask($idTask) {
		$idTask	= intval($idTask);

		if( ! self::isAddInTaskAllowed($idTask) ) {
			self::deny('comment:add');
		}
	}



	/**
	 * Restrict access to person which are allowed to see the comment
	 *
	 * @todo	state deny message more precisely
	 * @param	Integer		$idComment
	 */
	public static function restrictSee($idComment) {
		$idComment	= intval($idComment);

		if( ! self::isSeeAllowed($idComment) ) {
			self::deny('comment:see');
		}
	}



	/**
	 * Restrict delete to person which are allowed to delete comment
	 *
	 * @param	Integer		$idComment
	 */
	public static function restrictDelete($idComment) {
		$idComment	= intval($idComment);

		if( ! self::isDeleteAllowed($idComment)) {
			// @todo	state message more precisely when deletion is restricted because of locked projects its not the general right
			self::deny('comment:delete');
		}
	}

}
?>