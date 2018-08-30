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
 * Panel widget renderer
 * Collects the registered panel widgets and renders them
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuPanelWidgetRenderer {

	/**
	 * Render widgets from config
	 *
	 * @param	String		$area		Extension key
	 * @param	Array		$params		Custom parameters for current area
	 * @return	String
	 */
	public static function renderPanelWidgets($area, array $params = array()) {
		$widgetConfigs	= TodoyuPanelWidgetManager::getAreaPanelWidgets($area);
		$content		= '';

			// Render the widgets
		foreach($widgetConfigs as $widgetConfig) {
			if( class_exists($widgetConfig['class']) ) {
					// Check whether panelWidget is allowed to be displayed
				if( call_user_func(array($widgetConfig['class'], 'isAllowed') ) ) {
					$widget		= TodoyuPanelWidgetManager::getPanelWidget($widgetConfig['ext'], $widgetConfig['name'], $params);
					$content	.= $widget->render();
				} else {
					// Widget not allowed
				}
			} else {
				$debug	= 'Can\'t find requested panel widget: "' . $widgetConfig['class'] . '"';
				TodoyuDebug::printHtml($debug, 'PanelWidget not found!', true);
				TodoyuDebug::printHtml($widgetConfig, 'Widget config');
			}
		}

		return $content;
	}

}

?>