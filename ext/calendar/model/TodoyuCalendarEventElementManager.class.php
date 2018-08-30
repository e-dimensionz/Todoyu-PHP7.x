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
 * Manage event elements
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventElementManager {

	/**
	 * Check overlapping of elements and add column index and conflict counter
	 *
	 * @param	TodoyuCalendarEventElementDayWeek[]		$eventElements
	 * @return	TodoyuCalendarEventElementDayWeek[]
	 */
	public static function addOverlapInformationToEvents(array $eventElements) {
		$columns	= array();

			// 1st step: get left position of each event
		foreach($eventElements as $index => $eventElement) {
				// Just add the first event of the day
			if( empty($columns) ) {
				$eventElement->setColumnIndex(0);
				$columns[0][]	= $index;
			} else {
				$fittingColumnFound	= false;
				$columnIndex		= 0;

				foreach($columns as $columnIndex => $columnEvents) {
					$eventOverlaps	= false;

					foreach($columnEvents as $eventElementIndex) {
							// Check if the event overlaps with the current column element
						if( $eventElement->isOverlapping($eventElements[$eventElementIndex]) ) {
								// Overlapping in this column, try next
							$eventOverlaps	= true;
							break;
						}
					}

						// Event does not overlap with another in this column
					if( !$eventOverlaps ) {
							// Mark as found (no overlapping)
						$fittingColumnFound	= true;
							// Stop looping over the current column
						break;
					}
				}

					// No fitting column found. Increment column counter (= add to new column)
				if( !$fittingColumnFound ) {
						// Next column = new column
					$columnIndex++;
				}

					// Add eventIndex to current column which has no overlapping
				$columns[$columnIndex][]	= $index;

				$eventElement->setColumnIndex($columnIndex);
			}
		}

			// Set number columns for each event
		foreach($eventElements as $eventElement) {
			$eventElement->setOverlapCounter($eventElements);
		}


		return $eventElements;
	}

}

?>