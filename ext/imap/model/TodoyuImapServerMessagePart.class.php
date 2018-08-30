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
 * Server message part
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapServerMessagePart {

	/**
	 * @var TodoyuImapServerMessage		Message which contains this part
	 */
	protected $message;

	/**
	 * @var	stdClass	Part object delivered from php
	 */
	protected $part;

	/**
	 * @var	String		Section key (ex: 1.1.1 or just 2)
	 */
	protected $section;



	/**
	 * Initialize
	 *
	 * @param	TodoyuImapServerMessage		$message
	 * @param	String						$section
	 * @param	stdClass					$part
	 */
	public function __construct(TodoyuImapServerMessage $message, $section, stdClass $part) {
		$this->message	= $message;
		$this->section	= $section;
		$this->part		= $part;
	}



	/**
	 * Get message
	 *
	 * @return	TodoyuImapServerMessage
	 */
	public function getMessage() {
		return $this->message;
	}



	/**
	 * Get section
	 *
	 * @return	String
	 */
	public function getSection() {
		return $this->section;
	}



	/**
	 * Get part
	 *
	 * @return	stdClass
	 */
	public function getPart() {
		return $this->part;
	}



	/**
	 * Get part encoding key
	 *
	 * @return	Integer
	 */
	public function getEncoding() {
		return $this->part->encoding;
	}



	/**
	 * @return bool|String
	 */
	public function getCharset() {
		return TodoyuImapServerMessagePartManager::getPartCharset($this->part);
	}



	/**
	 * Check whether part is an attachment part
	 *
	 * @return	Boolean
	 */
	public function isAttachment() {
		return $this->part->disposition === 'attachment';
	}



	/**
	 * Get part type
	 *
	 * @see		constants.php
	 * @return	Integer
	 */
	public function getType() {
		return intval($this->part->type);
	}



	/**
	 * Check whether part is of type text
	 *
	 * @return	Boolean
	 */
	public function isTypeText() {
		return $this->getType() === IMAP_MESSAGE_TYPE_TEXT;
	}


	/**
	 * Check whether part is of type multipart
	 *
	 * @return	Boolean
	 */
	public function isTypeMultiPart() {
		return $this->getType() === IMAP_MESSAGE_TYPE_MULTIPART;
	}



	/**
	 * Check whether part is of type message
	 *
	 * @return	Boolean
	 */
	public function isTypeMessage() {
		return $this->getType() === IMAP_MESSAGE_TYPE_MESSAGE;
	}



	/**
	 * Check whether part is of type application
	 *
	 * @return	Boolean
	 */
	public function isTypeApplication() {
		return $this->getType() === IMAP_MESSAGE_TYPE_APPLICATION;
	}



	/**
	 * Check whether part is of type audio
	 *
	 * @return	Boolean
	 */
	public function isTypeAudio() {
		return $this->getType() === IMAP_MESSAGE_TYPE_AUDIO;
	}



	/**
	 * Check whether part is of type image
	 *
	 * @return	Boolean
	 */
	public function isTypeImage() {
		return $this->getType() === IMAP_MESSAGE_TYPE_IMAGE;
	}



	/**
	 * Check whether part is of type video
	 *
	 * @return	Boolean
	 */
	public function isTypeVideo() {
		return $this->getType() === IMAP_MESSAGE_TYPE_VIDEO;
	}



	/**
	 * Check whether part is of type other
	 *
	 * @return	Boolean
	 */
	public function isTypeOther() {
		return $this->getType() === IMAP_MESSAGE_TYPE_OTHER;
	}



	/**
	 * Get part subtype
	 *
	 * @return	String
	 */
	public function getSubType() {
		return strtoupper($this->part->subtype);
	}



	/**
	 * Check whether part has subtype plain
	 *
	 * @return	Boolean
	 */
	public function isSubTypePlain() {
		return $this->getSubType() === 'PLAIN';
	}



	/**
	 * Check whether part has subtype html
	 *
	 * @return	Boolean
	 */
	public function isSubTypeHtml() {
		return $this->getSubType() === 'HTML';
	}



	/**
	 * Check whether part has subtype png
	 *
	 * @return	Boolean
	 */
	public function isSubTypePng() {
		return $this->getSubType() === 'PNG';
	}



	/**
	 * Check whether part has subtype jpeg
	 *
	 * @return	Boolean
	 */
	public function isSubTypeJpeg() {
		return $this->getSubType() === 'JPEG';
	}



	/**
	 * Check whether part has subtype gif
	 *
	 * @return	Boolean
	 */
	public function isSubTypeGif() {
		return $this->getSubType() === 'GIF';
	}



	/**
	 * Check whether part has subtype alternative
	 *
	 * @return	Boolean
	 */
	public function isSubTypeAlternative() {
		return $this->getSubType() === 'ALTERNATIVE';
	}


	public function isSubTypeRFC822() {
		return $this->getSubType() === 'RFC822';
	}



	/**
	 * Check whether part has a subtype of any of the image types
	 *
	 * @return	Boolean
	 */
	public function isSubTypeAnInlineImage() {
		return $this->isSubTypeJpeg() || $this->isSubTypePng() || $this->isSubTypeGif();
	}



	/**
	 * Get part ID
	 *
	 * @return	String
	 */
	public function getID() {
		return TodoyuImapMessageManager::cleanID($this->part->id);
	}



	/**
	 * Get content part
	 *
	 * @param	Integer		$options
	 * @return	String
	 */
	public function getContent($options = 0) {
		$options |= FT_PEEK; // Don't set as seen

		$content = imap_fetchbody($this->message->getStream(), $this->message->getMessageNumber(), $this->section, $options);

		if( $this->isDecodingRequired() ) {
			$content = TodoyuImapServerMessageManager::decodePartContent($content, $this->getEncoding(), $this->getCharset());
		}

		return trim($content);
	}



	/**
	 * Check whether decoding is required
	 * Decoding is not required for binary and "other"
	 *
	 * @return	Boolean
	 */
	protected function isDecodingRequired() {
		return !in_array($this->getEncoding(), array(IMAP_ENCODING_BINARY, IMAP_ENCODING_OTHER));
	}



	/**
	 * Get parameter
	 *
	 * @param	String		$parameterName
	 * @return	String|Boolean
	 */
	public function getPartParameter($parameterName) {
		return TodoyuImapServerMessagePartManager::getPartParameter($this->part, $parameterName);
	}

}

?>