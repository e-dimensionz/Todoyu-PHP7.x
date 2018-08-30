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
 * System and extension repository controller
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRepositoryActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('sysmanager', 'extensions:modify');
	}



	/**
	 * Get rendered list of available extensions updates
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function searchAction(array $params) {
		$query	= trim($params['query']);

		TodoyuSysmanagerRepositoryManager::saveLastSearchKeyword($query);

		return TodoyuSysmanagerRepositoryRenderer::renderSearchResults($query);
	}



	/**
	 * Install update of todoyu core
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function installCoreUpdateAction(array $params) {
		$result		= TodoyuSysmanagerRepositoryManager::installCoreUpdate();

		if( $result !== true ) {
			TodoyuHeader::sendTodoyuError($result);
		}
	}



	/**
	 * Install extension update from tER
	 *
	 * @param	Array	$params
	 */
	public function installExtensionUpdateAction(array $params) {
		$ext	= trim($params['extkey']);

		$result	= TodoyuSysmanagerRepositoryManager::installExtensionUpdate($ext);

		if( $result !== true ) {
			TodoyuHeader::sendTodoyuError($result);
		}
	}



	/**
	 * Get list with available updates
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function refreshUpdateListAction(array $params) {
		return TodoyuSysmanagerRepositoryRenderer::renderUpdate();
	}



	/**
	 * Install an extension from tER
	 *
	 * @param	Array	$params
	 */
	public function installTerExtensionAction(array $params) {
		$extKey			= trim($params['extkey']);
		$majorVersion	= intval($params['major']);

		$result	= TodoyuSysmanagerRepositoryManager::installExtensionFromTER($extKey, $majorVersion);

		if( $result !== true ) {
			TodoyuHeader::sendTodoyuError($result);
		}
	}



	/**
	 * Get dialog for extension update
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function updateDialogAction(array $params) {
		$ext	= trim($params['extension']);

		return TodoyuSysmanagerRepositoryRenderer::renderExtensionUpdateDialog($ext);
	}



	/**
	 * Get dialog for extension installation
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function installDialogAction(array $params) {
		$ext	= trim($params['extension']);
		$isLocal= intval($params['local']) === 1;

		return TodoyuSysmanagerRepositoryRenderer::renderExtensionInstallDialog($ext, $isLocal);
	}



	/**
	 * Get dialog for core update
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function coreUpdateDialogAction(array $params) {
		return TodoyuSysmanagerRepositoryRenderer::renderCoreUpdateDialog();
	}

}

?>