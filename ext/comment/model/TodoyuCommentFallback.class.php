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
 * Comment fallback
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentFallback extends TodoyuBaseObject {

	/**
	 * Initialize
	 *
	 * @param	Integer		$idFallback
	 */
	public function __construct($idFallback) {
		parent::__construct($idFallback, 'ext_comment_fallback');
	}


	/**
	 * Get fallback title
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->get('title');
	}



	/**
	 * Get 'is public' flag
	 *
	 * @return	Integer
	 */
	public function getIsPublic() {
		return $this->getInt('is_public');
	}



	/**
	 * Check whether is public flag is set
	 *
	 * @return	Boolean
	 */
	public function hasIsPublic() {
		return $this->getIsPublic() !== 0;
	}



	/**
	 * Get feedback person ID
	 *
	 * @return	Integer
	 */
	public function getPersonFeedbackID() {
		return $this->getInt('id_person_feedback');
	}



	/**
	 * Check whether feedback person is set
	 *
	 * @return	Boolean
	 */
	public function hasPersonFeedback() {
		return $this->getPersonFeedbackID() !== 0;
	}




	/**
	 * Get feedback task person key
	 *
	 * @return	String
	 */
	public function getTaskPersonFeedbackKey() {
		return $this->get('taskperson_feedback');
	}



	/**
	 * Check whether a feedback task person is set
	 *
	 * @return	Boolean
	 */
	public function hasTaskPersonFeedback() {
		return $this->getTaskPersonFeedbackKey() !== '0';
	}



	/**
	 * Get feedback role ID
	 *
	 * @return	Integer
	 */
	public function getRoleFeedbackID() {
		return $this->getInt('id_role_feedback');
	}



	/**
	 * Check whether a feedback role is set
	 *
	 * @return	Boolean
	 */
	public function hasRoleFeedback() {
		return $this->getRoleFeedbackID() !== 0;
	}



	/**
	 * Get email person ID
	 *
	 * @return	Integer
	 */
	public function getPersonEmailID() {
		return $this->getInt('id_person_email');
	}



	/**
	 * Check whether an email person is set
	 *
	 * @return	Boolean
	 */
	public function hasPersonEmail() {
		return $this->getPersonEmailID() !== 0;
	}



	/**
	 * Get email task person key
	 *
	 * @return	String
	 */
	public function getTaskPersonEmailKey() {
		return $this->get('taskperson_email');
	}



	/**
	 * Check whether an email task person is set
	 *
	 * @return	Boolean
	 */
	public function hasTaskPersonEmail() {
		return $this->getTaskPersonEmailKey() !== '0';
	}



	/**
	 * Get email role ID
	 *
	 * @return	Integer
	 */
	public function getRoleEmailID() {
		return $this->getInt('id_role_email');
	}



	/**
	 * Check whether an email role is set
	 *
	 * @return	Boolean
	 */
	public function hasRoleEmail() {
		return $this->getRoleEmailID() !== 0;
	}



	/**
	 * Apply fallback data to comment
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$commentData
	 * @return	Array		Comment data with fallback values
	 * @deprecated
	 */
	public function apply($idTask, array $commentData) {
		return $this->applyFallbackData($idTask, $commentData);
	}



	/**
	 * Apply fallback data to comment
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$commentData
	 * @return	Array		Comment data with fallback values
	 */
	public function applyFallbackData($idTask, array $commentData) {
		$task	= TodoyuCommentTaskManager::getTask($idTask);

			// Is public
		if( !isset($commentData['is_public']) && $this->hasIsPublic() ) {
			$commentData['is_public'] = 1;
		}

			// Feedback
		if( !isset($commentData['feedback']) ) {
			if( $this->hasPersonFeedback() ) {
				$commentData['feedback'] 	= $this->getPersonFeedbackID();
			} elseif( $this->hasTaskPersonFeedback() ) {
				$commentData['feedback']	= $task->getPersonID($this->getTaskPersonFeedbackKey());
			} elseif( $this->hasRoleFeedback() ) {
				$projectRolePersonIDs		= $task->getProject()->getRolePersonIDs($this->getRoleFeedbackID());
				$commentData['feedback']	= reset($projectRolePersonIDs);
			}
		}

			// Email
		if( !isset($commentData['email_receivers']) ) {
			if( $this->hasPersonEmail() ) {
				$commentData['email_receivers'] = $this->getPersonEmailID();
			} elseif( $this->hasTaskPersonEmail() ) {
				$commentData['email_receivers'] = $task->getPersonID($this->getTaskPersonEmailKey());
			} elseif( $this->hasRoleEmail() ) {
				$projectRolePersonIDs 			= $task->getProject()->getRolePersonIDs($this->getRoleEmailID());
				$commentData['email_receivers'] = reset($projectRolePersonIDs);
			}
		}

		return $commentData;
	}

}

?>