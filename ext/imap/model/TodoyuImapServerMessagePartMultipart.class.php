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
 * Server message part RFC822 (eml)
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapServerMessagePartMultipart extends TodoyuImapServerMessagePart {

//
//
//	/**
//	 * @param int $options
//	 * @return	Array
//	 */
//	protected function parsePartsContents($options = 0) {
//		$options |= FT_PEEK; // Don't set as seen
//
//		foreach($this->part->parts as $index => $subPart) {
//			$section	= $this->section . '.' . ($index+1);
//			$subContent	= imap_fetchbody($this->message->getStream(), $this->message->getMessageNumber(), $section, $options);
//
//			$decodedSubContent	= TodoyuImapServerMessageManager::decodePartContent($subContent, $subPart->encoding);
//
//				// Decode sub part again to original encoding
//			$subPartEncoding 	= TodoyuImapServerMessagePartManager::getPartCharset($subPart);
//			if( $subPartEncoding ) {
//				$decodedSubContent = iconv('utf-8', $subPartEncoding, $decodedSubContent);
//			}
//
//			$this->contentParts[$subPart->subtype] = $decodedSubContent;
//		}
//	}



	/**
	 * Save part content as attachment file
	 *
	 * @param	Integer		$idMessage
	 * @return	Integer		Attachment ID
	 */
	public function saveAsAttachment($idMessage) {
		return TodoyuImapAttachmentManager::addAttachment($idMessage, $this->getAttachmentName(), $this->getContent());
	}



	/**
	 * Get message part content
	 *
	 * @param	Integer		$options
	 * @return	String
	 */
	public function getContent($options = 0) {
		$options |= FT_PEEK; // Don't set as seen

		$contentParts	= array();

		foreach($this->part->parts as $index => $subPart) {
			$section	= $this->section . '.' . ($index+1);
			$subContent	= imap_fetchbody($this->message->getStream(), $this->message->getMessageNumber(), $section, $options);

			$contentParts[] = $subContent;
		}

		return implode('', $contentParts);
	}



	/**
	 * Get attachment name
	 *
	 * @return	String
	 */
	public function getAttachmentName() {
		return 'message.eml';
	}

}

?>