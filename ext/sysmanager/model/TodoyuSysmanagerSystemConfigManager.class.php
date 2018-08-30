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
 * System config manager
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerSystemConfigManager {

	/**
	 * Save uploaded image (automatically resized) as logo
	 *
	 * @param	Array		$logoUploadData
	 * @return	Boolean
	 */
	public static function saveLogo(array $logoUploadData) {
		$success	= false;

		if( self::isValidImageUpload($logoUploadData) ) {
			$config	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['sysmanager']['logoUpload']);

			$pathDest	= TodoyuFileManager::pathAbsolute($config['path']);
			$width		= intval($config['width']);
			$height		= intval($config['height']);

			$success	= TodoyuImageManager::saveResizedImage($logoUploadData['tmp_name'], $pathDest, $width, $height, $logoUploadData['type']);
		}

		return $success;
	}



	/**
	 * Save data in system configuration file
	 *
	 * @param	Array		$formData
	 */
	public static function saveSystemConfig(array $formData) {

		$data	= array(
			'name'				=> trim($formData['name']),
			'email'				=> trim($formData['email']),
			'locale'			=> trim($formData['locale']),
			'timezone'			=> trim($formData['timezone']),
			'firstDayOfWeek'	=> intval($formData['firstDayOfWeek']),
			'todoyuURL'			=> trim($formData['todoyuURL']),
			'logLevel'			=> intval($formData['logLevel']),
			'mailer'			=> trim($formData['mailer'])
		);

		TodoyuConfigManager::saveSystemConfigConfig($data, false);
		TodoyuConfigManager::clearJavaScriptConfig();
		TodoyuConfigManager::saveJavaScriptConfig();
	}



	/**
	 * Save password strength file
	 *
	 * @param	Array	$data
	 */
	public static function savePasswordStrength(array $data) {
		Todoyu::$CONFIG['SETTINGS']['passwordStrength'] = array(
			'minLength'			=> intval($data['minLength']),
			'hasNumbers'		=> intval($data['hasNumbers']) === 1,
			'hasLowerCase'		=> intval($data['hasLowerCase']) === 1,
			'hasUpperCase'		=> intval($data['hasUpperCase']) === 1,
			'hasSpecialChars'	=> intval($data['hasSpecialChars']) === 1
		);

		TodoyuConfigManager::saveSettingsConfig();
	}



	/**
	 * Save repository config (into config\settings.php)
	 *
	 * @param	Array	$data
	 */
	public static function saveRepositoryConfig(array $data) {
		Todoyu::$CONFIG['SETTINGS']['repository'] = array(
			'todoyuid'	=> trim($data['todoyuid'])
		);

		TodoyuConfigManager::saveSettingsConfig();
	}



	/**
	 * Check whether uploaded image data is valid
	 *
	 * @param	Array		$imageUploadData
	 * @return	Boolean
	 */
	public static function isValidImageUpload(array $imageUploadData) {
		if( substr($imageUploadData['type'], 0, 6) !== 'image/' ) {
			return false;
		}

		if( intval($imageUploadData['error']) !== 0 ) {
			return false;
		}

		if( intval($imageUploadData['size']) === 0 ) {
			return false;
		}

		return true;
	}



	/**
	 * Get locale options
	 *
	 * @return		Array
	 * @deprecated
	 * @see			getAvailableLocaleOptions()
	 */
	public static function getLocaleOptions() {
		return self::getAvailableLocaleOptions();
	}



	/**
	 * Get config array of locale options
	 *
	 * @return	Array[]
	 */
	public static function getAvailableLocaleOptions() {
		$locales	= TodoyuLocaleManager::getAvailableLocales();
		$options	= array();
		$default	= TodoyuLocaleManager::getDefaultLocale();

		foreach($locales as $locale) {
			$labelLocale	= Todoyu::Label('core.locale.' . $locale, $locale);
			$labelDefault	= Todoyu::Label('core.locale.' . $locale, $default);
			$options[] = array(
				'value'	=> $locale,
				'label'	=> $labelLocale . ' <=> ' . $labelDefault
			);
		}

		return TodoyuArray::sortByLabel($options, 'label');
	}



	/**
	 * Get system setting of first day of week (0 = sunday, 1 = monday)
	 *
	 * @return	Integer
	 */
	public static function getFirstDayOfWeek() {
		$day	= Todoyu::$CONFIG['SYSTEM']['firstDayOfWeek'];

		return $day === 0 ? 0 : 1;
	}

}

?>