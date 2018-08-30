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
 * Assets (JS, CSS) requirements for project extension
 *
 * @package		Todoyu
 * @subpackage	Project
 */

Todoyu::$CONFIG['EXT']['project']['assets'] = array(
	'js' => array(
		array(
			'file'		=> 'ext/project/asset/js/Ext.js',
			'position'	=> 80
		),
		array(
			'file'		=> 'ext/project/asset/js/TaskPreset.js',
			'position'	=> 81
		),
		array(
			'file'		=> 'ext/project/asset/js/Project.js',
			'position'	=> 82
		),
		array(
			'file'		=> 'ext/project/asset/js/ProjectTab.js',
			'position'	=> 83
		),
		array(
			'file'		=> 'ext/project/asset/js/QuickTask.js',
			'position'	=> 109
		),
		array(
			'file'		=> 'ext/project/asset/js/HeadletQuickTask.js',
			'position'	=> 90
		),
			// Add creation engines to quick create headlet
		array(
			'file'		=> 'ext/project/asset/js/QuickCreateProject.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/project/asset/js/QuickCreateTask.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/project/asset/js/ProjectEdit.js',
			'position'	=> 102
		),
		array(
			'file'		=> 'ext/project/asset/js/Task.js',
			'position'	=> 102
		),
		array(
			'file'		=> 'ext/project/asset/js/TaskEdit.js',
			'position'	=> 102
		),
		array(
			'file'		=> 'ext/project/asset/js/Container.js',
			'position'	=> 103
		),
		array(
			'file'		=> 'ext/project/asset/js/TaskTree.js',
			'position'	=> 104
		),
		array(
			'file'		=> 'ext/project/asset/js/ContextMenuTask.js',
			'position'	=> 105
		),
		array(
			'file'		=> 'ext/project/asset/js/ContextMenuProject.js',
			'position'	=> 106
		),
		array(
			'file'		=> 'ext/project/asset/js/ProjectTaskTree.js',
			'position'	=> 107
		),
		array(
			'file'		=> 'ext/project/asset/js/TaskParentAc.js',
			'position'	=> 108
		),
		array(
			'file'		=> 'ext/project/asset/js/hooks.js',
			'position'	=> 1000
		),
		array(
			'file'		=> 'ext/project/asset/js/Filter.js',
			'position'	=> 200
		),
		array(
			'file'		=> 'ext/project/asset/js/PanelWidgetProjectList.js',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/project/asset/js/PanelWidgetProjectSelector.js',
			'position'	=> 115
		),
		array(
			'file' => 'ext/project/asset/js/PanelWidgetTaskStatusFilter.js',
			'position' => 120,
		),
		array(
			'file' => 'ext/project/asset/js/PanelWidgetProjectStatusFilter.js',
			'position' => 120,
		),
		array(
			'file' => 'ext/project/asset/js/Portal.js',
			'position' => 120,
		),
		array(
			'file' => 'ext/project/asset/js/TaskTreeSortable.js',
			'position' => 130,
		),
		array(
			'file' => 'ext/project/asset/js/TaskTreeSortableNode.js',
			'position' => 131,
		),
		array(
			'file' => 'ext/project/asset/js/ContextMenuProjectInline.js',
			'position' => 132,
		)
	),
	'css' => array(
		array(
			'file'		=> 'ext/project/asset/css/headlet-quicktask.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/project/asset/css/ext.scss',
			'media'		=> 'all',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/project/asset/css/task.scss',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/project/asset/css/project.scss',
			'position'	=> 102
		),
		array(
			'file'		=> 'ext/project/asset/css/contextmenu.scss',
			'position'	=> 103
		),
		array(
			'file'		=> 'ext/project/asset/css/taskparent-ac.scss',
			'position'	=> 104
		),
		array(
			'file'		=> 'ext/project/asset/css/panelwidget-projectlist.scss',
			'media'		=> 'all',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/project/asset/css/panelwidget-projectselector.scss',
			'media'		=> 'all',
			'position'	=> 110
		),
		array(
			'file' => 'ext/project/asset/css/panelwidget-statusfilter.scss',
			'position' => 120,
		),
		array(
			'file' => 'ext/project/asset/css/panelwidget-taskstatusfilter.scss',
			'position' => 120,
		),
		array(
			'file' => 'ext/project/asset/css/panelwidget-projectstatusfilter.scss',
			'position' => 120,
		)
	)
);

?>