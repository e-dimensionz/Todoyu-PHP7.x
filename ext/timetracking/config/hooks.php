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

TodoyuHookManager::registerHook('project', 'taskinfo', 'TodoyuTimetrackingManager::addTimetrackingInfosToTask');
TodoyuHookManager::registerHook('project', 'task.defaultData', 'TodoyuTimetrackingTaskManager::hookTaskDefaultData');

	// Add timetracking infos to task infos: more time tracked than estimated? add marking CSS class
TodoyuHookManager::registerHook('project', 'taskdata', 'TodoyuTimetrackingManager::hookAddWorkloadOverbookedWarning');
TodoyuHookManager::registerHook('project', 'taskHeaderExtras', 'TodoyuTimetrackingManager::addTimetrackingHeaderExtrasToTask');

	// On export (task)
TodoyuHookManager::registerHook('project', 'taskCSVExportParseData', 'TodoyuTimetrackingExportManager::parseTaskDataForExport');

	// Quicktask: add timetracking fields
TodoyuFormHook::registerBuildForm('ext/project/config/form/quicktask.xml', 'TodoyuTimetrackingManager::addWorkloadFieldToQuicktask');
	// Quicktask: Save timetracking fields
TodoyuFormHook::registerSaveData('ext/project/config/form/quicktask.xml', 'TodoyuTimetrackingManager::handleQuicktaskFormSave');

	// Quicktask: Saved hook
TodoyuHookManager::registerHook('project', 'quicktask.saved', 'TodoyuTimetrackingManager::hookQuickTaskSaved');

	// Remove fields when editing foreign trackings
TodoyuFormHook::registerBuildForm('ext/timetracking/config/form/track.xml', 'TodoyuTimetrackingManager::hookModifyTrackFields');

	// Add timetracking update callbacks
TodoyuTimetrackingCallbackManager::add('tasktab', 'TodoyuTimetrackingManager::callbackTaskTab');
TodoyuTimetrackingCallbackManager::add('trackheadlet', 'TodoyuTimetrackingManager::callbackHeadletOverlayContent');

	// Extend project extconf and taskpreset in sysmanager
TodoyuHookManager::registerHook('project', 'projectpresetdata', 'TodoyuTimetrackingManager::getProjectPresetDataAttributes');
TodoyuFormHook::registerBuildForm('ext/project/config/form/extconf.xml', 'TodoyuTimetrackingExtManagerRenderer::onRenderProjectExtConfig');
TodoyuFormHook::registerBuildForm('ext/project/config/form/admin/taskpreset.xml', 'TodoyuTimetrackingSysmanagerManager::hookBuildFormTaskPreset');

	// Callbacks for exteding filter widgets of other extensions
TodoyuHookManager::registerHook('core', 'loadconfig.project.filters', 'TodoyuTimetrackingManager::hookLoadProjectFilterConfig');
TodoyuHookManager::registerHook('core', 'loadconfig.contact.filters', 'TodoyuTimetrackingManager::hookLoadContactFilterConfig');

?>