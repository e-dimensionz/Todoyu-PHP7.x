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
 * Project specific Dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 */



/**
 * Get task status label by given status ID
 * Usage example:	{taskStatusLabel $statusID}
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	String		$idStatus
 * @return	String
 */
function Dwoo_Plugin_taskStatusLabel(Dwoo $dwoo, $idStatus) {
	$idStatus	= intval($idStatus);

	return TodoyuProjectTaskStatusManager::getStatusLabel($idStatus);
}



/**
 * Render task status key
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @param	Integer	$idStatus
 * @return	String
 */
function Dwoo_Plugin_taskStatusKey(Dwoo $dwoo, $idStatus) {
	$idStatus	= intval($idStatus);

	return TodoyuProjectTaskStatusManager::getStatusKey($idStatus);
}



/**
 * Get project status label
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idStatus
 * @return	String
 */
function Dwoo_Plugin_projectStatusLabel(Dwoo $dwoo, $idStatus) {
	$idStatus	= intval($idStatus);

	return TodoyuProjectProjectStatusManager::getStatusLabel($idStatus);
}



/**
 * Get label of given project role
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @param	Integer	$idProjectrole
 * @return	String
 */
function Dwoo_Plugin_projectRoleLabel(Dwoo $dwoo, $idProjectrole) {
	return TodoyuProjectProjectroleManager::getLabel($idProjectrole);
}



/**
 * Get project status key
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @param	Integer	$idStatus
 * @return	String
 */
function Dwoo_Plugin_projectStatusKey(Dwoo $dwoo, $idStatus) {
	$idStatus	= intval($idStatus);

	return TodoyuProjectProjectStatusManager::getStatusKey($idStatus);
}



/**
 * Check right of current person to see given project
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idProject
 * @return	Boolean
 */
function Dwoo_Plugin_isAllowedSeeProjectDetails(Dwoo $dwoo, $idProject) {
	return Todoyu::allowed('project', 'project:seeAll') || TodoyuProjectProjectManager::isPersonAssigned($idProject);
}



/**
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$text
 * @return	String
 */
function Dwoo_Plugin_linkTasks_compile(Dwoo_Compiler $compiler, $text) {
	return 'TodoyuProjectTaskManager::linkTaskIDsInText(' . $text . ')';
}


/**
 * @param	Dwoo		$dwoo
 * @param	Integer		$idTask
 * @return	String
 */
function Dwoo_Plugin_taskNumber(Dwoo $dwoo, $idTask) {
	$idTask	= intval($idTask);
	return TodoyuProjectTaskManager::getTask($idTask)->getTaskNumber(true);
}


?>