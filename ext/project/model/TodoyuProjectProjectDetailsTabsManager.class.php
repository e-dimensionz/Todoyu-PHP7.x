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
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProjectDetailsTabsManager {

	/**
	 * Register a tab for project header details
	 *
	 * @param	String		$key
	 * @param	String		$label
	 * @param	String		$function
	 * @param	Integer		$position
	 */
	public static function registerDetailsTab($key, $label, $function, $position = 100){
		TodoyuContentItemTabManager::registerTab('project', 'projectdetail', $key, $label, $function, $position);
	}



	/**
	 * Get tabs for project detail
	 *
	 * @param	Integer			$idProject
	 * @return	Array[]
	 */
	public static function getDetailsTabConfiguration($idProject) {
		return TodoyuContentItemTabManager::getTabs('project', 'projectdetail', $idProject);
 	}



	/**
	 * Get active tab key
	 *
	 * @param	Integer		$idProject
	 * @return	String
	 */
	public static function getActiveTab($idProject) {
		$activeTab	= TodoyuProjectPreferences::getActiveProjectDetailTab($idProject);

		if( $activeTab === null) {
			$activeTab = TodoyuContentItemTabManager::getDefaultTab('project', 'projectdetail', $idProject);
		}

		return $activeTab;
	}

}

?>