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
 * Extension installer
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerExtInstaller {

	/**
	 * Call setup function of extension if available
	 *
	 * @param	String		$extKey
	 * @param	String		$action
	 * @param	Array		$params
	 */
	private static function callSetup($extKey, $action = 'install', array $params = array()) {
		$className	= 'Todoyu' . ucfirst(strtolower(trim($extKey))) . 'Setup';
		$method		= $action;

		array_unshift($params, $extKey);

		if( class_exists($className, true) ) {
			if( method_exists($className, $method) ) {
				call_user_func_array(array($className, $method), $params);
			}
		}
	}



	/**
	 * Call and action on all other extension setup classes
	 *
	 * @param	String		$ignoreExt		This extension will be ignored
	 * @param	String		$action
	 */
	private static function callOtherSetups($ignoreExt, $action) {
		$extKeys	= TodoyuExtensions::getInstalledExtKeys();

		foreach($extKeys as $extKey) {
			if( $extKey !== $ignoreExt ) {
				self::callSetup($extKey, $action, array($extKey, $ignoreExt));
			}
		}
	}



	/**
	 * Call 'afterOtherExtensionInstall' for all other extensions
	 *
	 * @param	String		$extKey
	 */
	public static function callAfterOtherExtensionInstall($extKey) {
		self::callOtherSetups($extKey, 'afterOtherExtensionInstall');
	}



	/**
	 * Call 'afterOtherExtensionUninstall' for all other extensions
	 *
	 * @param	String		$extKey
	 */
	public static function callAfterOtherExtensionUninstall($extKey) {
		self::callOtherSetups($extKey, 'afterOtherExtensionUninstall');
	}



	/**
	 * Call 'beforeUpdate' for the extension
	 *
	 * @param	String		$extKey
	 */
	public static function callBeforeUpdate($extKey) {
		self::callSetup($extKey, 'beforeUpdate');
	}



	/**
	 * Call 'afterUpdate' for the extension
	 *
	 * @param	String		$extKey
	 * @param	String		$previousVersion
	 */
	public static function callAfterUpdate($extKey, $previousVersion) {
		$currentVersion	= TodoyuExtensions::getExtVersion($extKey);

		self::callSetup($extKey, 'afterUpdate', array($previousVersion, $currentVersion));
	}



	/**
	 * Call 'beforeUpdate' for the extension
	 *
	 * @param	String		$extKey
	 * @param	String		$previousVersion
	 * @param	String		$currentVersion
	 */
	public static function callBeforeDbUpdate($extKey, $previousVersion, $currentVersion) {
		self::callSetup($extKey, 'beforeDbUpdate', array($previousVersion, $currentVersion));
	}



	/**
	 * Call 'afterInstall' for the extension
	 *
	 * @param	String		$extKey
	 */
	public static function callAfterInstall($extKey) {
		self::callSetup($extKey, 'afterInstall');
		self::callAfterOtherExtensionInstall($extKey);
	}



	/**
	 * Call 'beforeUninstall' for extension
	 *
	 * @param	String		$extKey
	 */
	public static function callBeforeUninstall($extKey) {
		$currentVersion	= TodoyuExtensions::getExtVersion($extKey);

		self::callSetup($extKey, 'beforeUninstall', array($currentVersion));
	}



	/**
	 * Call 'beforeMajorUpdate' for an extension
	 *
	 * @param	String		$extKey
	 * @param	String		$nextVersion
	 */
	public static function callBeforeMajorUpdate($extKey, $nextVersion) {
		$currentVersion	= TodoyuExtensions::getExtVersion($extKey);

		self::callSetup($extKey, 'beforeMajorUpdate', array($currentVersion, $nextVersion));
	}



	/**
	 * Call 'afterMajorUpdate' for an extension
	 *
	 * @param	String		$extKey
	 * @param	String		$previousVersion
	 */
	public static function callAfterMajorUpdate($extKey, $previousVersion) {
		$currentVersion	= TodoyuExtensions::getExtVersion($extKey);

		self::callSetup($extKey, 'afterMajorUpdate', array($previousVersion, $currentVersion));
	}



	/**
	 * Save extensions as installed in extensions.php config file
	 *
	 * @param	Array		$extensions
	 */
	public static function saveInstalledExtensions(array $extensions) {
			// Update global config array
		Todoyu::$CONFIG['EXT']['installed'] = $extensions;

		$file	= TodoyuFileManager::pathAbsolute('config/extensions.php');
		$tmpl	= TodoyuFileManager::pathAbsolute('ext/sysmanager/asset/template/extensions.php.tmpl');
		$data	= array(
			'extensions'	=> $extensions
		);

		TodoyuFileManager::saveTemplatedFile($file, $tmpl, $data);
	}



	/**
	 * Install an extension (update extension config file)
	 *
	 * @param	String		$extKey
	 * @param	Boolean		$noDbUpdate		Don't update database
	 */
	public static function installExtension($extKey, $noDbUpdate = false) {
			// Add given ext key to  list of installed extensions
		$extKeys	= TodoyuExtensions::getInstalledExtKeys();
		$extKeys[]	= $extKey;

			// Remove duplicate entries
		$extKeys	= array_unique($extKeys);

			// Save installed extensions config file (config/extensions.php)
		self::saveInstalledExtensions($extKeys);

		TodoyuAutoloader::reload();

		$currentVersion	= TodoyuExtensions::getExtVersion($extKey);

		self::callBeforeDbUpdate($extKey, $currentVersion, $currentVersion);

		if( !$noDbUpdate ) {
			self::updateDatabaseFromFiles();
		}

		self::callAfterInstall($extKey);

		TodoyuHookManager::callHook('sysmanager', 'extensionInstalled', array($extKey, $noDbUpdate));
	}



	/**
	 * Update the database from files of all installed extensions
	 */
	public static function updateDatabaseFromFiles() {
		TodoyuSQLManager::updateDatabaseFromTableFiles();
	}



	/**
	 * Uninstall an extension (update extension config file)
	 *
	 * @param	String		$extKey
	 */
	public static function uninstallExtension($extKey) {
		self::callBeforeUninstall($extKey);

			// Get installed extensions with ext key as array key
		$installed	= array_flip(Todoyu::$CONFIG['EXT']['installed']);

			// Remove extension key from list
		unset($installed[$extKey]);

			// Get the list of extension keys
		$installed	= array_keys($installed);

			// Save installed extensions
		self::saveInstalledExtensions($installed);
		self::callAfterOtherExtensionUninstall($extKey);
	}



	/**
	 * Check whether an extension can be installed
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function canInstall($extKey) {
		return self::getInstallProblems($extKey) === false;
	}



	/**
	 * Collect all reasons which prevent the installation of an extension
	 *
	 * @param	String			$extKey
	 * @return	Array|Boolean	False or a list of problems
	 */
	public static function getInstallProblems($extKey) {
		$foundConflicts		= array();
		$foundDependencies	= array();
		$requiredCore		= false;
		$extInfo			= TodoyuExtensions::getExtInfo($extKey);

			// Check core version
		if( ! self::matchesCoreVersion($extKey) ) {
			$requiredCore	= $extInfo['constraints']['core'];
		}

			// Check conflicts
		$conflicts	= TodoyuExtensions::getConflicts($extKey);

		foreach($conflicts as $conflict) {
			if( TodoyuExtensions::isInstalled($conflict) ) {
				$foundConflicts[] = $conflict;
			}
		}

			// Check dependencies
		$dependencies	= TodoyuExtensions::getDependencies($extKey);

		foreach($dependencies as $ext => $version) {
			if( TodoyuExtensions::isInstalled($ext) ) {
				$extInfo	= TodoyuExtensions::getExtInfo($ext);
				if( version_compare($extInfo['version'], $version) === -1 ) {
					$foundDependencies[$ext] = $version;
				}
			} else {
				$foundDependencies[$ext] = $version;
			}
		}

			// Return false if no problems were found
		if( empty($foundConflicts) && empty($foundDependencies)  && !$requiredCore ) {
			return false;
		} else {
			return array(
				'conflicts'	=> $foundConflicts,
				'depends'	=> $foundDependencies,
				'core'		=> $requiredCore
			);
		}
	}



	/**
	 * Check whether extension core requirement is fulfilled
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function matchesCoreVersion($extKey) {
		$extInfo	= TodoyuExtensions::getExtInfo($extKey);

		if( $extInfo['constraints']['core'] ) {
			return version_compare(TODOYU_VERSION, $extInfo['constraints']['core']) !== -1;
		}

		return true;
	}



	/**
	 * Get all status array (required vs. installed version) of all missing extensions which are dependencies of given extension
	 *
	 * @param	String	$extKey
	 * @return	Array
	 */
	public static function getFailedDependencies($extKey) {
		$missingDependencies	= array();

			// Are there any dependencies?
		if( TodoyuExtensions::hasDependencies($extKey) ) {
			$dependencies	= TodoyuExtensions::getDependencies($extKey);

			foreach($dependencies as $neededExtKey => $neededExtVersion) {
					// Required dependency installed?
				$dependencyMet		= TodoyuExtensions::isInstalled($neededExtKey);
				$installedExtVersion= $dependencyMet ? TodoyuExtensions::getVersion($neededExtKey) : '';

					// Installed ext version up-to-date of required version?
				if( $dependencyMet && ! TodoyuNumeric::isVersionAtLeast($installedExtVersion, $neededExtVersion) ) {
					$dependencyMet  = false;
				}

				if( ! $dependencyMet ){
					$missingDependencies[$neededExtKey]	= array(
						'versionRequired'	=> $neededExtVersion,
						'versionInstalled'	=> $installedExtVersion,
					);
				}
			}
		}

		return $missingDependencies;
	}



	/**
	 * Get textual list of failed dependencies of given extension
	 *
	 * @param	String	$extKey
	 * @return	String
	 */
	public static function getFailedDependenciesList($extKey) {
		$list				= array();
		$failedDependencies	= self::getFailedDependencies($extKey);

		foreach($failedDependencies as $extKey => $conformance) {
			$list[]= $extKey . ' version ' . $conformance['versionRequired'];
		}

		return implode(', ', $list);
	}



	/**
	 * Check whether an extension can be uninstalled
	 * Check for: dependents, system
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function canUninstall($extKey) {
		$noDependents	= !TodoyuExtensions::hasDependents($extKey);
		$notSystem		= !TodoyuExtensions::isSystemExtension($extKey);

		return $noDependents && $notSystem;
	}



	/**
	 * Check constraints of the extension
	 * Core version, dependent extensions, conflicts
	 *
	 * @throws	TodoyuSysmanagerInstallerException
	 * @param	String		$ext
	 * @param	Array		$constraints
	 * @return	Boolean
	 */
	public static function checkConstraints($ext, array $constraints = null) {
		$depends	= TodoyuArray::assure($constraints['depends']);

			// Load constraints if not given
		if( is_null($constraints) ) {
			$constraints	= TodoyuExtensions::getExtInfo($ext);
		}

			// Check core version
		if( isset($constraints['core']) ) {
			if( version_compare($constraints['core'], TODOYU_VERSION) === 1 ) {
				throw new TodoyuSysmanagerInstallerException(Todoyu::Label('sysmanager.extension.install.error.core') . ': ' . TODOYU_VERSION . ' < ' . $constraints['core']);
			}
		}

			// Check if all dependencies are ok
		foreach($depends as $extKey => $requiredVersion) {
			if( ! TodoyuExtensions::isInstalled($extKey) ) {
				throw new TodoyuSysmanagerInstallerException(Todoyu::Label('sysmanager.extension.install.error.missing') . ': ' . $extKey);
			}
			$installedVersion	= TodoyuExtensions::getVersion($extKey);

			if( version_compare($requiredVersion, $installedVersion) === 1 ) {
				throw new TodoyuSysmanagerInstallerException(Todoyu::Label('sysmanager.extension.install.error.lowVersion') . ': ' . $extKey . ' - ' . $installedVersion . ' < ' . $requiredVersion);
			}
		}

			// Check if the extension conflicts with an installed one
		$installedConflicts	= TodoyuExtensions::getConflicts($ext);

		if( !empty($installedConflicts) ) {
			throw new TodoyuSysmanagerInstallerException(Todoyu::Label('sysmanager.extension.install.error.conflicts') . ': ' . implode(', ', $installedConflicts));
		}

			// Check if the extension has conflicts with an installed extension
		$extConflicts	= TodoyuArray::assure($constraints['conflicts']);
		$installedExts	= TodoyuExtensions::getInstalledExtKeys();
		$foundConflicts	= array_intersect($extConflicts, $installedExts);

		if( !empty($foundConflicts) ) {
			throw new TodoyuSysmanagerInstallerException(Todoyu::Label('sysmanager.extension.install.error.conflicts') . ': ' . implode(', ', $foundConflicts));
		}

		return true;
	}



	/**
	 * Get error message for failed uninstall
	 *
	 * @param	String		$extKey
	 * @return	String
	 */
	public static function getUninstallFailReason($extKey) {
		$message	= 'Unknown problem';

		if( TodoyuExtensions::hasDependents($extKey) ) {
			$dependents	= TodoyuExtensions::getDependents($extKey);
			$extInfos	= TodoyuSysmanagerExtManager::getExtInfos($extKey);

			$message	= Todoyu::Label('sysmanager.extension.uninstall.cannotuninstall') . ' "' . htmlspecialchars($extInfos['title'], ENT_QUOTES, 'UTF-8', false) . '" (' . $extKey . ').<br />' . Todoyu::Label('sysmanager.extension.uninstall.thefollowingextdependsonit') . implode(', ', $dependents);
		} elseif( TodoyuExtensions::isSystemExtension($extKey) ) {
			$extInfos	= TodoyuSysmanagerExtManager::getExtInfos($extKey);
			$message	= '"' . htmlentities($extInfos['title'], ENT_QUOTES, 'UTF-8', false) . '" ' . Todoyu::Label('sysmanager.extension.uninstall.error.sysext');
		}

		return $message;
	}



	/**
	 * Download an extension: Pack all extension files into an archive and send it to the browser
	 *
	 * @param	String		$extKey
	 */
	public static function downloadExtension($extKey) {
		$archivePath= TodoyuSysmanagerArchiver::createExtensionArchive($extKey);
		$fileName	= self::getExtensionArchiveName($extKey);
		$mimeType	= 'application/octet-stream';

		try {
			// Send file for download and delete temporary ZIP file after download
			TodoyuFileManager::sendFile($archivePath, $mimeType, $fileName);
			unlink($archivePath);
		} catch(TodoyuExceptionFileDownload $e) {
			// @todo catch error
		}
	}



	/**
	 * Get archive name for an extension
	 * Pattern: TodoyuExt_{EXTKEY}_{VERSION}.zip
	 *
	 * @param	String		$extKey
	 * @return	String
	 */
	public static function getExtensionArchiveName($extKey) {
		$extInfo	= TodoyuExtensions::getExtInfo($extKey);
		$version	= TodoyuString::getVersionInfo($extInfo['version']);

		return 'TodoyuExt_' . $extKey . '_' . $version['major'] . '.' . $version['minor'] . '.' . $version['revision'] . '.zip';
	}



	/**
	 * Assemble filename for an archive file of an extension with the given credentials
	 *
	 * @param	String	$extKey
	 * @param	String	$versionMajor
	 * @param	String	$versionMinor
	 * @param	String	$versionRevision
	 * @return	String
	 */
	public static function buildExtensionArchiveName($extKey, $versionMajor, $versionMinor, $versionRevision) {
		return 'TodoyuExt_' . $extKey . '_' . $versionMajor . '.' . $versionMinor . '.' . $versionRevision . '.zip';
	}



	/**
	 * Parse given (archive's) filename: extract attributes: ext, version, data
	 *
	 * @param	String		$archiveName
	 * @return	Array|Boolean
	 */
	public static function parseExtensionArchiveName($archiveName) {
		if( strncasecmp($archiveName, 'TodoyuExt_', 10) !== 0 ) {
			return false;
		}

		$fileInfo	= explode('_', $archiveName);

		if( sizeof($fileInfo) !== 3 ) {
			return false;
		}

		$version	= TodoyuString::getVersionInfo($fileInfo[2]);

		$info	= array(
			'ext'		=> trim(strtolower($fileInfo[1])),
			'version'	=> $version
		);

		return $info;
	}



	/**
	 * Remove extension folder from server
	 *
	 * @param	String		$ext
	 * @return	Boolean
	 */
	public static function removeExtensionFromServer($ext) {
		$extPath	= TodoyuExtensions::getExtPath($ext);

		TodoyuFileManager::deleteFolder($extPath);

		return !is_dir($extPath);
	}

}

?>