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
 * Manage temporary uploaded files during task creation
 * Add files to cache folder an keep track of current files
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
abstract class TodoyuAssetsTempUploader {

	/**
	 * Element ID
	 *
	 * @var	Integer
	 */
	private $idElement = 0;

	/**
	 * Type
	 *
	 * @var	String
	 */
	private $type = '';

	/**
	 * Session key where storage dir and files infos are stored
	 *
	 * @var	String
	 */
	private $sessionKey;



	/**
	 * Initialize
	 *
	 * @param	String			$type
	 * @param	String|Integer	$idElement
	 */
	public function __construct($type, $idElement) {
		$this->type			= $type;
		$this->idElement	= $idElement;
		$this->sessionKey	= 'assets/files/' . $this->getType() . '/' . $this->idElement;
	}



	/**
	 * @return	String
	 */
	protected function getSessionKey() {
		return $this->sessionKey;
	}



	/**
	 * Get type
	 *
	 * @return	String
	 */
	protected function getType() {
		return $this->type;
	}



	/**
	 * Get element ID
	 *
	 * @return	Integer
	 */
	protected function getElementID() {
		return $this->idElement;
	}



	/**
	 * Get path to store files for current session
	 *
	 * @return	String		Session storage path file temp files
	 */
	protected function getStorageDir() {
		if( !TodoyuSession::isIn($this->sessionKey) ) {
			$randomHash	= md5(NOW . Todoyu::personid() . uniqid() . $this->getType() . $this->getElementID());
			$storageDir	= TodoyuFileManager::pathAbsolute(PATH_CACHE . '/files/assets/' . $randomHash);

			TodoyuSession::set($this->sessionKey . '/storageDir', $storageDir);
		}

		return TodoyuSession::get($this->sessionKey . '/storageDir');
	}



	/**
	 * Add a temporary file
	 *
	 * @param	Array	$fileData		File upload info array from php
	 */
	public  function addFile(array $fileData) {
		$pathStoredFile	= $this->storeFile($fileData['tmp_name']);

		$fileInfo = array(
			'name'	=> $fileData['name'],
			'type'	=> $fileData['type'],
			'size'	=> $fileData['size'],
			'path'	=> $pathStoredFile,
			'time'	=> NOW,
			'key'	=> md5($pathStoredFile)
		);

		$this->saveFileInfo($fileInfo);

		return $fileInfo['key'];
	}



	/**
	 * Store a file to the temporary session folder
	 *
	 * @param	String			$sourceFile		Path to temporary uploaded file
	 * @return	String|Boolean	Path to file in session folder or false
	 */
	private function storeFile($sourceFile) {
		$sourceFile	= TodoyuFileManager::pathAbsolute($sourceFile);
		$storageDir	= $this->getStorageDir();
		$randomName	= md5(NOW . $sourceFile . uniqid());
		$targetFile	= TodoyuFileManager::pathAbsolute($storageDir . '/' . $randomName);

		TodoyuFileManager::makeDirDeep(dirname($targetFile));

		$success		= rename($sourceFile, $targetFile);

		return $success ? $targetFile : false;
	}



	/**
	 * Add file information to session
	 *
	 * @param	Array	$fileInfo
	 */
	private function saveFileInfo(array $fileInfo) {
		TodoyuSession::set($this->sessionKey . '/files/' . $fileInfo['key'], $fileInfo);
	}



	/**
	 * Get infos about uploaded files
	 *
	 * @return	Array
	 */
	public function getFilesInfos() {
		return TodoyuArray::assure(TodoyuSession::get($this->sessionKey . '/files'));
	}



	/**
	 * Delete a temporary uploaded file
	 *
	 * @param	String		$key		Key of the file (created at upload)
	 */
	public function removeFile($key) {
		$filesInfos	= $this->getFilesInfos();
		unset($filesInfos[$key]);

		TodoyuSession::set($this->sessionKey . '/files', $filesInfos);
	}



	/**
	 * Destroy the upload session
	 * - Remove uploaded files
	 * - Reset path and file infos
	 *
	 */
	public function clear() {
			// Delete files
		$storageDir	= $this->getStorageDir();
		TodoyuFileManager::deleteFolder($storageDir);

			// Remove session infos
		TodoyuSession::remove($this->sessionKey);
	}

}

?>