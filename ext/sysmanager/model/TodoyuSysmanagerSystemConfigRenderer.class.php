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
 * System Config Renderer
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerSystemConfigRenderer {

	/**
	 * Render module content
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function renderModule(array $params) {
		$tabs	= self::renderTabs($params);
		$body	= self::renderBody($params);

		return TodoyuRenderer::renderContent($body, $tabs);
	}



	/**
	 * Render module body content
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function renderBody(array $params) {
		switch($params['tab']) {
			case 'logo':
				return self::renderBodyLogo($params);

			case 'passwordstrength':
				return self::renderBodyPasswordStrength($params);

			case 'repository':
				return self::renderBodyRepository($params);

			case 'systemconfig':
			default:
				return self::renderBodySystemConfig($params);
		}
	}



	/**
	 * Render body content for logo upload
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	private static function renderBodyLogo(array $params) {
		$xmlPath= 'ext/sysmanager/config/form/config-logo.xml';
		$form	= TodoyuFormManager::getForm($xmlPath);

		$form->setUseRecordID(false);

		return $form->render();
	}



	/**
	 * Render content body for system config (config form)
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	private static function renderBodySystemConfig(array $params) {
		$data	= TodoyuArray::assure(Todoyu::$CONFIG['SYSTEM']);
		$xml	= 'ext/sysmanager/config/form/systemconfig.xml';
		$form	= TodoyuFormManager::getForm($xml);

		$form->setFormData($data);

		return $form->render();
	}



	/**
	 * Render content body for password strength settings
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	private static function renderBodyPasswordStrength(array $params) {
		$data	= TodoyuArray::assure(Todoyu::$CONFIG['SETTINGS']['passwordStrength']);
		$xml	= 'ext/sysmanager/config/form/passwordstrength.xml';
		$form	= TodoyuFormManager::getForm($xml);

		$form->setFormData($data);

		return $form->render();
	}



	/**
	 * Render repository configuration body
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	private static function renderBodyRepository(array $params) {
		$xml	= 'ext/sysmanager/config/form/repository-config.xml';
		$form	= TodoyuFormManager::getForm($xml);

		$data	= array(
			'todoyuid'			=> trim(Todoyu::$CONFIG['SETTINGS']['repository']['todoyuid']),
			'todoyuid_comment'	=> self::renderTodoyuIDComment()
		);

		$form->setFormData($data);

		return $form->render();
	}



	/**
	 * Render comment for todoyu ID field explanation with link to tER
	 *
	 * @return	String
	 */
	public static function renderTodoyuIDComment() {
		$tmpl	= 'ext/sysmanager/view/repository/config-todoyuidcomment.tmpl';
		$data	= array(
			'url'	=> Todoyu::$CONFIG['EXT']['sysmanager']['todoyuID']['url']
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render tabs
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	private static function renderTabs(array $params) {
		$name		= 'config';
		$jsHandler	= 'Todoyu.Ext.sysmanager.Config.onTabClick.bind(Todoyu.Ext.sysmanager.Config)';
		$tabs		= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['sysmanager']['configTabs']);
		$firstTab	= reset($tabs);
		$active		= $firstTab['id'];

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $active);
	}
}

?>