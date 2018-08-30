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
 * Create extension and core backups
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerBackupManager {

	/**
	 * Create a backup file of an extension for the current version
	 *
	 * @param	String		$extKey
	 * @return	String		Path to backup file
	 */
	public static function createExtensionBackup($extKey) {
		$archivePath	= TodoyuSysmanagerArchiver::createExtensionArchive($extKey);
		$fileName		= TodoyuSysmanagerExtInstaller::getExtensionArchiveName($extKey);

		return self::addFileToBackupArchive($archivePath, $fileName);
	}



	/**
	 * Create a backup file of the core for the current version
	 *
	 * @return	String		Path to backup file
	 */
	public static function createCoreBackup() {
		$archivePath	= TodoyuSysmanagerArchiver::createCoreArchive();
		$fileName		= 'Todoyu_' . TODOYU_VERSION . '.zip';

		return self::addFileToBackupArchive($archivePath, $fileName);
	}



	/**
	 * Move temporary backup file to backup folder
	 *
	 * @param	String		$tempFile
	 * @param	String		$fileName
	 * @return	String
	 */
	private static function addFileToBackupArchive($tempFile, $fileName) {
		TodoyuFileManager::makeDirDeep('backup');
		$pathBackup	= TodoyuFileManager::pathAbsolute('backup/' . $fileName);

		rename($tempFile, $pathBackup);

		return $pathBackup;
	}

}

?>