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
 * Manage zip archives
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuArchiveManager {

	/**
	 * Extract an archive to a folder
	 *
	 * @param	String			$pathArchive
	 * @param	String			$targetFolder
	 * @throws	TodoyuException
	 * @return	Boolean
	 */
	public static function extractTo($pathArchive, $targetFolder) {
		if( function_exists('set_time_limit') ) {
			@set_time_limit(120);
		}

		$pathArchive	= TodoyuFileManager::pathAbsolute($pathArchive);
		$targetFolder	= TodoyuFileManager::pathAbsolute($targetFolder);

		if( ! is_file($pathArchive) ) {
			throw new TodoyuException('Archive not found for extraction: ' . $pathArchive);
		}

		TodoyuFileManager::makeDirDeep($targetFolder);

		try {
			if( TodoyuServer::isPhp53() ) {
				return self::extractToPhp53($pathArchive, $targetFolder);
			} else {
				return self::extractToPhp52($pathArchive, $targetFolder);
			}
		} catch(TodoyuException $e) {
			TodoyuLogger::logFatal('Cannot extract archive: ' . $e->getMessage());

			return false;
		}
	}



	/**
	 * Extract archive on server with PHP 5.2
	 *
	 * @throws	TodoyuException
	 * @param	String		$pathArchive
	 * @param	String		$targetFolder
	 * @return	Boolean
	 */
	private static function extractToPhp52($pathArchive, $targetFolder) {
		self::loadPclZip();

			// Extract files
		$archive	= new PclZip($pathArchive);
		$result		= $archive->extract(PCLZIP_OPT_PATH, $targetFolder);

		if( $result == 0 ) {
			throw new TodoyuException($archive->errorInfo(true));
		}

		return true;
	}



	/**
	 * Load pcl Zip library
	 *
	 */
	private static function loadPclZip() {
		require_once( PATH_LIB . '/php/pclzip/pclzip.lib.php' );
	}



	/**
	 * Extract archive on server with PHP 5.3
	 *
	 * @throws	TodoyuException
	 * @param	String		$pathArchive
	 * @param	String		$targetFolder
	 * @return	Boolean
	 */
	private static function extractToPhp53($pathArchive, $targetFolder) {
		$archive	= new ZipArchive();
		$archive->open($pathArchive);

		$result	= $archive->extractTo($targetFolder);

		$archive->close();

		if( !$result ) {
			throw new TodoyuException('Unknown error');
		}

		return true;
	}



	/**
	 * Create an archive from a folder
	 *
	 * @param	String			$pathFolder
	 * @param	Array			$exclude
	 * @return	String
	 */
	public static function createArchiveFromFolder($pathFolder, array $exclude = array()) {
		$pathFolder		= TodoyuFileManager::pathAbsolute($pathFolder);
		$pathArchive	= TodoyuFileManager::getTempFile('zip', false);

			// Prevent empty archive (which will not be created)
		$elements	= TodoyuFileManager::getFolderContents($pathFolder);

		if( sizeof($elements) === 0 ) {
			self::createEmptyArchive($pathArchive);
		} else {
				// Prepare exclude paths
			array_walk($exclude, 'TodoyuFilemanager::pathAbsolute');

				// Create archive
			$archive = new ZipArchive();
			$archive->open($pathArchive, ZipArchive::CREATE);

			self::addFolderToArchive($archive, $pathFolder, $pathFolder, true, $exclude);

			$archive->close();
		}

		return $pathArchive;
	}



	/**
	 * Create an empty archive
	 *
	 * @param	String		$pathArchive
	 */
	private static function createEmptyArchive($pathArchive) {
		self::loadPclZip();

		$archive	= new PclZip($pathArchive);
		$archive->create('');
	}



	/**
	 * Add a folder (and sub elements) to an archive
	 *
	 * @param	ZipArchive		&$archive
	 * @param	String			$pathToFolder		Path to folder which elements should be added
	 * @param	String			$baseFolder			Base folder defined to root for the archive. Base path will be removed from internal archive path
	 * @param	Boolean			$recursive			Add also all sub folders and files
	 * @param	Array			$exclude
	 */
	private static function addFolderToArchive(ZipArchive &$archive, $pathToFolder, $baseFolder, $recursive = true, array $exclude = array()) {
		$files		= TodoyuFileManager::getFilesInFolder($pathToFolder);

			// Add files
		foreach($files as $file) {
			$filePath	= $pathToFolder . DIR_SEP . $file;

			if( ! in_array($filePath, $exclude) ) {
				$relPath	= str_replace($baseFolder . DIR_SEP, '', $filePath);
				$relPath	= self::sanitizePath($relPath);
				$archive->addFile($filePath, $relPath);
			}
		}

			// Add folders if recursive is enabled
		if( $recursive ) {
			$folders	= TodoyuFileManager::getFoldersInFolder($pathToFolder);
				// Add folders
			foreach($folders as $folder) {
				$folderPath	= $pathToFolder . DIR_SEP . $folder;

				if( ! in_array($folderPath, $exclude) ) {
					$relPath	= str_replace($baseFolder . DIR_SEP, '', $folderPath);
					$relPath	= self::sanitizePath($relPath);

					$archive->addEmptyDir($relPath);

					self::addFolderToArchive($archive, $folderPath, $baseFolder, true, $exclude);
				}
			}
		}
	}



	/**
	 * Replace backslash path separators (from windows) with normal slashes
	 * When you add a file or folder with a backslash in its path, archive will contain random folders
	 *
	 * @param	String		$path
	 * @return	String
	 */
	private static function sanitizePath($path) {
		return str_replace('\\', '/', $path);
	}

}

?>