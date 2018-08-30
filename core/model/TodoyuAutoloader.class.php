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
 * Handle auto loading of classes
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuAutoloader {

	/**
	 * Path of cache file
	 *
	 * @var	String
	 */
	private static $cacheFile = 'cache/autoload.php';

	/**
	 * Is cache file already loaded?
	 *
	 * @var	Boolean
	 */
	private static $loaded = false;



	/**
	 * Load a class
	 *
	 * @param	String		$className
	 */
	public static function load($className) {
		self::buildCache();

		$classNameLower	= strtolower($className);

		if( Todoyu::$CONFIG['AUTOLOAD']['CLASS'][$classNameLower] ) {
			include(Todoyu::$CONFIG['AUTOLOAD']['CLASS'][$classNameLower]);
		} else {
			// Let PHP throw a class not found error
			// This also happens when we check with class_exists, so we don't handle this condition here
		}
	}


	/**
	 * Reload the class paths. Clear the cache
	 *
	 */
	public static function reload() {
		self::clearCache();
		self::buildCache();
	}



	/**
	 * Add a custom static path for autoloading
	 *
	 * @param	String		$path
	 */
	public static function addPath($path) {
		Todoyu::$CONFIG['AUTOLOAD']['static'][] = $path;
	}



	/**
	 * Hook to clear the cache
	 *
	 */
	public static function hookClearCache() {
		self::clearCache();
	}



	/**
	 * Clear the cache
	 *
	 */
	private static function clearCache() {
		if( TodoyuFileManager::isFile(self::$cacheFile) ) {
			TodoyuFileManager::deleteFile(self::$cacheFile);
			self::$loaded = false;
		}
	}



	/**
	 * Build cache file and load cached elements
	 *
	 */
	private static function buildCache() {
		if( ! TodoyuFileManager::isFile(self::$cacheFile) ) {
			self::generateClassList();
		}

		if( ! self::$loaded ) {
			include(self::$cacheFile);
			self::$loaded = true;
		}
	}
	


	/**
	 * Generate a mapping from class names to class file paths
	 * and save it into the cache
	 *
	 */
	private static function generateClassList() {
		$classList	= self::getClassList();

		self::saveClassList($classList);

		self::$loaded = false;
	}



	/**
	 * Get class name to path mapping
	 *
	 * @return	Array
	 */
	private static function getClassList() {
//		require_once( PATH . '/core/model/TodoyuException.class.php' );
//		require_once( PATH . '/core/model/TodoyuExceptionClassNameConflict.class.php' );
		$classList		= array();

			// Static
		foreach(Todoyu::$CONFIG['AUTOLOAD']['static'] as $path) {
			$classList	= array_merge($classList, self::getClassListFromFolder($path));
		}

			// Ext
		$extKeys	= TodoyuExtensions::getInstalledExtKeys();
		foreach($extKeys as $extKey) {
			foreach(Todoyu::$CONFIG['AUTOLOAD']['ext'] as $extLoadPath) {
				$path			= TodoyuExtensions::getExtPath($extKey, $extLoadPath);
				$folderClassList= self::getClassListFromFolder($path);
				$doubleClasses	= array_intersect(array_keys($folderClassList), array_keys($classList));

					// Prevent class name conflicts
				if( sizeof($doubleClasses) > 0 ) {
//					throw new TodoyuExceptionClassNameConflict($doubleClasses);
				}

				$classList	= array_merge($classList, $folderClassList);
			}
		}

		return $classList;
	}



	/**
	 * Get a list of all classes in the folder (mapping to paths)
	 *
	 * @param	String		$pathFolder
	 * @return	Array
	 */
	private static function getClassListFromFolder($pathFolder) {
		$classList	= array();
		$classFiles	= TodoyuFileManager::getFilesInFolder($pathFolder, false, array('.class.php'));

		foreach($classFiles as $classFile) {
			$className	= strtolower(str_replace('.class.php', '', $classFile));
			$classPath	= TodoyuFileManager::pathWeb($pathFolder . '/' . $classFile);

			$classList[$className] = $classPath;
		}

		return $classList;
	}



	/**
	 * Save class list into the cache
	 *
	 * @param	Array	$classList
	 */
	private static function saveClassList(array $classList) {
		$pairStrings	= array();

		foreach($classList as $className => $classPath ) {
			$pairStrings[] = "'$className'=>'$classPath'";
		}

		$content	= '<?php Todoyu::$CONFIG[\'AUTOLOAD\'][\'CLASS\']=array(' . implode(',', $pairStrings) . ');?>';

		TodoyuFileManager::saveFileContent(self::$cacheFile, $content);
	}

}

?>