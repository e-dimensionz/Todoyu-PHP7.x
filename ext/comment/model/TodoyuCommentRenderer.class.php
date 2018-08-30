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
 * Render task comments
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentRenderer {

	/**
	 * Render a comment
	 *
	 * @param	Integer		$idComment
	 * @return	String
	 * @deprecated
	 */
	public static function renderComment($idComment) {
		return TodoyuCommentCommentRenderer::renderComment($idComment);
	}



	/**
	 * Render comment list in task tab
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$desc
	 * @return	String
	 * @deprecated
	 */
	public static function renderCommentList($idTask, $desc = true) {
		return TodoyuCommentCommentRenderer::renderCommentList($idTask, $desc);
	}



	/**
	 * Get tab label for portal feedback task: label and amount of feedbacks
	 *
	 * @param	Boolean		$count
	 * @return	String
	 */
	public static function renderPortalFeedbackTabLabel($count = true) {
		$label	= Todoyu::Label('comment.ext.portal.tab.feedback');

		if( $count ) {
			$numFeedbacks	= TodoyuCommentFeedbackManager::getOpenFeedbackCount();
			$label			= $label . ' (' . $numFeedbacks . ')';
		}

		return $label;
	}



	/**
	 * Render feedback tab content in portal
	 *
	 * @return	String
	 */
	public static function renderPortalFeedbackTabContent() {
		$amountFeedbackRequests	= TodoyuCommentFeedbackManager::getOpenFeedbackCount();
		TodoyuHeader::sendTodoyuHeader('items', $amountFeedbackRequests);

		TodoyuProjectPreferences::setForcedTaskTab('comment');

		$taskIDs	= TodoyuCommentCommentManager::getFeedbackTaskIDs();

		return TodoyuProjectTaskRenderer::renderTaskListing($taskIDs);
	}

}

?>