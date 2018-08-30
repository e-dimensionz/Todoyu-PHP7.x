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
 * Event series view helper
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventSeriesViewHelper {

	/**
	 * Get label for series (comment field)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	String
	 */
	public static function getSeriesLabel(TodoyuFormElement $field) {
		$eventData	= $field->getForm()->getVar('eventData');

		if( !is_array($eventData) ) {
			$eventData	= $field->getForm()->getStorageData();
		}

		$idSeries	= intval($eventData['id_series']);
		$series		= TodoyuCalendarEventSeriesManager::getSeries($idSeries);
		$series->setFormData($eventData);

		return $series->getLabel();
	}



	/**
	 * Get options for series frequencies
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getFrequencyOptions(TodoyuFormElement $field) {
		return array(
			array(
				'value'	=> 0,
				'label'	=> 'calendar.series.frequency.no'
			),
			array(
				'value'	=> CALENDAR_SERIES_FREQUENCY_DAY,
				'label'	=> 'calendar.series.frequency.day'
			),
			array(
				'value'	=> CALENDAR_SERIES_FREQUENCY_WEEKDAY,
				'label'	=> 'calendar.series.frequency.weekday'
			),
			array(
				'value'	=> CALENDAR_SERIES_FREQUENCY_WEEK,
				'label'	=> 'calendar.series.frequency.week'
			),
			array(
				'value'	=> CALENDAR_SERIES_FREQUENCY_MONTH,
				'label'	=> 'calendar.series.frequency.month'
			),
			array(
				'value'	=> CALENDAR_SERIES_FREQUENCY_YEAR,
				'label'	=> 'calendar.series.frequency.year'
			)
		);
	}



	/**
	 * Get options for series intervals
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getIntervalOptions(TodoyuFormElement $field) {
		$idFrequency= $field->getForm()->getField('seriesfrequency')->getStorageData();
		$options	= array();

		switch( $idFrequency ) {
			case CALENDAR_SERIES_FREQUENCY_WEEK:
				$stepLabel	= 'calendar.series.interval.week';
				break;
			case CALENDAR_SERIES_FREQUENCY_MONTH:
				$stepLabel	= 'calendar.series.interval.month';
				break;
			case CALENDAR_SERIES_FREQUENCY_YEAR:
				$stepLabel	= 'calendar.series.interval.year';
				break;
			case CALENDAR_SERIES_FREQUENCY_DAY:
			default:
				$stepLabel	= 'calendar.series.interval.day';
		}

		for($i=1; $i<=30; $i++) {
			$options[] = array(
				'value'	=> $i,
				'label'	=> $i . ' ' . Todoyu::Label($stepLabel)
			);
		}

		return $options;
	}



	/**
	 * Get options for series day of week
	 *
	 * @param	TodoyuFormElement		$field
	 * @return	Array
	 */
	public static function getWeekdayOptions(TodoyuFormElement $field) {
		$items	= Todoyu::$CONFIG['EXT']['calendar']['weekDays']['long'];
		$options= array();

		foreach($items as $dayKey => $labelKey) {
			$options[] = array(
				'value'	=> $dayKey,
				'label'	=> TodoyuCalendarManager::getWeekDayLabel($dayKey)
			);
		}

		return $options;
	}



	/**
	 * Get series overbooking warnings
	 *
	 * @param	TodoyuFormElement		$field
	 * @return	String
	 */
	public static function getOverbookingWarnings(TodoyuFormElement $field) {
			// Dont check if all overbooking is allowed
		if( ! TodoyuCalendarManager::isOverbookingAllowed() ) {
			$seriesData	= $field->getForm()->getStorageData();
			$frequency	= intval($seriesData['seriesfrequency']);

			if( $frequency !== 0 ) {
				$eventData	= TodoyuArray::assure($field->getForm()->getVar('eventData'));

					// No event data set, use form data (fallback in case of invalid save request)
				if( !sizeof($eventData) ) {
					$eventData = $field->getForm()->getFormData();
				}

					// Only check for blocking event types
				if( !TodoyuCalendarEventTypeManager::isOverbookable($eventData['eventtype']) ) {
					$idSeries	= intval($eventData['id_series']);
					$series		= TodoyuCalendarEventSeriesManager::getSeries($idSeries);
					$series->setFormData($eventData);

					$warningMessages	= $series->getOverbookingConflictsWarningMessages(true);

					if( sizeof($warningMessages) > 0 ) {
						return implode('<br>', $warningMessages);
					}
				}
			}
		}

		return Todoyu::Label('calendar.series.conflicts.noConflicts');
	}

}

?>