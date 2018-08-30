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
 * Extension management renderer
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerExtManagerRenderer {

	/**
	 * Render extension module
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderModule(array $params) {
		$extKey	= trim($params['extkey']);
		$tab	= trim($params['tab']);

		$tabs	= self::renderTabs($extKey, $tab);
		$body	= self::renderBody($extKey, $tab, $params);

		return TodoyuRenderer::renderContent($body, $tabs);
	}



	/**
	 * Render extension management module content
	 *
	 * @param	Array		$params		All request params
	 * @return	String
	 */
	public static function renderModuleContent(array $params) {
		Todoyu::restrict('sysmanager', 'general:extensions');

		$extKey	= $params['extkey'];
		$tab	= $params['tab'];

		return self::renderBody($extKey, $tab, $params);
	}



	/**
	 * Render extension manager tabs
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderModuleTabs(array $params) {
		$extKey	= $params['extkey'];
		$tab	= $params['tab'];

		return self::renderTabs($extKey, $tab);
	}



	/**
	 * Render tabbed view
	 *
	 * @param	String		$extKey			Extension key of active extension
	 * @param	String		$tab			Active tab
	 * @param	Array		$params			Request parameters
	 * @return	String		HTML content for tabbed view
	 */
	public static function renderBody($extKey = '', $tab = '', array $params = array()) {
		switch($tab) {
			case 'info':
				$content = self::renderInfo($extKey, $params);
				break;

			case 'config':
				$content = self::renderConfig($extKey, $params);
				break;

			case 'imported':
				$content = self::renderListImported($params);
				break;

			case 'update':
				$content = self::renderUpdate($params);
				break;

			case 'search':
				$content = self::renderSearch($params);
				break;

			case 'installed':
			default:
				$content = self::renderListInstalled($params);
				break;
		}

			// Call hook for possible content modifications
		$hookName		= 'renderExtContent.' . $extKey;
		$hookResults	= TodoyuHookManager::callHook('sysmanager', $hookName, array($tab, $params, $content));

		if( is_array($hookResults) && ! empty($hookResults[0]) ) {
			$content	= $hookResults[0];
		}

		return $content;
	}



	/**
	 * Render tabs based on current settings
	 *
	 * @param	String		$extKey		Extension key
	 * @param	String		$tab		Active tab key
	 * @return	String
	 */
	public static function renderTabs($extKey = '', $tab = '') {
		$name		= 'extension';
		$class		= 'sysmanager';
		$jsHandler	= 'Todoyu.Ext.sysmanager.Extensions.onTabClick.bind(Todoyu.Ext.sysmanager.Extensions)';
		$tabs		= TodoyuSysmanagerExtManager::getTabConfig($extKey, $tab);

		if( empty($tab) ) {
			$active	= $tabs[0]['id'];
		} elseif( empty($extKey) ) {
			$active	= $tab;
		} else {
			$active	= $extKey . '_' . $tab;
		}

			// Remove config tab if extension has no configuration
		if( ! TodoyuSysmanagerExtManager::extensionHasConfig($extKey) ) {
			foreach($tabs as $index => $tabConfig) {
				if( $tabConfig['id'] === $extKey . '_config' ) {
					unset($tabs[$index]);
					break;
				}
			}
		}

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $active, $class);
	}



	/**
	 * Render list of (installed / imported) extensions
	 *
	 * @param	Array		$extKeys
	 * @param	Boolean		$areInstalled
	 * @return	String
	 */
	public static function renderList(array $extKeys, $areInstalled = true) {
		sort($extKeys);

		$state	= $areInstalled ? 'installed' : 'imported';
		$tmpl	= 'ext/sysmanager/view/extension/list-' . $state . '.tmpl';

		$data	= array(
			'extensions'	=> array()
		);

		foreach($extKeys as $extension) {
			$data['extensions'][$extension] = TodoyuExtensions::getExtInfo($extension);
		}
		
		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render list of installed extensions
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderListInstalled(array $params = array()) {
		$extKeys	= TodoyuExtensions::getInstalledExtKeys();

		return self::renderList($extKeys, true);
	}



	/**
	 * Render list of imported/installable extensions
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function renderListImported(array $params = array()) {
		$extKeys	= TodoyuExtensions::getNotInstalledExtKeys();

		return self::renderList($extKeys, false);
	}



	/**
	 * Render extension info
	 *
	 * @param	String		$extKey		Extension key
	 * @param	Array		$params		Request parameters
	 * @return	String
	 */
	public static function renderInfo($extKey, array $params = array()) {
		$info	= TodoyuSysmanagerExtManager::getExtInfos($extKey);

		$tmpl	= 'ext/sysmanager/view/extension/info.tmpl';
		$data	= array(
			'ext'	=> $info,
			'extKey'=> $extKey
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render extension info
	 *
	 * @param	String		$extKey		Extension key
	 * @param	Array		$params		Request parameters
	 * @return	String
	 */
	public static function renderConfig($extKey, array $params = array()) {
		return TodoyuSysmanagerExtConfRenderer::renderConfig($extKey);
	}



	/**
	 * Render updates listing
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function renderUpdate(array $params = array()) {
		return TodoyuSysmanagerRepositoryRenderer::renderUpdate($params);
	}



	/**
	 * Render extension tER search
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function renderSearch(array $params = array()) {
		return TodoyuSysmanagerRepositoryRenderer::renderSearch($params);
	}

}

?>