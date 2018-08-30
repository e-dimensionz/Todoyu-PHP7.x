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
 * Extension archiver
 * Pack a whole extension into a ZIP archive file
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerArchiver {

	/**
	 * Create a extension archive (ZIP file) in cache and return the path to it
	 *
	 * @param	String		$extKey
	 * @return	String		Path to archive in cache
	 */
	public static function createExtensionArchive($extKey) {
		$extPath	= TodoyuExtensions::getExtPath($extKey);

		return TodoyuArchiveManager::createArchiveFromFolder($extPath);
	}



	/**
	 * Create archive which contains the core
	 *
	 * @return	String
	 */
	public static function createCoreArchive() {
		$exclude	= array(
			'backup',
			'cache',
			'ext',
			'files'
		);

		return TodoyuArchiveManager::createArchiveFromFolder(PATH, $exclude);
	}

}

?>