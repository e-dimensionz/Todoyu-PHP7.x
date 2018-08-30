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
 * Raw IMAP message
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapRawMessage {

	/**
	 * @var	String	Message key
	 */
	protected $messageKey;

	/**
	 * @var	Array	Message data (header/body)
	 */
	protected $data;



	/**
	 * Initialize
	 *
	 * @param	String		$messageKey
	 */
	public function __construct($messageKey) {
		$this->messageKey	= $messageKey;
	}



	/**
	 * Load content into internal data
	 *
	 */
	protected function loadData() {
		list($header, $body) = explode("\r\n\r\n", $this->getMessageContent(), 2);

		$this->data = array(
			'header'	=> $header,
			'body'		=> $body
		);
	}



	/**
	 * Get message storage path
	 *
	 * @return	String
	 */
	protected function getStoragePath() {
		return TodoyuImapRawMessageManager::getMessagePath($this->messageKey);
	}



	/**
	 * Get message content
	 *
	 * @return	String
	 */
	public function getMessageContent() {
		return TodoyuFileManager::getFileContent($this->getStoragePath());
	}



	/**
	 * Assure that the content is loaded into the internal data variable
	 *
	 */
	protected function assureDataIsLoaded() {
		if( is_null($this->data) ) {
			$this->loadData();
		}
	}



	/**
	 * Get raw header string
	 *
	 * @return	String
	 */
	public function getHeader() {
		$this->assureDataIsLoaded();

		return $this->data['header'];
	}



	/**
	 * Get raw body string
	 *
	 * @return	String
	 */
	public function getBody() {
		$this->assureDataIsLoaded();

		return $this->data['body'];
	}



	/**
	 * Get content in eml format
	 *
	 * @return	String
	 */
	public function getEmlContent() {
		return $this->getMessageContent();
	}



	/**
	 * Send headers for eml download
	 *
	 * @param	String		$filename
	 */
	protected function sendEmlDownloadHeaders($filename = '') {
		$mime		= 'message/rfc822';
		$filename	= $filename === '' ? 'message' : $filename;
		$size		= @filesize($this->getStoragePath());

		TodoyuHeader::sendDownloadHeaders($mime, $filename, $size);
	}



	/**
	 * Send/echo eml content
	 *
	 */
	protected function sendEmlDownloadContent() {
		readfile($this->getStoragePath());
	}



	/**
	 * Send raw message as eml download
	 *
	 * @param	String $filename
	 */
	public function sendAsEmlDownload($filename = '') {
		$this->sendEmlDownloadHeaders($filename);
		$this->sendEmlDownloadContent();
	}

}

?>