<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * Asset object
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsAsset extends TodoyuBaseObject {

	/**
	 * Initialize the asset object
	 *
	 * @param	Integer		$idAsset
	 */
	public function __construct($idAsset) {
		parent::__construct($idAsset, 'ext_assets_asset');
	}



	/**
	 * Get parent type of the asset (ex: task)
	 *
	 * @return	Integer
	 */
	public function getParentType() {
		return $this->get('parenttype');
	}



	/**
	 * Get ID of parent element
	 *
	 * @return	Integer
	 */
	public function getParentID() {
		return $this->getInt('id_parent');
	}



	/**
	 * Get absolute path to file in storage directory on the server
	 *
	 * @return	String
	 */
	public function getFileStoragePath() {
		$basePath	= TodoyuAssetsAssetManager::getStorageBasePath();
		$filePath	= $this->get('file_storage');

		return TodoyuFileManager::pathAbsolute($basePath . DIR_SEP . $filePath);
	}



	/**
	 * Get file size
	 *
	 * @return	Integer
	 */
	public function getFilesize() {
		return $this->getInt('file_size');
	}



	/**
	 * Get formatted file size
	 *
	 * @param	Array|null		$alternativeLabels
	 * @param	Boolean			$noLabel
	 * @return	String
	 */
	public function getFilesizeFormatted(array $alternativeLabels = null, $noLabel = false) {
		return TodoyuString::formatSize($this->getFilesize(), $alternativeLabels, $noLabel);
	}



	/**
	 * Get mime type
	 *
	 * @return	String
	 */
	public function getMimeType() {
		return $this->get('file_mime') . '/' . $this->get('file_ext');
	}



	/**
	 * Get filename
	 *
	 * @return	String
	 */
	public function getFilename() {
		return $this->get('file_name');
	}



	/**
	 * Check whether asset is public
	 *
	 * @return	Boolean
	 */
	public function isPublic() {
		return $this->isFlagSet('is_public');
	}



	/**
	 * Check whether file exists in storage
	 *
	 * @return	Boolean
	 */
	public function isFileAvailable() {
		$pathFileStorage	= $this->getFileStoragePath();

		return TodoyuFileManager::isFile($pathFileStorage);
	}



	/**
	 * Send asset as download
	 *
	 * @return	Boolean		Success
	 */
	public function sendAsDownload() {
		$filePath	= $this->getFileStoragePath();
		$mimeType	= $this->getMimeType();
		$filename	= $this->getFilename();

		TodoyuHookManager::callHook('assets', 'asset.download', array($this->getID()));

		try {
			$status = TodoyuFileManager::sendFile($filePath, $mimeType, $filename);
		} catch(TodoyuExceptionFileDownload $e) {
			// @todo catch error
			$status = false;
		}

		return $status;
	}



	/**
	 * Check whether the asset can be downloaded
	 * Only checks for problems with the file. No access checking
	 *
	 * @return	Boolean|String		True or the error message
	 */
	public function canDownload() {
		return TodoyuFileManager::canSendFile($this->getFileStoragePath());
	}



	/**
	 * Get label for asset
	 *
	 * @return String
	 */
	public function getLabel() {
		$data = array(
			$this->getFilename(),
			$this->getFilesizeFormatted(),
			$this->getPersonCreate()->getFullName(),
			TodoyuTime::format($this->getDateCreate(), 'datetime')
		);
		$format	= '%1$s - %2$s, %3$s, %4$s';

		return vsprintf($format, $data);
	}

}

?>