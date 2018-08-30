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
 * Manager for locales
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuLocaleManager {

	/**
	 * Get locale definitions
	 *
	 * @return	Array
	 */
	public static function getSystemLocales() {
		return TodoyuArray::assure(Todoyu::$CONFIG['LOCALE']['SYSTEMLOCALES']);
	}



	/**
	 * Check if locale exists in list
	 *
	 * @param	String		$locale
	 * @return	Boolean
	 */
	public static function hasSystemLocale($locale) {
		return array_key_exists($locale, self::getSystemLocales());
	}



	/**
	 * Get options config array of available languages
	 *
	 * @return	Array
	 */
	public static function getAvailableLocales() {
		$extKeys	= TodoyuExtensions::getInstalledExtKeys();
		$default	= self::getDefaultLocale();

			// Check core
		$locales	= TodoyuFileManager::getFoldersInFolder('core/locale');

			// Check extensions
		foreach($extKeys as $extKey) {
			$path		= TodoyuExtensions::getExtPath($extKey, 'locale');
			$extLocales	= TodoyuFileManager::getFoldersInFolder($path);
			$locales	= array_merge($locales, $extLocales);
		}

		$locales	= array_unique($locales);

		$locales	= TodoyuArray::removeByValue($locales, array($default));

		sort($locales);

			// Prepend default
		array_unshift($locales, $default);

		return $locales;
	}



	/**
	 * Get all codes (encoding type description, language abbreviation, language name) of a locale which may exists on a system
	 *
	 * @param	String		$locale
	 * @return	Array
	 */
	public static function getSystemLocaleCodes($locale) {
		$locales	= self::getSystemLocales();

		return TodoyuArray::assure($locales[$locale]);
	}



	/**
	 * Set system locale
	 *
	 * @param	String					$locale
	 * @return	Boolean / String		FALSE or the new locale string
	 */
	public static function setSystemLocale($locale) {
		$localeNames	= self::getSystemLocaleCodes($locale);

		if( sizeof($localeNames) > 0 ) {
			return setlocale(LC_ALL, $localeNames);
		} else {
			TodoyuLogger::logError('Failed to set system locale. No localnames available for locale: "' . $locale . '"');
			return false;
		}
	}



	/**
	 * Get currently on the system defined locale
	 *
	 * @return	String
	 */
	public static function getLocale() {
		return setlocale(LC_ALL, 0);
	}



	/**
	 * Get default fallback locale
	 *
	 * @return	String
	 */
	public static function getDefaultLocale() {
		return Todoyu::$CONFIG['LOCALE']['default'];
	}



	/**
	 * Get option array with locale key and label
	 *
	 * @return	Array[]
	 * @deprecated
	 * @see		getAvailableLocales()
	 */
	public static function getLocaleOptions() {
		return self::getAvailableLocaleOptions();
	}



	/**
	 * Get options for all available locales
	 *
	 * @return	Array[]
	 */
	public static function getAvailableLocaleOptions() {
		$locales	= self::getAvailableLocales();

		return self::buildLocaleOptions($locales);
	}



	/**
	 * Get options for all known locales
	 *
	 * @return	Array[]
	 */
	public static function getAllLocaleOptions() {
		$locales	= self::getAllLocaleKeys();

		return self::buildLocaleOptions($locales);
	}



	/**
	 * Build options based on locale keys
	 *
	 * @param	String[]	$locales
	 * @return	Array[]
	 */
	private static function buildLocaleOptions(array $locales) {
		$options	= array();

		foreach($locales as $locale) {
			$options[] = array(
				'value'	=> $locale,
				'label'	=> Todoyu::Label('core.locale.' . $locale)
			);
		}

		return $options;
	}



	/**
	 * Get keys of all know locales
	 *
	 * @return	String[]
	 */
	private static function getAllLocaleKeys() {
		$allLocaleLabels	= TodoyuLabelManager::getXmlFileLabels('core', 'locale', 'en_GB');

		return array_keys($allLocaleLabels);
	}



	/**
	 * Send a cookie with locale setting of current user
	 *
	 * @param	Void|String		$locale
	 */
	public static function setLocaleCookie($locale = null) {
		if( is_null($locale)) {
			if( TodoyuAuth::isLoggedIn() ) {
				$locale	= Todoyu::getLocale();
			}
		}

		if( ! is_null($locale) ) {
			setcookie('locale', $locale, NOW + TodoyuTime::SECONDS_WEEK * 100, PATH_WEB);
		} else {
			TodoyuLogger::logError('Tried to set locale cookie. But not logged in and no parameter set!');
		}
	}



	/**
	 * Get locale saved in cookie if available
	 *
	 * @return	String|Boolean		Locale or FALSE
	 */
	public static function getCookieLocale() {
		if( isset($_COOKIE['locale']) ) {
			return $_COOKIE['locale'];
		} else {
			return false;
		}
	}

}

?>