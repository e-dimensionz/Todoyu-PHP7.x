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
 * Manage inline images inside of html mails
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapInlineImageManager {

	/**
	 * Save the image as file
	 *
	 * @param	String		$imageKey
	 * @param	String		$content
	 * @return	String		Storage path
	 */
	public static function saveImage($imageKey, $content) {
		$path	= self::getImagePath($imageKey);

		TodoyuFileManager::saveFileContent($path, $content);

		return $path;
	}



	/**
	 * Get storage base path
	 *
	 * @return	String
	 */
	public static function getStorageBasePath() {
		return TodoyuFileManager::pathAbsolute(IMAP_PATH_INLINEIMAGE);
	}



	/**
	 * Get file path for image
	 *
	 * @param	String		$imageKey
	 * @return	String
	 */
	protected static function getImagePath($imageKey) {
		$basePath	= self::getStorageBasePath();
		$path		= $basePath . '/' . sha1($imageKey);
		$ext		= self::getExtensionFromKey($imageKey);

		return TodoyuFileManager::pathAbsolute($path . '.' . $ext);
	}



	/**
	 * Extract extension from image key
	 * The image key contains the filename
	 *
	 * @param	String		$imageKey
	 * @return	String
	 */
	protected static function getExtensionFromKey($imageKey) {
		list($filename) = explode('@', $imageKey, 2);
		$filename		= str_replace('<', '', $filename);

		return pathinfo($filename, PATHINFO_EXTENSION);
	}



	/**
	 * Get the content data of the image
	 *
	 * @param	String		$imageKey
	 * @return	String
	 */
	public static function getImageContent($imageKey) {
		return TodoyuFileManager::getFileContent(self::getImagePath($imageKey));
	}



	/**
	 * Get the mime type of the image
	 *
	 * @param	String		$imageKey
	 * @return	String
	 */
	public static function getMimeType($imageKey) {
		$path	= self::getImagePath($imageKey);

		return TodoyuFileManager::getMimeType($path, $path);
	}



	/**
	 * Send the image with headers to the browser
	 *
	 * @param	String		$imageKey
	 */
	public static function sendToBrowser($imageKey) {
		$path	= self::getImagePath($imageKey);
		$mime	= self::getMimeType($imageKey);

		TodoyuFileManager::sendFile($path, $mime, null, false);
	}

}

?>