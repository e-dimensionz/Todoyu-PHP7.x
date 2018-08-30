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
 * Server message part inline image
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapServerMessagePartInlineImage extends TodoyuImapServerMessagePart {

	/**
	 * Save inline image file in files folder
	 *
	 * @return	String		Storage path
	 */
	public function saveInFiles() {
		return TodoyuImapInlineImageManager::saveImage($this->getID(), $this->getContent());
	}



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
		$imageName	= $this->getPartParameter('name');


		if( $imageName ) {
			$ext = pathinfo($imageName, PATHINFO_EXTENSION);

			if( !$ext ) {
				$imageName .= '.' . $this->getSubType();
			}
		} else {
			$imageName = $this->getID() . '.' . $this->getSubType();
		}

		return TodoyuFileManager::makeCleanFilename(strtolower($imageName));
	}

}

?>