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
 * Rights management
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRightsActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrictAdmin();
	}



	/**
	 * Render tab in rights admin module
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		return TodoyuSysmanagerRightsEditorRenderer::renderModule($params);
	}



	/**
	 * Save rights
	 *
	 * @param	Array	$params
	 */
	public function saveAction(array $params) {
		$extKey	= $params['extension'];
		$rights	= TodoyuArray::assure($params['rights']);
		$roles	= TodoyuArray::intExplode(',', $params['roles']);

		TodoyuSysmanagerRightsEditorManager::saveRoleRights($extKey, $rights, $roles);
	}



	/**
	 * Save current extension's rights and render rights matrix
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function matrixAction(array $params) {
		$roles	= $params['rightseditor']['roles'];
		$roles	= TodoyuArray::intval($roles, true, true);
		$ext	= $params['rightseditor']['extension'];

		TodoyuSysmanagerPreferences::saveRightsExt($ext);
		TodoyuSysmanagerPreferences::saveRightsRoles($roles);

		return TodoyuSysmanagerRightsEditorRenderer::renderRightsMatrix($roles, $ext);
	}

}

?>