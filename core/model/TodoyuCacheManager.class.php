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
 * Dynamic context menu loaded by AJAX request
 * Extensions can register menu items for menu types
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCacheManager {

	/**
	 * Clear all cache: call all registered clearCache hooks
	 */
	public static function clearAllCache() {
			// Delete all files in cache folder
		TodoyuFileManager::deleteFolderContents(PATH_CACHE, false);
			// Call clearCache hook for other extensions
		TodoyuHookManager::callHook('core', 'clearCache');
	}



	/**
	 * Clear asset cache (JS + CSS)
	 */
	public static function clearAssetCache() {
		self::clearCacheFolder('js');
		self::clearCacheFolder('css');
	}



	/**
	 * Clear locale cache (compiled locale files)
	 */
	public static function clearLocaleCache() {
		self::clearCacheFolder('locale');
	}



	/**
	 * Clear template cache
	 */
	public static function clearTemplateCache() {
		self::clearCacheFolder('tmpl/cache');
		self::clearCacheFolder('tmpl/compile');
	}



	/**
	 * Clear a specific cache folter (all its content)
	 *
	 * @param	String		$cacheFolder		Relative path to filter from cache directory
	 */
	private static function clearCacheFolder($cacheFolder) {
		$pathToFolder	= TodoyuFileManager::pathAbsolute(PATH_CACHE . '/' . $cacheFolder);

		TodoyuFileManager::deleteFolderContents($pathToFolder);
	}

}

?>