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
 * Task status manager
 * Status access functions for task statuses
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskStatusManager {

	/**
	 * Get task status key by status index
	 *
	 * @param	Integer		$idStatus
	 * @return	String
	 */
	public static function getStatusKey($idStatus) {
		$idStatus	= intval($idStatus);

		return Todoyu::$CONFIG['EXT']['project']['STATUS']['TASK'][$idStatus];
	}



	/**
	 * Get task status label by index or key
	 *
	 * @param	Mixed		$status			Status index or key
	 * @return	String
	 */
	public static function getStatusLabel($status) {
		if( is_numeric($status) ) {
			$idStatus	= intval($status);
			$statusKey	= self::getStatusKey($idStatus);
		} else {
			$statusKey	= $status;
		}

		return Todoyu::Label('project.task.status.' . $statusKey);
	}



	/**
	 * Get allowed task statuses of given type
	 *
	 * @param	String		$check
	 * @param	Integer		$forceStatus
	 * @return	Array
	 */
	public static function getStatuses($check = 'see', $forceStatus = 0) {
		$forceStatus= intval($forceStatus);

		$statuses	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['project']['STATUS']['TASK']);

		foreach($statuses as $index => $statusKey) {
				// Only get allowed status which the person can see
			if( $index !== $forceStatus && ! TodoyuProjectTaskRights::hasStatusRight($statusKey, $check) ) {
				unset($statuses[$index]);
			}
		}

		return $statuses;
	}



	/**
	 * Get available status IDs
	 *
	 * @param	String		$check
	 * @param	Integer		$forceStatus
	 * @return	Array
	 */
	public static function getStatusIDs($check = 'see', $forceStatus = 0) {
		return array_keys(self::getStatuses($check, $forceStatus));
	}



	/**
	 * Get task status label arrays. The keys are the status indexes
	 *
	 * @return	Array
	 */
	public static function getStatusLabels() {
		$keys	= self::getStatuses();
		$labels	= array();

		foreach( $keys as $index => $statusKey ) {
			$labels[$index] = self::getStatusLabel($statusKey);
		}

		return $labels;
	}



	/**
	 * Get task status infos.
	 * The array index is the status index.
	 * The keys are: index, key, label
	 *
	 * @param	String	$check
	 * @return	Array
	 */
	public static function getStatusInfos($check = 'see') {
		$statuses	= self::getStatuses($check);
		$infos		= array();

		foreach($statuses as $index => $statusKey) {
			$label	= self::getStatusLabel($statusKey);
			$infos[$index] = TodoyuProjectTaskViewHelper::getStatusOption($index, $statusKey, $label);
		}

		$infos	= TodoyuHookManager::callHookDataModifier('project', 'task.statusInfos', $infos);

		return $infos;
	}

}

?>