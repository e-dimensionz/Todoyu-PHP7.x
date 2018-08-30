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
 * Extension management
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerExtManager {

	/**
	 * Get extension module tab configuration
	 *
	 * @param	String		$extKey
	 * @param	String		$tab
	 * @return	Array
	 */
	public static function getTabConfig($extKey = '', $tab = '') {
		$extKey	= trim($extKey);
		$tabs	= array();
		$config	= Todoyu::$CONFIG['EXT']['sysmanager']['extensionTabs'];

			// Listing tab
		if( Todoyu::allowed('sysmanager', 'general:extensions') ) {
			$tabs[] = $config['installed'];
		}

			// If an extension is selected, add editor tabs
		if( $extKey !== '' ) {
				// Config
			$tab		= $config['config'];
			$tab['id']	= $extKey . '_config';
			$tabs[]		= $tab;

				// Info
			$tab			= $config['info'];
			$tab['id']		= $extKey . '_info';
			$tab['label']	= $extKey . '.ext.ext.title';
			$tabs[]			= $tab;
		} else {
				// Browse market / download tab
			if( Todoyu::allowed('sysmanager', 'extensions:modify') ) {
				$tabs[] = $config['update'];
				$tabs[] = $config['search'];
				$tabs[] = $config['imported'];
			}
		}

		return $tabs;
	}



	/**
	 * Ext information about an extension provided by the config array
	 *
	 * @param	String		$extKey
	 * @param	Boolean		$load
	 * @return	Array
	 */
	public static function getExtInfos($extKey, $load = false) {
		if( $load ) {
			$pathFile	= TodoyuExtensions::getExtPath($extKey, 'config/extinfo.php');

			TodoyuFileManager::includeFile($pathFile, true, true);
		}

		return TodoyuExtensions::getExtInfo($extKey);
	}



	/**
	 * Check whether an extension is a system extension
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function isSysExt($extKey) {
		$extInfos	= self::getExtInfos($extKey);

		return (boolean)$extInfos['constraints']['system'];
	}



	/**
	 * Add record config for automatic record editing in sysmanager extension manager
	 *
	 * @param	String		$extKey
	 * @param	String		$recordName
	 * @param	Array		$config
	 */
	public static function addRecordConfig($extKey, $recordName, array $config) {
		Todoyu::$CONFIG['EXT']['sysmanager']['records'][$extKey][$recordName] = $config;
	}



	/**
	 * Get record type config
	 *
	 * @param	String		$extKey
	 * @param	String		$recordName
	 * @return	Array
	 */
	public static function getRecordConfig($extKey, $recordName) {
		TodoyuExtensions::loadAllSysmanager();

		return TodoyuArray::assure(Todoyu::$CONFIG['EXT']['sysmanager']['records'][$extKey][$recordName]);
	}



	/**
	 * Get label for a record element
	 *
	 * @param	String		$ext
	 * @param	String		$recordName
	 * @param	Integer		$idRecord
	 * @return	String
	 */
	public static function getRecordObjectLabel($ext, $recordName, $idRecord) {
		$config	= self::getRecordConfig($ext, $recordName);
		$class	= $config['object'];

		if( class_exists($class, true) ) {
			$object	= new $class($idRecord);

			if( method_exists($object, 'getLabel') ) {
				return $object->getLabel();
			}
		}

		return 'ID: ' . $idRecord;
	}



	/**
	 * Get all record configs
	 *
	 * @param	String		$extKey
	 * @return	Array
	 */
	public static function getRecordConfigs($extKey) {
		TodoyuExtensions::loadAllSysmanager();

		$config	= Todoyu::$CONFIG['EXT']['sysmanager']['records'][$extKey];

		if( ! is_array($config) ) {
			$config = array();
		}

		return $config;
	}



	/**
	 * Get all extension record configurations
	 *
	 * @return	Array
	 */
	public static function getAllRecordsConfig() {
		$extKeys	= TodoyuExtensions::getInstalledExtKeys();
		$extRecords	= array();

		foreach($extKeys as $extKey) {
			$records	= self::getRecordConfigs($extKey);

			if( sizeof($records) > 0 ) {
				$extRecords[$extKey] = $records;
			}
		}

		return $extRecords;
	}



	/**
	 * Get record types
	 *
	 * @param	String	$extKey
	 * @return	String[]
	 */
	public static function getRecordTypes($extKey) {
		$config	= self::getRecordConfigs($extKey);

		return array_keys($config);
	}



	/**
	 * Get record list data
	 *
	 * @param	String	$extKey
	 * @param	String	$recordName
	 * @param	Array	$params
	 * @return	Array
	 */
	public static function getRecordListData($extKey, $recordName, array $params = array()) {
		$recordConfig	= self::getRecordConfig($extKey, $recordName);
		$listData		= array();

			// Get records list
		if( TodoyuFunction::isFunctionReference($recordConfig['list']) ) {
			$listData	= TodoyuFunction::callUserFunction($recordConfig['list'], $params);
		}

			// Add isDeletable flag
		$listData = self::addDeletableFlag($listData, $recordConfig['isDeletable']);

		return $listData;
	}



	/**
	 * Add deletable flag for list with given value/callback
	 *
	 * @param	Array					$list		List data
	 * @param	String|Boolean|Null		$isDeletable
	 * @return	Array
	 */
	private static function addDeletableFlag(array $list, $isDeletable) {
			// Add isDeletable flag
		if( TodoyuFunction::isFunctionReference($isDeletable) ) {
			foreach($list as $index => $record) {
				$list[$index]['isDeletable'] = TodoyuFunction::callUserFunction($isDeletable, $record['id']);
			}
		} else {
			$isDeletable = is_bool($isDeletable) ? $isDeletable :  true;
			foreach($list as $index => $record) {
				$list[$index]['isDeletable'] = $isDeletable;
			}
		}

		return $list;
	}



	/**
	 * Check whether extension has something to configure
	 *
	 * @todo	How are configs registered? Add Check
	 *
	 * @param	string	$extKey
	 * @return	Boolean
	 */
	public static function extensionHasConfig($extKey) {
		$xmlPath	= TodoyuSysmanagerExtConfManager::getXmlPath($extKey);

		return TodoyuFileManager::isFile($xmlPath);
	}



	/**
	 * Parse major version from a version string
	 *
	 * @param	String	$versionString
	 * @return	Integer
	 */
	public static function parseMajorVersion($versionString) {
		$parts	= explode('.', $versionString);

		return intval($parts[0]);
	}



	/**
	 * Get major version of an extension
	 *
	 * @param	String		$extKey
	 * @return	Integer
	 */
	public static function getMajorVersion($extKey) {
		$extVersion	= TodoyuExtensions::getExtVersion($extKey);

		return self::parseMajorVersion($extVersion);
	}

}

?>