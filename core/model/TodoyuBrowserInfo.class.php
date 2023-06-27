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
 * Easy access to browser informations
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuBrowserInfo {

	/**
	 * Mobile detect instance
	 *
	 * @var	Mobile_Detect
	 */
	protected static $mobileDetect;



	/**
	 * Check if browser is Internet Explorer
	 *
	 * @return	Boolean
	 */
	public static function isIE() {
		return stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false;
	}



	/**
	 * Check if browser is Firefox
	 *
	 * @return	Boolean
	 */
	public static function isFirefox() {
		return stripos($_SERVER['HTTP_USER_AGENT'], 'firefox') !== false;
	}



	/**
	 * Check if browser is Safari
	 *
	 * @return	Boolean
	 */
	public static function isSafari() {
		return stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false;
	}



	/**
	 * Check if browser is Opera
	 *
	 * @return	Boolean
	 */
	public static function isOpera() {
		return stripos($_SERVER['HTTP_USER_AGENT'], 'opera') !== false;
	}



	/**
	 * Check if browser is chrome
	 *
	 * @return	Boolean
	 */
	public static function isChrome() {
		return stripos($_SERVER['HTTP_USER_AGENT'], 'chrome') !== false;
	}



	/**
	 * Get identification (name) of browser
	 *
	 * @return	String
	 */
	public static function getBrowserIdent() {
		$ident	= 'unknown';

		if( self::isFirefox() ) {
			$ident	= 'firefox';
		} elseif( self::isIE() ) {
			$ident	= 'ie';
		} elseif( self::isSafari() ) {
			$ident	= 'safari';
		} elseif( self::isOpera() ) {
			$ident	= 'opera';
		} elseif( self::isChrome() ) {
			$ident	= 'chrome';
		}

		return $ident;
	}



	/**
	 * Get the version of the browser
	 *
	 * @return	String			Version string like 5.3.21
	 */
	public static function getVersion() {
		$version	= 'unknown';
		$userAgent	= $_SERVER['HTTP_USER_AGENT'];

		if( self::isFirefox() ) {
			$pos	= strpos($userAgent, 'Firefox/');
			$parts	= explode(' ', substr($userAgent, $pos + 8));

			$version= trim($parts[0]);
		} elseif( self::isIE() ) {
			$pos	= strpos($userAgent, 'MSIE ');
			$parts	= explode(' ', substr($userAgent, $pos + 5));

			$version= trim(str_replace(';', '', $parts[0]));
		} elseif( self::isChrome() ) {
			$pos	= strpos($userAgent, 'Chrome/');
			$parts	= explode(' ', substr($userAgent, $pos + 7));

			$version= trim($parts[0]);
		} elseif( self::isSafari() ) {
			$pos	= strpos($userAgent, 'Version/');
			$parts	= explode(' ', substr($userAgent, $pos + 8));

			$version= trim($parts[0]);
		} elseif( self::isOpera() ) {
			$pos	= strpos($userAgent, 'Opera/');
			$parts	= explode(' ', substr($userAgent, $pos + 6));

			$version= trim($parts[0]);
		}

		return $version;
	}



	/**
	 * Get major version of the browser
	 *
	 * @return	Integer
	 */
	public static function getMajorVersion() {
		$version	= self::getVersion();
		$vParts		= explode('.', $version);

		return (int) $vParts[0];
	}



	/**
	 * Get browser locale
	 * Example of input string: de-ch,de-de;q=0.8,de;q=0.6,en-us;q=0.4,en;q=0.2
	 *
	 * @return	String		Or FALSE if not found
	 */
	public static function getBrowserLocale() {
		$locale		= false;
		$accepted	= explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

		foreach($accepted as $acceptedLocale) {
			$l = explode(';', $acceptedLocale);
            $localeString = $l[0];

			if( strstr($localeString, '-') ) {
				list($lang, $country) = explode('-', $localeString);
				$locale		= strtolower($lang) . '_' . strtoupper($country);
				break;
			}
		}

		return $locale;
	}



	/**
	 * Get mobile detect instance
	 *
	 * @return	Mobile_Detect
	 */
	public static function getMobileDetect() {
		if( is_null(self::$mobileDetect) ) {
			require_once( PATH_LIB . '/php/MobileDetect/Mobile_Detect.php' );

			self::$mobileDetect = new Mobile_Detect();
		}

		return self::$mobileDetect;
	}



	/**
	 * Is mobile browser
	 *
	 * @return	Boolean
	 */
	public static function isMobile() {
		return self::getMobileDetect()->isMobile();
	}

}

?>