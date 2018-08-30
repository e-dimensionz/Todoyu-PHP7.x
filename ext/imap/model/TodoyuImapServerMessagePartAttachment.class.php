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
 * Server message part attachment
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapServerMessagePartAttachment extends TodoyuImapServerMessagePart {

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
	 * Get attachment name
	 *
	 * @return	String
	 */
	public function getAttachmentName() {
		if( isset( $this->part->dparameters ) ) {
			$name = $this->part->dparameters[0]->value;
		} else {
			$name = 'unknown_filename';
		}

		$decodedObjects	= imap_mime_header_decode($name);
		$decoded 		= $decodedObjects[0];

		return trim($decoded ? $decoded->text : $name);
	}

}

?>