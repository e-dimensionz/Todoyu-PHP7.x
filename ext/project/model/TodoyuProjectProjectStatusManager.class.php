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
 * Project status manager
 * Status access functions for project statuses
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectProjectStatusManager {

	/**
	 * Get project status key by index
	 *
	 * @param	Integer		$idStatus
	 * @return	Array
	 */
	public static function getStatusKey($idStatus) {
		$idStatus	= intval($idStatus);

		return Todoyu::$CONFIG['EXT']['project']['STATUS']['PROJECT'][$idStatus];
	}



	/**
	 * Get project status label by index or key
	 *
	 * @param	Mixed		$status			Status index or key
	 * @return	String
	 */
	public static function getStatusLabel($status) {
		if( is_numeric($status) ) {
			$idStatus	= intval($status);
			$statusKey	= self::getStatusKey($idStatus);
		} elseif( $status != '' ) {
			$statusKey	= $status;
		} else {
			$statusKey	= 'undefined';
		}

		return Todoyu::Label('project.ext.status.' . $statusKey);
	}



	/**
	 * Get all project statuses
	 *
	 * @param	Integer		$forceStatus
	 * @return	Array
	 */
	public static function getStatuses($forceStatus = 0) {
		$forceStatus= intval($forceStatus);

		$statuses	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['project']['STATUS']['PROJECT']);

		foreach($statuses as $index => $statusKey) {
				// Only get allowed status which the person can see
			if( ! Todoyu::allowed('project', 'project:' . $statusKey . ':see') && $index !== $forceStatus) {
				unset($statuses[$index]);
			}
		}

		return $statuses;
	}



	/**
	 * Get allowed status IDs
	 *
	 * @param	Integer		$forceStatus
	 * @return	Array
	 */
	public static function getStatusIDs($forceStatus = 0) {
		return array_keys(self::getStatuses($forceStatus));
	}



	/**
	 * Get project status label arrays. The keys are the status indexes
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
	 * Get project status infos.
	 * The array index is the status index.
	 * The keys are: index, key, label
	 *
	 * @return	Array
	 */
	public static function getStatusInfos() {
		$statuses	= self::getStatuses();
		$infos		= array();

		foreach($statuses as $index => $statusKey) {
			$label	= self::getStatusLabel($statusKey);
			$infos[$index] = TodoyuProjectProjectViewHelper::getStatusOption($index, $statusKey, $label);
		}

		return $infos;
	}

}

?>