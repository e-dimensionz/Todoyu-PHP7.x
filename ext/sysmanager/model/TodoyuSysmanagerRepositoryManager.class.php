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
 * Manage updates
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRepositoryManager {

	/**
	 * Notify about a general repository error
	 */
	public static function notifyRepositoryError() {
		TodoyuNotification::notifyError('sysmanager.repository.error.general');
	}



	/**
	 * Get unique todoyu ID
	 *
	 * @return	String
	 */
	public static function getTodoyuID() {
		return trim(Todoyu::$CONFIG['SETTINGS']['repository']['todoyuid']);
	}



	/**
	 * Get last used search query
	 *
	 * @return	String
	 */
	public static function getLastSearchKeyword() {
		return TodoyuSysmanagerPreferences::getPref('repositoryQuery');
	}



	/**
	 * Save last used search query
	 *
	 * @param  $query
	 * @return void
	 */
	public static function saveLastSearchKeyword($query) {
		TodoyuSysmanagerPreferences::savePref('repositoryQuery', trim($query), 0, true);
	}



	/**
	 * Install extension update
	 *
	 * @param	String			$extKey
	 * @return	Boolean|String
	 */
	public static function installExtensionUpdate($extKey) {
		try {
			$update		= self::getRepoInfo($extKey);

				// Create a backup from the extension
			TodoyuSysmanagerBackupManager::createExtensionBackup($extKey);

				// Get extension information before update
			$currentVersion= TodoyuExtensions::getExtVersion($extKey);

				// Callback: Before update
			TodoyuSysmanagerExtInstaller::callBeforeUpdate($extKey);

				// Download and import extension
			$idVersion	= intval($update['version']['id']);
			self::downloadAndImportExtension($extKey, $idVersion, true);

			$previousVersion= $currentVersion;
			$currentVersion	= TodoyuExtensions::getExtVersion($extKey);

			TodoyuSysmanagerExtInstaller::callBeforeDbUpdate($extKey, $previousVersion, $currentVersion);

				// Update database from files
			TodoyuSysmanagerExtInstaller::updateDatabaseFromFiles();

				// Callback: After update
			TodoyuSysmanagerExtInstaller::callAfterUpdate($extKey, $previousVersion, $currentVersion);
		} catch(TodoyuSysmanagerRepositoryException $e) {
			return $e->getMessage();
		} catch(TodoyuException $e) {
			return $e->getMessage();
		}

		return true;
	}



	/**
	 * Install a new extension from tER
	 *
	 * @param	String		$extKey
	 * @param	Integer		$majorVersion
	 * @return	Boolean|String
	 */
	public static function installExtensionFromTER($extKey, $majorVersion) {
		try {
				// Get url from hash map
			$extInfo	= self::getRepoInfo($extKey);

				// Buy extension if it's commercial
			if( $extInfo['commercial'] ) {

				self::registerCommercialExtension($extInfo['ext_key'], $majorVersion);
			}

				// Download and install extension
			$idVersion	= intval($extInfo['version']['id']);
			self::downloadAndImportExtension($extKey, $idVersion, false);

			$isMajorUpdate	= TodoyuExtensions::isInstalled($extKey);

			if( $isMajorUpdate ) {
				$previousVersion	= TodoyuExtensions::getExtVersion($extKey);
				TodoyuSysmanagerExtInstaller::callBeforeMajorUpdate($extKey, $extInfo['version']['version']);
			}

			TodoyuSysmanagerExtInstaller::installExtension($extKey);

			if( $isMajorUpdate ) {
				TodoyuSysmanagerExtInstaller::callAfterMajorUpdate($extKey, $previousVersion);
			}

		} catch(TodoyuException $e) {
			return $e->getMessage();
		}

		return true;
	}



	/**
	 * Install core update. Extract update files over local files
	 *
	 * @return	Boolean
	 */
	public static function installCoreUpdate() {
		$update		= self::getRepoInfo('core');
		$idVersion	= intval($update['id']);

			// Make sure we have enough time to download and extract the update
		if( function_exists('set_time_limit') ) {
			set_time_limit(120);
		}

		try {
				// Backup Core
			TodoyuSysmanagerBackupManager::createCoreBackup();
				// Download and import core update
			self::downloadAndImportCoreUpdate($idVersion);
			TodoyuInstallerManager::runCoreVersionUpdates();
		} catch(TodoyuException $e) {
			return $e->getMessage();
		}

		return true;
	}



	/**
	 * Download and import (install) a core update
	 *
	 * @throws	TodoyuException
	 * @param	String	$idVersion
	 * @return	Boolean
	 */
	private static function downloadAndImportCoreUpdate($idVersion) {
		$pathArchive= self::downloadArchive('core', $idVersion);

		self::importCoreUpdate($pathArchive);

		return true;
	}




	/**
	 * Import the core update from an archive
	 *
	 * @throws	TodoyuException
	 * @param	String				$pathArchive
	 */
	private static function importCoreUpdate($pathArchive) {
		$pathTemp	= TodoyuFileManager::pathAbsolute('cache/temp/' . md5(NOW));

			// Extract archive
		$success	= TodoyuArchiveManager::extractTo($pathArchive, $pathTemp);

		if( !$success ) {
			throw new TodoyuException('Extraction of core update archive failed');
		}

			// Prepare paths
		$pathTodoyuRoot			= PATH;
//		$pathTodoyuRoot			= PATH . '/dummyupdate';

			// Remove elements which should not be overwritten from temp update folder
		self::removeLocalElementsFromCoreUpdate($pathTemp);

		TodoyuFileManager::moveRecursive($pathTemp, $pathTodoyuRoot, true);
		TodoyuFileManager::deleteFolder($pathTemp);

		TodoyuCacheManager::clearAllCache();
	}




	/**
	 * Remove folders and files from core update which should not be updated
	 *
	 * @param	String		$pathTempCoreUpdate			Path to temporary core update folder
	 */
	private static function removeLocalElementsFromCoreUpdate($pathTempCoreUpdate) {
			// Remove folders/files which should not be overwritten
		$ignore	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['sysmanager']['update']['ignoreElements']);

		foreach($ignore as $element) {
			$pathElement	= TodoyuFileManager::pathAbsolute($pathTempCoreUpdate . '/' . $element);

			if( is_dir($pathElement) ) {
				TodoyuFileManager::deleteFolder($pathElement);
			} elseif( is_file($pathElement) ) {
				TodoyuFileManager::deleteFile($pathElement);
			}
		}
	}



	/**
	 * Download external archive file and extract it into the cache folder
	 *
	 * @throws	TodoyuSysmanagerRepositoryException
	 * @param	String		$extKey
	 * @param	Integer		$idVersion
	 * @param	Boolean		$isUpdate
	 * @return	Boolean		Success
	 */
	private static function downloadAndImportExtension($extKey, $idVersion, $isUpdate = false) {
		$override	= $isUpdate;
		$pathArchive= self::downloadArchive('ext', $idVersion);
		$canImport	= TodoyuSysmanagerExtImporter::canImportExtension($extKey, $pathArchive, $override);

		if( $canImport !== true ) {
			throw new TodoyuSysmanagerRepositoryException($canImport);
		}

			// Import to todoyu extension folder
		TodoyuSysmanagerExtImporter::importExtensionArchive($extKey, $pathArchive);

			// Delete temporary file
		TodoyuFileManager::deleteFile($pathArchive);

		return true;
	}



	/**
	 * Register a commercial extension on tER server
	 *
	 * @throws	TodoyuSysmanagerRepositoryException
	 * @param	String		$extKey
	 * @param	Integer		$majorVersion
	 * @return	Boolean
	 */
	public static function registerCommercialExtension($extKey, $majorVersion) {
		$repository	= new TodoyuSysmanagerRepository();
		$result		= $repository->registerForDomain($extKey, $majorVersion);

		if( $result !== true ) {
			throw new TodoyuSysmanagerRepositoryException($result);
		}

		return true;
	}



	/**
	 * Download an archive from an URL to local hard drive
	 *
	 * @param	String		$type
	 * @param	String		$idVersion
	 * @return	String		Path to local archive
	 * @throws	TodoyuException
	 */
	private static function downloadArchive($type, $idVersion) {
		$repository	= new TodoyuSysmanagerRepository();

		try {
			return $repository->download($type, $idVersion);
		} catch(TodoyuSysmanagerRepositoryConnectionException $e) {
			throw new TodoyuException('Download of update archive failed: ' . $idVersion);
		}
	}



	/**
	 * Save path to archive of extension or core
	 *
	 * @param	String		$key
	 * @param	Array		$data
	 */
	public static function saveRepoInfo($key, array $data) {
		TodoyuSession::set('repository/info/' . $key, $data);
	}



	/**
	 * Get path to archive of extension or core
	 *
	 * @param	String		$key
	 * @return	Array
	 */
	public static function getRepoInfo($key) {
		return TodoyuArray::assure(TodoyuSession::get('repository/info/' . $key));
	}



	/**
	 * Clear all data from repository info session
	 */
	public static function clearRepoInfo() {
		TodoyuSession::remove('repository/info');
	}




	/**
	 * Get license text for license type
	 *
	 * @param	String		$license
	 * @return	String|Boolean
	 */
	public static function getExtensionLicenseText($license) {
		$license	= strtolower(trim($license));
		$path		= TodoyuExtensions::getExtPath('sysmanager', 'asset/license/' . $license . '.html');

		if( is_file($path) ) {
			return file_get_contents($path);
		} else {
			return false;
		}
	}



	/**
	 * Get extension installation infos from repository
	 *
	 * @param	String		$extKey
	 * @param	Integer		$major
	 * @return	Array
	 */
	public static function getExtInfoFromRepository($extKey, $major) {
		$repository	= new TodoyuSysmanagerRepository();

		try {
			$info	= $repository->getExtInfo($extKey, $major);

			self::saveRepoInfo($extKey, $info);

			return $info;
		} catch(TodoyuSysmanagerRepositoryConnectionException $e) {
			return array();
		} catch(TodoyuSysmanagerRepositoryException $e) {
			return array();
		}
	}



	/**
	 * Check whether extension is commercial in TER
	 *
	 * @param	String		$extKey
	 * @param	Integer		$major
	 * @return	Boolean
	 */
	public static function isCommercial($extKey, $major) {
		$info	= self::getExtInfoFromRepository($extKey, $major);
		
		return $info['commercial'] ? true : false;
	}



	/**
	 * Check whether a registration is required
	 * It's required if not free and not already licensed
	 *
	 * @param	String		$extKey
	 * @param	Integer		$major
	 * @return	Boolean
	 */
	public static function isRegistrationRequired($extKey, $major) {
		$info	= self::getExtInfoFromRepository($extKey, $major);
		$status	= trim($info['installStatus']);
		$required	= array(
			'noLicense',
			'freeLicense'
		);

		return in_array($status, $required);
	}



	/**
	 * License an extension
	 *
	 * @param	String		$extKey
	 * @param	Integer		$majorVersion
	 * @return	Boolean
	 */
	public static function licenseExtension($extKey, $majorVersion) {
		try {
			TodoyuSysmanagerRepositoryManager::registerCommercialExtension($extKey, $majorVersion);
			return true;
		} catch(TodoyuException $e) {
			return false;
		}
	}

}

?>