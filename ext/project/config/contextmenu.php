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
 * Context menu configuration for project extension
 *
 * @package		Todoyu
 * @subpackage	Project
 */

	/**
	 * Context menu configuration for task
	 */
Todoyu::$CONFIG['EXT']['project']['ContextMenu']['Task'] = array(
	'edit'	=> array(
		'key'		=> 'edit',
		'label'		=> 'project.task.contextmenu.edit',
		'jsAction'	=> 'Todoyu.Ext.project.Task.edit(#ID#)',
		'class'		=> 'taskContextMenu taskEdit',
		'position'	=> 10
	),
	'showinproject'	=> array(
		'key'		=> 'showinproject',
		'label'		=> 'project.task.contextmenu.showinproject',
		'jsAction'	=> 'Todoyu.Ext.project.goToTaskInProject(#ID#)',
		'class'		=> 'taskContextMenu task-showinproject',
		'position'	=> 20
	),
	'actions' => array(
		'key'		=> 'actions',
		'label'		=> 'project.task.contextmenu.actions',
		'jsAction'	=> 'void(0)',
		'class'		=> 'taskContextMenu taskActions',
		'position'	=> 30,
		'submenu'	=> array(
			'copy'	=> array(
				'key'		=> 'copy',
				'label'		=> 'project.task.contextmenu.copy',
				'jsAction'	=> 'Todoyu.Ext.project.Task.copy(#ID#)',
				'class'		=> 'taskContextMenu taskCopy',
				'position'	=> 10
			),
			'cut'	=> array(
				'key'		=> 'cut',
				'label'		=> 'project.task.contextmenu.cut',
				'jsAction'	=> 'Todoyu.Ext.project.Task.cut(#ID#)',
				'class'		=> 'taskContextMenu taskCut',
				'position'	=> 20
			),
			'clone'	=> array(
				'key'		=> 'clone',
				'label'		=> 'project.task.contextmenu.clone',
				'jsAction'	=> 'Todoyu.Ext.project.Task.clone(#ID#)',
				'class'		=> 'taskContextMenu taskClone',
				'position'	=> 30
			),
			'delete'	=> array(
				'key'		=> 'delete',
				'label'		=> 'project.task.contextmenu.delete',
				'jsAction'	=> 'Todoyu.Ext.project.Task.remove(#ID#)',
				'class'		=> 'taskContextMenu taskDelete',
				'position'	=> 40
			)
		)
	),
	'add'	=> array(
		'key'		=> 'add',
		'label'		=> 'project.task.contextmenu.add',
		'jsAction'	=> 'void(0)',
		'class'		=> 'taskContextMenu taskAdd',
		'position'	=> 40,
		'submenu'	=> array(
			'task'	=> array(
				'key'		=> 'add-task',
				'label'		=> 'project.task.contextmenu.add.task',
				'jsAction'	=> 'Todoyu.Ext.project.Task.addSubTask(#ID#)',
				'class'		=> 'taskContextMenu taskAddTask'
			),
			'container'	=> array(
				'key'		=> 'add-container',
				'label'		=> 'project.task.contextmenu.add.container',
				'jsAction'	=> 'Todoyu.Ext.project.Task.addSubContainer(#ID#)',
				'class'		=> 'taskContextMenu taskAddContainer'
			)
		)
	),
	'status' => array(
		'key'		=> 'status',
		'label'		=> 'project.task.contextmenu.status.change',
		'jsAction'	=> 'void(0)',
		'class'		=> 'taskContextMenu taskStatus',
		'position'	=> 50,
		'submenu'	=> array(
			'planning'	=> array(
				'key'		=> 'status-planning',
				'label'		=> 'project.task.status.planning',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_PLANNING . ')',
				'class'		=> 'taskContextMenu taskStatusPlanning'
			),
			'open'	=> array(
				'key'		=> 'status-open',
				'label'		=> 'project.task.status.open',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_OPEN . ')',
				'class'		=> 'taskContextMenu taskStatusOpen'
			),
			'progress'	=> array(
				'key'		=> 'status-progress',
				'label'		=> 'project.task.status.progress',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_PROGRESS . ')',
				'class'		=> 'taskContextMenu taskStatusProgress'
			),
			'waiting'	=> array(
				'key'		=> 'status-waiting',
				'label'		=> 'project.task.status.waiting',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_WAITING . ')',
				'class'		=> 'taskContextMenu taskStatusWaiting'
			),
			'rejected'	=> array(
				'key'		=> 'status-rejected',
				'label'		=> 'project.task.status.rejected',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_REJECTED . ')',
				'class'		=> 'taskContextMenu taskStatusRejected'
			),
			'confirm'	=> array(
				'key'		=> 'status-confirm',
				'label'		=> 'project.task.status.confirm',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_CONFIRM . ')',
				'class'		=> 'taskContextMenu taskStatusConfirm'
			),
			'done'	=> array(
				'key'		=> 'status-done',
				'label'		=> 'project.task.status.done',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_DONE . ')',
				'class'		=> 'taskContextMenu taskStatusDone'
			),
			'accepted'	=> array(
				'key'		=> 'status-accepted',
				'label'		=> 'project.task.status.accepted',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_ACCEPTED . ')',
				'class'		=> 'taskContextMenu taskStatusAccepted'
			),
			'cleared'	=> array(
				'key'		=> 'status-cleared',
				'label'		=> 'project.task.status.cleared',
				'jsAction'	=> 'Todoyu.Ext.project.Task.updateStatus(#ID#, ' . STATUS_CLEARED . ')',
				'class'		=> 'taskContextMenu taskStatusCleared'
			)
		)
	)
);





	/**
	 * Context menu configuration for container
	 */
