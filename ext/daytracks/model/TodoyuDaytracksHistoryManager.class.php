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
 * Manager for daytracks history
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */
class TodoyuDaytracksHistoryManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_timetracking_track';



	/**
	 * Check whether given person has time tracks in given month of given year
	 *
	 * @param	Integer		$idPerson
	 * @param	Integer		$month
	 * @param	Integer		$year
	 * @return	Boolean
	 */
	public static function personHasTracksInMonth($idPerson, $month, $year) {
		$idPerson	= intval($idPerson);
		$month		= intval($month);
		$year		= intval($year);

		$monthStart	= mktime(0, 0, 0, $month, 1, $year);
		$monthEnd	= mktime(0, 0, 0, $month + 1, 0, $year);

		$tracks = TodoyuTimetracking::getPersonTracks($monthStart, $monthEnd, $idPerson);

		return count($tracks) > 0;
	}



	/**
	 * Get timestamp of last time tracking of given/current user
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer					UNIX timestamp
	 */
	public static function getDateLastTimeTracking($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		$field	= 'date_create';
		$table	= TodoyuTimetrackingManager::TABLE;
		$where	= 'id_person_create = ' . $idPerson;
		$order	= 'date_create DESC';
		$limit	= 1;

		return intval(Todoyu::db()->getFieldValue($field, $table, $where, '', $order, $limit));
	}



	/**
	 * Get year month combinations of months where the user has time trackings
	 * Result: [2010-01, 2010-02, 2010-04, ...]
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getMonthsWithTracks($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		$q = '	SELECT
					DATE_FORMAT(FROM_UNIXTIME(date_track), \'%Y-%m\') as `date`
				FROM
					ext_timetracking_track
				WHERE
					id_person_create = ' . $idPerson . '
				GROUP BY
					`date`';

		$res	= Todoyu::db()->query($q);
		$rows	= Todoyu::db()->resourceToArray($res);

		return TodoyuArray::getColumn($rows, 'date');
	}



	/**
	 * Get the date of the first and the last tracking
	 *
	 * @param	Integer		$idPerson
	 * @return	Array		[min,max]
	 */
	public static function getTrackingRanges($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		$fields	= '	MIN(' . TodoyuSql::backtick('date_track') . ') as ' . TodoyuSql::backtick('min') . ',
					MAX(' . TodoyuSql::backtick('date_track') . ') as ' . TodoyuSql::backtick('max');
		$table	= self::TABLE;
		$where	= 'id_person_create = ' . $idPerson;

		$result	= Todoyu::db()->getRecordByQuery($fields, $table, $where);

		return array(
			'min'	=> intval($result['min']),
			'max'	=> intval($result['max'])
		);
	}



	/**
	 * Get data array for the range selector in the history pop-up
	 * The years are the keys, the month the sub elements of each year
	 * The range is starts at the first tracking, and ends at the last
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getMonthSelectorOptions($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		$range		= self::getTrackingRanges($idPerson);
		$options	= array();

		$current = $range['max'];
		$min	 = mktime(0, 0, 0, date('n', $range['min']), 1, date('Y', $range['min']));
		$trackMap= self::getMonthsWithTracks($idPerson);

		while( $min <= $current ) {
			$year	= date('Y', $current);
			$month	= date('m', $current);

			if( in_array($year.'-'.$month, $trackMap) ) {
				$options[$year][$month]	= array(
					'label'		=> Todoyu::Label('core.date.month.' . strtolower(date('F', $current)))
				);
			}

				// Advance to next month / year
			if( $month === 1 ) {
				$month	= 12;
				$year--;
			} else {
				$month--;
			}

			$current = mktime(0, 0, 0, $month, 1, $year);
		}

		return $options;
	}



	/**
	 * Get all tracks in the given range (one month) for a person
	 * The tracks are grouped by day and already summed up in the total key
	 *
	 * @param	Integer		$year
	 * @param	Integer		$month
	 * @param	Boolean		$details
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getRangeTracks($year, $month, $details = false, $idPerson = 0) {
		$year		= intval($year);
		$month		= intval($month);
		$idPerson	= Todoyu::personid($idPerson);

		$dateStart	= mktime(0, 0, 0, $month, 1, $year);
		$dateEnd	= mktime(0, 0, 0, $month + 1, 1, $year) - 1;

		if(!TodoyuTime::isWeekendDate($dateStart)) {
			$dateStart	= TodoyuTime::getWeekStart($dateStart, true);
		}

		$dateEnd	= TodoyuTime::getWeekEnd($dateEnd, true);

		$tracks		= TodoyuTimetracking::getPersonTracks($dateStart, $dateEnd, $idPerson);

		$workloads	= TodoyuArray::getColumn($tracks, 'workload_tracked');
		$total		= array_sum($workloads);
		$tracksByDay= array();

		foreach($tracks as $track) {
			$timestamp	= TodoyuTime::getDayStart($track['date_track']);

			$tracksByDay[$timestamp]['tracks'][]	= $track;
			$tracksByDay[$timestamp]['total']		+= $track['workload_tracked'];
		}

		if( $details ) {
			foreach($tracksByDay as $timestamp => $dayTracks) {
				foreach($dayTracks['tracks'] as $index => $track) {
					$task	= TodoyuProjectTaskManager::getTask($track['id_task']);

					$tracksByDay[$timestamp]['tracks'][$index]['task']		= $task->getTemplateData(0);
					$tracksByDay[$timestamp]['tracks'][$index]['seeTask']	= TodoyuProjectTaskRights::isSeeAllowed($track['id_task']);
				}
			}
		}

		return array(
			'dateStart'	=> $dateStart,
			'dateEnd'	=> $dateEnd,
			'total'		=> $total,
			'dayTracks'	=> array_reverse($tracksByDay, true)
		);
	}



	public static function getUserOptions(){
		$options	= array();

				// Internal persons
		$groupLabel	= Todoyu::Label('daytracks.ext.persons.internal');
		$options[$groupLabel]	= TodoyuContactPersonManager::getInternalPersonIDs();

			// External persons
		$groupLabel	= Todoyu::Label('daytracks.ext.persons.external');
		$options[$groupLabel]	= self::getExternalPersonOptions();

		return $options;
	}



	/**
	 * @param $true
	 * @return array
	 */
	private static function getExternalPersonOptions() {
		$loginPersonIDs		= TodoyuArray::getColumn(TodoyuContactPersonManager::getAllActivePersons(array('id'), false), 'id');
		$internalPersonIDs	= TodoyuContactPersonManager::getInternalPersonIDs();

		return array_diff($loginPersonIDs, $internalPersonIDs);
	}
}

?>