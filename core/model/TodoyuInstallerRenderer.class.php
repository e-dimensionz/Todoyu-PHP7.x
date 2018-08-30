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
class TodoyuInstallerRenderer {

	/**
	 * Render progress panel widget listing all steps of installation
	 *
	 * @param	String		$step
	 * @return	String
	 */
	public static function renderProgressWidget($step) {
		$tmpl	= 'install/view/panelwidget-progress.tmpl';
		$data	= array(
			'steps'	=> TodoyuInstaller::getStepsWithLabels(),
			'step'	=> TodoyuInstaller::getStep(),
			'title'	=> Todoyu::Label('install.installer.type.' . TodoyuInstaller::getMode())
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Assure installer assets being available (parse + cache SCSS), return config array of needed assets
	 *
	 * @return	Array
	 */
	private static function getInstallerAssetsConfig() {
		$assetsConfig	= Todoyu::$CONFIG['INSTALLER']['assets'];

			// Rewrite SCSS assets to their resp. CSS file, parse it to cache if not present yet
		foreach($assetsConfig['css'] as $index => $stylesheetFile) {
			if( TodoyuString::endsWith($stylesheetFile, '.scss') ) {
				$pathCss	=	'../' . TodoyuFileManager::pathWeb(TodoyuPageAssetManager::parseScssStylesheet($stylesheetFile));
				$assetsConfig['css'][$index]	= $pathCss;
			}
		}

		return $assetsConfig;
	}



	/**
	 * Render installer step: select installer language (preset for system language)
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderLocale(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'			=> 'install.installer.locale.title',
			'button'		=> false,
			'text'			=> Todoyu::Label('install.installer.locale.text'),
			'textClass'		=> 'text textInfo',
			'locales'		=> TodoyuInstaller::getAvailableLocaleOptions(),
			'userLocale'	=> TodoyuBrowserInfo::getBrowserLocale(),
		);


		return $data;
	}



	/**
	 * Render installer step: todoyu end user license agreement
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderLicense(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'		=> 'install.installer.license.title',
			'button'	=> 'install.installer.license.button',
			'text'		=> Todoyu::Label('install.installer.license.text'),
			'textClass'	=> 'text textInfo'
		);

		return $data;
	}



	/**
	 * Render installer step: Server Check (correct PHP version and writable files, folders)
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderServerCheck(array $result) {
		$info	= TodoyuInstallerManager::checkServer();

		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'		=> 'install.installer.servercheck.title',
			'button'	=> 'install.installer.servercheck.button',
			'info'		=> array(
				'phpversion'=> $info['phpversion'],
				'files'		=> $info['files'],
				'stop'		=> $info['stop']
			)
		);

			// Get result of server check, set response display
		if( !$info['stop'] ) {
			$data['text']		= Todoyu::Label('install.installer.servercheck.ready');
			$data['textClass']	= 'success';
		} else {
			$data['text']		= Todoyu::Label('install.installer.servercheck.NotReady');
			$data['textClass']	= 'error';
		}

		return $data;
	}



	/**
	 * Render installer step: Setup Database Connection (check DB connection details, this step repeats itself on connection failure)
	 *
	 * @param	Array	$result
	 * @return	Array
	 */
	public static function renderDbConnection(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'		=> 'install.installer.dbconnection.title',
			'button'	=> 'install.installer.dbconnection.button',
			'text'		=> Todoyu::Label('install.installer.dbconnection.text'),
			'textClass'	=> 'info'
		);

		return $data;
	}



	/**
	 * Render installer step: Select database
	 *
	 * @param	Array	$result
	 * @return	Array
	 */
	public static function renderDbSelect(array $result) {
		$dbConfig	= TodoyuSession::get('installer/db');
		$databases	= TodoyuDbAnalyzer::getDatabasesOnServer($dbConfig);

		$dbOptions	= array();
		$dbConf		= $dbConfig;

			// PreRender database selection options
		foreach($databases as $database) {
			$dbConf['database']	= $database;
			$tables				= TodoyuDbAnalyzer::getDatabaseTables($dbConf);
			$dbOptions[] = array(
				'database'	=> $database,
				'tables'	=> $tables,
				'size'		=> sizeof($tables),
				'block'		=> self::hasTableConflicts($tables)
			);
		}
			// Setup rendering data
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'		=> 'install.installer.dbselect.title',
			'button'	=> 'install.installer.dbselect.button',
			'databases'	=> $dbOptions,
			'text'		=> Todoyu::Label('install.installer.dbselect.text'),
			'textClass'	=> 'info'
		);

		return $data;
	}