Todoyu::$CONFIG['EXT']['project']['ContextMenu']['Container'] = array(
	'edit'	=> array(
		'key'		=> 'edit',
		'label'		=> 'project.task.contextmenu.container.edit',
		'jsAction'	=> 'Todoyu.Ext.project.Container.edit(#ID#)',
		'class'		=> 'taskContextMenu containerEdit',
		'position'	=> 10
	),
	'showinproject'	=> array(
		'key'		=> 'showinproject',
		'label'		=> 'project.task.contextmenu.showinproject',
		'jsAction'	=> 'Todoyu.Ext.project.goToTaskInProject(#ID#)',
		'class'		=> 'taskContextMenu task-showinproject',
		'position'	=> 20
	),
	'actions' => array(
		'key'		=> 'actions',
		'label'		=> 'project.task.contextmenu.container.actions',
		'jsAction'	=> 'void(0)',
		'class'		=> 'taskContextMenu containerActions',
		'position'	=> 30,
		'submenu'	=> array(
			'copy'	=> array(
				'key'		=> 'copy',
				'label'		=> 'project.task.contextmenu.container.copy',
				'jsAction'	=> 'Todoyu.Ext.project.Container.copy(#ID#)',
				'class'		=> 'taskContextMenu containerCopy',
				'position'	=> 10
			),
			'cut'	=> array(
				'key'		=> 'cut',
				'label'		=> 'project.task.contextmenu.container.cut',
				'jsAction'	=> 'Todoyu.Ext.project.Container.cut(#ID#)',
				'class'		=> 'taskContextMenu containerCut',
				'position'	=> 20
			),
			'clone'	=> array(
				'key'		=> 'clone',
				'label'		=> 'project.task.contextmenu.container.clone',
				'jsAction'	=> 'Todoyu.Ext.project.Container.clone(#ID#)',
				'class'		=> 'taskContextMenu containerClone',
				'position'	=> 30
			),
			'delete'	=> array(
				'key'		=> 'delete',
				'label'		=> 'project.task.contextmenu.container.delete',
				'jsAction'	=> 'Todoyu.Ext.project.Container.remove(#ID#)',
				'class'		=> 'taskContextMenu containerDelete',
				'position'	=> 40
			)
		)
	),
	'add' => array(
		'key'		=> 'add',
		'label'		=> 'project.task.contextmenu.add',
		'jsAction'	=> 'void(0)',
		'class'		=> 'taskContextMenu containerAdd',
		'position'	=> 40,
		'submenu'	=> array(
			'task'	=> array(
				'key'		=> 'add-task',
				'label'		=> 'project.task.contextmenu.add.task',
				'jsAction'	=> 'Todoyu.Ext.project.Container.addSubTask(#ID#)',
				'class'		=> 'taskContextMenu containerAddTask'
			),
			'container'	=> array(
				'key'		=> 'add-container',
				'label'		=> 'project.task.contextmenu.add.container',
				'jsAction'	=> 'Todoyu.Ext.project.Container.addSubContainer(#ID#)',
				'class'		=> 'taskContextMenu containerAddContainer'
			)
		)
	),
	'status' => Todoyu::$CONFIG['EXT']['project']['ContextMenu']['Task']['status']
);






	/**
	 * Context menu configuration for project
	 */
