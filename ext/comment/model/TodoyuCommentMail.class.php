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
 * Comment sent as mail
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentMail extends TodoyuMail {

	/**
	 * @var	TodoyuMailReceiverInterface[]		Email receiver
	 */
	protected $mailReceivers;

	/**
	 * @var	TodoyuCommentComment	Comment to be send
	 */
	protected $comment;


	/**
	 * Initialize comment mail
	 *
	 * @param	Integer							$idComment
	 * @param	TodoyuMailReceiverInterface[]	$mailReceivers
	 * @param	Array							$config
	 */
	public function __construct($idComment, array $mailReceivers, array $config = array()) {
		parent::__construct($config);

		$this->comment			= TodoyuCommentCommentManager::getComment($idComment);
		$this->mailReceivers	= $mailReceivers;

		$this->init();
	}



	/**
	 * Init mail
	 *
	 */
	protected function init() {
		$this->setMailSubject();
		$this->setHeadline('comment.ext.mail.newcomment');

			// Add all receivers
		foreach($this->mailReceivers as $mailReceiver) {
			$this->addReceiver($mailReceiver);
		}

		$this->setHtmlContent($this->getContent(true));
		$this->setTextContent($this->getContent(false));
		$this->addCommentAssetsAsAttachments();
	}



	/**
	 * Get comment
	 *
	 * @return	TodoyuCommentComment
	 */
	public function getComment() {
		return $this->comment;
	}



	/**
	 * Get receivers list
	 *
	 * @return	TodoyuMailReceiverInterface[]
	 */
	public function getReceivers() {
		return $this->mailReceivers;
	}




	/**
	 * Set mail subject
	 */
	protected function setMailSubject() {
		$subject	= Todoyu::Label('comment.ext.mail.subject') . ': ' . $this->comment->getTask()->getTitle() . ' (#' . $this->comment->getTask()->getTaskNumber(true) . ')';

		$this->setSubject($subject);
	}



	/**
	 * Add all assets of the comment as attachments
	 *
	 */
	protected function addCommentAssetsAsAttachments() {
		$assets	= $this->comment->getAssets();

		foreach($assets as $asset) {
			$this->addAttachment($asset->getFileStoragePath(), $asset->getFilename());
		}
	}



	/**
	 * Get email content
	 *
	 * @param	Boolean		$asHtml
	 * @return	String
	 */
	protected function getContent($asHtml = false) {
		$tmpl	= $this->getTemplate($asHtml);
		$data	= $this->getData();

		$data['hideEmails']	= true;

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get template
	 *
	 * @param	Boolean		$asHtml
	 * @return	String
	 */
	protected function getTemplate($asHtml = false) {
		$basePath	= 'ext/comment/view';
		$type		= $asHtml ? 'html' : 'text';
		$template	= $basePath . '/comment-mail-' . $type . '.tmpl';

		return TodoyuFileManager::pathAbsolute($template);
	}



	/**
	 * Get data to render email
	 *
	 * @return	Array
	 */
	protected function getData() {
		$task			= $this->comment->getTask();
		$project		= $this->comment->getProject();
		$personWrite	= $this->comment->getPersonCreate();
		$personSend		= TodoyuAuth::getPerson();

		$data	= array(
			'comment'			=> $this->comment->getTemplateData(),
			'project'			=> $project->getTemplateData(true),
			'task'				=> $task->getTemplateData(),
//			'personReceive'		=> $this->person->getTemplateData(),
			'personWrite'		=> $personWrite->getTemplateData(),
			'personSend'		=> $personSend->getTemplateData(),
			'feedback_persons'	=> $this->comment->getFeedbackPersonsData()
		);

		$idTask	= $task->getID();

			// Add deep-link URLs for task and comment
		$data['tasklink']	= self::buildUrlForTask($idTask);
		$data['commentlink']= self::buildUrlForComment($this->comment->getID());

		return $data;
	}



	/**
	 * Build task deep-link
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$encode
	 * @param	Boolean		$absolute
	 * @return	String
	 */
	private static function buildUrlForTask($idTask, $encode = true, $absolute = true) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		return TodoyuString::buildUrl(
			array(
				'ext'		=> 'project',
				'project'	=> $task->getProjectID(),
				'task'		=> $idTask,
			),
			'task-' . $idTask,
			$absolute,
			$encode
		);
	}



	/**
	 * Build comment deep-link
	 *
	 * @param	Integer		$idComment
	 * @param	Boolean		$encode
	 * @param	Boolean		$absolute
	 * @return	String
	 */
	private static function buildUrlForComment($idComment, $encode = true, $absolute = true) {
		$idComment	= intval($idComment);
		$comment	= TodoyuCommentCommentManager::getComment($idComment);

		return TodoyuString::buildUrl(
			array(
				'ext'		=> 'project',
				'project'	=> $comment->getProjectID(),
				'task'		=> $comment->getTaskID(),
				'tab'		=> 'comment'
			),
			'task-comment-' . $idComment,
			$absolute,
			$encode
		);
	}

}

?>