	/**
	 * Check whether tables from the database conflict with tables from the install SQL files
	 *
	 * @param	Array	$databaseTableNames
	 * @return	Boolean
	 */
	private static function hasTableConflicts(array $databaseTableNames) {
		$tableStructures= TodoyuSQLManager::getFileTableStructures();
		$fileTableNames	= array_keys($tableStructures);

		return sizeof(array_intersect($databaseTableNames, $fileTableNames)) > 0;
	}



	/**
	 * Render installer step: Import Database Tables
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderImportDbTables(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'			=> 'install.installer.importtables.title',
			'button'		=> 'install.installer.importtables.button',
			'coreStructure'	=> TodoyuSQLManager::getCoreTablesFromFile(),
			'extStructure'	=> TodoyuSQLManager::getExtTablesFromFile(),
			'text'			=> Todoyu::Label('install.installer.importtables.text'),
			'textClass'		=> 'info'
		);

		return $data;
	}



	/**
	 * Render installer step: System Configuration Setup (name, email, primary language)
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderSystemConfig(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'			=> 'install.installer.systemconfig.title',
			'button'		=> 'install.installer.systemconfig.button',
			'userLocale'	=> TodoyuSession::get('installer/locale'),
			'locales'		=> TodoyuLocaleManager::getAvailableLocaleOptions(),
			'timezones'		=> TodoyuStaticRecords::getAllTimezones(),
			'text'			=> Todoyu::Label('install.installer.systemconfig.text'),
			'textClass'		=> 'info'
		);

		return $data;
	}



	/**
	 * Render installer step: Create Administrator Account
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderAdminAccount(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'		=> 'install.installer.adminaccount.title',
			'button'	=> 'install.installer.adminaccount.button',
			'email'		=> TodoyuSession::get('installer/systememail'),
			'text'		=> Todoyu::Label('install.installer.adminaccount.text'),
			'textClass'	=> 'info'
		);

		return $data;
	}



	/**
	 * Render installer step: Import Demo Data
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderImportDemoData(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'		=> 'install.installer.importdemodata.title',
			'button'	=> 'install.installer.importdemodata.button',
			'text'		=> Todoyu::Label('install.installer.importdemodata.text'),
			'textClass'	=> 'info'
		);

		return $data;
	}



	/**
	 * Render installer step: Finish Installation
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderFinish(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'		=> 'install.installer.finish.title',
			'button'	=> 'install.installer.finish.button',
			'text'		=> Todoyu::Label('install.installer.finish.text'),
			'textClass'	=> 'success'
		);

		return $data;
	}





	######## UPDATE ##############




	/**
	 * Render updater welcome screen
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderUpdate(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'			=> 'install.installer.update.title',
			'button'		=> 'install.installer.update.button',
			'text'			=> Todoyu::Label('install.installer.update.title'),
			'textClass'		=> 'info'
		);

		return $data;
	}



	/**
	 * Render detected and conducted mandatory version DB updates
	 *
	 * @param	Array	$result
	 * @return	String
	 */
	public static function renderUpdateToCurrentVersion(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'			=> 'install.installer.updatetocurrentversion.title',
			'button'		=> 'install.installer.updatetocurrentversion.button',
			'buttonClass'	=> 'updateDatabase',
			'diff'			=> TodoyuSQLManager::getStructureDifferences()
		);

		if( sizeof($data['diff']['missingTables']) === 0 && sizeof($data['diff']['missingColumns']) === 0 && sizeof($data['diff']['changedColumns']) === 0 && sizeof($data['diff']['missingKeys']) === 0 ) {
			$data['text']		= Todoyu::Label('install.installer.updatetocurrentversion.noupdates');
			$data['textClass']	= 'success';
			$data['button']		= 'install.installer.updatetocurrentversion.button.noupdates';
			$data['noUpdates']	= true;
		} else {
			$data['text']		= Todoyu::Label('install.installer.updatetocurrentversion.updates');
			$data['textClass']	= 'info';
		}

		return $data;
	}



	/**
	 * Render update finish screen
	 *
	 * @param	Array		$result
	 * @return	Array
	 */
	public static function renderFinishUpdate(array $result) {
		$data	= array(
			'assets'		=> self::getInstallerAssetsConfig(),

			'title'			=> 'install.installer.finish.title',
			'button'		=> 'install.installer.finish.button',
			'text'			=> Todoyu::Label('install.installer.finishupdate.text'),
			'textClass'		=> 'success'
		);

		return $data;
	}



	/**
	 * Render version selector which comes before updater
	 *
	 * @return	String
	 */
	public static function renderVersionSelector() {
		$tmpl	= 'install/view/version-selector.tmpl';

		return Todoyu::render($tmpl);
	}


}
?>