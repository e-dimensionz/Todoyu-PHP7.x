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
 * Sysmanager modules panel widget
 *
 * @name		Sysmanager renderer
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerPanelWidgetModules extends TodoyuPanelWidget {

	/**
	 * Sysmanager modules panel widget constructor
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'sysmanager',									// ext key
			'sysmanagermodules',							// panel widget ID
			'sysmanager.panelwidget-sysmanagermodules.title',// widget title text
			$config,										// widget config array
			$params											// widget parameters
		);

		$this->addHasIconClass();
	}



	/**
	 * Render content
	 *
	 * @return	String
	 */
	public function renderContent() {
		$modules	= TodoyuSysmanagerManager::getModules();
		$active		= TodoyuSysmanagerPreferences::getActiveModule();

		if( ! $active ) {
			$active = $modules[0]['key'];
		}

		$tmpl	= 'ext/sysmanager/view/panelwidget/sysmanagermodules.tmpl';
		$data	= array(
			'active'	=> $active,
			'modules'	=> $modules
		);

		$content	= Todoyu::render($tmpl, $data);

		$this->setContent($content);

		return $content;
	}




	/**
	 * Check whether usage of sysmanager modules selector widget is allowed
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return Todoyu::allowed('sysmanager', 'general:use');
	}

}

?>