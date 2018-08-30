<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * General project extension manager
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectManager {

	/**
	 * Add the last 3 projects as sub menu items to the project main tab
	 */
	public static function addLastProjectsAsSubmenuItems() {
		$projectEntries	= TodoyuProjectProjectManager::getOpenProjectLabels();

		$counter	= 0;
		foreach($projectEntries as $idProject => $title) {
			TodoyuFrontend::addSubmenuEntry('project', 'project' . $idProject, $title, 'index.php?ext=project&amp;project=' . $idProject, $counter++);
		}
	}



	/**
	 * Get fallback preset ID from extconf
	 *
	 * @return	Integer
	 */
	public static function getFallbackTaskPresetID() {
		$idPreset	= TodoyuSysmanagerExtConfManager::getExtConfValue('project', 'fallbacktaskpreset');

		return intval($idPreset);
	}



	/**
	 * Load configs of project related filter widgets of contact persons
	 */
	public static function hookLoadContactFilterConfig() {
		TodoyuFileManager::includeFile('ext/project/config/filters-contact.php', true);
	}



	/**
	 * Get panel widget project selector
	 * Try to get a custom implementation for the current area
	 *
	 * @param	String		$areaExtKey		Extension key of current area
	 * @return	TodoyuProjectPanelWidgetProjectSelector	Or an extension of this class
	 */
	public static function getPanelWidgetProjectSelector($areaExtKey) {
		$widgetName	= 'ProjectSelector';

		$overrideExists	= TodoyuPanelWidgetManager::exists(AREA, $areaExtKey, $widgetName);
		$extKey			= $overrideExists ? $areaExtKey : 'project';

		return TodoyuPanelWidgetManager::getPanelWidget($extKey, $widgetName);
	}

}

?>