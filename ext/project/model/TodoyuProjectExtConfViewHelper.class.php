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
 * View helper for project extconf
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectExtConfViewHelper {

	/**
	 * Get options for dynamically calculated date options (creation date +1, 2, 3...)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getDateOffsetOptions(TodoyuFormElement $field) {

		return array(
			array(	// Creation day
				'label'	=> 'project.task.default.date.date_1',
				'value'	=> 1
			),
			array(	// Creation day
				'label'	=> 'project.task.default.date.date_work_1',
				'value'	=> 'work_1'
			),
			array(	// Creation day + 1 day
				'label'	=> 'project.task.default.date.date_2',
				'value'	=> 2
			),
			array(	// Creation day + 1 working day
				'label'	=> 'project.task.default.date.date_work_2',
				'value'	=> 'work_2'
			),
			array(	// Creation day + 2 days
				'label'	=> 'project.task.default.date.date_3',
				'value'	=> 3
			),
			array(	// Creation day + 2 working days
				'label'	=> 'project.task.default.date.date_work_3',
				'value'	=> 'work_3'
			),
			array(	// Creation day + 3 days
				'label'	=> 'project.task.default.date.date_4',
				'value'	=> 4
			),
			array(	// Creation day + 3 days
				'label'	=> 'project.task.default.date.date_work_4',
				'value'	=> 'work_4'
			),
			array(	// Creation day + 1 week
				'label'	=> 'project.task.default.date.date_7',
				'value'	=> 7
			),
			array(	// Creation day + 1 working week
				'label'	=> 'project.task.default.date.date_work_7',
				'value'	=> 'work_7'
			),
			array(	// Creation day + 2 weeks
				'label'	=> 'project.task.default.date.date_14',
				'value'	=> 14
			),
			array(	// Creation day + 2 working weeks
				'label'	=> 'project.task.default.date.date_work_14',
				'value'	=> 'work_14'
			)
		);
	}



	/**
	 * Get status infos of default task
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getDefaultValueStatusOptions(TodoyuFormElement $field) {
		return TodoyuProjectTaskStatusManager::getStatusInfos('see');
	}



	/**
	 * Get label of calculated date option with given value
	 *
	 * @param	Integer		$value
	 * @return	String
	 */
	public static function getDateOffsetLabel($value) {
		return Todoyu::Label('project.task.default.date.date_' . $value);
	}



	/**
	 * @return	Integer
	 */
	public static function getMaxNumberOfOpenProjects(){
		$conf	= self::getExtConf();

		return intval($conf['maxOpenProjects'] ?? 0) > 0 ? intval($conf['maxOpenProjects'] ?? 0) : 3;
	}



	/**
	 * @return	Integer
	 */
	public static function getToleranceDateDeadline() {
		$conf = self::getExtConf();

		return intval($conf['toleranceDeadline']) * 60;
	}



	/**
	 * @return	Integer
	 */
	public static function getToleranceDateEnd() {
		$conf = self::getExtConf();

		return intval($conf['toleranceEnddate']) * 60;
	}



	/**
	 * @return	Array
	 */
	protected static function getExtConf() {
		return TodoyuSysmanagerExtConfManager::getExtConf('project');
	}
}

?>