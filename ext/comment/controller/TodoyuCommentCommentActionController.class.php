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
 * Comment action controller
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentCommentActionController extends TodoyuActionController {

	/**
	 * Init comment controller: restrict rights
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('comment', 'general:use');
	}



	/**
	 * Add (form for adding) a new comment
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function addAction(array $params) {
		$idTask				= intval($params['task']);
		$idCommentQuote		= intval($params['quote']);
		$idCommentMailReply	= intval($params['mailReply']);

		TodoyuCommentRights::restrictAddInTask($idTask);

		return TodoyuCommentCommentRenderer::renderAddForm($idTask, $idCommentQuote, $idCommentMailReply);
	}



	/**
	 * Load edit view of the comment
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idTask		= intval($params['task']);
		$idComment	= intval($params['comment']);

			// Person is the creator + has right editOwn or has right editAll
		TodoyuCommentRights::restrictEdit($idComment);

		return TodoyuCommentCommentRenderer::renderEditForm($idTask, $idComment);
	}



	/**
	 * Delete a comment
	 *
	 * @param	Array		$params
	 */
	public function deleteAction(array $params) {
		Todoyu::restrict('comment', 'comment:deleteOwn');

		$idComment		= intval($params['comment']);

		TodoyuCommentRights::restrictDelete($idComment);
		TodoyuCommentCommentManager::deleteComment($idComment);

		$comment= TodoyuCommentCommentManager::getComment($idComment);
		$idTask	= $comment->getTaskID();

		TodoyuHeader::sendTodoyuHeader('task', $idTask);
		TodoyuHeader::sendTodoyuHeader('comment', $idComment);
		TodoyuHeader::sendTodoyuHeader('tabLabel', TodoyuCommentTaskManager::getTaskTabLabel($idTask));
	}



	/**
	 * Save (update) comment (+save comment mail if option activated)
	 *
	 * @param	Array			$params
	 * @return	Void|String		Failure returns re-rendered form with error messages
	 */
	public function saveAction(array $params) {
		$formData	= $params['comment'];
		$idComment	= intval($formData['id']);
		$idTask		= intval($formData['id_task']);

			// Check editing rights for existing comments
		if( $idComment !== 0 ) {
			TodoyuCommentRights::restrictEdit($idComment);
		} else {
			TodoyuCommentRights::restrictAddInTask($idTask);
		}

		$form	= TodoyuCommentCommentManager::getCommentForm($idComment, $idTask, $formData);

			// Validate and save comment and notify about success/failure
			// Also send email(s) if any receivers are selected.
		if( $form->isValid() ) {
			$storageData = $form->getStorageData();

				// Store comment
			$saveResult	= TodoyuCommentCommentManager::saveComment($storageData);
			$idComment	= $saveResult['id'];

				// Send info headers
			TodoyuHeader::sendTodoyuHeader('comment', $idComment);
			TodoyuHeader::sendTodoyuHeader('tabLabel', TodoyuCommentTaskManager::getTaskTabLabel($idTask));

			if( AREAEXT === 'portal' ) {
				TodoyuHeader::sendTodoyuHeader('openFeedbackCount', TodoyuCommentFeedbackManager::getOpenFeedbackCount());
			}

				// Send back feedback status
			if( sizeof($saveResult['feedback']) ) {
				$feedbackData = array();
				foreach($saveResult['feedback'] as $idPerson) {
					$feedbackData[] = array(
						'id'	=> $idPerson,
						'name'	=> TodoyuContactPersonManager::getLabel($idPerson)
					);
				}
				TodoyuHeader::sendTodoyuHeader('feedback', $feedbackData);
			}

				// Send back email status data if any sent
			if( $saveResult['email'] ) {
				$emailStatusData = array();
				foreach($saveResult['email']['receivers'] as $mailReceiver) {
					$emailStatusData[] = array(
						'name'		=> $mailReceiver->getName(),
						'status'	=> $saveResult['email']['sendStatus']
					);
				}
				TodoyuHeader::sendTodoyuHeader('emailStatus', $emailStatusData);
			}

			return '';
		} else { // Form data is invalid
			TodoyuHeader::sendTodoyuErrorHeader();
			TodoyuHeader::sendTodoyuHeader('comment', $idComment);

			$form->setRecordID($idTask . '-' . $idComment);

			return $form->render();
		}
	}



	/**
	 * @param	Array		$params
	 * @return	String
	 */
	public function uploadassetfileAction($params) {
		$idComment	= intval($params['comment']['id']);
		$idTask		= intval($params['comment']['id_task']);
		$maxFileSize= intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']);

		$file	= TodoyuRequest::getUploadFile('file', 'comment');
		$error	= intval($file['error']);

			// Check again for file limit
		if( !$error && $file['size'] > $maxFileSize ) {
			$error	= UPLOAD_ERR_FORM_SIZE;
		}
			// Check length of file name
		if( strlen($file['name']) > Todoyu::$CONFIG['EXT']['assets']['max_length_filename'] ) {
			$file['error']	= 3;
		}

			// Render frame content. Success or error
		if( $error === UPLOAD_ERR_OK && is_array($file) && !$file['error'] ) {
			$uploader	= new TodoyuCommentTempUploader($idComment, $idTask);
			$fileKey	= $uploader->addFile($file);
			$fileInfo	= $uploader->getFileInfo($fileKey);

			return TodoyuCommentCommentRenderer::renderFileUploadSuccess(TodoyuCommentAssetManager::getTempFileLabel($fileInfo), $fileKey, $idComment, $idTask);
		} else {
				// Notify upload failure
			TodoyuLogger::logError('File upload failed: ' . $file['name'] . ' (ERROR:' . $error . ')');
			return TodoyuCommentCommentRenderer::renderFileUploadFailed($error, $file['name'], $idTask);
		}
	}



	/**
	 * @param	Array		$params
	 * @return	String
	 */
	public function refreshfileselectorAction($params) {
		$idComment	= intval($params['comment']);
		$idTask		= intval($params['task']);

		return TodoyuCommentCommentRenderer::renderFileSelector($idComment, $idTask);
	}



	/**
	 * @param	Array		$params
	 */
	public function cleartempuploadsAction($params) {
		$idComment	= intval($params['comment']);
		$idTask		= intval($params['task']);

		$uploader	= new TodoyuCommentTempUploader($idComment, $idTask);
		$uploader->clear();
	}



	/**
	 * Mark a comment with feedback request as seen/not seen
	 *
	 * @param	Array		$params
	 */
	public function seenAction(array $params) {
		$idComment	= intval($params['comment']);
		$setSeen	= intval($params['setseen']);

		TodoyuCommentRights::restrictSee($idComment);

		if( $setSeen === 1 ) {
			TodoyuCommentFeedbackManager::setAsSeen($idComment);
		} else {
			TodoyuCommentFeedbackManager::setAsUnseen($idComment);
		}

		$numOpenFeedbacks = TodoyuCommentFeedbackManager::getOpenFeedbackCount();

		TodoyuHeader::sendTodoyuHeader('feedback', $numOpenFeedbacks);
	}



	/**
	 * Mark a comment with feedback request as seen/not seen by dummy user
	 *
	 * @param	Array		$params
	 */
	public function seenbydummyAction(array $params) {
		$idComment		= intval($params['comment']);
		$setSeen		= intval($params['setseen']);
		$idDummyPerson	= intval($params['dummyperson']);

		TodoyuCommentRights::restrictSee($idComment);

		if( !Todoyu::allowed('comment', 'overrideDummy:acknowledgeFeedback') ) {
			TodoyuRightsManager::deny('comment', 'overrideDummy:acknowledgeFeedback');
		}

		if( !TodoyuContactPersonManager::getPerson($idDummyPerson)->isDummy() ) {
			TodoyuLogger::logSecurity('Access denied: toggle feedback of other non-dummy person');
			die('Access denied: toggle feedback of other non-dummy person');
		}

		if( $setSeen === 1 ) {
			$success	= TodoyuCommentFeedbackManager::setAsSeen($idComment, $idDummyPerson);
		} else {
			$success	= TodoyuCommentFeedbackManager::setAsUnseen($idComment, $idDummyPerson);
		}

		if( $success ) {
			TodoyuHeader::sendTodoyuHeader('setSeen', $setSeen ? 1 : 0);
		}
	}

}

?>