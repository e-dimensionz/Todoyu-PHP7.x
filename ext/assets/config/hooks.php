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

if( Todoyu::allowed('assets', 'general:use') ) {
	TodoyuHookManager::registerHook('project', 'taskIcons', 'TodoyuAssetsAssetManager::hookAddTaskIcons');
	TodoyuHookManager::registerHook('project', 'quickcreatetask', 'TodoyuAssetsTempUploaderTask::hookClearNewTaskFiles');
	TodoyuHookManager::registerHook('project', 'task.create', 'TodoyuAssetsTempUploaderTask::hookTaskCreate');
	TodoyuHookManager::registerHook('project', 'task.edit', 'TodoyuAssetsTempUploaderTask::hookTaskEdit');
	TodoyuHookManager::registerHook('project', 'quickcreateproject', 'TodoyuAssetsTempUploaderProject::hookClearNewProjectFiles');
	TodoyuHookManager::registerHook('project', 'project.create', 'TodoyuAssetsTempUploaderProject::hookProjectCreate');
	TodoyuHookManager::registerHook('project', 'project.edit', 'TodoyuAssetsTempUploaderProject::hookProjectEdit');

	TodoyuFormHook::registerBuildForm('ext/project/config/form/task.xml', 'TodoyuAssetsAssetManager::hookAddAssetUploadToTaskCreateForm');
	TodoyuFormHook::registerSaveData('ext/project/config/form/task.xml', 'TodoyuAssetsAssetManager::hookStoreUplodedTaskAssets');

	TodoyuFormHook::registerBuildForm('ext/project/config/form/project.xml', 'TodoyuAssetsAssetManager::hookAddAssetUploadToProjectCreateForm');
	TodoyuFormHook::registerSaveData('ext/project/config/form/project.xml', 'TodoyuAssetsAssetManager::hookStoreUplodedProjectAssets');

	TodoyuProjectProjectDetailsTabsManager::registerDetailsTab('assets', 'assets.ext.tab.assets', 'TodoyuAssetsProjectRenderer::renderProjectDetailsTab', 100);
}

?>