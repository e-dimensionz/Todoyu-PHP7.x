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
 * Installer
 *
 * @package		Todoyu
 * @subpackage	Installer
 */
class TodoyuInstaller {

	/**
	 * Path to last version file
	 *
	 * @var	String
	 */
	private static $versionFile = 'install/config/LAST_VERSION';



	/**
	 * Process and display current step of installer
	 */
	public static function run() {
			// Send headers to prevent caching
		TodoyuHeader::sendNoCacheHeaders();
			// Start output buffer
		ob_start();
			// Save last version if submitted
		self::saveVersionDetection();

			// Show special form to set last version if file is not available
		if( self::isUpdate() && ! self::hasVersionFile() ) {
			echo TodoyuInstallerRenderer::renderVersionSelector();
			$_SESSION = array();
			exit();
		}

			// No installation step or restart? initialize installer
		if( ! self::hasStep() || self::isRestart() ) {
			self::firstRun();
		}

		$step	= self::getStep();

			// Get post data
		if( self::hasData() ) {
			$postData	= TodoyuRequest::getAll();
		} else {
			$postData	= array();
		}

			// Set installation language
		$locale	= TodoyuSession::get('installer/locale');
		if( $locale != '' ) {
			Todoyu::setLocale($locale);
		}

			// Process current step of installation
		$result	= self::process($step, $postData);

		$step	= self::getStep();

		echo self::display($step, $result, $postData);

			// Flush output buffer
		ob_end_flush();
	}



	/**
	 * Ensure installer CSS files to be available (SCSS being parsed)
	 */
	public static function prepareInstallerCss() {
		$scssFiles	= array(
			array('file'	=> 'core/asset/css/base.scss'),
			array('file'	=> 'core/asset/css/layout.scss'),
			array('file'	=> 'core/asset/css/panel.scss'),
			array('file'	=> 'core/asset/css/form.scss'),
			array('file'	=> 'core/asset/css/button.scss'),
		);
		TodoyuPageAssetManager::getStyleSheets($scssFiles);
	}



	/**
	 * First run of installer: clear cache, init session, run PHP and SQL updates
	 */
	private static function firstRun() {
		$_SESSION = array();
		session_regenerate_id();

			// Clear all cache
		TodoyuInstallerManager::clearCache();
			// Ensure installer SCSS to be parsed into cached CSS
		self::prepareInstallerCss();

			// Initialize step in session
		self::initStep();

			// Run update scripts and SQL
		if( self::isUpdate() ) {
			TodoyuInstallerManager::runCoreVersionUpdates();
		}
	}



	/**
	 * Check whether the installation has a LAST_VERSION file
	 *
	 * @return	Boolean
	 */
	private static function hasVersionFile() {
		return is_file(self::$versionFile);
	}



	/**
	 * Save last installed version from form which comes before update
	 * Only necessary when no LAST_VERSION file was available
	 *
	 */
	private static function saveVersionDetection() {
		if( TodoyuRequest::isPostRequest() ) {
			if( isset($_POST['version']) ) {
				$version	= trim($_POST['version']);

				TodoyuFileManager::saveFileContent(self::$versionFile, $version);
			}
		}
	}



	/**
	 * Set installation step (session)
	 *
	 * @param	String		$step
	 */
	public static function setStep($step) {
		TodoyuSession::set('installer/step', $step);
	}



	/**
	 * Get current installation step
	 *
	 * @return	String
	 */
	public static function getStep() {
		return TodoyuSession::get('installer/step');
	}



	/**
	 * Check if a step is set
	 *
	 * @return	Boolean
	 */
	private static function hasStep() {
		return TodoyuSession::isIn('installer/step');
	}



	/**
	 * Check if restart flag is set
	 *
	 * @return	Boolean
	 */
	private static function isRestart() {
		return ((int) $_GET['restart']) === 1;
	}



	/**
	 * Check if data is submitted
	 *
	 * @return	Boolean
	 */
	private static function hasData() {
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}



	/**
	 * Check if ENABLE file is available
	 *
	 * @return	Boolean
	 */
	public static function isEnabled() {
		$file	= TodoyuFileManager::pathAbsolute('install/ENABLE');

		return is_file($file);
	}



