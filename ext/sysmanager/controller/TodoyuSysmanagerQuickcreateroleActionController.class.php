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
class TodoyuSysmanagerQuickCreateRoleActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrictAdmin();
	}



	/**
	 * Get quick role creation form rendered
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		return TodoyuSysmanagerRoleEditorRenderer::renderRoleQuickCreateForm($params);
	}



	/**
	 * Save role record
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function saveAction(array $params) {
		$data	= $params['role'];
		$idRole	= intval($data['id']);

			// Get form, call save hooks, set data
		$form	= TodoyuSysmanagerRoleEditorManager::getQuickCreateForm($idRole);
		$data	= TodoyuFormHook::callSaveData('ext/sysmanager/config/form/role.xml', $data, $idRole);
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

			$idRole	= TodoyuRoleManager::saveRole($storageData);

			TodoyuHeader::sendTodoyuHeader('idRole', $idRole);
			TodoyuHeader::sendTodoyuHeader('recordLabel', $storageData['title']);

			return $idRole;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}

}

?>