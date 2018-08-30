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
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerConfigActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrictAdmin();
	}



	/**
	 * Update system config content body
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		return TodoyuSysmanagerSystemConfigRenderer::renderBody($params);
	}



	/**
	 * Save system configuration form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function saveSystemConfigAction(array $params) {
		$data	= TodoyuArray::assure($params['systemconfig']);

		$xml	= 'ext/sysmanager/config/form/systemconfig.xml';
		$form	= TodoyuFormManager::getForm($xml);
		$form->setFormData($data);

		if( $form->isValid() ) {
			$storageData	= $form->getStorageData();
			TodoyuSysmanagerSystemConfigManager::saveSystemConfig($storageData);
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();
		}

		return $form->render();
	}



	/**
	 * Save uploaded logo if valid
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function logoAction(array $params) {
		$logoData	= TodoyuRequest::getUploadFile('image', 'logo');

		$success	= TodoyuSysmanagerSystemConfigManager::saveLogo($logoData);

		$commands	= 'window.parent.Todoyu.Ext.sysmanager.Config.Logo.onUploadFinished(' . ($success?'true':'false') . ');';

		return TodoyuRenderer::renderUploadIFrameJsContent($commands);
	}



	/**
	 * Save password strength config
	 *
	 * @param	Array	$params
	 */
	public function savePasswordStrengthAction(array $params) {
		$data	= TodoyuArray::assure($params['passwordstrength']);

		TodoyuSysmanagerSystemConfigManager::savePasswordStrength($data);
	}



	/**
	 * Save repository config (into config\settings.php)
	 *
	 * @param	Array	$params
	 */
	public function saveRepositoryConfigAction(array $params) {
		$data	= TodoyuArray::assure($params['repository']);

		TodoyuSysmanagerSystemConfigManager::saveRepositoryConfig($data);
	}
}

?>