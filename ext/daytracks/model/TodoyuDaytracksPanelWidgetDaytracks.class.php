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

/**
 * Hours sheet which lists all task the person tracked today and cumulates today's working time
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */
class TodoyuDaytracksPanelWidgetDaytracks extends TodoyuPanelWidget {

	/**
	 * Construct PanelWidget (init basic configuration)
	 *
	 * @param	Array	$config
	 * @param	Array	$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'daytracks',								// ext key
			'daytracks',								// panelwidget ID
			'daytracks.panelwidget-daytracks.title',	// widget title text
			$config,									// widget config array
			$params										// widget parameters
		);

		$this->addClass('daytracks');
		$this->addHasIconClass();

			// Add onload init function
		TodoyuPage::addJsInit('Todoyu.Ext.daytracks.PanelWidget.Daytracks.init()', 100);
	}



	/**
	 * Render widget content
	 *
	 * @return	String
	 */
	public function renderContent() {
		$tmpl	= 'ext/daytracks/view/panelwidget-daytracks.tmpl';

		$tasks	= TodoyuDaytracksManager::getTodayTrackedTasks();

			// Add unsaved currently running task
		$current= TodoyuDaytracksManager::getCurrentTrackedUnsavedTask();
		if( $current ) {
			$tasks[] = $current;
		}

			// Add 'isTrackable' and 'seeTask' flags to listed tasks
		foreach($tasks as $index => $task) {
			$tasks[$index]['isTrackable']	= TodoyuTimetracking::isTrackable($task['type'], $task['status'], $task['id']);
			$tasks[$index]['seeTask']		= TodoyuProjectTaskRights::isSeeAllowed($task['id']);
			$tasks[$index]['isDeleted']		= TodoyuProjectTaskManager::isDeleted($task['id']) ? 1:0;
		}

		$data	= array(
			'tasks'		=> $tasks,
			'current'	=> TodoyuTimetracking::getTaskID(),
			'total'		=> TodoyuTimetracking::getTodayTrackedTime(),
			'tracking'	=> TodoyuTimetracking::isTrackingActive()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get widget content
	 *
	 * @return	String
	 */
	public function getContent() {
		return $this->renderContent();
	}



	/**
	 * Get context menu items for daytracks list panel widget
	 *
	 * @param	Integer		$idTask			Task ID
	 * @param	Array		$items			Current items
	 * @return	Array
	 */
	public static function getContextMenuItems($idTask, array $items) {
		$idTask	= intval($idTask);

		if( ! TodoyuProjectTaskManager::isDeleted($idTask) ) {
				// Add timetracking options (if extension installed)
			if( TodoyuExtensions::isInstalled('timetracking') ) {
				$items = array_merge_recursive($items, TodoyuTimetrackingManager::getContextMenuItemStartStop($idTask));
			}

			$ownItems	= Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget'];
			$items		= array_merge_recursive($items, $ownItems);
		}

		return $items;
	}



	/**
	 * Check whether panel widget is allowed
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return Todoyu::allowed('daytracks', 'general:use');
	}

}

?>