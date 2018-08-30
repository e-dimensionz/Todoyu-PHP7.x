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

	// Add create for task
if( Todoyu::allowed('project', 'addtask:addTaskInOwnProjects') ) {
		// Check if user can add tasks with quick-add
		// Needs at least one project where he can add tasks
	if( TodoyuProjectTaskRights::isQuickAddAllowed() ) {
		TodoyuQuickCreateManager::addEngine('project', 'task', 'project.task.create.label', 20, array('portal', 'project'));
	}
}

	// Add create for project
if( Todoyu::allowed('project', 'project:add') ) {
	TodoyuQuickCreateManager::addEngine('project', 'project', 'project.ext.create.label', 10, array());
}

?>