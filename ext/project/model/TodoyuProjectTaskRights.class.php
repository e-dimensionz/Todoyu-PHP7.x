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
 * Task rights functions
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskRights {

	/**
	 * Deny access
	 * Shortcut for project
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('project', $right);
	}



	/**
	 * Check whether person can edit a task
	 * Check whether person has edit rights and if person can edit a status
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isEditAllowed($idTask) {
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		if( $task->isTask() ) {
			if( !self::isStatusChangeAllowed($idTask) ) {
				return false;
			}

				// Check if person can edit his own tasks
			if( $task->isCurrentPersonCreator() ) {
				if( !Todoyu::allowed('project', 'edittask:editOwnTasks') ) {
					return false;
				}
			}

				// Check whether edit for status is allowed
			if( !self::isStatusEditAllowed($idTask) ) {
				return false;
			}
		}

		if( $task->isContainer() ) {
				// Check if person can edit his own containers
			if( $task->isCurrentPersonCreator() && ! Todoyu::allowed('project', 'edittask:editOwnContainers') ) {
				return false;
			}
		}

		return self::isEditInProjectAllowed($idTask);
	}



	/**
	 * Check whether a task can get deleted
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isDeleteAllowed($idTask) {
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		if( $task->isTask() ) {
			if( $task->isCurrentPersonCreator() ) {
				if( Todoyu::allowed('project', 'deletetask:deleteOwnTasks') ) {
					return true;
				}
			}
		} elseif( $task->isContainer() ) {
				// Check if person can delete his own containers
			if( $task->isCurrentPersonCreator() ) {
				if( Todoyu::allowed('project', 'deletetask:deleteOwnContainers') ) {
					return true;
				}
			}
		}

		return self::isDeleteInProjectAllowed($idTask);
	}



	/**
	 * Check whether a status change of a task is allowed
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isStatusChangeAllowed($idTask) {
		$idTask		= intval($idTask);
		$task		= TodoyuProjectTaskManager::getTask($idTask);

		if( $task->isLocked() ) {
			return false;
		}

			// Task edit right in project
		if( ! self::isEditInProjectAllowed($idTask) ) {
			return false;
		}

		$statusIDs	= array_keys(TodoyuProjectTaskStatusManager::getStatuses('changefrom'));

		return in_array($task->getStatus(), $statusIDs);
	}



	/**
	 * Check whether task edit for status of given task is allowed
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isStatusEditAllowed($idTask) {
		$idTask		= intval($idTask);
		$task		= TodoyuProjectTaskManager::getTask($idTask);

		$statuses	= TodoyuProjectTaskStatusManager::getStatuses('edit');
		$statusIDs	= array_keys($statuses);

		return in_array($task->getStatus(), $statusIDs);
	}



	/**
	 * Check whether person can edit tasks/containers in this project
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isEditInProjectAllowed($idTask) {
		$idTask		= intval($idTask);
		$task		= TodoyuProjectTaskManager::getTask($idTask);
		$project	= $task->getProject();
		$status		= $project->getStatus();

		if( $project->isLocked() ) {
			return false;
		}

		if( in_array($status, Todoyu::$CONFIG['EXT']['project']['projectStatusDisallowChildrenEditing'])  ) {
			return false;
		}

		$typeName	= $task->isTask() ? 'Task' : 'Container';
		$rightName	= $project->isCurrentPersonAssigned() ? 'edit' . $typeName . 'InOwnProjects' : 'edit' . $typeName . 'InAllProjects';

		return Todoyu::allowed('project', 'edittask:' . $rightName);
	}



	/**
	 * Check whether a task can be cloned by a person
	 * -Must be in project area
	 * -Parent task must be not locked
	 * -Must be allowed to see the task to be cloned
	 * -Must be allowed to edit tasks in the resulting status
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isCloneAllowed($idTask) {
			// Must be in project area
		if( AREA != EXTID_PROJECT ) {
			return false;
		}

			// Must be allowed to see task
		if( !self::isSeeAllowed($idTask) ) {
			return false;
		}

			// Must be allowed to add task into parent (parent not locked)
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		if( !self::isAddInTaskProjectAllowed($idTask, $task->isContainer()) ) {
			return false;
		}

			// Must be allowed to edit tasks in resulting status
		$cloneResultStatus	= Todoyu::$CONFIG['EXT']['project']['taskDefaults']['status'];

		$idProject	= $task->getProjectID();
		$project	= $task->getProject();
		if( $project->hasTaskPreset() || TodoyuProjectTaskPresetManager::hasFallbackTaskPreset() ) {
			$projectTaskPreset	= TodoyuProjectTaskPresetManager::getTaskPresetOrFallback($idProject);
			if( $projectTaskPreset->hasStatus() ) {
					// Override default status from preset
				$cloneResultStatus	= intval($projectTaskPreset->getStatus());
			}
		}

		$statusIDsAllowedForEdit	= array_keys(TodoyuProjectTaskStatusManager::getStatuses('edit'));
		if( $cloneResultStatus === 0 || !in_array($cloneResultStatus, $statusIDsAllowedForEdit) ) {
			return false;
		}

		return true;
	}



	/**
	 * Check whether a task can get deleted by project rights
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isDeleteInProjectAllowed($idTask) {
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		$idTask		= intval($idTask);
		$task		= TodoyuProjectTaskManager::getTask($idTask);
		$project	= $task->getProject();
		$status		= $project->getStatus();

		if( in_array($status, Todoyu::$CONFIG['EXT']['project']['projectStatusDisallowChildrenEditing']) || $project->isLocked() ) {
			return false;
		}

			// Build rights dynamically with type and right
		$typeName	= $task->isTask() ? 'Task' : 'Container';
		$rightName	= $project->isCurrentPersonAssigned() ? 'delete' . $typeName . 'InOwnProjects' : 'delete' . $typeName . 'InAllProjects';

		return Todoyu::allowed('project', 'deletetask:' . $rightName);
	}



	/**
	 * Check whether person can add a new task under the parent task
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$isContainer		Element to be added is container?
	 * @return	Boolean
	 */
	public static function isAddInTaskProjectAllowed($idTask, $isContainer = false) {
		$idTask		= intval($idTask);
		$idProject	= TodoyuProjectTaskManager::getProjectID($idTask);

		return self::isAddInProjectAllowed($idProject, $isContainer);
	}



	/**
	 * Check whether person can add a new container under the parent task
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isAddInContainerProjectAllowed($idTask) {
		return self::isAddInTaskProjectAllowed($idTask, true);
	}



	/**
	 * Check whether a person can add a new task/container in this project
	 *
	 * @param	Integer		$idProject
	 * @param	Boolean		$isContainer	added element is a container?
	 * @return	Boolean
	 */
	public static function isAddInProjectAllowed($idProject, $isContainer = false) {
		$idProject	= intval($idProject);
		$project	= TodoyuProjectProjectManager::getProject($idProject);

		if( $project->isLocked() ) {
			return false;
		}

		if( in_array($project->getStatus(), Todoyu::$CONFIG['EXT']['project']['projectStatusDisallowChildrenEditing']) ) {
			return false;
		}

		$elementType	= $isContainer ? 'Container' : 'Task';

		if( TodoyuProjectProjectManager::isPersonAssigned($idProject) ) {
			return Todoyu::allowed('project', 'addtask:add' . $elementType . 'InOwnProjects');
		} else {
			return Todoyu::allowed('project', 'addtask:add' . $elementType . 'InAllProjects');
		}
	}



	/**
	 * Check whether quick-add of tasks is allowed
	 * Needs at least one project where he can add tasks
	 *
	 * @return	Boolean
	 */
	public static function isQuickAddAllowed() {
		$projectIDs	= TodoyuProjectProjectManager::getProjectIDsForTaskAdd();

		return !empty($projectIDs);
	}



	/**
	 * Check whether a person can see the task
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isSeeAllowed($idTask) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		if( $task->isDeleted() ) {
			return false;
		}

		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

			// If container, check if person can see the project
		$seeProject	= TodoyuProjectProjectRights::isSeeAllowed($task->getProjectID());

		if( $task->isContainer() || !$seeProject ) {
			return $seeProject;
		}

		$status	= $task->getStatusKey();

			// Check status
		if( ! self::hasStatusRight($status, 'see') ) {
			return false;
		}

			// Check view rights with assignment
		if( ! TodoyuProjectTaskManager::isPersonAssigned($idTask, 0, true) ) {
			if( ! $task->isPublic() ) {
				return Todoyu::allowed('project', 'seetask:seeAll');
			}
		}

		return true;
	}



	/**
	 * @param	Boolean		$isPublic
	 * @return	Boolean
	 */
	public static function isSeePublicFlagAllowed($isPublic) {
		return $isPublic && (Todoyu::person()->isInternal() || TodoyuAuth::isAdmin());
	}



	/**
	 * Check whether user can use drag and drop for tasks
	 *
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function isDragAndDropForTaskAllowed($idTask) {
		$idTask	= intval($idTask);

		if( Todoyu::person()->isAdmin() ) {
			return true;
		}

		$task	= TodoyuProjectTaskManager::getTask($idTask);

		$addTasksInOwnProjects	= Todoyu::allowed('project', 'addtask:addTaskInOwnProjects');
		$hasTaskEditRight		= TodoyuProjectTaskRights::hasStatusRight($task->getStatusKey(), 'edit');

		return $addTasksInOwnProjects && $hasTaskEditRight;
	}



	/**
	 * Check whether person can see a taskstatus
	 *
	 * @param	String		$status
	 * @param	String		$type
	 * @return	Boolean
	 */
	public static function hasStatusRight($status, $type = 'see') {
		$group = self::getStatusRightGroupByType($type);

		return Todoyu::allowed('project', $group . ':' . $status . ':' . $type);
	}



	/**
	 * Gives back the right group of the task status query
	 *
	 * @param	String		$type
	 * @return	String
	 */
	protected static function getStatusRightGroupByType($type = 'see') {
		switch( $type ) {
			case 'see':
				return 'seetask';

			case 'create':
				return 'addtask';

			case 'edit':
				return 'edittask';

			case 'changefrom':
			case 'changeto':
				return 'edittaskdetail';

			default:
				return 'unknowntype';
		}
	}



	/**
	 * Restrict access to persons who are allowed to add tasks in the project if this task
	 *
	 * @param	Integer		$idTask
	 */
	public static function restrictEdit($idTask) {
		if( ! self::isEditAllowed($idTask) ) {
			self::deny('task:edit');
		}
	}



	/**
	 * Restrict access if person cannot delete the task
	 *
	 * @param	Integer		$idTask
	 */
	public static function restrictDelete($idTask) {
		if( ! self::isDeleteAllowed($idTask) ) {
			self::deny('task:delete');
		}
	}



	/**
	 * Restrict access to persons who are allowed to add tasks in the project of this task
	 *
	 * @param	Integer		$idTask
	 */
	public static function restrictAddSubTask($idTask) {
		if( ! self::isAddInTaskProjectAllowed($idTask) ) {
			self::deny('task:add');
		}
	}



	/**
	 * Restricts the adding a task over the quickcreate headlet
	 */
	public static function restrictShowPopupForm() {
		if( ! Todoyu::allowed('project', 'addtask:addTaskInOwnProjects') ) {
			self::deny('task:add');
		}
	}



	/**
	 * Restrict access to persons who are allowed to add tasks in the project
	 *
	 * @param	Integer		$idProject
	 */
	public static function restrictAddToProject($idProject) {
		if( ! self::isAddInProjectAllowed($idProject) ) {
			self::deny('task:add');
		}
	}



	/**
	 * Restrict access to persons who are allowed to see the task
	 *
	 * @param	Integer		$idTask
	 */
	public static function restrictSee($idTask) {
		if( ! self::isSeeAllowed($idTask) ) {
			self::deny('task:see');
		}
	}



	/**
	 * Checks if the change of status is allowed
	 *
	 * @param	String	$status		Status key
	 * @param	Integer	$idTask
	 */
	public static function restrictStatusChangeTo($status, $idTask) {
		self::restrictSee($idTask);

		Todoyu::restrict('project', 'edittaskdetail:' . $status . ':changeto');
	}



	/**
	 * Restrict access to task drag and drop
	 *
	 * @param	Integer		$idTask
	 */
	public static function restrictDragAndDrop($idTask) {
		if( !self::isDragAndDropForTaskAllowed($idTask) ) {
			self::deny('task:dragdrop');
		}
	}

}
?>