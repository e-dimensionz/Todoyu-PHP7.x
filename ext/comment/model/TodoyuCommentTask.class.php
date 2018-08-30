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
 * Comment task
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentTask extends TodoyuProjectTask {

	/**
	 * Get comment project
	 *
	 * @return	TodoyuCommentProject
	 */
	public function getProject() {
		return TodoyuCommentProjectManager::getProject($this->getProjectID());
	}



	/**
	 * Get comment fallback ID of project
	 *
	 * @return	Integer
	 */
	public function getCommentFallbackID() {
		return $this->getProject()->getCommentFallbackID();
	}



	/**
	 * Get comment fallback of project
	 *
	 * @return	TodoyuCommentFallback
	 */
	public function getCommentFallback() {
		return $this->getProject()->getCommentFallback();
	}



	/**
	 * Check whether project has a comment fallback
	 *
	 * @return	Boolean
	 */
	public function hasCommentFallback() {
		return $this->getProject()->hasCommentFallback();
	}



	/**
	 * Apply comment fallback if project has one (or try global fallback)
	 *
	 * @param	Array		$data
	 * @return	Array
	 */
	public function applyCommentFallback(array $data) {
		return $this->getProject()->applyCommentFallback($this->getID(), $data);
	}



	/**
	 * Get ID of the first comment
	 *
	 * @return	Integer
	 */
	public function getFirstCommentID() {
		return TodoyuCommentTaskManager::getFirstCommentID($this->getID());
	}



	/**
	 * Get ID of the last comment
	 *
	 * @return	Integer
	 */
	public function getLastCommentID() {
		return TodoyuCommentTaskManager::getLastCommentID($this->getID());
	}



	/**
	 * Get first (oldest) comment
	 *
	 * @return	TodoyuCommentComment|Boolean
	 */
	public function getFirstComment() {
		$idFirstComment	= $this->getFirstCommentID();

		if( $idFirstComment !== 0 ) {
			return TodoyuCommentCommentManager::getComment($idFirstComment);
		} else {
			return false;
		}
	}

}

?>