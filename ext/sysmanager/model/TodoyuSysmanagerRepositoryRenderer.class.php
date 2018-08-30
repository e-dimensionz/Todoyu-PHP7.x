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
 * Render updates screens
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRepositoryRenderer {

	/**
	 * Fetch available updates from tER server and render listing
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function renderSearch(array $params = array()) {
		if( isset($params['query']) ) {
			$query	= trim($params['query']);
		} else {
			$query	= TodoyuSysmanagerRepositoryManager::getLastSearchKeyword();
			$params['query'] = $query;
		}

		$xmlPath	= 'ext/sysmanager/config/form/repository-search.xml';
		$form		= TodoyuFormManager::getForm($xmlPath);
		$form->setFormData($params);
		$form->setUseRecordID(false);

		$tmpl	= 'ext/sysmanager/view/repository/search.tmpl';
		$data	= array(
			'query'		=> $query,
			'form'		=> $form->render(),
			'results'	=> self::renderSearchResults($query)
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Search available extensions updates
	 *
	 * @param	String	$query
	 * @return	String
	 */
	public static function renderSearchResults($query) {
		$repository	= new TodoyuSysmanagerRepository();

		$tmpl	= 'ext/sysmanager/view/repository/search-list.tmpl';

		try {
			$data	= $repository->searchExtensions($query);
		} catch(TodoyuSysmanagerRepositoryConnectionException $e) {
			return self::renderConnectionError($e->getMessage());
		} catch(TodoyuSysmanagerRepositoryException $e) {
			return self::renderRepositoryGeneralError($e->getMessage());
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Find available updates for current client and render updates list
	 *
	 * @return	String
	 */
	public static function renderUpdate() {
		$repository	= new TodoyuSysmanagerRepository();

		try {
			$updates	= $repository->searchUpdates();
		} catch(TodoyuSysmanagerRepositoryConnectionException $e) {
			return self::renderConnectionError($e->getMessage());
		} catch(TodoyuSysmanagerRepositoryException $e) {
			return self::renderRepositoryGeneralError($e->getMessage());
		}

		$tmpl	= 'ext/sysmanager/view/repository/update.tmpl';
		$data	= array(
			'updates'	=> $updates
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render repository connection problem
	 *
	 * @param	String		$message
	 * @return	String
	 */
	private static function renderConnectionError($message) {
		$tmpl	= 'ext/sysmanager/view/repository/connection-error.tmpl';
		$data	= array(
			'message'	=> $message
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render repository general error
	 *
	 * @param	String		$message
	 * @return	String
	 */
	private static function renderRepositoryGeneralError($message) {
		$tmpl	= 'ext/sysmanager/view/repository/general-error.tmpl';
		$data	= array(
			'message'	=> $message
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render dialog for extension update
	 *
	 * @param  String	$extKey
	 * @return String
	 */
	public static function renderExtensionUpdateDialog($extKey) {
		$info	= TodoyuSysmanagerRepositoryManager::getRepoInfo($extKey);

		$data	= array(
			'info'			=> $info,
			'title'			=> 'Update: ' . $info['title'],
			'actionOk'		=> 'Todoyu.Ext.sysmanager.Repository.Update.installExtensionUpdate(\'' . $extKey . '\')',
			'labelCancel'	=> Todoyu::Label('sysmanager.repository.extension.installUpdate.cancel'),
			'labelOk'		=> Todoyu::Label('sysmanager.repository.extension.installUpdate')
		);

		return self::renderExtensionDialog($data, true);
	}



	/**
	 * Render install dialog for extension
	 *
	 * @param	String		$extKey
	 * @param	Boolean		$isLocal		Is extension already imported locally?
	 * @return	String
	 */
	public static function renderExtensionInstallDialog($extKey, $isLocal = false) {
		$info	= TodoyuSysmanagerRepositoryManager::getRepoInfo($extKey);

		$majorVersion	= TodoyuSysmanagerExtManager::parseMajorVersion($info['version']['version']);

		$data	= array(
			'info'			=> $info,
			'title'			=> 'Install: ' . $info['title'],
			'domain'		=> TodoyuServer::getDomain(),
			'labelCancel'	=> Todoyu::Label('sysmanager.repository.extension.install.cancel'),
			'isMajorUpdate'	=> TodoyuExtensions::isInstalled($extKey)
		);

			// Different OK actions for TER and local extensions
		if( $isLocal ) {
			$data['actionOk']	= 'Todoyu.Ext.sysmanager.Extensions.Install.installAndLicenseImportedExtension(\'' . $extKey . '\')';
		} else {
			$data['actionOk']	= 'Todoyu.Ext.sysmanager.Repository.Search.installExtension(\'' . $extKey . '\', ' . $majorVersion. ')';
		}

		$labelInstall		= Todoyu::Label('sysmanager.repository.extension.install');

			// Check for free licences if commercial
		if( $info['commercial'] ) {
			if( $info['installStatus'] === 'noLicense' ) {
				$data['labelOk']	= Todoyu::Label('sysmanager.repository.license.required');
				$data['disableOk']	= true;
				$data['licenseInfo']= Todoyu::Label('sysmanager.repository.license.status.noLicense');
			} elseif( $info['installStatus'] === 'licensed' ) {
				$data['labelOk']	= $labelInstall;
				$data['disableOk']	= false;
				$data['licenseInfo']= Todoyu::Label('sysmanager.repository.license.status.licensed');
			} elseif( $info['installStatus'] === 'freeLicense' ) {
				$labelUseLicense	= Todoyu::Label('sysmanager.repository.license.useLicense');
				$data['labelOk']	= $labelInstall . ' (' . $labelUseLicense . ')';
				$data['disableOk']	= false;
				$data['licenseInfo']= Todoyu::Label('sysmanager.repository.license.status.freeLicense');
			}
		} else {
			$data['labelOk']	= $labelInstall;
			$data['disableOk']	= false;
		}

		if( $info['commercial'] && $info['license'] ) {
			$data['license']	= TodoyuSysmanagerRepositoryManager::getExtensionLicenseText($info['license']);

			if( $info['commercial'] ) {
				$data['disableOk']	= true;
			}
		}
		
		return self::renderExtensionDialog($data);
	}



	/**
	 * Render dialog window for extension installation/update
	 *
	 * @param	Array		$data
	 * @param	Boolean		$isUpdate
	 * @return	String
	 */
	private static function renderExtensionDialog(array $data, $isUpdate = false) {
		$tmpl	= 'ext/sysmanager/view/repository/dialog-ext.tmpl';

		$data['update']		= $isUpdate;
		$data['install']	= !$isUpdate;
		$data['dialogClass']= $isUpdate ? 'extUpdate' : 'extInstall';

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render dialog for core update
	 *
	 * @return	String
	 */
	public static function renderCoreUpdateDialog() {
		$tmpl		= 'ext/sysmanager/view/repository/dialog-core.tmpl';
		$coreUpdate	= TodoyuSysmanagerRepositoryManager::getRepoInfo('core');

		$data	= array(
			'update'	=> $coreUpdate
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>