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
 * Controller for task comments
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentTaskActionController extends TodoyuActionController {

	/**
	 * Initialize controller, check use right
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('comment', 'general:use');
	}



	/**
	 * Get task ID to comment number
	 *
	 * @param	Array		$params
	 * @return	Integer
	 */
	public function getcommenttaskidAction(array $params) {
		$idComment	= trim($params['commentnumber']);

		TodoyuCommentRights::restrictSee($idComment);

		return TodoyuCommentCommentManager::getTaskID($idComment);
	}



	/**
	 * Get comment list for task tab
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		$idTask	= intval($params['task']);
		$desc	= intval($params['desc']) === 1;

		TodoyuProjectTaskRights::restrictSee($idTask);

		return TodoyuCommentCommentRenderer::renderCommentList($idTask, $desc);
	}



	/**
	 * Toggle visibility of a comment
	 *
	 * @param	Array		$params
	 */
	public function togglepublicAction(array $params) {
		$idComment	= intval($params['comment']);

		Todoyu::restrict('comment', 'comment:makePublic');
		TodoyuCommentRights::restrictSee($idComment);

		TodoyuCommentCommentManager::togglePublic($idComment);
		TodoyuCache::flush();

		$publicFeedbackWarning	= TodoyuCommentCommentManager::getComment($idComment)->getPublicFeedbackWarning();
		if( $publicFeedbackWarning !== false ) {
			TodoyuHeader::sendTodoyuHeader('publicFeedbackWarning', $publicFeedbackWarning);
		}
	}

}

?>