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

	// Add record infos
TodoyuSysmanagerExtManager::addRecordConfig('project', 'projectrole', array(
	'label'			=> 'project.ext.records.projectrole',
	'description'	=> 'project.ext.records.projectrole.desc',
	'form'			=> 'ext/project/config/form/admin/projectrole.xml',
	'list'			=> 'TodoyuProjectProjectroleManager::getRecords',
	'save'			=> 'TodoyuProjectProjectroleManager::saveProjectrole',
	'delete'		=> 'TodoyuProjectProjectroleManager::deleteProjectrole',
	'object'		=> 'TodoyuProjectProjectrole',
	'table'			=> 'ext_project_role',
	'isDeletable'	=> 'TodoyuProjectProjectroleManager::isDeletable'
));

TodoyuSysmanagerExtManager::addRecordConfig('project', 'activity', array(
	'label'			=> 'project.ext.records.activity',
	'description'	=> 'project.ext.records.activity.desc',
	'form'			=> 'ext/project/config/form/admin/activity.xml',
	'list'			=> 'TodoyuProjectActivityManager::getRecords',
	'save'			=> 'TodoyuProjectActivityManager::saveActivity',
	'delete'		=> 'TodoyuProjectActivityManager::deleteActivity',
	'object'		=> 'TodoyuProjectActivity',
	'table'			=> 'ext_project_activity',
	'isDeletable'	=> 'TodoyuProjectActivityManager::isDeletable'
));

TodoyuSysmanagerExtManager::addRecordConfig('project', 'taskpreset', array(
	'label'			=> 'project.ext.records.taskpreset',
	'description'	=> 'project.ext.records.taskpreset.desc',
	'form'			=> 'ext/project/config/form/admin/taskpreset.xml',
	'list'			=> 'TodoyuProjectTaskPresetManager::getRecords',
	'save'			=> 'TodoyuProjectTaskPresetManager::saveTaskPreset',
	'delete'		=> 'TodoyuProjectTaskPresetManager::deleteTaskPreset',
	'object'		=> 'TodoyuProjectTaskPreset',
	'table'			=> 'ext_project_taskpreset'
));

?>