	/**
	 * Initialize first step. Install or update? Save mode in session
	 */
	private static function initStep() {
		if( self::isUpdate() ) {
			self::setStep(INSTALLER_INITIALSTEP_UPDATE);
			self::setMode('update');
		} else {
			self::setStep(INSTALLER_INITIALSTEP_INSTALL);
			self::setMode('install');
		}

			// Try to detect to cookie locale
		$cookieLocale = TodoyuLocaleManager::getCookieLocale();
		if( $cookieLocale !== false ) {
			TodoyuSession::set('installer/locale', $cookieLocale);
		}
	}



	/**
	 * Set run mode (install or update)
	 *
	 * @param	String		$mode
	 */
	private static function setMode($mode) {
		TodoyuSession::set('installer/mode', $mode);
	}



	/**
	 * Get run mode
	 *
	 * @return	String
	 */
	public static function getMode() {
		return TodoyuSession::get('installer/mode');
	}



	/**
	 * Get configuration array for a step
	 *
	 * @param	String		$step
	 * @return	Array
	 */
	public static function getStepConfig($step) {
		return TodoyuArray::assure(Todoyu::$CONFIG['INSTALLER']['steps'][$step]);
	}



	/**
	 * Process submitted data for a step. Call processing function
	 *
	 * @param	String		$step
	 * @param	Array		$data
	 * @return	Array
	 */
	private static function process($step, array $data = array()) {
		$stepConfig	= self::getStepConfig($step);

		if( TodoyuFunction::isFunctionReference($stepConfig['process']) ) {
			return TodoyuFunction::callUserFunction($stepConfig['process'], $data);
		} else {
			return array();
		}
	}



	/**
	 * Display step
	 *
	 * @param	String		$step
	 * @param	Array		$result
	 * @param	Array		$postData
	 * @return	String
	 */
	private static function display($step, array $result = array(), array $postData = array()) {
		$stepConfig	= self::getStepConfig($step);
		$tmpl		= 'install/view/' . $stepConfig['tmpl'];

		if( TodoyuFunction::isFunctionReference($stepConfig['render']) ) {
			$data	= TodoyuFunction::callUserFunction($stepConfig['render'], $result);
		} else {
			$data	= array();
		}

		$data['progress']	= TodoyuInstallerRenderer::renderProgressWidget($step);
		$data['result']		= $result;
		$data['postData']	= $postData;

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Check if step if part of the update run
	 *
	 * @param	String		$step
	 * @return	Boolean
	 */
	public static function isUpdateStep($step) {
		return in_array($step, Todoyu::$CONFIG['INSTALLER']['update']);
	}



	/**
	 * Get steps of the current mode
	 *
	 * @return	Array
	 */
	public static function getModeSteps() {
		$type	= self::getMode();

		return TodoyuArray::assure(Todoyu::$CONFIG['INSTALLER'][$type]);
	}



	/**
	 * Get mode steps (update or install) with labels
	 *
	 * @return	Array
	 */
	public static function getStepsWithLabels() {
		$steps		= self::getModeSteps();
		$withLabels	= array();

		foreach($steps as $step) {
			$withLabels[$step] = Todoyu::Label('install.installer.' . $step . '.title');
		}

		return $withLabels;
	}



	/**
	 * Get locale options with localized labels
	 *
	 * @return Array
	 */
	public static function getAvailableLocaleOptions() {
		$locales	= TodoyuLocaleManager::getAvailableLocales();
		$options	= array();

		foreach($locales as $locale) {
			$options[] = array(
				'key'	=> $locale,
				'label'	=> Todoyu::Label('install.installer.locale.selectthislocale', $locale)
			);
		}

		return $options;
	}



	/**
	 * Check if two enable files are existing
	 *
	 * @return	Boolean
	 */
	public static function hasDoubleEnableFile() {
		$file1	= TodoyuFileManager::pathAbsolute(PATH . '/install/ENABLE');
		$file2	= TodoyuFileManager::pathAbsolute(PATH . '/install/_ENABLE');

		return is_file($file1) && is_file($file2);
	}



	/**
	 * Check if system is already set up, and this is an update call
	 *
	 * @return	Boolean
	 */
	public static function isUpdate() {
		return	( self::getMode() !== 'install' && TodoyuInstallerManager::isDatabaseConfigured() )
				|| self::hasDoubleEnableFile();
	}

}
?>