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
class TodoyuImapServerMessagePartRFC822 extends TodoyuImapServerMessagePartMultipart {

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
	 * Get content of forwarded message
	 * This is the whole eml content
	 *
	 * @param	Integer		$options
	 * @return	String
	 */
	public function getContent($options = 0) {
		$options |= FT_PEEK; // Don't set as seen

		return imap_fetchbody($this->message->getStream(), $this->message->getMessageNumber(), $this->section, $options);
	}



	/**
	 * Get attachment name
	 *
	 * @return	String
	 */
	public function getAttachmentName() {
		return 'message_' . $this->section . '.eml';
	}

}

?>