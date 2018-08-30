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
 *
 * @package			Todoyu
 * @subpackage		Comment
 */
class TodoyuCommentCommentRenderer {

	/**
	 * Render a comment
	 *
	 * @param	Integer		$idComment
	 * @return	String
	 */
	public static function renderComment($idComment) {
		$idComment	= intval($idComment);
		$comment	= TodoyuCommentCommentManager::getComment($idComment);

		$tmpl		= 'ext/comment/view/comment.tmpl';
		$data		= $comment->getTemplateData(true, true);

		return Todoyu::render($tmpl, $data);
	}


	/**
	 * Render comment list in task tab
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$desc
	 * @return	String
	 */
	public static function renderCommentList($idTask, $desc = true) {
		$idTask		= intval($idTask);
		$desc		= $desc ? true : false;

		$tmpl	= 'ext/comment/view/list.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'desc'		=> $desc,
			'comments'	=> array(),
			'locked'	=> TodoyuProjectTaskManager::isLocked($idTask)
		);

		$commentIDs	= TodoyuCommentCommentManager::getTaskCommentIDs($idTask, $desc);

		foreach($commentIDs as $idComment) {
			$data['comments'][$idComment] = self::renderComment($idComment);
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render comment edit form
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idComment
	 * @param	Array		$formParams
	 * @return	String
	 */
	public static function renderEditForm($idTask, $idComment, array $formParams = array()) {
		return self::renderForm($idTask, $idComment, 0, 0, $formParams);
	}



	/**
	 * Render comment add form
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idCommentQuote				Quote comment
	 * @param	Integer		$idCommentMailReply			Quote comment and reply to creator
	 * @param	Array		$formParams
	 * @return	String
	 */
	public static function renderAddForm($idTask, $idCommentQuote = 0, $idCommentMailReply = 0, array $formParams = array()) {
		return self::renderForm($idTask, 0, $idCommentQuote, $idCommentMailReply, $formParams);
	}



	/**
	 * Render a comment form for edit or add
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idComment
	 * @param	Integer		$idCommentQuote
	 * @param	Integer		$idCommentMailReply
	 * @param	Array		$formParams
	 * @return	String
	 */
	protected static function renderForm($idTask, $idComment = 0, $idCommentQuote = 0, $idCommentMailReply = 0, array $formParams = array()) {
		$idComment	= intval($idComment);

		if( $idComment === 0 ) {
			$form = TodoyuCommentCommentManager::getAddForm($idTask, $idCommentQuote, $idCommentMailReply, $formParams);
		} else {
			$form = TodoyuCommentCommentManager::getEditForm($idTask, $idComment, $formParams);
		}

		$tmpl	= 'ext/comment/view/edit.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'idComment'	=> $idComment,
			'formhtml'	=> $form->render()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * @static
	 * @param	String		$filename
	 * @param	String		$filekey
	 * @param	Integer		$idComment
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderFileUploadSuccess($filename, $filekey, $idComment, $idTask) {
		$idComment			= intval($idComment);
		$idTask				= intval($idTask);

		$javaScriptString	= TodoyuString::wrapScript('window.parent.Todoyu.Ext.comment.Edit.uploadFinished(' . $idComment . ', ' . $idTask . ', \'' . $filename . '\', \'' . $filekey . '\');');

		return self::renderUploadMessage($javaScriptString);
	}



	/**
	 *
	 * @todo	Also check and display php.ini value here (or better in general for upload messages)
	 * @param	Integer		$error
	 * @param	String		$fileName
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderFileUploadFailed($error, $fileName, $idTask){
		$idTask				= intval($idTask);

		$maxFileSize		= TodoyuString::formatSize(intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']));
		$javaScriptString	= TodoyuString::wrapScript('window.parent.Todoyu.Ext.comment.Edit.uploadFailed(' . $idTask . ', ' . $error . ', \'' . $fileName . '\', \'' . $maxFileSize . '\');');

		return self::renderUploadMessage($javaScriptString);
	}



	/**
	 * @static
	 * @param	String		$javaScriptString
	 * @return	String
	 */
	protected static function renderUploadMessage($javaScriptString) {
		$tmpl = 'core/view/htmldoc.tmpl';
		$data = array(
			'title' => 'Uploader IFrame',
			'content' => $javaScriptString
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * @static
	 * @param	Integer		$idComment
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderFileSelector($idComment, $idTask) {
		$form = TodoyuCommentCommentManager::getCommentForm($idComment, $idTask, array('id_task' => $idTask, 'id' => $idComment));

		$form->setRecordID($idTask . '-' . $idComment);

		return $form->getField('assets')->render();
	}
}

?>