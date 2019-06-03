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
 * Task Manager
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskManager {

	/**
	 * Expanded tasks in project view
	 *
	 * @var	Array
	 */
	private static $expandedTaskIDs = null;

	/**
	 * Default ext table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_project_task';

	/**
	 * Installed tabs for tasks
	 *
	 * @var	Array
	 */
	private static $tabs = null;



	/**
	 * Get task quick create form object
	 *
	 * @param	Integer			$idProject
	 * @param	Array			$formData
	 * @return	TodoyuForm		form object
	 */
	public static function getQuickCreateForm($idProject = 0, array $formData = array()) {
		$idProject	= intval($idProject);

			// Check rights for project
		if( $idProject !== 0 ) {
			if( !TodoyuProjectTaskRights::isAddInProjectAllowed($idProject) ) {
				$idProject = 0;
			}
		}

			// Create empty record of type task cache first. so hooks know what kind of task it is
		self::createNewTaskWithDefaultsInCache(0, 0, TASK_TYPE_TASK);

			// Construct form object
		$xmlPath	= 'ext/project/config/form/task.xml';
		$taskForm	= TodoyuFormManager::getForm($xmlPath, 0, array('quickcreate'=>true));

			// Adjust for quick create
		$taskForm->removeField('id_parenttask', true);
		$taskForm->removeHiddenField('id_project');

			// Load form with extra field data
		$xmlPathInsert	= 'ext/project/config/form/field-id_project.xml';
		$insertForm		= TodoyuFormManager::getForm($xmlPathInsert);

			// If person can add tasks in all project, show auto-completion field, else only a select element
		$field	= $insertForm->getField( Todoyu::allowed('project', 'addtask:addTaskInAllProjects') ? 'id_project_ac' : 'id_project_select');
		$taskForm->getFieldset('basic')->addField('id_project', $field, 'before:title');

			// Change form action and button functions
		$taskForm->setAttribute('action', 'index.php?ext=project&amp;controller=quickcreatetask');
		$taskForm->getField('save')->setAttribute('onclick', 'Todoyu.Ext.project.QuickCreateTask.save(this.form)');
		$taskForm->getField('cancel')->setAttribute('onclick', 'Todoyu.Popups.close(\'quickcreate\')');

			// Merge default data with current form data (only override empty fields)
		$defaultFormData= self::getTaskDefaultData(0, $idProject);
		$formData		= TodoyuArray::mergeEmptyFields($formData, $defaultFormData);
		$formData		= TodoyuFormHook::callLoadData($xmlPath, $formData, 0, array('form'=>$taskForm));

		$taskForm->setFormData($formData);

		return $taskForm;
	}



	/**
	 * Get object of a task.
	 *
	 * @param	Integer		$idTask		Task ID
	 * @return	TodoyuProjectTask
	 */
	public static function getTask($idTask) {
		return TodoyuRecordManager::getRecord('TodoyuProjectTask', $idTask);
	}



	/**
	 * Get task ID by full task number
	 *
	 * @param	String		$fullTaskNumber			Task number separated by point (.)
	 * @return	Integer		0 if task not found
	 */
	public static function getTaskIDByTaskNumber($fullTaskNumber) {
		$idTask	= 0;
		$parts	= TodoyuArray::intExplode('.', $fullTaskNumber, true, true);

		if( sizeof($parts) === 2 ) {
			$field	= 'id';
			$table	= self::TABLE;
			$where	= '		id_project	= ' . $parts[0]
					. ' AND	tasknumber	= ' . $parts[1];
			$limit	= 1;

			$foundID= Todoyu::db()->getFieldValue($field, $table, $where, '', '', $limit);

			if( $foundID !== false ) {
				$idTask = intval($foundID);
			}
		}

		return $idTask;
	}



	/**
	 * Get a number of tasks as array
	 *
	 * @param	Array	$taskIDs
	 * @param	String	$order
	 * @return	Array
	 */
	public static function getTasks(array $taskIDs, $order = 'id') {
		$taskIDs= TodoyuArray::intval($taskIDs, true, true);
		$tasks	= array();

		if( !empty($taskIDs) ) {
			$where	= 'id IN(' . implode(',', $taskIDs) . ')';
			$tasks	= TodoyuRecordManager::getAllRecords(self::TABLE, $where, $order);
		}

		return $tasks;
	}



	/**
	 * Save a task. If a task number is given, the task will be updated, otherwise a new task will be created
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function saveTask(array $data) {
		$xmlPath	= 'ext/project/config/form/task.xml';
		$idTask		= intval($data['id']);
		$isNew		= $idTask === 0;

		if( $idTask === 0 ) {
				// Create new task with necessary data
			$firstData	= array(
				'id_project'	=> intval($data['id_project']),
				'id_parenttask'	=> intval($data['id_parenttask'])
			);

			$idTask = self::addTask($firstData);
		}

			// Check for type
		if( empty($data['type']) ) {
			$data['type']	= TASK_TYPE_TASK;
		}

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idTask, array('new'=>$isNew));

			// Get default data from preset for new task
		if( $isNew ) {
			$data	= self::setDefaultValuesForNotAllowedFields($data);
		}

			// Set acknowledged if assigned to the user itself
		if( !isset($data['is_acknowledged']) && intval($data['id_person_assigned']) === Todoyu::personid() ) {
			$data['is_acknowledged'] = 1;
		}

			// Update the task with cleaned and collected fallback data
		self::updateTask($idTask, $data, $isNew);
		self::removeTaskFromCache($idTask);

			// Inform about new added task (delayed, because the actual add only adds some very basic dat
		if( $isNew ) {
			TodoyuHookManager::callHook('project', 'task.add', array($idTask));
		}

		return $idTask;
	}



	/**
	 * Update task
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$data
	 * @param	Boolean		$isNew
	 * @return	Boolean
	 */
	public static function updateTask($idTask, array $data, $isNew = false) {
		$idTask	= intval($idTask);

		self::removeTaskFromCache($idTask);

		$success = TodoyuRecordManager::updateRecord(self::TABLE, $idTask, $data);

		if( !$isNew ) {
			TodoyuHookManager::callHook('project', 'task.update', array($idTask, $data));
		}

		return $success;
	}



	/**
	 * Add a new task
	 *
	 * @param	Array		$data
	 * @return	Integer		Task ID
	 */
	public static function addTask(array $data = array()) {
			// Create task number
		$idProject			= intval($data['id_project']);
		$data['tasknumber'] = TodoyuProjectProjectManager::getNextTaskNumber($idProject);

			// Create sorting flag
		$idParent		= intval($data['id_parenttask']);
		$data['sorting']= self::getNextSortingPosition($idProject, $idParent);

		$data	= TodoyuProjectTaskPresetManager::applyTaskPreset($data);
		$data	= self::applyMissingRequiredFields($data);

		$idTask	= TodoyuRecordManager::addRecord(self::TABLE, $data);

		return $idTask;
	}



	/**
	 * Get next sorting position for a new task. For every sub task, sorting starts new
	 *
	 * @param	Integer		$idProject
	 * @param	Integer		$idParentTask
	 * @return	Integer
	 */
	public static function getNextSortingPosition($idProject, $idParentTask) {
		$idProject		= intval($idProject);
		$idParentTask	= intval($idParentTask);

		$field		= 'MAX(sorting) as maxSorting';
		$table		= self::TABLE;
		$where		= '		id_project		= ' . $idProject .
					  ' AND	id_parenttask	= ' . $idParentTask;
		$group		= 'id_parenttask';
		$fieldName	= 'maxSorting';

		TodoyuCache::disable();
		$maxSorting	= Todoyu::db()->getFieldValue($field, $table, $where, $group, '', '', $fieldName); // getRecordByQuery($fields, $table, $where, $group);
		TodoyuCache::enable();

		if( $maxSorting === false ) {
			return 0;
		} else {
			return intval($maxSorting) + 1;
		}
	}



	/**
	 * Delete a task
	 *
	 * @param	Integer		$idTask
	 */
	public static function deleteTask($idTask) {
		$data	= array(
			'deleted'		=> 1,
		);

		self::updateTask($idTask, $data);

		TodoyuHookManager::callHook('project', 'task.delete', array($idTask));

			// Delete all sub tasks

		$allSubTaskIDs	= self::getAllSubTaskIDs($idTask);

		if( !empty($allSubTaskIDs) ) {
			$where	= 'id IN(' . implode(',', $allSubTaskIDs) . ')';
			$update	= array(
				'deleted'		=> 1,
				'date_update'	=> NOW
			);

			Todoyu::db()->doUpdate(self::TABLE, $where, $update);
		}

			// Call delete hook for all subtasks
		foreach($allSubTaskIDs as $idSubTask) {
			TodoyuHookManager::callHook('project', 'task.delete', array($idSubTask));
		}
	}



	/**
	 * Delete all tasks of given project
	 *
	 * @param	Integer		$idProject
	 */
	public static function deleteProjectTasks($idProject) {
		$idProject	= intval($idProject);

		$where	= 'id_project = ' . $idProject;
		$data	= array(
			'deleted'	=> 1
		);

		TodoyuRecordManager::updateRecords(self::TABLE, $where, $data);

		$allTaskIDs	= TodoyuProjectProjectManager::getAllTaskIDs($idProject);

		foreach($allTaskIDs as $idTask) {
			TodoyuHookManager::callHook('project', 'task.delete', array($idTask));
		}
	}



	/**
	 * Add a new container
	 *
	 * @param	Array		$data
	 * @return	Integer		New container ID
	 */
	public static function addContainer(array $data) {
		$data['type']	= TASK_TYPE_CONTAINER;

		return self::addTask($data);
	}



	/**
	 * Update task status only (shortcut for updateTask)
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$newStatus
	 */
	public static function updateTaskStatus($idTask, $newStatus) {
		$data = array(
			'status' => intval($newStatus)
		);

		$data	= TodoyuHookManager::callHookDataModifier('project', 'onTaskStatusChanged', $data, array($idTask));

		self::updateTask($idTask, $data);
	}



	/**
	 * Update status of multiple tasks
	 *
	 * @param	Array		$taskIDs
	 * @param	Integer		$status
	 */
	public static function updateTaskStatuses(array $taskIDs, $status) {
		$update	= array(
			'status'	=> intval($status)
		);

		foreach($taskIDs as $idTask) {
			$update	= TodoyuHookManager::callHookDataModifier('project', 'onTaskStatusChanged', $update, array($idTask));
		}

		self::updateTasks($taskIDs, $update);
	}



	/**
	 * Update multiple tasks
	 *
	 * @param	Array		$taskIDs
	 * @param	Array		$data
	 */
	public static function updateTasks(array $taskIDs, array $data) {
		$taskIDs= TodoyuArray::intval($taskIDs);

		if( !empty($taskIDs) ) {
			$where	= 'id IN(' . implode(',', $taskIDs) . ')';

			Todoyu::db()->doUpdate(self::TABLE, $where, $data);

			foreach($taskIDs as $idTask) {
				TodoyuHookManager::callHook('project', 'task.update', array($idTask, $data));
			}
		}
	}



	/**
	 * Get the project ID of a task
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getProjectID($idTask) {
		$idTask	= intval($idTask);

		return self::getTask($idTask)->getProjectID();
	}



	/**
	 * Get the project object of a task
	 *
	 * @param	Integer		$idTask
	 * @return	TodoyuProjectProject
	 */
	public static function getProject($idTask) {
		$idProject	= self::getProjectID($idTask);

		return TodoyuProjectProjectManager::getProject($idProject);
	}



	/**
	 * Get the context menu items for a task/container
	 *
	 * @param	Int		$idTask
	 * @param	Array	$items
	 * @return	Array	Config array for context menu
	 */
	public static function getContextMenuItems($idTask, array $items) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		if( $task->isDeleted() ) {
			return $items;
		}

		$project	= $task->getProject();
		$allowed	= array();
		$taskType	= $task->isTask() ? 'Task' : 'Container';
		$ownItems	= Todoyu::$CONFIG['EXT']['project']['ContextMenu'][$taskType];

			// Edit
		if( $task->isEditable(false) ) {
			$allowed['edit'] = $ownItems['edit'];
		}

			// Add project back-link if not in project area
		if( AREA !== EXTID_PROJECT ) {
			if( TodoyuProjectProjectRights::isSeeAllowed($task->getProjectID()) ) {
				$allowed['showinproject'] = $ownItems['showinproject'];
			}
		}

			// Actions (with sub menu)
		$allowed['actions'] = $ownItems['actions'];
		unset($allowed['actions']['submenu']);

			// Prepare rights check
		$addTasksInOwnProjects	= Todoyu::allowed('project', 'addtask:addTaskInOwnProjects');
		$hasTaskEditRight		= TodoyuProjectTaskRights::hasStatusRight($task->getStatusKey(), 'edit');

			// Copy & Cut
		if( $addTasksInOwnProjects ) {
				// Copy
			$allowed['actions']['submenu']['copy']	= $ownItems['actions']['submenu']['copy'];

				// Cut
			if( $hasTaskEditRight ) {
				$allowed['actions']['submenu']['cut']	= $ownItems['actions']['submenu']['cut'];
			}
		}

			// Clone
		if( TodoyuProjectTaskRights::isCloneAllowed($idTask) ) {
			$allowed['actions']['submenu']['clone']	= $ownItems['actions']['submenu']['clone'];
		}

			// Delete
		if( $task->isDeletable() ) {
			$allowed['actions']['submenu']['delete'] = $ownItems['actions']['submenu']['delete'];
		}

			// Add... (with sub menu: container/task)
		$allowed['add'] = $ownItems['add'];
		unset($allowed['add']['submenu']);

		if( AREA !== EXTID_SEARCH && ! $project->isLocked() ) {
				// Add sub task
			if( TodoyuProjectTaskRights::isAddInTaskProjectAllowed($idTask) ) {
				$allowed['add']['submenu']['task'] = $ownItems['add']['submenu']['task'];
			}
				// Add sub container
			if( TodoyuProjectTaskRights::isAddInContainerProjectAllowed($idTask) ) {
				$allowed['add']['submenu']['container'] = $ownItems['add']['submenu']['container'];
			}
		}

			// Status
		if( TodoyuProjectTaskRights::isStatusChangeAllowed($idTask) ) {
			$allowed['status'] = $ownItems['status'];

			$statuses = TodoyuProjectTaskStatusManager::getStatuses('changeto');

			foreach($allowed['status']['submenu'] as $key => $status) {
				if( ! in_array($key, $statuses) ) {
					unset($allowed['status']['submenu'][$key]);
				}
			}
		}

		return array_merge_recursive($items, $allowed);
	}



	/**
	 * Remove empty parent menus if they have no sub entries
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$items
	 * @return	Array
	 */
	public static function removeEmptyContextMenuParents($idTask, array $items) {
			// Remove actions if empty
		if( ! is_array($items['actions']['submenu']) ) {
			unset($items['actions']);
		}

			// Remove add if empty
		if( ! is_array($items['add']['submenu']) || empty($items['add']['submenu']) ) {
			unset($items['add']);
		}

			// Remove status if empty
		if( ! is_array($items['status']['submenu']) ) {
			unset($items['status']);
		}

		return $items;
	}



	/**
	 * Get the IDs of all sub tasks of a task
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getSubTaskIDs($idTask) {
		$idTask	= intval($idTask);

		$filters	= TodoyuProjectProjectManager::getTaskTreeFilterStruct();

		$filters[]	= array(
			'filter'=> 'parentTask',
			'value'	=> $idTask
		);

		$taskFilter	= new TodoyuProjectTaskFilter($filters);

		 return $taskFilter->getTaskIDs();
	}



	/**
	 * Get ALL sub tasks of a task (the whole tree, instead only the direct children)
	 * Get also sub-sub-...-tasks
	 *
	 * @param	Integer			$idTask
	 * @param	String|Boolean	$extraWhere
	 * @return	Array
	 */
	public static function getAllSubTaskIDs($idTask, $extraWhere = false) {
		$idTask		= intval($idTask);
		$subTasks	= array();

		if( $idTask > 0 ) {
			$field	= 'id';
			$table	= self::TABLE;
			$whereF	= '		id_parenttask	IN(%s)'
					. '	AND	deleted			= 0';

			if( $extraWhere ) {
				$whereF .= ' AND (' . $extraWhere . ')';
			}

			$where	= sprintf($whereF, $idTask);
			$order	= 'sorting';

			$newTasks	= Todoyu::db()->getColumn($field, $table, $where, '', $order);
			if(empty($newTasks)) $newTasks = array();
			while( sizeof($newTasks) > 0 ) {
				$subTasks = array_merge($subTasks, $newTasks);
				$where = sprintf($whereF, implode(',', $newTasks));
				$newTasks = Todoyu::db()->getColumn($field, $table, $where, '', $order);
			}
		}

		return $subTasks;
	}



	/**
	 * Get estimated workload of task
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getEstimatedWorkload($idTask) {
		$idTask	= intval($idTask);

		return self::getTask($idTask)->get('estimated_workload');
	}



	/**
	 * Get direct sub tasks (as data array) of given task (1 level)
	 *
	 * @param	Integer		$idTask
	 * @param	String		$order
	 * @return	Array
	 */
	public static function getSubTasks($idTask, $order = 'date_create') {
		$idTask	= intval($idTask);

		if( $idTask === 0 ) {
			return array();
		}

		$where	= '		id_parenttask	= ' . $idTask
				. ' AND	deleted			= 0';

		return TodoyuRecordManager::getAllRecords(self::TABLE, $where, $order);
	}



	/**
	 * Get IDs of direct (1 level) sub tasks of given task
	 *
	 * @param	Integer		$idTask
	 * @param	String		$order
	 * @return	Array
	 */
	public static function getSubTasksIDs($idTask, $order = 'sorting') {
		$idTask	= intval($idTask);

		if( $idTask === 0 ) {
			return array();
		}

		$field	= 'id';
		$where	= '		id_parenttask	= ' . $idTask
				. ' AND	deleted			= 0';

		return Todoyu::db()->getColumn($field, self::TABLE, $where, '', $order);
	}



	/**
	 * Check whether a task has sub tasks
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function hasSubTasks($idTask) {
		$idTask	= intval($idTask);

		$subTaskIDs	= self::getSubTaskIDs($idTask);

		return sizeof($subTaskIDs) > 0;
	}



	/**
	 * Check whether a task is a sub task of a task.
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idParent
	 * @param	Boolean		$checkDeep		TRUE: check all levels, FALSE: check only direct childs
	 * @return	Boolean
	 */
	public static function isSubTaskOf($idTask, $idParent, $checkDeep = false) {
		$idTask		= intval($idTask);
		$idParent	= intval($idParent);

		if( $checkDeep ) {
			$subTasks	= self::getAllSubTaskIDs($idParent);
		} else {
			$subTasks	= self::getSubTaskIDs($idParent);
		}

		return in_array($idTask, $subTasks);
	}



	/**
	 * Check whether task has a parent
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function hasParentTask($idTask) {
		$idTask	= intval($idTask);

		$field	= 'id_parenttask';
		$table	= self::TABLE;
		$where	= 'id = ' . $idTask;

		$task	= Todoyu::db()->getRecordByQuery($field, $table, $where);

		return intval($task['id_parenttask']) > 0;
	}



	/**
	 * Check whether the given task is deleted
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isDeleted($idTask) {
		return self::getTask($idTask)->isDeleted();
	}



	/**
	 * Get all persons which are somehow connected with this task (and allowed to be seen by the current user)
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$withAccount
	 * @return	Array
	 */
	public static function getTaskPersons($idTask, $withAccount = false) {
		$idTask	= intval($idTask);

		$fields	= ' p.*';
		$tables	= ' ext_contact_person p,
					ext_project_task t';

		$where	= '	t.id				= ' . $idTask
				. '	AND	(
							t.id_person_create	= p.id
						OR	t.id_person_assigned= p.id
						OR	t.id_person_owner	= p.id
					)';

			// Add public/allowed persons check for external person
		if( ! Todoyu::person()->isInternal() && ! Todoyu::person()->isAdmin() && ! Todoyu::allowed('contact', 'person:seeAllPersons') ) {
			$allowedPersonIDs = TodoyuContactPersonRights::getPersonIDsAllowedToBeSeen();
			if( count($allowedPersonIDs) > 0 ) {
				$where .= ' AND ' . TodoyuSql::buildInListQueryPart($allowedPersonIDs, 'p.id');
			} else {
				return array();
			}
		}

		$group	= 'p.id';
		$order	= 'p.lastname, p.firstname';

			// Add check for active account
		if( $withAccount ) {
			$where .= ' AND p.is_active = 1';
		}

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order);
	}



	/**
	 * Get IDs of persons which are assigned to the task
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$withAccount
	 * @return	Array
	 */
	public static function getTaskPersonIDs($idTask, $withAccount = false) {
		$persons	= self::getTaskPersons($idTask, $withAccount);

		return TodoyuArray::getColumnUnique($persons, 'id');
	}



	/**
	 * Get task owner
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskOwner($idTask) {
		$idTask	= intval($idTask);

		$fields	= ' u.*';
		$tables	= ' ext_project_task t,
					ext_contact_person u';
		$where	= '		t.id	= ' . $idTask .
				  ' AND	u.id	= t.id_person_owner';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get all task data informations.
	 * Information from all extensions are merged and the list is sorted
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getAllTaskAttributes($idTask) {
		$idTask	= intval($idTask);
		$data	= array();

		return TodoyuHookManager::callHookDataModifier('project', 'taskdata', $data, array($idTask));
	}



	/**
	 * Get info array for a task. This array contains the data from getTemplateData()
	 * of the task and the data provided by all registered hooks
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$infoLevel
	 * @return	Array
	 */
	public static function getTaskInfoArray($idTask, $infoLevel = 0) {
		$idTask		= intval($idTask);
		$infoLevel	= intval($infoLevel);
		$task		= self::getTask($idTask);

		$data	= $task->getTemplateData($infoLevel);

			// Call hooks to add extra data (filled in in the data array)
		$data	= TodoyuHookManager::callHookDataModifier('project', 'taskinfo', $data, array($idTask, $infoLevel));

		return $data;
	}



	/**
	 * Attributes for task data list
	 *
	 * @param	Array		$data
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskAttributes(array $data, $idTask) {
		$idTask		= intval($idTask);
		$task		= TodoyuProjectTaskManager::getTask($idTask);

		$isInternal			= TodoyuAuth::isInternal();
		$visiblePersonIDs	= TodoyuContactPersonRights::getPersonIDsAllowedToBeSeen();
		$seeAllPersons		= Todoyu::allowed('contact', 'person:seeAllPersons');

			// Attributes which are only for tasks (not relevant for containers)
		if( $task->isTask() ) {
				// Date end (if set) (internal deadline)
			if( $isInternal && $task->hasDateEnd()  ) {
				$formatEnd	= self::getTaskDetailsDateFormat($task->getDateEnd());
				$data['date_end'] = array(
					'label'		=> 'project.task.attr.date_end',
					'value'		=> TodoyuTime::format($task->getDateEnd(), $formatEnd),
					'position'	=> 20,
					'className'	=> $task->isDateEndExceeded() ? 'red' : ''
				);
			}

				// Person assigned
			if( $isInternal && $task->hasPersonAssigned() ) {
				$data['person_assigned']	= array(
					'label'		=> 'project.task.attr.person_assigned',
					'value'		=> $task->getPersonAssigned()->getLabel(),
					'position'	=> 110,
					'className'	=> 'sectionStart ' . ( $task->isAcknowledged() ? 'acknowledged' : 'unread')
				);
			}

				// Activity type
			$data['activity'] = array(
				'label'		=> 'project.task.attr.activity',
				'value'		=> $task->getActivity()->getTitle(),// 'Internes / Administration',
				'position'	=> 220
			);

				// Estimated workload
			if( $isInternal && $task->hasEstimatedWorkload() ) {
				$data['estimated_workload']	= array(
					'label'		=> 'project.task.attr.estimated_workload',
					'value'		=> TodoyuTime::formatHours($task->getEstimatedWorkload()),
					'position'	=> 230
				);
			}

				// Date create
			$data['date_create']	= array(
				'label'		=> 'project.task.attr.date_create',
				'value'		=> TodoyuTime::format($task->getDateCreate(), 'datetime'),
				'position'	=> 140,
				'className'	=> ''
			);
		}



			// Attributes of tasks and containers

			// Date start
		if( $isInternal ) {
			$dateFormat = self::getTaskDetailsDateFormat($task->getDateStart());
			$data['date_start']	= array(
				'label'		=> 'project.task.attr.date_start',
				'value'		=> TodoyuTime::format($task->getDateStart(), $dateFormat),
				'position'	=> 10
			);
		}


			// Date deadline
		if( $task->hasDateDeadline() ) {
			$dateFormat	= self::getTaskDetailsDateFormat($task->getDateDeadline());
			$data['date_deadline']	= array(
				'label'		=> 'project.task.attr.date_deadline',
				'value'		=> TodoyuTime::format($task->getDateDeadline(), $dateFormat),
				'position'	=> 30,
				'className'	=> $task->isDateDeadlineExceeded() ? 'red' : ''
			);
		}


			// -------- SECTION ------------
			// Person owner
		if( $task->hasOwnerPerson() ) {
			if( $seeAllPersons || in_array($task->getPersonOwnerID(), $visiblePersonIDs) ) {
				$data['person_owner'] = array(
					'label'		=> $task->isContainer() ? 'project.task.container.attr.person_owner' : 'project.task.attr.person_owner',
					'value'		=> $task->getPersonOwner()->getLabel(),
					'position'	=> 120,
					'className'	=> $task->isContainer() ? 'sectionStart' : ''
				);
			}
		}

			// Task creator: Different person owns / created task? have both displayed
		if( !$task->isOwnerAndCreatorSame() ) {
			if( $seeAllPersons || in_array($task->getPersonOwnerID(), $visiblePersonIDs) ) {
				$data['person_create'] = array(
					'label'		=> 'project.task.attr.person_create',
					'value'		=> $task->getPersonCreate()->getLabel(),
					'position'	=> 130
				);
			}
		}

			// Status
		$data['status']	= array(
			'label'		=> 'core.global.status',
			'value'		=> $task->getStatusLabel(),
			'position'	=> 210,
			'className'	=> 'sectionStart'
		);
			// Public
		if( $isInternal ) {
			$data['is_public']	= array(
				'label'		=> $task->isContainer() ? 'project.task.container.attr.is_public' : 'project.task.attr.is_public',
				'value'		=> Todoyu::Label('project.task.attr.is_public.' . ($task->isPublic() ? 'public' : 'private') . ($task->isContainer() ? '.container' : '')) ,
				'position'	=> 240
			);
		}

		return $data;
	}



	/**
	 * Add container info to task data
	 *
	 * @param	Array		$taskData
	 * @param	Integer		$idTask
	 * @param	Integer		$infoLevel
	 * @return	Array
	 */
	public static function addContainerInfoToTaskData($taskData, $idTask, $infoLevel) {
		$idTask		= intval($idTask);
		$task		= self::getTask($idTask);

			// Add special CSS class for containers
		if( $task->isContainer() ) {
			$taskData['class'] .= ' container';
		}

		return $taskData;
	}



	/**
	 * Get all info icons
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getAllTaskIcons($idTask) {
		$idTask	= intval($idTask);
		$icons	= array();

		$icons	= TodoyuHookManager::callHookDataModifier('project', 'taskIcons', $icons, array($idTask));

		$icons	= TodoyuArray::sortByLabel($icons, 'position');

		return $icons;
	}



	/**
	 * Get all task header extras
	 * This extras will be displayed between the task label and the task number
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getAllTaskHeaderExtras($idTask) {
		$idTask	= intval($idTask);
		$extras	= array();

		$extras	= TodoyuHookManager::callHookDataModifier('project', 'taskHeaderExtras', $extras, array($idTask));

		$extras	= TodoyuArray::sortByLabel($extras, 'position');

		return $extras;
	}



	/**
	 * Get project task info icons
	 *
	 * @param	Array		$icons
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskIcons(array $icons, $idTask) {
		$idTask	= intval($idTask);
		$task	= self::getTask($idTask);

			// Add public icon for internals
		if( TodoyuProjectTaskRights::isSeePublicFlagAllowed($task->isPublic()) ) {
			$icons['public'] = array(
				'id'		=> 'task-' . $idTask . '-public',
				'class'		=> 'isPublic',
				'label'		=> 'project.task.attr.is_public.public' . ($task->isContainer() ? '.container' : ''),
				'position'	=> 80
			);
		}

			// Is acknowledged?
		if( $task->isTask() && ! $task->isAcknowledged() && $task->isCurrentPersonAssigned() ) {
			$icons['notacknowledged'] = array(
				'id'		=> 'task-' . $idTask . '-notacknowledged',
				'class'		=> 'notAcknowledged',
				'label'		=> 'project.task.attr.notAcknowledged',
				'onclick'	=> 'Todoyu.Ext.project.Task.setAcknowledged(' . $idTask . ')',
				'position'	=> 100
			);
		}

			// Locked (not editable)
		if( $task->isLocked() ) {
			$icons['locked'] = array(
				'id'		=> 'task-' . $idTask . '-locked',
				'class'		=> 'locked',
				'label'		=> 'project.task.attr.locked',
				'position'	=> 150
			);
		}


			// Deadline or Enddate exceeded
		if( $task->isDateDeadlineExceeded() || $task->isDateEndExceeded() ) {
			$icons['dateover'] = array(
				'id'		=> 'task-' . $idTask . '-dateover',
				'class'		=> 'dateover',
				'label'		=> 'project.task.attr.dateover',
				'position'	=> 150
			);
		}

		return $icons;
	}



	/**
	 * Remove a task from cache (only necessary if the task has been loaded from database
	 * and updated after in the same request and needs to be loaded again
	 *
	 * @param	Integer		$idTask
	 */
	public static function removeTaskFromCache($idTask) {
		$idTask	= intval($idTask);

		TodoyuRecordManager::removeRecordCache('TodoyuProjectTask', $idTask);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idTask);
	}



	/**
	 * Set task acknowledged
	 *
	 * @param	Integer		$idTask
	 */
	public static function setTaskAcknowledged($idTask) {
		$idTask	= intval($idTask);

		if( self::getTask($idTask)->isCurrentPersonAssigned() ) {
			$update	= array(
				'is_acknowledged' => 1
			);

			self::updateTask($idTask, $update);
		}
	}



	/**
	 * Get task auto-completion label
	 *
	 * @param	Integer	$idTask
	 * @return	String
	 */
	public static function getAutocompleteLabel($idTask) {
		$idTask	= intval($idTask);
		$label	= '';

		if( $idTask > 0 ) {
			$task	= self::getTask($idTask);
			$label	= '[' . $task->getTaskNumber(true) . '] ' . $task->getTitle();
		}

		return $label;
	}



	/**
	 * Get tasks in given timespan
	 * If timestamp of start/end == 0: don't use it (there by this method can be used as well to query for tasks before / after a given timestamp)
	 * If personIDs given:	limit to tasks assigned to given persons
	 * If statuses given:	limit to tasks with given statuses
	 *
	 * @param	Integer		$start
	 * @param	Integer		$end
	 * @param	Array		$statusIDs
	 * @param	Array		$personIDs		(id_person_assigned)
	 * @param	String		$limit
	 * @param	Boolean		$getContainers
	 * @return	Array
	 */
	public static function getTasksInTimeSpan($start = 0, $end = 0, array $statusIDs = array(), array $personIDs = array(), $limit = '', $getContainers = false) {
		$fields	= '*';
		$table	= self::TABLE;
		$where	= self::getTasksInTimeSpanWhereClause($start, $end, $statusIDs, $personIDs, $getContainers);
		$order	= 'date_start';
		$index	= 'id';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit, $index);
	}



	/**
	 * Get IDs of tasks in given timespan
	 * If timestamp of start/end == 0: don't use it (there by this method can be used as well to query for tasks before / after a given timestamp)
	 * If personIDs given:	limit to tasks assigned to given persons
	 * If statuses given:	limit to tasks with given statuses
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @param	Array		$projectIDs
	 * @param	Array		$statusIDs
	 * @param	Array		$personIDs		(id_person_assigned)
	 * @param	String		$limit
	 * @param	Boolean		$getContainers
	 * @return	Array
	 */
	public static function getTaskIDsInTimeSpan($dateStart, $dateEnd, array $projectIDs = array(), array $statusIDs = array(), array $personIDs = array(), $limit = '', $getContainers = false) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$projectIDs	= TodoyuArray::intval($projectIDs, true, true);
		$statusIDs	= TodoyuArray::intval($statusIDs, true, true);
		$personIDs	= TodoyuArray::intval($personIDs, true, true);

		$where	= self::getTasksInTimeSpanWhereClause($dateStart, $dateEnd, $statusIDs, $personIDs, $getContainers);

		if( sizeof($projectIDs) > 0 ) {
			$where .= ' AND ' . TodoyuSql::buildInListQueryPart($projectIDs, 'id_project');
		}

		$field	= 'id';
		$order	= 'date_start';

		return Todoyu::db()->getColumn($field, self::TABLE, $where, '', $order, $limit);
	}



	/**
	 * Get WHERE clause for tasks in timespan query
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @param	Array		$statusIDs
	 * @param	Array		$personIDs
	 * @param	Boolean		$getContainers
	 * @return	String
	 */
	public static function getTasksInTimeSpanWhereClause($dateStart, $dateEnd, array $statusIDs = array(), array $personIDs = array(), $getContainers = false) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$statusIDs	= TodoyuArray::intval($statusIDs, true, true);
		$personIDs	= TodoyuArray::intval($personIDs, true, true);

		$where	=  ' deleted = 0 ';

		if( $getContainers !== true ) {
			$where .= ' AND `type` = 1 ';
		}

			// Start and end given: task must intersect with span defined by them
		if( $dateStart > 0 && $dateEnd > 0 ) {
			$where	.= ' AND date_start		<= ' . $dateEnd . '
						 AND date_deadline	>= ' . $dateStart;
		} else {
				// Only start or end given. Start and end of task must be (at or) after given starting time
			if( $dateStart > 0 ) {
				$where	.= ' AND date_deadline >= ' . $dateStart;
			}
				// Start of task must be (at or) before given ending time
			if( $dateEnd > 0 ) {
				$where	.='	AND date_start <= ' . $dateEnd;
			}
		}

			// Filter by status IDs
		if( count($statusIDs) > 0 ) {
			$where .= ' AND `status` IN(' . implode(',', $statusIDs) . ')';
		}
			// Filter by assigned person IDs
		if( sizeof($personIDs) ) {
			$where .= ' AND id_person_assigned IN(' . implode(',', $personIDs) . ')';
		}

		return $where;
	}



	/**
	 * Get default task data values for a new task/container
	 *
	 * @param	Integer		$idParentTask
	 * @param	Integer		$idProject
	 * @param	Integer		$type
	 * @param	Boolean		$isQuickTask
	 * @return	Array
	 */
	public static function getTaskDefaultData($idParentTask = 0, $idProject = 0, $type = TASK_TYPE_TASK, $isQuickTask = false) {
		$idParentTask	= intval($idParentTask);
		$idProject		= intval($idProject);
		$type			= intval($type);

			// Find project if not available as parameter
		if( $idProject === 0 && $idParentTask !== 0 ) {
			$idProject	= self::getProjectID($idParentTask);
		}

			// Set default data
		$data	= array(
			'id'				=> 0,
			'id_project'		=> $idProject,
			'id_person_assigned'=> 0,
			'id_person_owner'	=> Todoyu::personid(),
			'id_parenttask'		=> $idParentTask,
			'type'				=> $type,
			'status'			=> STATUS_OPEN
		);

			// Call hook to modify default task data
		$data	= TodoyuHookManager::callHookDataModifier('project', 'task.defaultData', $data, array($type, $idProject, $idParentTask, $isQuickTask));

		return $data;
	}



	/**
	 * Create a new task with default values and ID 0
	 * After we have done this, we can access this template task by ID 0 over normal mechanism
	 *
	 * @param	Integer			$idParentTask		ID of the parent task (if it has one)
	 * @param	Integer			$idProject			ID of the project. If task is in the root, there will be no parent task, so you have to give the project ID
	 * @param	Integer			$type				Type of the new task
	 */
	public static function createNewTaskWithDefaultsInCache($idParentTask, $idProject = 0, $type = TASK_TYPE_TASK) {
		$idParentTask	= intval($idParentTask);
		$idProject		= intval($idProject);
		$type			= intval($type);

			// Default task data
		$defaultData= self::getTaskDefaultData($idParentTask, $idProject, $type);

			// Store task with default data in cache
		$key	= TodoyuRecordManager::makeClassKey('TodoyuProjectTask', 0);
		$task	= TodoyuProjectTaskManager::getTask(0);
		$task->injectData($defaultData);
		TodoyuCache::set($key, $task);
	}



	/**
	 * Set default task values if missing
	 * Person may not be allowed to enter the values, so we use the defaults from taskpreset and extConf
	 *
	 * @param	Array		$taskData
	 * @return	Array
	 */
	private static function setDefaultValuesForNotAllowedFields(array $taskData) {
		$idProject		= intval($taskData['id_project']);
		$originalData	= $taskData;

			// Add data from task presets
		$taskData	= TodoyuProjectTaskPresetManager::applyTaskPreset($taskData);
		
			// Call hook to allow other extensions to set default values
		$taskData	= TodoyuHookManager::callHookDataModifier('project', 'task.defaultsForNotAllowedFields', $taskData, array($idProject, $originalData));

			// Add still missing fields which are required
		$taskData	= self::applyMissingRequiredFields($taskData);

		return $taskData;
	}



	/**
	 * Add values for required fields which are still missing
	 *
	 * @param	Array		$data
	 * @return	Array
	 */
	private static function applyMissingRequiredFields(array $data) {
		if( !isset($data['status']) ) {
			$data['status'] = Todoyu::$CONFIG['EXT']['project']['taskDefaults']['status'];
		}

		return $data;
	}



	/**
	 * Get parent element date ranges. Parent means in this case container or project (not parent task)
	 *
	 * @param	Integer		$idTask			Task ID to check upwards from
	 * @param	Integer		$idProject		Used for project range check, if task ID is 0
	 * @param	Boolean		$checkSelf		Check element itself for container
	 * @return	Array		[start,end]
	 */
	public static function getParentDateRanges($idTask, $idProject = 0, $checkSelf = false) {
		$idTask		= intval($idTask);
		$idProject	= intval($idProject);
		$range		= false;

		if( $idTask > 0 ) {
			$rootLineTasks	= self::getRootlineTasksData($idTask);


			if( $checkSelf !== true ) {
					// Remove element itself
				array_shift($rootLineTasks);
			}

				// Check all parent elements if there is a container and use its dates for the range
			foreach($rootLineTasks as $task) {
				if( $task['type'] == TASK_TYPE_CONTAINER ) {
					$range	= array(
						'start'	=> $task['date_start'],
						'end'	=> $task['date_end']
					);
					break;
				}
			}
		}

			// If no container found, use project
		if( !$range ) {
			if( $idProject !== 0 ) {
				$project	= TodoyuProjectProjectManager::getProject($idProject);
			} elseif( $idTask !== 0 ) {
				$project	= TodoyuProjectTaskManager::getProject($idTask);
			}

			if( isset($project) ) {
				$range	= array(
					'start'	=> $project->getDateStart(),
					'end'	=> $project->getDateEnd()
				);
			}
		}

		return $range;
	}



	/**
	 * Get parent task ID
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getParentTaskID($idTask) {
		$idTask	= intval($idTask);

		$field	= 'id_parenttask';
		$table	= self::TABLE;
		$where	= 'id = ' . $idTask;

		$idParent	= Todoyu::db()->getFieldValue($field, $table, $where);

		return intval($idParent);
	}



	/**
	 * Get the root line of a task (all parent task IDs)
	 *
	 * @param	Integer		$idTask
	 * @return	Integer[]
	 */
	public static function getRootlineTaskIDs($idTask) {
		$idTask		= intval($idTask);

			// Check whether already cached
		$idCache	= 'rootline:' . $idTask;

		if( TodoyuCache::isIn($idCache) ) {
			$rootLine	= TodoyuCache::get($idCache);
		} else {
			$rootLine	= array();
			$idParent	= self::getParentTaskID($idTask);

			while( $idParent !== 0 ) {
				$rootLine[] = $idParent;
				$idParent = self::getParentTaskID($idParent);
			}

			TodoyuCache::set($idCache, $rootLine);
		}

		return $rootLine;
	}



	/**
	 * Get array which contains all tasks in the rootline of a task
	 * The task itself is the first element
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getRootlineTasksData($idTask) {
		$idTask	= intval($idTask);

		$rootLine	= self::getRootlineTaskIDs($idTask);
		$list		= implode(',', $rootLine);

		$where	= 'id IN(' . $list . ')';
		$order	= 'FIND_IN_SET(id, \'' . $list . '\')';

		return TodoyuRecordManager::getAllRecords(self::TABLE, $where, $order);
	}



	/**
	 * Get all tasks which are in the rootline of a task
	 *
	 * @param	Integer		$idTask
	 * @return	TodoyuProjectTask[]
	 */
	public static function getRootlineTasks($idTask) {
		$taskIDs	= self::getRootlineTaskIDs($idTask);

		return TodoyuRecordManager::getRecordList('TodoyuProjectTask', $taskIDs);
	}




	/**
	 * Get parent task of a task
	 * If there is no parent task (task is in project root), return false
	 *
	 * @param	Integer		$idTask
	 * @return	TodoyuProjectTask	Or FALSE if there is no parent task
	 */
	public static function getParentTask($idTask) {
		$idTask	= intval($idTask);

		$task		= self::getTask($idTask);
		$idParent	= $task->getParentTaskID();

		if( $idParent != 0 ) {
			return self::getTask($idParent);
		} else {
			return false;
		}
	}



	/**
	 * Check whether a task exists
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function taskExists($idTask) {
		$idTask	= intval($idTask);

		return TodoyuRecordManager::isRecord(self::TABLE, $idTask);
	}



	/**
	 * Check whether a task number is valid
	 * $mustExist is not set (default), only the format is checked.
	 * If $mustExist is set, also a database request will check if this task exists
	 *
	 * @param	Integer		$fullTaskNumber			Identifier with project ID and task number
	 * @param	Boolean		$mustExist				TRUE = Has to be in database
	 * @return	Boolean
	 */
	public static function isTaskNumber($fullTaskNumber, $mustExist = false) {
		$valid	= false;

			// Check for point (.)
		if( self::isTaskNumberFormat($fullTaskNumber) ) {
				// Split into project / task number
			$parts	= TodoyuArray::intExplode('.', $fullTaskNumber, true, true);

				// If 2 valid integers found
			if( sizeof($parts) === 2 ) {
					// Database check required?
				if( $mustExist ) {
						// Get task ID for validation
					$idTask	= self::getTaskIDByTaskNumber($fullTaskNumber);
					if( $idTask !== 0 ) {
						$valid = true;
					}
				} else {
						// If no db check required, set valid
					$valid = true;
				}
			}
		}

		return $valid;
	}



	/**
	 * Check whether string looks like a task number
	 *
	 * @param	String		$taskNumber
	 * @return	Boolean
	 */
	public static function isTaskNumberFormat($taskNumber) {
		$taskNumber	= trim($taskNumber);
		$pattern	= '/^\d+\.\d+$/';

		return preg_match($pattern, $taskNumber) === 1;
	}



	/**
	 * Check whether a task is visible (available for rendering)
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskVisible($idTask) {
		$idTask	= intval($idTask);

		if( self::taskExists($idTask) ) {
			$task	= TodoyuProjectTaskManager::getTask($idTask);

			if( ! $task->isDeleted() ) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Check whether a task is expanded
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isTaskExpanded($idTask) {
		$idTask	= intval($idTask);

		if( is_null(self::$expandedTaskIDs) ) {
			self::$expandedTaskIDs = TodoyuProjectPreferences::getExpandedTaskIDs();
		}

		return in_array($idTask, self::$expandedTaskIDs);
	}



	/**
	 * Modify form for task edit
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idTask
	 * @return	TodoyuForm
	 */
	public static function hookModifyFormfieldsForTask(TodoyuForm $form, $idTask) {
		$idTask	= intval($idTask);
		$task	= self::getTask($idTask);

		if( $task->isTask() ) {
				// New task has no parent?
			if( $idTask === 0 ) {
				$form->getField('id_parenttask')->remove();
				$form->addHiddenField('id_parenttask', 0);

				if( $form->hasField('status') ) {
						// Remove empty status field
					$statuses	= TodoyuProjectTaskStatusManager::getStatuses('create');

					if( sizeof($statuses) === 0 ) {
						$form->getField('status')->remove();
					}
				}
			} else {
				if( $form->hasField('status') ) {
						// Remove empty status field
					$statusesFrom	= TodoyuProjectTaskStatusManager::getStatuses('changefrom');
					$statusesTo		= TodoyuProjectTaskStatusManager::getStatuses('changeto');

					if( sizeof($statusesFrom) === 0 || sizeof($statusesTo) === 0 ) {
						$form->getField('status')->remove();
					}
				}
			}
		}

		return $form;
	}



	/**
	 * Modify task form object for container editing
	 *
	 * @param	TodoyuForm	$taskForm			Task edit form object
	 * @param	Integer		$idTask			Task ID
	 * @return	TodoyuForm	Modified form object
	 */
	public static function hookModifyFormfieldsForContainer(TodoyuForm $taskForm, $idTask) {
		$idTask	= intval($idTask);
		$task	= self::getTask($idTask);

			// Remove fields which are not needed in containers
		if( $task->isContainer() ) {
			$formFields			= $taskForm->getFieldnames();
				// Ensure the fields to be removed do still exist
			$fieldsToBeRemoved	= array_intersect($formFields, array(
				'id_activity',
				'estimated_workload',
				'date_end',
				'id_person_assigned',
			));

				// Remove the fields
			foreach( $fieldsToBeRemoved as $fieldName ) {
				if( $taskForm->getField($fieldName) ) {
					$taskForm->getField($fieldName)->remove();
				}
			}

				// Remove
			if( $idTask === 0 ) {
				if( in_array('id_parenttask', $formFields) ) {
					$taskForm->getField('id_parenttask')->remove();
				}
				$taskForm->addHiddenField('id_parenttask', 0);
			}

				// Set 'end date' label
			if( $taskForm->getField('date_deadline') ) {
				$taskForm->getField('date_deadline')->setLabel('project.ext.attr.date_end');
			}

			if( $taskForm->hasField('id_person_owner') ) {
				$taskForm->getField('id_person_owner')->setAttribute('label', 'project.task.container.attr.person_owner');
			}
			if( $taskForm->hasField('is_public') ) {
				$taskForm->getField('is_public')->setAttribute('label', 'project.task.container.attr.is_public');
			}
		}

			// Call hooks to modify $form
		$taskForm	= TodoyuHookManager::callHookDataModifier('project', 'task.modifyFormfieldsForContainer', $taskForm, array($idTask));

		return $taskForm;
	}



	/**
	 * Copy a task (set also a new parent)
	 *
	 * @param	Integer		$idTaskSource
	 * @param	Integer		$idParent
	 * @param	Boolean		$withSubTasks
	 * @param	Integer		$idProject
	 * @return	Integer
	 */
	public static function copyTask($idTaskSource, $idParent, $withSubTasks = true, $idProject = 0) {
		$idTaskSource	= intval($idTaskSource);
		$taskSource		= self::getTask($idTaskSource);
		$idParent		= intval($idParent);
		$idProject		= intval($idProject);

			// Get original task data
		$data	= $taskSource->getObjectData();

			// Set new project ID if given
		if( $idProject !== 0 ) {
			$data['id_project'] = $idProject;
		}
			// Set new parent (needed for sorting)
		$data['id_parenttask']	= $idParent;

			// Remove status if not allowed for copied tasks (new default status will be set during add process)
		if( !self::isAllowedStatusForCopiedTask($taskSource->getStatus()) ) {
			unset($data['status']);
		}

			// Call data modifier hook for task data
		$data	= TodoyuHookManager::callHookDataModifier('project', 'taskcopydata', $data, array($idTaskSource, $idParent, $withSubTasks, $idProject));

			// Add new task (with old data)
		$idTaskNew	= self::addTask($data);

			// Copy sub tasks if enabled
		if( $withSubTasks && $idTaskSource !== $idParent ) {
			$subTaskIDs = self::getSubTaskIDs($idTaskSource);

			foreach($subTaskIDs as $idSubTask) {
				self::copyTask($idSubTask, $idTaskNew, true, $idProject);
			}
		}

		TodoyuHookManager::callHook('project', 'task.copy', array($idTaskSource, $idTaskNew));

		return $idTaskNew;
	}



	/**
	 * Check whether status is allowed for copied tasks
	 *
	 * @param	Integer		$status
	 * @return	Boolean
	 */
	public static function isAllowedStatusForCopiedTask($status) {
		$allowedStatuses	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['project']['allowedCopiedStatus']);

		return in_array($status, $allowedStatuses);
	}



	/**
	 * Move task to new position
	 *
	 * @param	Integer		$idTaskMove
	 * @param	Integer		$idTaskRef
	 * @param	String		$position
	 */
	public static function moveTask($idTaskMove, $idTaskRef, $position = 'in') {
		if( $position === 'in' ) {
			self::insertAsSubtask($idTaskMove, $idTaskRef);
		} elseif( $position === 'after' || $position === 'before' ) {
			self::changeTaskOrder($idTaskMove, $idTaskRef, $position);
		}
	}



	/**
	 * Insert a task as subtasks
	 * Update the sorting position of the old and the new sorting group
	 *
	 * @param	Integer		$idTaskMove
	 * @param	Integer		$idParentTask
	 * @param	Integer		$idProject
	 */
	public static function insertAsSubtask($idTaskMove, $idParentTask, $idProject = 0) {
		$taskMove	= self::getTask($idTaskMove);

			// Update tasks in the old sorting group
		self::updateSortingGroup($taskMove->getProjectID(), $taskMove->getParentTaskID(), $taskMove->getTreePosition(), true);

			// Insert into new sorting group
		self::changeTaskParent($idTaskMove, $idParentTask, $idProject);
	}



	/**
	 * Move a task. Change its parent
	 * Move to another project is also supported
	 *
	 * @param	Integer		$idTaskMove				Task to move
	 * @param	Integer		$idTaskParent		New parent task
	 * @param	Integer		$idProject
	 * @return	Integer
	 */
	public static function changeTaskParent($idTaskMove, $idTaskParent, $idProject = 0) {
		$idTaskMove		= intval($idTaskMove);
		$idTaskParent	= intval($idTaskParent);
		$idProject		= intval($idProject);
		$taskMove		= self::getTask($idTaskMove);

			// Get project ID from parent task
		if( $idTaskParent !== 0 ) {
			$taskParent	= self::getTask($idTaskParent);
			$idProject	= $taskParent->getProjectID();
		}

			// Basic update
		$update		= array(
			'id_parenttask'	=> $idTaskParent,
			'id_project'	=> $idProject,
			'sorting'		=> self::getNextSortingPosition($idProject, $idTaskParent)
		);

			// If project changed, generate a new task number
		if( $taskMove->getProjectID() != $idProject ) {
			$update['tasknumber']	= TodoyuProjectProjectManager::getNextTaskNumber($idProject);
		}

			// Update the moved task
		self::updateTask($idTaskMove, $update);

			// If project changed, update also all sub tasks with new project ID and generate new task number
		if( $taskMove->getProjectID() != $idProject ) {
			$allSubTaskIDs	= self::getAllSubTaskIDs($idTaskMove);

			foreach($allSubTaskIDs as $idSubTask) {
				$subUpdate	= array(
					'id_project'	=> $idProject,
					'tasknumber'	=> TodoyuProjectProjectManager::getNextTaskNumber($idProject)
				);

				self::updateTask($idSubTask, $subUpdate);
			}
		}

		return $idTaskMove;
	}



	/**
	 * Clone given task
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$withSubTasks
	 * @return	Integer
	 */
	public static function cloneTask($idTask, $withSubTasks = true) {
		$idTask		= intval($idTask);
		$task		= self::getTask($idTask);
		$idNewTask	= self::copyTask($idTask, $task->getParentTaskID(), $withSubTasks, $task->getProjectID());

		self::changeTaskOrder($idNewTask, $idTask, 'after');

		return $idNewTask;
	}



	/**
	 * Change to sorting order of the tasks
	 *
	 * @param	Integer		$idTaskMove			Task which was moved
	 * @param	Integer		$idTaskRef			Task which is the reference for after/before
	 * @param	String		$moveMode			Mode: after or before
	 * @return	Integer		New position
	 */
	public static function changeTaskOrder($idTaskMove, $idTaskRef, $moveMode) {
		$idTaskMove	= intval($idTaskMove);
		$idTaskRef	= intval($idTaskRef);
		$taskMove	= self::getTask($idTaskMove);
		$taskRef	= self::getTask($idTaskRef);
		$idProject	= $taskMove->getProjectID();

		$isInsertAfter		= strtolower(trim($moveMode)) === 'after';
		$isInsertBefore		= !$isInsertAfter;
		$isDifferentGroup	= $taskMove->getParentTaskID() !== $taskRef->getParentTaskID();
		$isSameGroup		= !$isDifferentGroup;
		$isRefTaskAfter		= $isSameGroup && $taskMove->getTreePosition() < $taskRef->getTreePosition();
		$refPosition		= $taskRef->getTreePosition();
		$hasToFixPosition	= $isInsertBefore || $isRefTaskAfter;

			// Move up all tasks which are lower old position
		self::updateSortingGroup($idProject, $taskMove->getParentTaskID(), $taskMove->getTreePosition(), true);

			// Correct reference position, because to upper query already changed the ref position
		if( $hasToFixPosition ) {
			$refPosition--;
		}
		self::updateSortingGroup($idProject, $taskRef->getParentTaskID(), $refPosition, false);

			// Set new position
		$newPosition = $hasToFixPosition ? $taskRef->getTreePosition() : $taskRef->getTreePosition() + 1;
		$newTaskMoveData = array(
			'sorting'		=> $newPosition,
			'id_parenttask'	=> $taskRef->getParentTaskID()
		);
		TodoyuRecordManager::updateRecord(self::TABLE, $idTaskMove, $newTaskMoveData);

		return $newPosition;
	}



	/**
	 * Update the sorting for a group
	 *
	 * @param	Integer		$idProject
	 * @param	Integer		$idParentTask		Parent task
	 * @param	Integer		$refPosition		Reference position
	 * @param	Boolean		$moveLowerUp		Shift every task one step to the top/bottom
	 */
	public static function updateSortingGroup($idProject, $idParentTask, $refPosition, $moveLowerUp) {
		$calc			= $moveLowerUp ? '-' : '+';
		$noQuoteFields 	= array('sorting');
		$where			= '		id_project		= ' . $idProject
						. ' AND id_parenttask	= '  . $idParentTask
						. ' AND	sorting 		>'  . $refPosition;
		$updateFields	= array(
			'sorting'	=> 'sorting' . $calc . '1'
		);

		Todoyu::db()->doUpdate(self::TABLE, $where, $updateFields, $noQuoteFields);
	}



	/**
	 * Sort task IDs by a field in the database
	 * This is useful if you have task IDs from severals sources (filters) and
	 * they should all be sorted by one field
	 *
	 * @param	Array		$taskIDs		Task IDs to sort
	 * @param	String		$order			Order statement
	 * @return	Array
	 */
	public static function sortTaskIDs(array $taskIDs, $order) {
		$taskIDs	= TodoyuArray::intval($taskIDs, true, true);

		if( sizeof($taskIDs) === 0 ) {
			return array();
		}

		$field	= 'id';
		$table	= self::TABLE;
		$where	= 'id IN(' . implode(',', $taskIDs) . ')';

		return Todoyu::db()->getColumn($field, $table, $where, '', $order);
	}



	/**
	 * Check whether a person is assigned to a task as owner or assigned person
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idPerson
	 * @param	Boolean		$checkCreator		Creator is an assigned person too
	 * @return	Boolean
	 */
	public static function isPersonAssigned($idTask, $idPerson = 0, $checkCreator = false) {
		$idTask		= intval($idTask);
		$idPerson	= Todoyu::personid($idPerson);

		$fields	= 'id';
		$table	= self::TABLE;
		$where	= '	id					= ' . $idTask
				. ' AND (
							 id_person_assigned	= ' . $idPerson .
						' OR id_person_owner	= ' . $idPerson;

			// Add creator field check
		if( $checkCreator ) {
			$where .= ' OR id_person_create = ' . $idPerson;
		}

		$where .= ')';

		return Todoyu::db()->hasResult($fields, $table, $where);
	}



	/**
	 * Check whether a person is assigned to the task's project
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isPersonAssignedToProject($idTask, $idPerson = 0) {
		$idTask		= intval($idTask);
		$idPerson	= Todoyu::personid($idPerson);

		$fields	= '	t.id';
		$table	=	self::TABLE . ' t,
					ext_project_mm_project_person mm';
		$where	= '		t.id			= ' . $idTask
				. ' AND	t.id_project	= mm.id_project '
				. '	AND	mm.id_person	= ' . $idPerson;

		return Todoyu::db()->hasResult($fields, $table, $where);
	}



	/**
	 * Check whether a person is assigned to the task or the project
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isPersonAssignedToTaskOrProject($idTask, $idPerson = 0) {
		return self::isPersonAssigned($idTask, $idPerson) || self::isPersonAssignedToProject($idTask, $idPerson);
	}



	/**
	 * Set active project as project if new task is created in project area and no project is set (quicktask)
	 *
	 * @param	Array		$data
	 * @param	Integer		$type
	 * @param	Integer		$idProject
	 * @param	Integer		$idParentTask
	 * @return	Array
	 */
	public static function hookTaskDefaultDataFromEnvironment(array $data, $type, $idProject, $idParentTask = 0) {
		$idProject	= intval($idProject);

		if( $idProject === 0 ) {
			$idProject = intval($idProject);
		}

			// Set active project when in project area
		if( $idProject === 0 && AREA === EXTID_PROJECT ) {
				// Set project ID
			$data['id_project']	= TodoyuProjectPreferences::getActiveProject();
		}

		return $data;
	}



	/**
	 * Hook to load default task data from project preset
	 *
	 * @param	Array		$data
	 * @param	Integer		$type
	 * @param	Integer		$idProject
	 * @param	Integer		$idParentTask
	 * @return	Array
	 */
	public static function hookTaskDefaultDataFromPreset(array $data, $type, $idProject, $idParentTask = 0) {
		$idProject	= intval($idProject);

			// Fetch project id from data (set before by other hook)
		if( $idProject === 0 ) {
			$idProject = intval($data['id_project']);
		}

		if( $idProject !== 0 ) {
			$project	= TodoyuProjectProjectManager::getProject($idProject);

			if( $project->hasTaskPreset() ) {
				$taskPreset	= $project->getTaskPreset();
				$presetData	= $taskPreset->getPresetData();

				$data = array_merge($data, $presetData);
			}
		}

		return $data;
	}



	/**
	 * Freeze a task
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function freeze($idTask) {
		return TodoyuFreezeManager::freezeObject('TodoyuProjectTask', $idTask);
	}



	/**
	 * Unfreeze a task
	 *
	 * @param	Integer					$idTask
	 * @return	Boolean|TodoyuProjectTask
	 */
	public static function unfreeze($idTask) {
		return TodoyuFreezeManager::unfreezeElement('TodoyuProjectTask', $idTask);
	}



	/**
	 * Lock a task
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$ext		ext ID
	 */
	public static function lockTask($idTask, $ext = EXTID_PROJECT) {
		TodoyuLockManager::lock($ext, 'ext_project_task', $idTask);
		TodoyuHookManager::callHook('project', 'task.lock', array($idTask));
	}



	/**
	 * Unlock a task
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$ext		ext ID
	 */
	public static function unlockTask($idTask, $ext = EXTID_PROJECT) {
		TodoyuLockManager::unlock($ext, 'ext_project_task', $idTask);
		TodoyuHookManager::callHook('project', 'task.unlock', array($idTask));
	}



	/**
	 * Lock multiple tasks
	 *
	 * @param	Array		$taskIDs
	 * @param	Integer		$ext		ext ID
	 */
	public static function lockTasks(array $taskIDs, $ext = EXTID_PROJECT) {
		foreach($taskIDs as $idTask) {
			self::lockTask($idTask, $ext);
		}
	}



	/**
	 * Unlock multiple tasks
	 *
	 * @param	Array		$taskIDs
	 * @param	Integer		$ext		ext ID
	 */
	public static function unlockTasks(array $taskIDs, $ext = EXTID_PROJECT) {
		foreach($taskIDs as $idTask) {
			self::unlockTask($idTask, $ext);
		}
	}



	/**
	 * Check whether task is locked
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isLocked($idTask) {
		return TodoyuLockManager::isLocked('ext_project_task', $idTask);
	}



	/**
	 * Check if a container is locked
	 * A container is not locked directly, but if a subtask is locked, the container is locked too
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function areSubtasksLocked($idTask) {
		$idTask			= intval($idTask);
		$allSubtaskIDs	= $idTask > 0 ? self::getAllSubTaskIDs($idTask) : array();

		if( sizeof($allSubtaskIDs) === 0 ) {
			return false;
		} else {
			return TodoyuLockManager::areLocked('ext_project_task', $allSubtaskIDs);
		}
	}



	/**
	 * Link task IDs in given text
	 *
	 * @param	String		$text
	 * @return	String
	 */
	public static function linkTaskIDsInText($text) {
		if( Todoyu::allowed('project', 'general:area') ) {
			$pattern= '/(^|[^\w\.\/#\-]+)(\d+\.\d+)([^\w\.\/#\-]+|$)/';
			$text	= preg_replace_callback($pattern, array('TodoyuProjectTaskManager', 'callbackLinkTaskIDsInText'), $text);
		}

		return $text;
	}



	/**
	 * Callback to replace task number with link to task in project view
	 * Match: 0=>all, 1=>before, 2=>number, 3=>after
	 *
	 * @param	Array		$matches
	 * @return	String
	 */
	private static function callbackLinkTaskIDsInText(array $matches) {
		$idTask			= TodoyuProjectTaskManager::getTaskIDByTaskNumber($matches[2]);

		if( $idTask === 0 ) {
			return $matches[0];
		} else {
			list($idProject)= explode('.', $matches[2]);

			$taskUrl	= TodoyuString::buildUrl(array(
				'ext'		=> 'project',
				'project'	=> $idProject,
				'task'		=> $idTask
			), 'task-' . $idTask);
			$linkTag	= TodoyuString::buildATag($taskUrl, $matches[2]);

			return $matches[1] . $linkTag . $matches[3];
		}
	}



	/**
	 * if the time of task isn't set to midnight the dates in the task details are shown with time
	 *
	 * @param	Integer		$date
	 * @return	String
	 */
	protected static function getTaskDetailsDateFormat($date) {
		return date('Hi', $date) === '0000' ? 'date' : 'datetime';
	}



	/**
	 * Get task tabs config array (labels parsed)
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTabs($idTask) {
		$typeKey	= self::getTask($idTask)->getTypeKey();

		return TodoyuContentItemTabManager::getTabs('project', $typeKey, $idTask);
	}



	/**
	 * Get a tab configuration
	 *
	 * @param	String		$tabKey
	 * @param	Integer		$typeID
	 * @return	Array
	 */
	public static function getTabConfig($tabKey, $typeID = TASK_TYPE_TASK) {
		$typeKey	= ((int) $typeID === TASK_TYPE_TASK) ? 'task' : 'container';

		return TodoyuContentItemTabManager::getTabConfig('project', $typeKey, $tabKey);
	}



	/**
	 * Get the tab which is active by default (if no preference is stored)
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getDefaultTab($idTask) {
		$typeKey	= self::getTask($idTask)->getTypeKey();

		return TodoyuContentItemTabManager::getDefaultTab('project', $typeKey, $idTask);
	}



	/**
	 * Get filtered task autocompletion suggestions to given input
	 *
	 * @param	String	$input
	 * @param	Array	$formData
	 * @param	String	$name
	 * @return	Array
	 */
	public static function autocompleteProjectTasks($input, array $formData, $name = '') {
		$idProject	= intval($formData['id_project']);
		$idTask		= intval($formData['id']);

		$filters	= array(
			array(
				'filter'=> 'tasknumberortitle',
				'value'	=> $input
			),
			array(
				'filter'=> 'nottask',
				'value'	=> $idTask
			),
			array(
				'filter'=> 'project',
				'value'	=> $idProject
			),
			array(
				'filter'=> 'subtask',
				'value'	=> $idTask,
				'negate'=> true
			)
		);

		return TodoyuProjectTaskFilterDataSource::getTaskAutocompleteListByFilter($filters);
	}



	/**
	 * Get task autocomplete list
	 *
	 * @param	String		$input
	 * @param	Array		$formData
	 * @param	String		$name
	 * @return	Array
	 */
	public static function autocompleteTasks($input, array $formData, $name = '') {
		$filters	= array(
			array(
				'filter'=> 'tasknumberortitle',
				'value'	=> $input
			)
		);

		return TodoyuProjectTaskFilterDataSource::getTaskAutocompleteListByFilter($filters);
	}



	/**
	 * Get task edit form
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$type
	 * @param	Integer		$idProject
	 * @param	Integer		$idParentTask
	 * @return	TodoyuForm
	 */
	public static function getTaskEditForm($idTask, $type = TASK_TYPE_TASK, $idProject = 0, $idParentTask = 0) {
		$idTask			= intval($idTask);
		$idProject		= intval($idProject);
		$idParentTask	= intval($idParentTask);

		$task		= self::getTask($idTask);
		$xmlPath	= 'ext/project/config/form/task.xml';

		if( $idTask === 0 ) {
			$task->set('type', $type);
		} else {
			if( $idProject === 0 ) {
				$idProject	= $task->getProjectID();
			}
			$type = $task->getType();
		}

			// Construct form object
		$form		= TodoyuFormManager::getForm($xmlPath, $idTask, array(
			'project'	=> $idProject,
			'parent'	=> $idParentTask,
			'type'		=> $type
		));

			// Load form data
		$data	= $task->getTemplateData(0);
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idTask, array('type'=>$type));

			// Set form data
		$form->setFormData($data);

		return $form;
	}



	/**
	 * Get task label
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$full
	 * @return	String
	 */
	public static function getLabel($idTask, $full = false) {
		return self::getTask($idTask)->getLabel($full);
	}


}

?>