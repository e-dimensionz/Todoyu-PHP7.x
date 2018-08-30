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

if( Todoyu::allowed('comment', 'general:use') ) {
		// Substitute comment identifiers in text by hyperlinks
	TodoyuHookManager::registerHook('core', 'substituteLinkableElements', 'TodoyuCommentCommentManager::linkCommentIDsInText');

	//TodoyuHookManager::registerHook('project', 'renderTasks', 'TodoyuCommentCommentManager::onTasksRender');

	TodoyuFormHook::registerBuildForm('ext/project/config/form/project.xml', 'TodoyuCommentFallbackManager::hookAddFallbackField');
	TodoyuFormHook::registerLoadData('ext/project/config/form/project.xml', 'TodoyuCommentFallbackManager::hookSetProjectDefaultData');


		// Callbacks for exteding filter widgets of other extensions
	TodoyuHookManager::registerHook('core', 'loadconfig.project.filters', 'TodoyuCommentManager::hookLoadProjectFilterConfig');

	if( Todoyu::allowed('asset', 'general:use')) {
		TodoyuHookManager::registerHook('project', 'taskIcons', 'TodoyuCommentAssetManager::hookAddTaskIcons');
	}
}

?>