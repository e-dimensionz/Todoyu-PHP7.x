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
 * Event search
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventSearch implements TodoyuSearchEngineIf {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE	= 'ext_calendar_event';



	/**
	 * Search project in full-text mode. Return the ID of the matching projects
	 *
	 * @param	Array		$find		Keywords which have to be in the events
	 * @param	Array		$ignore		Keywords which must not be in the event
	 * @param	Integer		$limit
	 * @return	Array		Project IDs
	 */
	public static function searchEvents(array $find, array $ignore = array(), $limit = 100) {
		$table	= self::TABLE;
		$fields	= array('title', 'description', 'place');

		return TodoyuSearch::searchTable($table, $fields, $find, $ignore, $limit);
	}



	/**
	 * Get suggestions data array for event search
	 *
	 * @param	Array		$find
	 * @param	Array		$ignore
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getSuggestions(array $find, array $ignore = array(), $limit = 5) {
		$limit			= intval($limit);
		$suggestions	= array();

		$eventIDs		= self::searchEvents($find, $ignore, $limit);

			// Get event details
		if( sizeof($eventIDs) > 0 ) {
			$fields	= '	e.id,
						e.date_start,
						e.date_end,
						e.title,
						e.description';
			$table	= self::TABLE . '	e';
			$where	= '	e.id IN(' . implode(',', $eventIDs) . ')';
			$order	= '	e.date_start DESC';

			$events	= Todoyu::db()->getArray($fields, $table, $where, '', $order);

			foreach($events as $event) {
				if( TodoyuCalendarEventRights::isSeeDetailsAllowed($event['id']) ) {
					$labelTitle	= TodoyuTime::format($event['date_start'], 'datetime') . ' - ' . TodoyuTime::format($event['date_end'], 'datetime') . ' | ' . TodoyuString::wrap($event['title'], '<span class="keyword">|</span>');

					$suggestions[] = array(
						'labelTitle'=> $labelTitle,
						'labelInfo'	=> TodoyuString::getSubstring($event['description'], $find[0], 20, 30, false),
						'title'		=> strip_tags($labelTitle),
						'onclick'	=> 'location.href=\'index.php?ext=calendar&amp;tab=view&amp;event=' . $event['id'] . '\''
					);
				}
			}
		}

		return $suggestions;
	}
}

?>