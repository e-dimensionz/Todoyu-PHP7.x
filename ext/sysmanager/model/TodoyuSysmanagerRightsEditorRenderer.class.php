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
 * Render rights editor
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRightsEditorRenderer {

	/**
	 * Render rights editor module of active tab
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function renderModule(array $params) {
		$tab	= trim($params['tab']);

		if( $tab === '' ) {
			$tab = TodoyuSysmanagerRightsEditorManager::getActiveTab();
		} else {
			TodoyuSysmanagerRightsEditorManager::saveActiveTab($tab);
		}

		$tabs	= self::renderTabs($tab);
		$body	= self::renderBody($tab, $params);

		return TodoyuRenderer::renderContent($body, $tabs);
	}



	/**
	 * Render rights module tabs
	 *
	 * @param	String	$tab
	 * @return	String
	 */
	private static function renderTabs($tab) {
		$name		= 'rights';
		$tabs		= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['sysmanager']['rightsTabs']);
		$jsHandler	= 'Todoyu.Ext.sysmanager.Rights.onTabClick.bind(Todoyu.Ext.sysmanager.Rights)';
		$activeTab	= $tab;

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}



	/**
	 * Render rights editor body
	 *
	 * @param	String	$tab
	 * @param	Array	$params
	 * @return	String
	 */
	private static function renderBody($tab, array $params) {
		if( $tab === 'roles' ) {
			return self::renderBodyRoles($params);
		} else {
			return self::renderBodyRights($params);
		}
	}



	/**
	 * Render module view for rights editor
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderBodyRights(array $params) {
		$ext	= trim($params['extkey']);

		if( $ext === '' ) {
			$ext	= TodoyuSysmanagerPreferences::getRightsExt();
		} else {
			TodoyuSysmanagerPreferences::saveRightsExt($ext);
		}

		$selectedRoles	= TodoyuSysmanagerPreferences::getRightsRoles();

		$tmpl	= 'ext/sysmanager/view/rights.tmpl';
		$data	= array(
			'form'		=> self::renderRightsEditorForm($selectedRoles, $ext),
			'matrix'	=> self::renderRightsMatrix($selectedRoles, $ext)
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render module view for role editor
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderBodyRoles(array $params) {
		$idRole	= intval($params['role']);

		if( $idRole === 0 ) {
			return TodoyuListingRenderer::render('sysmanager', 'roles');
		} else {
			return TodoyuSysmanagerRoleEditorRenderer::renderEdit($idRole);
		}
	}



	/**
	 * Render form for rights editor. Includes the role and extension selector
	 *
	 * @param	Array		$roles
	 * @param	String		$ext
	 * @return	String
	 */
	public static function renderRightsEditorForm(array $roles = array(), $ext = '') {
		$xmlPath= 'ext/sysmanager/config/form/rightseditor.xml';
		$form	= TodoyuFormManager::getForm($xmlPath);

		$data	= array(
			'roles'		=> $roles,
			'extension'	=> $ext
		);

		$form->setFormData($data);
		$form->setUseRecordID(false);

		return $form->render();
	}



	/**
	 * Render rights matrix for all all extension rights for the selected roles
	 *
	 * @param	Array		$roleIDs		Roles to display
	 * @param	String		$ext			Extension key
	 * @return	String
	 */
	public static function renderRightsMatrix(array $roleIDs, $ext) {
		if( sizeof($roleIDs) === 0 ) {
			$tmpl	= 'ext/sysmanager/view/rights-noroles.tmpl';
			$data	= array();
		} else {
				// Read rights XML file
			$rights		= TodoyuSysmanagerRightsEditorManager::getExtRights($ext);

				// Get required chain
			$required	= TodoyuSysmanagerRightsEditorManager::extractRequiredInfos($rights);

				// Get current group infos
			$roles		= TodoyuRoleManager::getRoles($roleIDs);

				// Get current checked rights (default or db)
			$activeRights = TodoyuSysmanagerRightsEditorManager::getCurrentActiveRights($rights, $ext);

			$tmpl	= 'ext/sysmanager/view/rightsmatrix.tmpl';
			$data	= array(
				'amountColors'	=> sizeof(TodoyuArray::assure(Todoyu::$CONFIG['COLORS'])),
				'extension'		=> $ext,
				'rights'		=> $rights,
				'roles'			=> $roles,
				'activeRights'	=> $activeRights,
				'required'		=> $required
			);
		}

		return Todoyu::render($tmpl, $data);
	}

}

?>