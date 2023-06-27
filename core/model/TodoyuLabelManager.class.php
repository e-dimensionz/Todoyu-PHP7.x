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
 * Language management for todoyu
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuLabelManager {

	/**
	 * Locale
	 *
	 * @var	String		Current locale key
	 */
	private static $locale = 'en_GB';

	/**
	 * Locallang labels cache
	 *
	 * @var	Array
	 */
	private static $cache = array();

	/**
	 * Custom path to locale files for extKeys
	 *
	 * @var	Array
	 */
	private static $customPaths = array(
		'core'		=> 'core',
		'installer'	=> 'install'
	);



	/**
	 * Set locale. All request for labels without a locale will use this locale.
	 * Default locale is en_GB (british english)
	 *
	 * @param	String		$locale
	 */
	public static function setLocale($locale) {
		self::$locale = $locale;
	}



	/**
	 * Get current locale
	 *
	 * @return	String
	 */
	public static function getLocale() {
		return self::$locale;
	}



	/**
	 * Add a custom path for keys which are not located in the normal file structure
	 * The folder has to contain a "locale" folder with locale key sub folders like the extensions
	 *
	 * @param	String		$key
	 * @param	String		$customPath		Path relative to todoyu root
	 */
	public static function addCustomPath($key, $customPath) {
		self::$customPaths[$key] = $customPath;
	}



	/**
	 * Get label which will be parsed with wild cards like printf()
	 *
	 * @param	String		$labelKey
	 * @param	Array		$data
	 * @param	String		$locale
	 * @return	String
	 */
	public static function getFormatLabel($labelKey, array $data = array(), $locale = null) {
		$label	= self::getLabel($labelKey, $locale);

		return vsprintf($label, $data);
	}



	/**
	 * Get translated label
	 *
	 * @param	String		$fullKey		Key to label. First part is the fileKey
	 * @param	String		$locale			Force language. If not set, us defined language
	 * @return	String		Translated label
	 */
	public static function getLabel($fullKey, $locale = null) {
		$locale	= is_null($locale) ? self::$locale : $locale ;
		$label	= self::getLabelInternal($fullKey, $locale);

		return !$label ? $fullKey : $label;
	}



	/**
	 * Get label or an empty string if not found
	 *
	 * @param	String		$fullKey
	 * @param	String		$locale
	 * @return	String
	 */
	public static function getLabelOrEmpty($fullKey, $locale = null) {
		$locale	= is_null($locale) ? self::$locale : $locale ;
		$label	= self::getLabelInternal($fullKey, $locale);

		return !$label ? '' : $label;
	}



	/**
	 * Get label (or false if not found)
	 *
	 * @param	String		$fullKey
	 * @param	String		$locale
	 * @return	String|Boolean
	 */
	private static function getLabelInternal($fullKey, $locale) {
		if( substr($fullKey, 0, 4) === 'LLL:' ) {
			$fullKey = substr($fullKey, 4);
		}

		if( substr_count($fullKey, '.') < 2 ) {
			if( Todoyu::$CONFIG['LOCALE']['logInvalidKeys'] ) {
				TodoyuLogger::logError('Invalid label key: <' . $fullKey . '>');
			}
			return false;
		} else {
			list($extKey, $fileKey, $labelKey) = explode('.', $fullKey, 3);

			$label	= self::getCachedLabel($extKey, $fileKey, $labelKey, $locale);

			return is_null($label) ? false : $label;
		}
	}



	/**
	 * Get a label from internal cache. If the label is not available, load it
	 *
	 * @param	String		$fileKey		Filekey
	 * @param	String		$extKey
	 * @param	String		$fileKey		Filekey
	 * @param	String		$labelKey		Index of the label in the file
	 * @param	String		$locale			Locale to load the label
	 * @return	String		The label with the key $index for $language
	 */
	private static function getCachedLabel($extKey, $fileKey, $labelKey, $locale) {
        if(empty(self::$cache)) self::$cache = array();
        if(empty(self::$cache[$extKey])) self::$cache[$extKey] = array();
        if(empty(self::$cache[$extKey][$fileKey])) self::$cache[$extKey][$fileKey] = array();
        if(empty(self::$cache[$extKey][$fileKey][$locale])) self::$cache[$extKey][$fileKey][$locale] = array();

		if( empty(self::$cache[$extKey][$fileKey][$locale][$labelKey]) || ! is_string(self::$cache[$extKey][$fileKey][$locale][$labelKey]) ) {
			self::$cache[$extKey][$fileKey][$locale] = self::getFileLabels($extKey, $fileKey, $locale);
		}

		return self::$cache[$extKey][$fileKey][$locale][$labelKey] ?? '';
	}



	/**
	 * Get path of the file which is registered for a file key
	 *
	 * @param	String		$extKey
	 * @param	String		$fileKey
	 * @param	String		$locale
	 * @return	String		Abs. path to file
	 */
	private static function getFilePath($extKey, $fileKey, $locale) {
		$localePath	= self::getLocalePath($extKey, $locale);

		return $localePath . DIR_SEP . $fileKey . '.xml';
	}



	/**
	 * Get path where locales are stored
	 *
	 * @param	String		$extKey
	 * @param	String		$locale
	 * @return	String
	 */
	public static function getLocalePath($extKey, $locale) {
		if( array_key_exists($extKey, self::$customPaths) ) {
			$basePath	= TodoyuFileManager::pathAbsolute(self::$customPaths[$extKey]);
		} else {
			$basePath	= TodoyuExtensions::getExtPath($extKey);
		}

		return $basePath . DIR_SEP . 'locale' . DIR_SEP . $locale;
	}



	/**
	 * Get all fallback locales of a locale
	 *
	 * @param	String		$locale
	 * @return	Array
	 */
	private static function getFallbackLocales($locale) {
		$fallbacks	= TodoyuArray::assure(Todoyu::$CONFIG['LOCALE']['fallback']);
		$fallback	= array();
		$tmpLocale	= (string)$locale;
		$counter	= 0;

			// Dig down the fallback languages. The counter prevents endless loops for bad configuration
		while( $counter < 10 ) {
			if( array_key_exists($tmpLocale, $fallbacks) ) {
					// If fallback defined, add it and check again
				$fallback[] = $fallbacks[$tmpLocale];
				$tmpLocale	= $fallbacks[$tmpLocale];
			} else {
					// If no fallback defined for locale, add default locale and stop searching
				$fallback[] = Todoyu::$CONFIG['LOCALE']['default'];
				break;
			}
		}

		return array_reverse(array_unique($fallback));
	}



	/**
	 * Get labels for an identifier for a locale
	 *
	 * @param	String		$extKey
	 * @param	String		$fileKey
	 * @param	String		$locale
	 * @return	Array
	 */
	private static function getFileLabels($extKey, $fileKey, $locale) {
		$locales	= self::getFallbackLocales($locale);
		$cacheFile	= self::getCacheFileName($extKey, $fileKey, $locale);

		if( is_file($cacheFile) ) {
			return self::readCachedLabelFile($cacheFile);
		}

		$locales[] = $locale;

		$labels	= array();

		foreach($locales as $fallbackLocale) {
			$fileLabels	= self::getXmlFileLabels($extKey, $fileKey, $fallbackLocale);

			if( sizeof($fileLabels) > 0 ) {
				$labels	= array_merge($labels, $fileLabels);
			}
		}

			// Only write a cache file when labels are found
		if( sizeof($labels) > 0 ) {
			self::writeCachedLabelFile($cacheFile, $labels);
		}

		return $labels;
	}



	/**
	 * Read labels from an XML file
	 *
	 * @param	String		$extKey
	 * @param	String		$fileKey
	 * @param	String		$locale
	 * @return	Array
	 */
	public static function getXmlFileLabels($extKey, $fileKey, $locale) {
		$labels		= array();
		$pathFile	= self::getFilePath($extKey, $fileKey, $locale);

		if( is_file($pathFile) ) {
			$labels	= self::readXmlFile($pathFile);
		}

		return $labels;
	}



	/**
	 * Clear locale cache
	 *
	 * @param	String|Boolean	$locale
	 */
	public static function clearCache($locale = false) {
		$pathCache	= Todoyu::$CONFIG['LOCALE']['labelCacheDir'];

		if( $locale  ) {
			$pathCache	.= '/' . $locale;
		}

		$pathCache	= TodoyuFileManager::pathAbsolute($pathCache);

		TodoyuFileManager::deleteFolderContents($pathCache);
	}



	/**
	 * Read a locallang XML file using a XML parser.
	 * Transforms the parser result in an useful array
	 * Structure [de][INDEX] = Label
	 *
	 * @param	String		$absPathToLocallangFile		Absolute path to locallang file
	 * @return	Array
	 */
	private static function readXmlFile($absPathToLocallangFile) {
		if( !is_file($absPathToLocallangFile) ) {
			return array();
		}

		$xmlString	= file_get_contents($absPathToLocallangFile);
		$parser		= xml_parser_create('UTF-8');

		$values	= $index = array();

		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');

		xml_parse_into_struct($parser, $xmlString, $values, $index);

		xml_parser_free($parser);

		return self::extractLabelsFromXmlResult($values);
	}



	/**
	 * Transform the output of the XML parser to an useful array
	 *
	 * @param	Array		$xmlValueArray
	 * @return	Array
	 */
	private static function extractLabelsFromXmlResult(array $xmlValueArray) {
		$labels	= array();

		foreach($xmlValueArray as $xmlTag) {
			switch($xmlTag['type']) {

				case 'open':
				case 'close':
					// Nothing to do
					break;

				case 'complete':
					$index = $xmlTag['attributes']['index'];
					$labels[$index] = $xmlTag['value'];
					break;
			}
		}

		return $labels;
	}



	/**
	 * Save locallang array to cache
	 *
	 * @param	String		$pathFile
	 * @param	Array		$labels
	 * @return	Boolean
	 */
	private static function writeCachedLabelFile($pathFile, array $labels) {
		$cacheData	= serialize($labels);

		return TodoyuFileManager::saveFileContent($pathFile, $cacheData) !== false;
	}



	/**
	 * Load cache file and get back locallang array
	 *
	 * @param	String		$cacheFile
	 * @return	Array
	 */
	private static function readCachedLabelFile($cacheFile) {
		if( is_file($cacheFile) ) {
			$cacheData	= file_get_contents($cacheFile);
			$locallang	= unserialize($cacheData);
		} else {
			$locallang	= array();
		}

		return $locallang;
	}



	/**
	 * Make cache file name. Based on the path to the XML file and its modification time
	 *
	 * @param	String		$extKey
	 * @param	String		$fileKey
	 * @param	String		$locale
	 * @return	String
	 */
	private static function getCacheFileName($extKey, $fileKey, $locale) {
		return TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['LOCALE']['labelCacheDir'] . DIR_SEP . $locale . DIR_SEP . $extKey . '.' . $fileKey . '.labels');
	}

}

?>