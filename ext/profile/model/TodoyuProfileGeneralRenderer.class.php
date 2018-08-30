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
 * Renderer for general module
 *
 * @package		Todoyu
 * @subpackage	Profile
 */
class TodoyuProfileGeneralRenderer {

	/**
	 * Render tabs in general area
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderTabs(array $params) {
		$name		= 'profile-general';
		$class		= 'profile';
		$jsHandler	= 'Todoyu.Ext.profile.General.onTabClick.bind(Todoyu.Ext.profile.General)';
		$tabs		= TodoyuTabManager::getAllowedTabs(Todoyu::$CONFIG['EXT']['profile']['generalTabs']);
		$active		= $params['tab'];

		if( is_null($active) ) {
			$active = $tabs[0]['id'];
		}

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $active, $class);
	}



	/**
	 * Render tab content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderContent(array $params) {
		$tab	= $params['tab'];

		switch($tab) {
			case 'password':
				return self::renderContentPassword();
				break;

			case 'main':
			default:
				return self::renderContentMain();
				break;
		}
	}



	/**
	 * Render content for main tab
	 *
	 * @return	String
	 */
	public static function renderContentMain() {
		$xmlPath= 'ext/profile/config/form/general-main.xml';
		$form	= TodoyuFormManager::getForm($xmlPath);

		$formData	= array(
			'locale'	=> Todoyu::person()->getLocale()
		);

		$form->setFormData($formData);

		$tmpl	= 'ext/profile/view/general-main.tmpl';
		$data	= array(
			'name'	=> Todoyu::person()->getFullName(),
			'form'	=> $form->render()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render content for password tab
	 *
	 * @return	String
	 */
	public static function renderContentPassword() {
		$xmlPath= 'ext/profile/config/form/general-password.xml';
		$form	= TodoyuFormManager::getForm($xmlPath);

		$tmpl	= 'ext/profile/view/general-password.tmpl';
		$data	= array(
			'form'	=> $form->render()
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>