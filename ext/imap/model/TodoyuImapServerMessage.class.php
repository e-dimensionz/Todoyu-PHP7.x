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
 * Message on the IMAP server
 * Wrapper to parse steam infos
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapServerMessage {

	/**
	 * @var	TodoyuImapMailbox	Mailbox
	 */
	protected $mailbox;

	/**
	 * @var	Integer		Message number/index on server
	 */
	protected $messageNo;

	/**
	 * @var	Resource	IMAP resource connection
	 */
	protected $stream;

	/**
	 * @var	stdClass	Message structure object
	 */
	protected $structure;

	/**
	 * @var	TodoyuImapServerMessagePart[]
	 */
	protected $parts = array();

	/**
	 * @var	Array		Parsed header data
	 */
	protected $header = array();

	/**
	 * @var	TodoyuImapServerMessagePartAttachment[]		Attachment parts
	 */
	protected $attachments = array();

	/**
	 * @var	TodoyuImapServerMessagePartInlineImage[]	Inline image parts
	 */
	protected $inlineImages = array();

	/**
	 * @var	Array		Plain text content parts
	 */
	protected $contentPlain = array();

	/**
	 * @var	Array		Html content parts
	 */
	protected $contentHtml = array();

	/**
	 * @var	String		Parse errors
	 */
	protected $parseErrors = array();



	/**
	 * Initialize
	 *
	 * @param	TodoyuImapMailbox		$mailbox
	 * @param	Integer					$messageNo
	 */
	public function __construct(TodoyuImapMailbox $mailbox, $messageNo) {
		$this->mailbox	= $mailbox;
		$this->stream	= $mailbox->getStream();
		$this->messageNo= intval($messageNo);
		$this->structure= $this->getStructure();

		if( is_object($this->structure) ) {
			$this->parseStructure();
		} else {
			TodoyuLogger::logError('Invalid email message structure. Mailbox: "' . $this->mailbox->getMailboxName() . '", MessageNumber: ' . $this->messageNo);
		}
	}



	/**
	 * Get IMAP resource stream
	 *
	 * @return	Resource
	 */
	public function getStream() {
		return $this->stream;
	}



	/**
	 * Get message number
	 *
	 * @return	Integer
	 */
	public function getMessageNumber() {
		return $this->messageNo;
	}



	/**
	 * Get message send date
	 *
	 * @return	Integer
	 */
	public function getDate() {
		return intval($this->header['date']);
	}



	/**
	 * Get message subject
	 *
	 * @return	String
	 */
	public function getSubject() {
		return $this->header['subject'];
	}



	/**
	 * Get size
	 *
	 * @return	Integer
	 */
	public function getSize() {
		return $this->header['size'];
	}



	/**
	 * Check whether sender address is available
	 *
	 * @return	Boolean
	 */
	public function hasSenderAddress() {
		return isset($this->header['sender']['addresss']);
	}



	/**
	 * Get sender address data
	 *
	 * @return	Array
	 */
	public function getSenderAddressData() {
		return $this->header['sender'];
	}



	/**
	 * Get from address data
	 *
	 * @return	Array
	 */
	public function getFromAddressData() {
		return $this->header['from'];
	}



	/**
	 * Get TO addresses data
	 *
	 * @return	Array[]
	 */
	public function getToAddressesData() {
		return TodoyuArray::assure($this->header['to']);
	}



	/**
	 * Get REPLYTO addresses data
	 *
	 * @return	Array[]
	 */
	public function getReplyToAddressesData() {
		return TodoyuArray::assure($this->header['replyto']);
	}



	/**
	 * Get CC addresses data
	 *
	 * @return	Array[]
	 */
	public function getCcAddressesData() {
		return TodoyuArray::assure($this->header['cc']);
	}



	/**
	 * Parse message data
	 *
	 * @return	Boolean
	 */
	protected function parseStructure() {
			// Read header data
		$this->header = $this->getHeaderData();

			// Parse parts and add them flattened to the parts list
		$this->parseStructureIntoPartsList();

			// Process all parts and fill internal storage
		$this->processPartsList();
	}



	/**
	 * Parse message structure recursively
	 *
	 */
	protected function parseStructureIntoPartsList() {
		if( is_array($this->structure->parts) ) {	// There some sub parts
			foreach($this->structure->parts as $index => $part) {
				$this->parsePartsRecursive($part, $index+1);
			}
		} else {	// Email does not have a separate mime attachment for text
			$this->parts[] = TodoyuImapServerMessagePartManager::getMessagePart($this, '1', $this->structure);
		}
	}



	/**
	 * Parse the part and it's sub parts
	 * Create message part objects in a flat parts list
	 *
	 * @param	stdClass	$part
	 * @param	String		$section
	 */
	protected function parsePartsRecursive($part, $section) {
		if( $part->type == 2 ) { // Check to see if the part is an attached email message, as in the RFC-822 type
			$this->parts[] = new TodoyuImapServerMessagePartRFC822($this, $section, $part); // TodoyuImapServerMessagePartManager::getMessagePart($this, $section . '.1', $part);
		} else { // If there are more sub-parts, expand them out.
			$this->parts[] = TodoyuImapServerMessagePartManager::getMessagePart($this, $section, $part);
			if( is_array($part->parts) ) {
				foreach($part->parts as $index => $subPart) {
					$subPartSection	= $section . '.' . ($index + 1);
					$this->parsePartsRecursive($subPart, $subPartSection);
				}
			}
		}
	}



	/**
	 * Process the parts list and fill internal storage
	 */
	protected function processPartsList() {
		foreach($this->parts as $part) {

			switch( get_class($part) ) {
				case 'TodoyuImapServerMessagePartAttachment':
				case 'TodoyuImapServerMessagePartRFC822':
					$this->attachments[] = $part;
					break;

				case 'TodoyuImapServerMessagePartInlineImage':
					$this->inlineImages[] = $part;
					$this->attachments[] = $part;
					break;
				
				default:
					if( $part->getType() == IMAP_MESSAGE_TYPE_TEXT ) {
						if( $part->isSubTypePlain() ) {
							$this->contentPlain[] = $part->getContent();
						} elseif( $part->isSubTypeHtml() ) {
							$this->contentHtml[] = $part->getContent();
						}	
					}
					break;
			}
		}
	}



	/**
	 * Get attachment parts
	 *
	 * @return	TodoyuImapServerMessagePartAttachment[]|TodoyuImapServerMessagePartRFC822[]
	 */
	public function getAttachments() {
		return $this->attachments;
	}



	/**
	 * Get inline images
	 *
	 * @return	TodoyuImapServerMessagePartInlineImage[]
	 */
	public function getInlineImages() {
		return $this->inlineImages;
	}



	/**
	 * Get message structure
	 *
	 * @param	Integer		$options
	 * @return	stdClass
	 */
	public function getStructure($options = 0) {
		return imap_fetchstructure($this->stream, $this->messageNo, $options);
	}



	/**
	 * Get amount of attachments
	 * @return int
	 */
	public function getAmountAttachments() {
		return sizeof($this->attachments);
	}



	/**
	 * Get parsed header data
	 *
	 * @return	Array
	 */
	public function getHeaderData() {
		$headerInfo	= $this->getHeaderInfo();

		return array(
			'date'			=> strtotime($headerInfo->date),
			'subject'		=> TodoyuImapServerMessageManager::decodeHeader($headerInfo->subject),
			'message_id'	=> $headerInfo->message_id,
			'size'			=> $headerInfo->Size,
			'msgno'			=> $headerInfo->Msgno,
			'from'			=> TodoyuImapServerMessageManager::extractAddress($headerInfo->from[0]),
			'sender'		=> TodoyuImapServerMessageManager::extractAddress($headerInfo->sender[0]),
			'to'			=> TodoyuImapServerMessageManager::extractAddresses($headerInfo->to),
			'cc'			=> TodoyuImapServerMessageManager::extractAddresses($headerInfo->cc),
			'replyto'		=> TodoyuImapServerMessageManager::extractAddresses($headerInfo->reply_to)
		);
	}



	/**
	 * Get header info object
	 *
	 * @return	stdClass
	 */
	protected function getHeaderInfo() {
		return imap_headerinfo($this->stream, $this->messageNo);
	}



	/**
	 * Get raw header string
	 *
	 * @param	Integer		$options
	 * @return	String
	 */
	public function getRawHeader($options = 0) {
		return imap_fetchheader($this->stream, $this->messageNo, $options);
	}



	/**
	 * Get raw body string
	 *
	 * @param	Integer		$options
	 * @return	String
	 */
	public function getRawBody($options = 0) {
		$options	|= FT_PEEK;

		return imap_body($this->stream, $this->messageNo, $options);
	}



	/**
	 * Get plain text
	 *
	 * @return	String
	 */
	public function getContentPlain() {
		return implode('', $this->contentPlain);
	}



	/**
	 *
	 *
	 * @return	Array
	 */
	public function getContentPlainParts() {
		return $this->contentPlain;
	}



	/**
	 * Get message body HTML text
	 *
	 * @return	String
	 */
	public function getContentHtml() {
		return implode('', $this->contentHtml);
	}



	/**
	 * @return	Array
	 */
	public function getContentHtmlParts() {
		return $this->contentHtml;
	}



	/**
	 * Get message ID
	 *
	 * @return	String
	 */
	public function getMessageID() {
		return TodoyuImapMessageManager::cleanID($this->header['message_id']);
	}

}



?>