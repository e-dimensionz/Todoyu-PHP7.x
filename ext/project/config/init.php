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

/* ---------------------------------------------
	Add autocompleters for project data types
   --------------------------------------------- */
	// Person
TodoyuAutocompleter::addAutocompleter('projectperson', 'TodoyuContactPersonFilterDataSource::autocompletePersons', array('project', 'general:use'));
	// Task
TodoyuAutocompleter::addAutocompleter('task', 'TodoyuProjectTaskManager::autocompleteTasks', array('project', 'general:use'));
	// Task in project
TodoyuAutocompleter::addAutocompleter('projecttask', 'TodoyuProjectTaskManager::autocompleteProjectTasks', array('project', 'general:use'));
	// Project
TodoyuAutocompleter::addAutocompleter('project', 'TodoyuProjectProjectFilterDataSource::autocompleteProjects', array('project', 'general:use'));
	// Project that tasks can be added to
TodoyuAutocompleter::addAutocompleter('taskaddableproject', 'TodoyuProjectProjectFilterDataSource::autocompleteTaskAddableProjects', array('project', 'general:use'));



/* ----------------------------
	Context Menu Callbacks
   ---------------------------- */
TodoyuContextMenuManager::addFunction('Project', 'TodoyuProjectProjectManager::getContextMenuItems', 10);
TodoyuContextMenuManager::addFunction('Project', 'TodoyuProjectTaskClipboard::getProjectContextMenuItems', 100);
TodoyuContextMenuManager::addFunction('Task', 'TodoyuProjectTaskManager::getContextMenuItems', 10);
TodoyuContextMenuManager::addFunction('Task', 'TodoyuProjectTaskClipboard::getTaskContextMenuItems', 100);
TodoyuContextMenuManager::addFunction('Task', 'TodoyuProjectTaskManager::removeEmptyContextMenuParents', 100000);
TodoyuContextMenuManager::addFunction('ProjectInline', 'TodoyuProjectProjectManager::getInlineContextMenuItems', 10);



/* -----------------------
	Add filter exports
   ----------------------- */
TodoyuSearchActionPanelManager::addExport('task', 'csvexport', 'TodoyuProjectTaskExportManager::exportCSV', 'project.task.export.csv', 'exportCsv', 'project:export:taskcsv');
TodoyuSearchActionPanelManager::addExport('project', 'csvexport', 'TodoyuProjectExportManager::exportCSV', 'project.ext.export.csv', 'exportCsv', 'project:export:projectcsv');


/* -----------------------
	Form elements
   ----------------------- */
	// Records selector: projects
TodoyuFormRecordsManager::addType('project', 'TodoyuProjectFormElement_RecordsProject', 'TodoyuProjectProjectManager::getMatchingProjects');


/* -----------------------
	Project Detail tabs
   ----------------------- */
TodoyuProjectProjectDetailsTabsManager::registerDetailsTab('general', 'project.ext.project.tabs.general', 'TodoyuProjectProjectRenderer::renderProjectDetailGeneral', 1);

if( Todoyu::person()->isInternal() ) {
	TodoyuProjectProjectDetailsTabsManager::registerDetailsTab('preferences', 'project.ext.project.tabs.preferences', 'TodoyuProjectProjectRenderer::renderProjectDetailPreferences', 30);
}




Todoyu::$CONFIG['EXT']['project'] = array(
	'STATUS'	=> array( // Available type statuses
		'PROJECT' => array(
			STATUS_PLANNING		=> 'planning',
			STATUS_PROGRESS		=> 'progress',
			STATUS_DONE			=> 'done',
			STATUS_WARRANTY		=> 'warranty',
			STATUS_CLEARED		=> 'cleared'
		),
		'TASK' => array(
			STATUS_PLANNING		=> 'planning',
			STATUS_OPEN			=> 'open',
			STATUS_PROGRESS		=> 'progress',
			STATUS_WAITING		=> 'waiting',
			STATUS_REJECTED		=> 'rejected',
			STATUS_CONFIRM		=> 'confirm',
			STATUS_DONE			=> 'done',
			STATUS_ACCEPTED		=> 'accepted',
			STATUS_CLEARED		=> 'cleared'
		)
	),
	'projectStatusDisallowChildrenEditing' => array( // Non-editable project status (tasks/containers in project cannot be modified)
		STATUS_CLEARED,
		STATUS_DONE
	),
	'allowedCopiedStatus' => array( // Copied and cloned tasks can have this status. Fallback to default if not
		STATUS_OPEN,
		STATUS_PLANNING,
		STATUS_PROGRESS
	),
	'taskStatusTimeExceedingRelevant' => array( // In which task status is time exceeding relevant?
		STATUS_CONFIRM,
		STATUS_OPEN,
		STATUS_PLANNING,
		STATUS_PROGRESS,
		STATUS_REJECTED,
		STATUS_WAITING
	),
	'portalTodoTabFilters' => array( // Filters used in "todo" tab
		'assigned' => array( // Assigned tasks to be worked on
			array(
				'filter'	=> 'type',
				'value'		=> TASK_TYPE_TASK
			),
			array(
				'filter'	=> 'currentPersonAssigned'
			),
			array(
				'filter'	=> 'status',
				'value'		=> STATUS_OPEN . ',' . STATUS_PROGRESS
			)
		),
		'owner' => array( // Tasks the current user has to review and confirm
			array(
				'filter'	=> 'type',
				'value'		=> TASK_TYPE_TASK
			),
			array(
				'filter'	=> 'currentPersonOwner'
			),
			array(
				'filter'	=> 'status',
				'value'		=> STATUS_CONFIRM
			)
		)
	),
	'taskDefaults' => array( // Task default values. Overridden with extConf / task preset set values if set
		'status'			=> STATUS_PLANNING,
		'statusQuickTask'	=> STATUS_OPEN
	),
	'quicktask' => array(
		'durationDays'  => 3 // Duration (timespan from date_start to date_end/deadline) of quicktasks
	),
	'panelWidgetProjectList' => array(
		'maxProjects' => 30 // Maximum projects in project listing widget
	)
);



/* ----------------------------
	Add search filter widgets
   ---------------------------- */
	// Projectrole
Todoyu::$CONFIG['EXT']['search']['widgettypes']['projectrole'] =array(
	'tmpl'			=> 'ext/project/view/filterwidget/projectrole.tmpl',
	'configFunc'	=> 'TodoyuProjectProjectFilter::prepareDataForProjectroleWidget'
);

?>