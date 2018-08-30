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
 * Role Action Controller
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRoleActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrictAdmin();
	}



	/**
	 * List roles
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listingAction(array $params) {
		return TodoyuListingRenderer::render('sysmanager', 'roles');
	}



	/**
	 * Edit role
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idRole	= intval($params['role']);

		return TodoyuSysmanagerRoleEditorRenderer::renderEdit($idRole);
	}



	/**
	 * Delete role
	 *
	 * @param	Array		$params
	 */
	public function deleteAction(array $params) {
		$idRole	= intval($params['role']);

		TodoyuRoleManager::deleteRole($idRole);
	}



	/**
	 * Save role (new or edit)
	 *
	 * @param	Array			$params
	 * @return	Void|String		Failure returns re-rendered form with error messages
	 */
	public function saveAction(array $params) {
		$data	= $params['role'];
		$idRole	= intval($data['id']);

			// Construct form object
		$xmlPath= 'ext/sysmanager/config/form/role.xml';
		$form	= TodoyuFormManager::getForm($xmlPath, $idRole);

			// Set form data
		$form->setFormData($data);

		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

				// Save role
			$idRoleNew	= TodoyuRoleManager::saveRole($storageData);

			TodoyuHeader::sendTodoyuHeader('idRole', $idRoleNew);
		} else {
			TodoyuHeader::sendTodoyuHeader('idRoleOld', $idRole);
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}



	/**
	 * Add a sub form to the person form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		$xmlPath	= 'ext/sysmanager/config/form/role.xml';

		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);

		return TodoyuFormManager::renderSubFormRecord($xmlPath, $fieldName, $formName, $index, $idRecord);
	}

}

?>