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
 * Task preset manager
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectTaskPresetManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_project_taskpreset';



	/**
	 * Gets a taskpreset object
	 *
	 * @param	Integer		$idTaskPreset
	 * @return	TodoyuProjectTaskPreset
	 */
	public static function getTaskPreset($idTaskPreset) {
		$idTaskPreset	= intval($idTaskPreset);

		return TodoyuRecordManager::getRecord('TodoyuProjectTaskPreset', $idTaskPreset);
	}


	/**
	 * Gets data of taskpreset
	 *
	 * @param	Integer				$idTaskpreset		Taskpreset ID
	 * @return	TodoyuProjectTaskpreset
	 */
	public static function getTaskpresetData($idTaskpreset) {
		$idTaskpreset	= intval($idTaskpreset);

		$preset	= TodoyuRecordManager::getRecord('TodoyuProjectTaskpreset', $idTaskpreset);

		return $preset->getData();
	}



	/**
	 * Get all task presets
	 *
	 * @return	Array
	 */
	public static function getAllTaskPresets() {
		return TodoyuRecordManager::getAllRecords(self::TABLE);
	}



	/**
	 * Save task preset record to database
	 *
	 * @param	Array	$data
	 * @return	Integer
	 */
	public static function saveTaskPreset(array $data) {
		$idTaskPreset	= intval($data['id']);
		$xmlPath		= 'ext/project/config/form/admin/taskpreset.xml';

		if( $idTaskPreset === 0 ) {
			$idTaskPreset = self::addTaskpreset();
		}

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idTaskPreset);

		self::updateTaskPreset($idTaskPreset, $data);

		return $idTaskPreset;
	}



	/**
	 * Add task preset record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addTaskpreset(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update task preset record
	 *
	 * @param	Integer		$idTaskPreset
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateTaskPreset($idTaskPreset, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idTaskPreset, $data);
	}



	/**
	 * Gets task preset records for list
	 *
	 * @return	Array
	 */
	public static function getRecords() {
		$taskPresets	= self::getAllTaskPresets();
		$reformConfig	= array(
			'id'	=> 'id',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($taskPresets, $reformConfig);
	}



	/**
	 * Sets deleted flag for given task preset record
	 *
	 * @param	Integer		$idTaskPreset
	 * @return	Boolean
	 */
	public static function deleteTaskPreset($idTaskPreset) {
		$idTaskPreset	= intval($idTaskPreset);

		return TodoyuRecordManager::deleteRecord(self::TABLE, $idTaskPreset);
	}



	/**
	 * Get the assigned task preset or the fallback task preset
	 * Return false if no preset assigned AND no fallback set
	 *
	 * @param	Integer		$idProject
	 * @return	TodoyuProjectTaskPreset|Boolean
	 */
	public static function getTaskPresetOrFallback($idProject) {
		$idProject	= intval($idProject);
		$project	= TodoyuProjectProjectManager::getProject($idProject);
		$taskPreset	= false;

		if( $project->hasTaskPreset() ) {
			$taskPreset = $project->getTaskPreset();
		} else {
			if( self::hasFallbackTaskPreset() ) {
				$taskPreset = self::getFallbackTaskPreset();
			}
		}

		return $taskPreset;
	}


	/**
	 * Check whether a fallback task preset has been selected
	 *
	 * @return	Boolean
	 */
	public static function hasFallbackTaskPreset() {
		return TodoyuProjectManager::getFallbackTaskPresetID() !== 0;
	}



	/**
	 * Get fallback task preset
	 *
	 * @return	TodoyuProjectTaskPreset
	 */
	public static function getFallbackTaskPreset() {
		$idFallbackPreset	= TodoyuProjectManager::getFallbackTaskPresetID();

		return self::getTaskPreset($idFallbackPreset);
	}



	/**
	 * Apply a task preset to given data. All missing values which are configured in the preset are filled in
	 *
	 * @param	Array	$data
	 * @return	Array
	 */
	public static function applyTaskPreset(array $data) {
		$idProject		= intval($data['id_project']);
		$taskPreset		= self::getTaskPresetOrFallback($idProject);

			// Set defaults if a task preset is selected for project
		if( $taskPreset ) {
			$data	= $taskPreset->apply($data);
		}

		return $data;
	}



	/**
	 * Get a date based on the extConf value set for this type
	 *
	 * @param	Integer		$duration		Identifier for number of days of the date in the future from now
	 * @param	Integer		$dateStart
	 * @return	Integer
	 */
	public static function getDateFromDayDuration($duration, $dateStart = 0) {
		$dateStart		= TodoyuTime::getDayStart($dateStart);
		$preventWeekend	= false;
		$addDaysToDate	= 0;

			// Handle working day config
		if( substr($duration, 0, 5) === 'work_' ) {
			$preventWeekend = true;
			$duration 		= substr($duration, 5);
		}

			// Real value depends in selected group
		switch( $duration ) {
				// Day of creation (NOW)
			case 1:
				$addDaysToDate= 0;
				break;

				// Creation day + 1, 2, 3 days
			case 2:	case 3:	case 4:
				$addDaysToDate= $duration-1;
				break;

				// Creation day + 1, 2 weeks
			case 7:	case 14:
				$addDaysToDate= $duration;
				break;
		}

			// Add the selected amount of days
		$dateEnd	= TodoyuTime::addDays($dateStart, $addDaysToDate);

			// Only count working days
		if( $preventWeekend ) {
			$weekendDays		= TodoyuTime::getWeekEndDayIndexes();
			$dateStartWeekDay	= date('w', $dateStart);
			$dateStartPos 		= array_search($dateStartWeekDay, $weekendDays);

				// If today is weekend, add days depending on the day of the weekend
			if( $dateStartPos !== false ) {
				$dateEnd	= TodoyuTime::addDays($dateEnd, 2-$dateStartPos);
			}

				// Add the days for all the weekends between start and end
			if( $duration >= 7 ) {
				$daysToAdd	= $duration/7 * 2;
				$dateEnd	= TodoyuTime::addDays($dateEnd, $daysToAdd);
			}

				// Make sure the end date is not on a weekend day
			$dateEndWeekDay		= date('w', $dateEnd);
			$dateEndPos			= array_search($dateEndWeekDay, $weekendDays);

			if( $dateEndPos !== false ) {
				$dateEnd	= TodoyuTime::addDays($dateEnd, 2-$dateEndPos);
			}
		}

		return $dateEnd + TodoyuTime::getTimeOfDay();
	}

}

?>