Todoyu::$CONFIG['EXT']['project']['ContextMenu']['Project'] = array(
	'showinproject'	=> array(
		'key'		=> 'showinproject',
		'label'		=> 'project.ext.contextmenu.showinproject',
		'jsAction'	=> 'Todoyu.Ext.project.goToTaskInProject(0, #ID#)',
		'class'		=> 'projectContextMenu showInProject',
		'position'	=> 10
	),
	'edit'	=> array(
		'key'		=> 'edit',
		'label'		=> 'project.ext.contextmenu.edit',
		'jsAction'	=> 'Todoyu.Ext.project.Project.edit(#ID#)',
		'class'		=> 'projectContextMenu projectEdit',
		'position'	=> 20
	),
	'delete' => array(
		'key'		=> 'delete',
		'label'		=> 'project.ext.contextmenu.delete',
		'jsAction'	=> 'Todoyu.Ext.project.Project.remove(#ID#)',
		'class'		=> 'projectContextMenu projectDelete',
		'position'	=> 25
	),
	'status' => array(
		'key'		=> 'status',
		'label'		=> 'project.task.contextmenu.status.change',
		'jsAction'	=> 'void(0)',
		'class'		=> 'projectContextMenu projectStatus',
		'position'	=> 30,
		'submenu'	=> array(
			'planning'	=> array(
				'key'		=> 'status-planning',
				'label'		=> 'project.task.status.planning',
				'jsAction'	=> 'Todoyu.Ext.project.Project.updateStatus(#ID#, ' . STATUS_PLANNING . ')',
				'class'		=> 'projectContextMenu projectStatusPlanning'
			),
			'progress'	=> array(
				'key'		=> 'status-progress',
				'label'		=> 'project.task.status.progress',
				'jsAction'	=> 'Todoyu.Ext.project.Project.updateStatus(#ID#, ' . STATUS_PROGRESS . ')',
				'class'		=> 'projectContextMenu projectStatusProgress'
			),
			'done'	=> array(
				'key'		=> 'status-done',
				'label'		=> 'project.task.status.done',
				'jsAction'	=> 'Todoyu.Ext.project.Project.updateStatus(#ID#, ' . STATUS_DONE . ')',
				'class'		=> 'projectContextMenu projectStatusDone'
			),
			'warranty'	=> array(
				'key'		=> 'status-warranty',
				'label'		=> 'project.task.status.warranty',
				'jsAction'	=> 'Todoyu.Ext.project.Project.updateStatus(#ID#, ' . STATUS_WARRANTY . ')',
				'class'		=> 'projectContextMenu projectStatusWarranty'
			),
			'cleared'	=> array(
				'key'		=> 'status-cleared',
				'label'		=> 'project.task.status.cleared',
				'jsAction'	=> 'Todoyu.Ext.project.Project.updateStatus(#ID#, ' . STATUS_CLEARED . ')',
				'class'		=> 'projectContextMenu projectStatusCleared'
			)
		)
	),
	'addtask'	=> array(
		'key'		=> 'addtask',
		'label'		=> 'project.ext.contextmenu.add.task',
		'jsAction'	=> 'Todoyu.Ext.project.Project.addTask(#ID#)',
		'class'		=> 'projectContextMenu projectAddTask',
		'position'	=> 40
	),
	'addcontainer'	=> array(
		'key'		=> 'addcontainer',
		'label'		=> 'project.ext.contextmenu.add.container',
		'jsAction'	=> 'Todoyu.Ext.project.Project.addContainer(#ID#)',
		'class'		=> 'projectContextMenu projectAddContainer',
		'position'	=> 50
	)
);



	/**
	 * Context menu configuration task clipboard functions
	 */
Todoyu::$CONFIG['EXT']['project']['ContextMenu']['TaskClipboard'] = array(
	'paste'	=> array(
		'key'		=> 'paste',
		'label'		=> 'project.task.contextmenu.paste',
		'jsAction'	=> 'void(0)',
		'class'		=> 'taskContextMenu taskPaste',
		'position'	=> 35,
		'submenu'	=> array(
			'in'	=> array(
				'key'		=> 'paste-in',
				'label'		=> 'project.task.contextmenu.paste.in',
				'jsAction'	=> 'Todoyu.Ext.project.Task.paste(#ID#, \'in\')',
				'class'		=> 'taskContextMenu taskPasteIn'
			),
			'before'	=> array(
				'key'		=> 'paste-before',
				'label'		=> 'project.task.contextmenu.paste.before',
				'jsAction'	=> 'Todoyu.Ext.project.Task.paste(#ID#, \'before\')',
				'class'		=> 'taskContextMenu taskPasteBefore'
			),
			'after'	=> array(
				'key'		=> 'paste-after',
				'label'		=> 'project.task.contextmenu.paste.after',
				'jsAction'	=> 'Todoyu.Ext.project.Task.paste(#ID#, \'after\')',
				'class'		=> 'taskContextMenu taskPasteAfter'
			)
		)
	)
);

Todoyu::$CONFIG['EXT']['project']['ContextMenu']['TaskClipboardProject'] = array(
	'paste'	=> array(
		'key'		=> 'paste',
		'label'		=> 'project.task.contextmenu.paste',
		'jsAction'	=> 'Todoyu.Ext.project.Project.pasteTask(#ID#)',
		'class'		=> 'projectContextMenu taskPaste',
		'position'	=> 35
	)
);

?>