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
 * Manager for IMAP attachment records
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapAttachmentManager {

	/**
	 * @var String		Default table for database requests
	 */
	const TABLE = 'ext_imap_attachment';

	/**
	 * @var String		Record class name
	 */
	const CLASSNAME	= 'TodoyuImapAttachment';



	/**
	 * Get IMAP attachment record
	 *
	 * @param	Integer					$idAttachment
	 * @return	TodoyuImapAttachment
	 */
	public static function getAttachment($idAttachment) {
		$idAttachment	= intval($idAttachment);

		return TodoyuRecordManager::getRecord(self::CLASSNAME, $idAttachment);
	}



	/**
	 * Get all selected DB rows of attachments as an array
	 *
	 * @param	String	$fields
	 * @param	String	$where
	 * @param	String	$order
	 * @return	Array[]
	 */
	protected static function getAttachmentsArray($fields = '*', $where = 'deleted = 0', $order = 'file_name') {
		return Todoyu::db()->getArray($fields, self::TABLE, $where, '', $order);
	}



	/**
	 * Get attachment files' storage base path (absolute path)
	 *
	 * @return	String
	 */
	public static function getStorageBasePath() {
		return TodoyuFileManager::pathAbsolute(IMAP_PATH_ATTACHMENT);
	}



	/**
	 * Get full path to attachments folder of message with given message_id
	 *
	 * @param	String	$idMessage
	 * @return	String
	 */
	public static function getStoragePath($idMessage) {
		$idMessage	= intval($idMessage);

		$messagePath= self::getStorageBasePath() . '/' . $idMessage;

		return TodoyuFileManager::pathAbsolute($messagePath);
	}



	/**
	 * Store attachment file to the system and add attachment record to the database.
	 *
	 * @param	Integer		$idMessage
	 * @param	String		$fileContent
	 * @param	String		$filename
	 * @return	Integer		Attachment ID
	 */
	public static function addAttachment($idMessage, $filename, $fileContent) {
		$idMessage	= intval($idMessage);
		$storagePath= self::saveAttachment($idMessage, $filename, $fileContent);


			// Get storage path (relative to basePath)
		$basePath		= self::getStorageBasePath();
		$relStoragePath	= str_replace($basePath . DIR_SEP, '', $storagePath);

			// Get mime type
		$fileSize				= filesize($storagePath);
		$mimeType				= TodoyuFileManager::getMimeType($storagePath, $filename);
		list($mime, $subMime)	= explode('/', $mimeType, 2);

			// Add record to database
		$data		= array(
			'id_message'		=> $idMessage,
			'deleted'			=> 0,
			'file_ext'			=> pathinfo($filename, PATHINFO_EXTENSION),
			'file_mime'			=> $mime,
			'file_mime_sub'		=> $subMime,
			'file_storage'		=> $relStoragePath,
			'file_name'			=> $filename,
			'file_size'			=> $fileSize
		);

		$idAttachment = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('imap', 'attachment.add', array($idAttachment));

		return $idAttachment;
	}



	/**
	 * Save attachment into storage folder for the message
	 *
	 * @param	Integer		$idMessage
	 * @param	String		$filename
	 * @param	String		$content
	 * @return	String
	 */
	protected static function saveAttachment($idMessage, $filename, $content) {
		$idMessage	= intval($idMessage);

			// Store attachment locally
		$pathStorage= self::getStoragePath($idMessage);
		$filename	= TodoyuFileManager::makeCleanFilename($filename);
		$filePath	= TodoyuFileManager::pathAbsolute($pathStorage . '/' . $filename);

		TodoyuFileManager::saveFileContent($filePath, $content);

		return $filePath;
	}



	/**
	 * Get data of attachments of given message
	 *
	 * @param	Integer		$idMessage		ID of parent element
	 * @return	Array
	 */
	public static function getMessageAttachmentsData($idMessage) {
		$idMessage	= intval($idMessage);

		$field	= '*';
		$where	= 'id_message	= ' . $idMessage . ' AND deleted	= 0';

		return self::getAttachmentsArray($field, $where);
	}



	/**
	 * Get attachment objects of given message
	 *
	 * @param	Integer		$idMessage		ID of parent element
	 * @return	TodoyuImapAttachment[]
	 */
	public static function getMessageAttachmentsList($idMessage) {
		$idMessage	= intval($idMessage);

		$field	= 'id';
		$where	= 'id_message	= ' . $idMessage . ' AND deleted	= 0';
		$attachmentIDs	= self::getAttachmentsArray($field, $where);

		return TodoyuRecordManager::getRecordList(self::CLASSNAME, $attachmentIDs);
	}



	/**
	 * Get ID of message the given attachment belongs to
	 *
	 * @param	Integer		$idAttachment
	 * @return	Integer
	 */
	public static function getMessageID($idAttachment) {
		$idAttachment	= intval($idAttachment);

		$attachment		= self::getAttachment($idAttachment);

		return intval($attachment['id_message']);
	}



	/**
	 * Download given attachment. Send headers and data to the browser
	 *
	 * @param	Integer		$idAttachment
	 * @return	Boolean
	 */
	public static function downloadAttachment($idAttachment) {
		$idAttachment	= intval($idAttachment);
		$attachment		= self::getAttachment($idAttachment);

		return $attachment->sendAsDownload();
	}



	/**
	 * Delete an attachment (file stays in file system)
	 *
	 * @param	Integer		$idAttachment
	 */
	public static function deleteAttachment($idAttachment) {
		$idAttachment	= intval($idAttachment);
		$update	= array(
			'deleted'	=> 1
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idAttachment, $update);

			// Delete file from hard disk?
		if( Todoyu::$CONFIG['EXT']['imap']['deleteFiles'] ) {
			$attachment	= self::getAttachment($idAttachment);
			$filePath	= $attachment->getFileStoragePath();

			TodoyuFileManager::deleteFile($filePath);
		}

		TodoyuHookManager::callHook('imap', 'attachment.delete', array($idAttachment));
	}

}

?>