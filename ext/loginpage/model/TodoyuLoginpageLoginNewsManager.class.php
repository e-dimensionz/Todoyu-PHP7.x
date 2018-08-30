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
 * Panel widget manager for the login news
 *
 * @package		Todoyu
 * @subpackage	Loginpage
 */
class TodoyuLoginpageLoginNewsManager {

	/**
	 * Get content of news file
	 * Checks cache first
	 *
	 * @return	String
	 */
	public static function getNewsFileContent() {
		$isSecure		= TodoyuRequest::isSecureRequest();
		$pathCacheFile	= self::getNewsFileCachePath($isSecure);

		if( ! self::hasUpToDateNewsCacheFile($isSecure) ) {
			self::downloadNewsFile($isSecure);
		}

		if( file_exists($pathCacheFile) ) {
			return file_get_contents($pathCacheFile);
		} else {
			return false;
		}
	}



	/**
	 * Creates a File with news from todoyu.com
	 * -first try over curl
	 * -second try over file get contents
	 *
	 * @param	Boolean		$isSecure
	 */
	private static function downloadNewsFile($isSecure) {
		$url		= Todoyu::$CONFIG['EXT']['loginpage']['panelWidgetLoginNews']['url'];

		$pageContent= TodoyuFileManager::downloadFile($url);
		$bodyContent= self::extractBodyFromNewsFile($pageContent);

		self::writeCacheFile($bodyContent, $isSecure);
	}



	/**
	 * Check if a cache file exists
	 *
	 * @param	Boolean		$isSecure		HTTP request
	 * @return	Boolean
	 */
	private static function hasUpToDateNewsCacheFile($isSecure) {
		$pathCacheFile	= self::getNewsFileCachePath($isSecure);
		$age			= intval(Todoyu::$CONFIG['EXT']['loginpage']['panelWidgetLoginNews']['age']);

		if( file_exists($pathCacheFile) ) {
			$dateMod	= intval(filemtime($pathCacheFile));

			if( $dateMod + $age > NOW ) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Get cache path for news file
	 *
	 * @param	Boolean		$isSecure
	 * @return	String
	 */
	private static function getNewsFileCachePath($isSecure) {
		$protocol		= $isSecure ? 'https' : 'http';

		return TodoyuFileManager::pathAbsolute('cache/output/loginnews.' . $protocol . '.html');
	}



	/**
	 * Extract body content from an html page string
	 *
	 * @param	String		$content
	 * @return	String
	 */
	private static function extractBodyFromNewsFile($content) {
		$pattern	= '/.*<body.*?>(.*?)<\/body>.*?/is';
		preg_match($pattern, $content, $match);

		return trim($match[1]);
	}



	/**
	 * Write the content to a cache file
	 *
	 * @param	String		$content
	 * @param	Boolean		$isSecure
	 */
	private static function writeCacheFile($content, $isSecure) {
		$pathCacheFile	= self::getNewsFileCachePath($isSecure);

			// Make sure content is https valid (prevents security notices in browser
		if( $isSecure ) {
			$content	= str_replace('http://', 'https://', $content);
		}

		TodoyuFileManager::saveFileContent($pathCacheFile, $content);
	}

}