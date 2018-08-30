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
 * Render rights editor manager
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRightsEditorManager {

	/**
	 * Cache for loaded ext rights
	 *
	 * @var	Array
	 */
	private static $extRights = array();



	/**
	 * Check whether an extension has a rights config XML file
	 * File: ext/EXTKEY/config/rights.xml
	 *
	 * @param	String		$extKey		Extension key
	 * @return	Boolean
	 */
	public static function hasRightsConfig($extKey) {
		return is_file( PATH_EXT . '/' . $extKey . '/config/rights.xml');
	}



	/**
	 *  Read the rights.xml of an extension
	 *
	 * @param	String		$extKey		Extension key
	 * @return	Array
	 */
	public static function getExtRights($extKey) {
		if( self::hasRightsConfig($extKey) ) {
			$xmlFile		= TodoyuExtensions::getExtPath($extKey) . '/config/rights.xml';
			self::$extRights= self::readXML($extKey, $xmlFile);
		}

		return self::$extRights;
	}

	

	/**
	 * Check whether extension defines a specific right
	 * This doesn't mean the user has grant for this right
	 *
	 * @param	String		$extKey
	 * @param	String		$right
	 * @return	Boolean
	 */
	public static function hasRight($extKey, $right) {
		$extRights					= self::getExtRights($extKey);
		list($section, $rightName)	= explode(':', $right, 2);

		return isset($extRights[$section]['rights'][$rightName]);
	}



	/**
	 * Read an XML file into a rights array
	 *
	 * @param	String		$extKey
	 * @param	String		$xmlFile		Path to XML file
	 * @return	Array
	 */
	public static function readXML($extKey, $xmlFile) {
		$xmlFile	= TodoyuFileManager::pathAbsolute($xmlFile);
		$localeKey	= $extKey . '.rights';
		$data		= array();

		$xml		= simplexml_load_file($xmlFile);

			// Load sections
		foreach($xml->section as $section) {
			$sectionName	= (string)$section['name'];

			$data[$sectionName] = array();

			$data[$sectionName]['label']	= Todoyu::Label($localeKey . '.' . $sectionName);
			$data[$sectionName]['rights']	= array();

			if( $section['require'] ) {
				$sectionRequire	= explode(',', $section['require']);
			} else {
				$sectionRequire	= array();
			}

			foreach($section->right as $right) {
				$rightName = (string)$right['name'];

				$data[$sectionName]['rights'][$rightName] = array(
					'right'		=> $rightName,
					'full'		=> $sectionName . ':' . $rightName,
					'label'		=> Todoyu::Label($localeKey . '.' . $sectionName . '.' . $rightName),
					'comment'	=> TodoyuLabelManager::getLabelOrEmpty($localeKey . '.' . $sectionName . '.' . $rightName . '.comment'),
					'require'	=> array()
				);

				$rightRequire	= $right['require'] ? explode(',', $right['require']) : array();

				$data[$sectionName]['rights'][$rightName]['require'] = array_merge($sectionRequire, $rightRequire);
			}
		}

		return $data;
	}



	/**
	 * Get dependent rights of a right
	 *
	 * @param	Array		$rightsConfig
	 * @param	String		$rightToCheck
	 * @return	Array
	 */
	public static function getDependents(array $rightsConfig, $rightToCheck) {
		$dependents	= array();

		foreach($rightsConfig as $rights) {
			foreach($rights as $right => $rightConfig) {
				if( in_array($rightToCheck, $rightConfig['depends']) ) {
					$dependents[] = $right;
				}
			}
		}

		return $dependents;
	}



	/**
	 * Extract the required info from rights
	 *
	 * @param	Array		$rightsConfig		Rights with sections
	 * @return	Array
	 */
	public static function extractRequiredInfos(array $rightsConfig) {
		$require = array();

		foreach($rightsConfig as $sectionName => $section) {
			foreach($section['rights'] as $right) {
				$require[$sectionName . ':' . $right['right']] = $right['require'];
			}
		}

		return $require;
	}



	/**
	 * Get all dependencies between the rights
	 *
	 * @param	Array		$rightsConfig
	 * @return	Array
	 */
	public static function getAllDependencies(array $rightsConfig) {
		$dependencies	= array();

		foreach($rightsConfig as $rights) {
			foreach($rights as $right => $rightConfig) {
				$dependencies[$right] = self::getDependents($rightsConfig, $right);
			}
		}

		return $dependencies;
	}



	/**
	 * Save role rights
	 *
	 * @param	String		$extKey		Extension key
	 * @param	Array		$rights		Submitted rights
	 * @param	Array		$roleIDs
	 */
	public static function saveRoleRights($extKey, array $rights, array $roleIDs) {
		$extID	= TodoyuExtensions::getExtID($extKey);

			// Delete the rights of the selected roles
		foreach($roleIDs as $idRole) {
			TodoyuRightsManager::deleteExtRoleRights($extID, $idRole);
		}

			// Add new rights
		foreach($rights as $rightName => $allowedRoles) {
			foreach($allowedRoles as $idRole => $dummy) {
				TodoyuRightsManager::setRight($extID, $idRole, $rightName);
			}
		}

		TodoyuRightsManager::saveChangeTime();
		TodoyuRightsManager::reloadRights();
	}



	/**
	 * Get the current active extension to edit
	 * If non is selected yet, use sysmanager
	 *
	 * @return	String
	 */
	public static function getCurrentExtension() {
		$ext	= TodoyuPreferenceManager::getPreference(EXTID_SYSMANAGER, 'ext');

		if( !$ext ) {
			$ext = 'sysmanager';
		}

		return $ext;
	}



	/**
	 * Save the currently edited extension
	 *
	 * @param	String		$ext
	 */
	public static function saveCurrentExtension($ext) {
		TodoyuPreferenceManager::savePreference(EXTID_SYSMANAGER, 'ext', $ext, 0, true);
	}



	/**
	 * Get preference: active tab of rights editor
	 *
	 * @return	String
	 */
	public static function getActiveTab() {
		$tab	= TodoyuSysmanagerPreferences::getActiveTab('rights');

		if( !$tab ) {
			$tab = 'rights';
		}

		return $tab;
	}



	/**
	 * Save preference: active tab of rights editor
	 *
	 * @param	String	$tab
	 */
	public static function saveActiveTab($tab) {
		TodoyuSysmanagerPreferences::saveActiveTab('rights', $tab);
	}



	/**
	 * Get custom set
	 *
	 * @param	Array	$rights
	 * @param	String	$ext
	 * @return	Array
	 */
	public static function getCurrentActiveRights(array $rights, $ext) {
		$roleRights		= TodoyuRightsManager::getExtRoleRights($ext);
		$activeRights	= array();

		foreach($roleRights as $idRole => $rightKeys) {
			foreach($rightKeys as $rightKey) {
				$activeRights[$rightKey][$idRole] = true;
			}
		}

		return $activeRights;
	}

}

?>