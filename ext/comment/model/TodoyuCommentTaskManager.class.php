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
 * [Description]
 *
 * @package		Todoyu
 * @subpackage	[Subpackage]
 */
class TodoyuCommentTaskManager {

	/**
	 *
	 *
	 * @param	Integer		$idTask
	 * @return	TodoyuCommentTask
	 */
	public static function getTask($idTask) {
		return TodoyuRecordManager::getRecord('TodoyuCommentTask', $idTask);
	}



	/**
	 * Get label for comment tab in the task
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getTaskTabLabel($idTask) {
		$idTask	= intval($idTask);

		$numComments = TodoyuCommentCommentManager::getNumberOfTaskComments($idTask);

		if( $numComments === 0 ) {
			return Todoyu::Label('comment.ext.tab.noComments');
		} elseif( $numComments === 1 ) {
			return '1 ' . Todoyu::Label('comment.ext.tab.comment');
		} else {
			return $numComments . ' ' . Todoyu::Label('comment.ext.tab.comments');
		}
	}



	/**
	 * Get tab content for a task
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getTaskTabContent($idTask) {
		$idTask		= intval($idTask);
		$numComments= TodoyuCommentCommentManager::getNumberOfTaskComments($idTask);

			// If no comments
		if( $numComments === 0 ) {
				// Show form to add first task if allowed
			return TodoyuCommentCommentRenderer::renderAddForm($idTask);
		} else {
			return TodoyuCommentCommentRenderer::renderCommentList($idTask);
		}
	}



	/**
	 * Get last comment ID in task
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getLastCommentID($idTask) {
		$taskCommentIDs	= TodoyuCommentCommentManager::getTaskCommentIDs($idTask, true);

		return intval($taskCommentIDs[0]);
	}



	/**
	 * Get first comment ID
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getFirstCommentID($idTask) {
		$taskCommentIDs	= TodoyuCommentCommentManager::getTaskCommentIDs($idTask, false);

		return intval($taskCommentIDs[0]);
	}

}

?>