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

if( Todoyu::allowed('timetracking', 'general:use') ) {
	require_once( PATH_EXT_TIMETRACKING . '/config/hooks.php' );
}



/* ----------------------------
	Trackable task status
   ---------------------------- */
Todoyu::$CONFIG['EXT']['timetracking']['trackableStatus'] = array(
	STATUS_OPEN,
	STATUS_PROGRESS,
	STATUS_CONFIRM,
	STATUS_REJECTED,
	STATUS_WAITING
);



/* ------------------------------------------
	Add inline sub tabs into task element
   ------------------------------------------ */
if( Todoyu::allowed('timetracking', 'general:use') ) {
		// Register tab for task
	TodoyuContentItemTabManager::registerTab('project', 'task', 'timetracking', 'TodoyuTimetrackingTaskManager::getTabLabel', 'TodoyuTimetrackingTaskManager::getTabContent', 10);

	if( Todoyu::allowed('timetracking', 'task:track') ) {
			// Register context menu function for task
		TodoyuContextMenuManager::addFunction('task', 'TodoyuTimetracking::getContextMenuItems', 100);
		TodoyuHookManager::registerHook('core', 'logout', 'TodoyuTimetracking::onLogout');
	}

	TodoyuHookManager::registerHook('project', 'taskIcons', 'TodoyuTimetrackingTaskManager::hookGetTaskIcons');
}



/* ------------------------
	Headlet parameters
   ------------------------ */
	// Amount of previous tracks to be listed
Todoyu::$CONFIG['EXT']['timetracking']['headletLastTasks']	= 5;

?>