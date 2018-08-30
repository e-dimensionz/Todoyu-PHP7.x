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
 * Manage Todoyu extensions
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuExtensions {

	/**
	 * Default extension documentation URL
	 */
	const EXTINFO_DEFAULT_DOC = 'http://doc.todoyu.com/?';

	/**
	 * Already loaded extension config types
	 *
	 * @var array
	 */
	private static $loadedExtConfigTypes = array();



	/**
	 * Get extension keys of all installed extensions
	 *
	 * @return	Array
	 */
	public static function getInstalledExtKeys() {
		return TodoyuArray::assure(Todoyu::$CONFIG['EXT']['installed']);
	}



	/**
	 * Get extension keys (folder names) of extensions which are located in
	 * the /ext folder, but not installed at the moment
	 *
	 * @return	Array
	 */
	public static function getNotInstalledExtKeys() {
		$extFolders		= TodoyuFileManager::getFoldersInFolder(PATH_EXT);
		$extInstalled	= TodoyuExtensions::getInstalledExtKeys();

		return array_values(array_diff($extFolders, $extInstalled));
	}



	/**
	 * Get extension ids and keys of all installed extensions
	 *
	 * @return	Array
	 */
	public static function getInstalledExtIDs() {
		$extKeys	= self::getInstalledExtKeys();
		$extIDs		= array();

		foreach($extKeys as $extKey) {
			$extIDs[$extKey] = constant('EXTID_' . strtoupper($extKey));
		}

		return $extIDs;
	}



	/**
	 * Check if an extension is installed
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function isInstalled($extKey) {
		$installed	= self::getInstalledExtKeys();

		return in_array($extKey, $installed);
	}





	/**
	 * Get extID by extKey
	 *
	 * @param	String		$extKey
	 * @return	Integer
	 */
	public static function getExtID($extKey) {
		$name	= 'EXTID_' . strtoupper(trim($extKey));

		if( defined($name) ) {
			return constant($name);
		} else {
			return 0;
		}
	}



	/**
	 * Check if file path is in the path of the extension
	 *
	 * @param	String		$extKey
	 * @param	String		$path
	 * @return	Boolean
	 */
	public static function isPathInExtDir($extKey, $path) {
		$path = TodoyuFileManager::pathAbsolute($path);

			// Extension path
		$extPath	= self::getExtPath($extKey);

			// Check if the extension path is the first part of the file path (position = 0)
		return strpos($path, $extPath) === 0;
	}



	/**
	 * Get full path of the extension
	 * This is the path an extension would have. Doesn't mean the path exists or extension is installed
	 *
	 * @param	String		$extKey
	 * @param	String		$appendPath
	 * @return	String		Absolute path to extension
	 */
	public static function getExtPath($extKey, $appendPath = '') {
		return TodoyuFileManager::pathAbsolute(PATH_EXT . DIR_SEP . $extKey . DIR_SEP . trim($appendPath, '/\\'));
	}



	/**
	 * Get extension information
	 *
	 * @param	String		$extKey			Extension key
	 * @return	Array		Or false if not defined
	 */
	public static function getExtInfo($extKey) {
		self::loadConfig($extKey, 'extinfo');
		self::setDefaultDocumentationLink($extKey);

		if( is_array(Todoyu::$CONFIG['EXT'][$extKey]['info']) ) {
			return Todoyu::$CONFIG['EXT'][$extKey]['info'];
		} else {
			return false;
		}
	}



	/**
	 * Get extension version
	 *
	 * @param	String		$extKey
	 * @return	String|Boolean
	 */
	public static function getExtVersion($extKey) {
		$info	= self::getExtInfo($extKey);

		if( !$info ) {
			return false;
		} else {
			return $info['version'];
		}
	}



	/**
	 * Get list of all extensions info
	 *
	 * @return	Array
	 */
	public static function getAllExtInfo() {
		$extensions	= self::getInstalledExtKeys();
		$infos		= array();

		foreach($extensions as $ext) {
			$infos[$ext] = self::getExtInfo($ext);
		}

		return $infos;
	}



	/**
	 * Load a configuration file of an extension if it's available
	 *
	 * @param	String		$extKey		Extension key
	 * @param	String		$type		Type of the config file (=filename)
	 * @return	Boolean		Loading status
	 */
	public static function loadConfig($extKey, $type) {
		if( !isset(self::$loadedExtConfigTypes[$extKey . $type]) ) {
			$pathConfig	= 'ext/' . $extKey . '/config/' . $type . '.php';

				// Attempt load given config
			if( self::isPathInExtDir($extKey, $pathConfig) && TodoyuFileManager::isFile($pathConfig) ) {
					// Load config file
				TodoyuFileManager::includeFile($pathConfig, true, true);
					// Register the type config to be loaded
				self::$loadedExtConfigTypes[$extKey . $type] = true;
					// Call hook, e.g. 'loadconfig.contact.filters'
				TodoyuHookManager::callHook('core', 'loadconfig.' . $extKey . '.' . $type);

					// Config loaded
				return true;
			}
		} else {
				// Config was already loaded
			 return true;
		}

		return false;
	}



	/**
	 * Set default doc link if not defined in extension config
	 *
	 * @param	String		$extKey
	 */
	private static function setDefaultDocumentationLink($extKey) {
		if( is_array(Todoyu::$CONFIG['EXT'][$extKey]['info']) ) {
			if ( !Todoyu::$CONFIG['EXT'][$extKey]['info']['urlDocumentation'] ) {
				Todoyu::$CONFIG['EXT'][$extKey]['info']['urlDocumentation'] = self::EXTINFO_DEFAULT_DOC . $extKey;
			}
		}
	}



	/**
	 * Load rights config of an extension
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function loadRights($extKey) {
		return self::loadConfig($extKey, 'rights');
	}



	/**
	 * Load filter config of an extension
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function loadFilters($extKey) {
		return self::loadConfig($extKey, 'filters');
	}



	/**
	 * Load all configuration files of an extension
	 *
	 * @param	String		$extKey
	 */
	public static function loadAllConfig($extKey) {
		$extPath	= self::getExtPath($extKey);

		$configDir	= $extPath . DIR_SEP . 'config';
		$configFiles= array_slice(scandir($configDir), 2);

		foreach($configFiles as $file) {
			include_once( $configDir . DIR_SEP . $file );
		}
	}



	/**
	 * Load config of a type from all extension (require /config/type.php files of extensions)
	 *
	 * @param	String		$type
	 */
	public static function loadAllTypeConfig($type) {
		$extKeys	= self::getInstalledExtKeys();

		foreach($extKeys as $extKey) {
			self::loadConfig($extKey, $type);
		}

			// Check if a config in core is available
		$coreConf	= PATH_CONFIG . '/' . $type . '.php';
		if( is_file($coreConf) ) {
			require_once($coreConf);
		}
	}



	/**
	 * Load filter config from all extensions
	 */
	public static function loadAllFilters() {
		self::loadAllTypeConfig('filters');
	}



	/**
	 * Load rights config from all extensions
	 */
	public static function loadAllRights() {
		self::loadAllTypeConfig('rights');
	}



	/**
	 * Load context menu config from all extensions
	 */
	public static function loadAllContextMenus() {
		self::loadAllTypeConfig('contextmenu');
	}



	/**
	 * Load form config from all extensions
	 */
	public static function loadAllForm() {
		require_once( PATH_CONFIG . '/form.php' );

		self::loadAllTypeConfig('form');
	}



	/**
	 * Load asset config for all extensions
	 */
	public static function loadAllAssets() {
		self::loadAllTypeConfig('assets');
	}



	/**
	 * Load admin config for all extensions
	 */
	public static function loadAllSysmanager() {
		self::loadAllTypeConfig('sysmanager');
	}



	/**
	 * Load extension informations for all extensions
	 */
	public static function loadAllExtinfo() {
		self::loadAllTypeConfig('extinfo');
	}



	/**
	 * Load panelwidget config for all extensions
	 */
	public static function loadAllPanelWidget() {
		self::loadAllTypeConfig('panelwidgets');
	}



	/**
	 * Load all page config (tabs, etc)
	 */
	public static function loadAllPage() {
		self::loadAllTypeConfig('page');
	}



	/**
	 * Load all search config (/config/search.php files of all loaded extensions)
	 */
	public static function loadAllSearch() {
		self::loadAllTypeConfig('search');
	}



	/**
	 * Load all create configs (/config/create.php files of all loaded extensions)
	 */
	public static function loadAllCreate() {
		self::loadAllTypeConfig('create');
	}



	/**
	 * Load all boot configs (/config/boot.php)
	 */
	public static function loadAllBoot() {
		self::loadAllTypeConfig('boot');
	}



	/**
	 * Load all init configs (config/init.php)
	 */
	public static function loadAllInit() {
		self::loadAllTypeConfig('init');
	}



	/**
	 * Init extensions
	 * Load init.php and global extconf
	 *
	 */
	public static function initExtensions() {
		self::loadAllInit();
		self::loadExtConf();
	}



	/**
	 * Load extension config from config/extconf.php
	 *
	 */
	public static function loadExtConf() {
		require( PATH_LOCALCONF . '/extconf.php');
	}


	
	/**
	 * Only for backwards compatibility
	 * Will just reload the
	 *
	 * @param	String		$extKey
	 * @deprecated
	 * @todoyu	Remove in later version
	 */
	public static function addExtAutoloadPaths($extKey) {
		TodoyuAutoloader::reload();
	}




	/**
	 * Check whether given extension depends on other extensions
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function hasDependencies($extKey) {
		$dependencies	= self::getDependencies($extKey);

		return sizeof($dependencies) > 0;
	}



	/**
	 * Get keys of extensions the given extension depends on
	 *
	 * @param	String	$extKey
	 * @return	Array
	 */
	public static function getDependencies($extKey) {
		$extInfo	= self::getExtInfo($extKey);

		return TodoyuArray::assure($extInfo['constraints']['depends']);
	}



	/**
	 * Check whether other extensions depend on given extension
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function hasDependents($extKey) {
		$dependents	= self::getDependents($extKey);

		return sizeof($dependents) > 0;
	}



	/**
	 * Get all dependents of an extensions
	 *
	 * @param	String		$extKeyToCheck
	 * @return	Array
	 */
	public static function getDependents($extKeyToCheck) {
		self::loadAllExtinfo();

		$dependents	= array();
		$extKeys	= self::getInstalledExtKeys();

		foreach($extKeys as $extKey) {
			$dependInfo	= Todoyu::$CONFIG['EXT'][$extKey]['info']['constraints']['depends'];

			if( is_array($dependInfo) ) {
				if( array_key_exists($extKeyToCheck, $dependInfo) ) {
					$dependents[] = $extKey;
				}
			}
		}

		return $dependents;
	}



	/**
	 * Check if an extension has the system flag (should not be uninstalled)
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function isSystemExtension($extKey) {
		$extInfo	= self::getExtInfo($extKey);

		return (boolean)$extInfo['constraints']['system'];
	}



	/**
	 * Check whether the extension has conflicts
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function hasConflicts($extKey) {
		return sizeof(self::getConflicts($extKey)) > 0;
	}



	/**
	 * Check whether the extension conflicts with another installed extension
	 *
	 * @param	String		$extKeyToCheck
	 * @return	Array		List of extensions which conflict with the checked one
	 */
	public static function getConflicts($extKeyToCheck) {
		self::loadAllExtinfo();

		$ownExtInfo		= self::getExtInfo($extKeyToCheck);
		$conflicts		= TodoyuArray::assure($ownExtInfo['constraints']['conflicts']);
		$extKeys		= self::getInstalledExtKeys();

		foreach($extKeys as $extKey) {
			$extInfo		= self::getExtInfo($extKey);
			$extConflicts	= TodoyuArray::assure($extInfo['constraints']['conflicts']);

			if( in_array($extKeyToCheck, $extConflicts) ) {
				$conflicts[] = $extKey;
			}
		}

		return $conflicts;
	}



	/**
	 * Get installed extension version
	 *
	 * @param	String		$extKey
	 * @return	String
	 */
	public static function getVersion($extKey) {
		self::loadAllExtinfo();

		return Todoyu::$CONFIG['EXT'][$extKey]['info']['version'];
	}



	/**
	 * Load all extensions
	 */
	public static function loadAllExtensions() {
		self::loadAllBoot();
		self::initExtensions();
	}

}

?>