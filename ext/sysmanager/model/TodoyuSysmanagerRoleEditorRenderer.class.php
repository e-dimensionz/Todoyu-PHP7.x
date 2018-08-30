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
 * Role Editor Renderer
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRoleEditorRenderer {

	/**
	 * Render action buttons for role listing
	 *
	 * @param	Integer		$idRole
	 * @return	String
	 */
	public static function renderRoleActions($idRole) {
		$tmpl	= 'ext/sysmanager/view/role-actions.tmpl';
		$data	= array(
			'id'	=> intval($idRole)
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render role quick creation form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderRoleQuickCreateForm(array $params) {
		$form	= TodoyuSysmanagerRoleEditorManager::getQuickCreateForm();

			// Preset (empty) form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData('ext/sysmanager/config/form/role.xml', $formData, 0);

		$form->setFormData($formData);

		return $form->render();
	}



	/**
	 * Render edit form for role
	 *
	 * @param	Integer		$idRole
	 * @return	String
	 */
	public static function renderEdit($idRole) {
		$idRole		= intval($idRole);
		$xmlPath	= 'ext/sysmanager/config/form/role.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idRole);

		$role	= TodoyuRoleManager::getRole($idRole);

		$formData	= $role->getTemplateData(true);
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $idRole);
		$formData['persons']	= TodoyuArray::sortByLabel($formData['persons'], 'lastname');

		$form->setFormData($formData);
		$form->setRecordID($idRole);

		return $form->render();
	}

}

?>