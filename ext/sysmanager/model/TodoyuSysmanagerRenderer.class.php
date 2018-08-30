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
 * Sysmanager renderer
 *
 * @name		Sysmanager renderer
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRenderer {

	/**
	 * Render fullContent of a module
	 *
	 * @param	String		$module
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderModule($module, array $params = array()) {
		$renderFunc = TodoyuSysmanagerManager::getModuleRenderFunction($module);

		if( empty($renderFunc) ) {
			return self::renderModuleFallback();
		}

		return TodoyuFunction::callUserFunction($renderFunc, $params);
	}



	/**
	 * Fallback if no module allowed: message to contact admin
	 *
	 * @return	String
	 */
	public static function renderModuleFallback() {
		$tabConfig	= array(array(
			'id'	=> 'nomodules',
			'label'	=> 'Sysmanager',
		));
		$tabs	= TodoyuTabheadRenderer::renderTabs('sysmanager', $tabConfig, '', '');
		$body	= Todoyu::Label('sysmanager.ext.message.nomoduleallowed');

		return TodoyuRenderer::renderContent($body, $tabs);
	}



	/**
	 * Render panel widgets
	 *
	 * @return	String
	 */
	public static function renderPanelWidgets() {
		return TodoyuPanelWidgetRenderer::renderPanelWidgets('sysmanager');
	}

}

?>