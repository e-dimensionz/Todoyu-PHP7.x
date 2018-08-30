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
 * Manage event series
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventSeriesManager {

	/**
	 * @var String		Default table for database requests
	 */
	const TABLE = 'ext_calendar_series';

	protected $createdSeriesID = 0;



	/**
	 * Get a series
	 *
	 * @param	Integer		$idSeries
	 * @return	TodoyuCalendarEventSeries
	 */
	public static function getSeries($idSeries) {
		return TodoyuRecordManager::getRecord('TodoyuCalendarEventSeries', $idSeries);
	}



	/**
	 * Create a new series
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addSeries(array $data) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a series
	 *
	 * @param	Integer		$idSeries
	 * @param	Array		$data
	 */
	public static function updateSeries($idSeries, array $data) {
		if( !isset($data['config']) ) {
			$data['config'] = '';
		}

		TodoyuRecordManager::updateRecord(self::TABLE, $idSeries, $data);
	}



	/**
	 * Delete a series (and all assigned events)
	 *
	 * @param	Integer		$idSeries
	 * @return	Integer[]	Deleted event IDs
	 */
	public static function deleteSeries($idSeries) {
		$idSeries	= intval($idSeries);

			// Delete series record
		TodoyuRecordManager::deleteRecord(self::TABLE, $idSeries);
			// Delete events
		$deletedEventIDs	= self::deleteSeriesEventsAfter($idSeries);
		$idFirstDeletedEvent= reset($deletedEventIDs);

		TodoyuHookManager::callHook('calendar', 'series.delete', array($idSeries));
		TodoyuHookManager::callHook('calendar', 'series.events.delete', array($idSeries, $deletedEventIDs));
		TodoyuHookManager::callHook('calendar', 'event.delete', array($idFirstDeletedEvent, array('series'=>true,'batch'=>false,'events'=>$deletedEventIDs)));

		return $deletedEventIDs;
	}



	/**
	 * Save a series and create the series events
	 *
	 * @param	Array		$seriesData
	 * @param	Integer		$idSavedEvent
	 * @return	Integer		The new event ID, just in case it changed because of
	 */
	public static function saveSeries(array $seriesData, $idSavedEvent) {
		$idSeries	= intval($seriesData['id']);
		$editFuture	= (boolean)$seriesData['editfuture'];
		unset($seriesData['editfuture']);

		TodoyuCalendarEventStaticManager::removeEventFromCache($idSavedEvent);

		if( $idSeries === 0 ) { // New series
			$idSeries	= self::addSeries($seriesData);
			self::setSeriesID($idSavedEvent, $idSeries);
			$result = self::createNewSeriesEvents($idSavedEvent, $idSeries);
		} else { // Update series
				// Create a new series
			$idSeriesNew	= self::addSeries($seriesData);

			if( $editFuture ) { // Replace events after saved one
				$result = self::modifySeriesAfterEvent($idSavedEvent, $idSeries, $idSeriesNew);
			} else { // Replace all not passed events
				$result = self::modifySeriesComplete($idSavedEvent, $idSeries, $idSeriesNew);
			}
		}

		$idBaseEvent	= $result->getNewBaseEventID();

		TodoyuCalendarEventStaticManager::removeEventFromCache($idBaseEvent);

		return $idBaseEvent;
	}



	/**
	 * Create events for a new series
	 *
	 * @param	Integer		$idBaseEvent
	 * @param	Integer		$idSeries
	 * @return	TodoyuCalendarEventSeriesCreateResult
	 */
	private static function createNewSeriesEvents($idBaseEvent, $idSeries) {
		$baseEvent		= TodoyuCalendarEventStaticManager::getEvent($idBaseEvent);
		$series			= self::getSeries($idSeries);
		$dateStartEvent	= $baseEvent->getDateStart();
		$dateStart		= $series->getFixedStartDate($dateStartEvent);
		$reCreateBase	= false;
		$result			= new TodoyuCalendarEventSeriesCreateResult($idBaseEvent, $idSeries);

			// Series doesn't match the event date
		if( $dateStartEvent !== $dateStart ) {
			TodoyuCalendarEventStaticManager::deleteEvent($idBaseEvent);
			$reCreateBase = true;
			$result->setBaseEventDeleted();
			$result->setDeletedEvents(array($idBaseEvent));
		}

		$createdEventIDs	= $series->createEvents($idBaseEvent, $dateStart, $reCreateBase);
		$result->setCreatedEvents($createdEventIDs);

		return $result;
	}



	/**
	 * Create events for a modified series.
	 * Only create events which will occur after the base event
	 *
	 * @param	Integer		$idBaseEvent
	 * @param	Integer		$idSeriesOld		ID of the old series
	 * @param	Integer		$idSeriesNew		ID of the new series
	 * @return	TodoyuCalendarEventSeriesCreateResult
	 */
	private static function modifySeriesAfterEvent($idBaseEvent, $idSeriesOld, $idSeriesNew) {
		$baseEvent		= TodoyuCalendarEventStaticManager::getEvent($idBaseEvent);
		$seriesNew		= self::getSeries($idSeriesNew);
		$dateStartEvent	= $baseEvent->getDateStart();
		$result			= new TodoyuCalendarEventSeriesCreateResult($idBaseEvent, $idSeriesNew, $idSeriesOld);

			// Fix start date
//		$dateStart	= $seriesNew->getFixedStartDate($dateStartEvent);

			// Delete all events of the old series which are not in the past
		$deletedEventIDs = self::deleteSeriesEventsAfter($idSeriesOld, $dateStartEvent-1);
		$result->setDeletedEvents($deletedEventIDs);

			// Create events for new series
		$createdEventIDs	= $seriesNew->createEvents($idBaseEvent, $dateStartEvent, true);
		$result->setCreatedEvents($createdEventIDs);

		return $result;
	}


	/**
	 * Create events for a modified series
	 * The events will replace all events of the series which are not in the past
	 *
	 * @param	Integer		$idBaseEvent
	 * @param	Integer		$idSeriesOld
	 * @param	Integer		$idSeriesNew
	 * @return	TodoyuCalendarEventSeriesCreateResult
	 */
	private static function modifySeriesComplete($idBaseEvent, $idSeriesOld, $idSeriesNew) {
			// Get series
		$seriesOld	= self::getSeries($idSeriesOld);
		$seriesNew	= self::getSeries($idSeriesNew);

			// Result
		$result		= new TodoyuCalendarEventSeriesCreateResult($idBaseEvent, $idSeriesNew, $idSeriesOld);

			// Get oldest event of the old series to find the start date
		$oldestEvent= $seriesOld->getFirstEvent();

			// If the start date is in the future, start there
		if( $oldestEvent->getDateStart() > NOW ) {
			$dateStart = $oldestEvent->getDateStart();
		} else {
			$dateStart	= NOW;
		}

			// Fix start date
		$dateStart	= self::applyEventTimeToDate($dateStart, $idBaseEvent);
		$dateStart	= $seriesNew->getFixedStartDate($dateStart);

			// Delete all events of the old series which are not in the past
		$deletedEventIDs = self::deleteSeriesEventsAfter($idSeriesOld, TodoyuTime::getDayStart(NOW), 0, true);
		$result->setDeletedEvents($deletedEventIDs);

			// Create events for new series
		$createdEventIDs = $seriesNew->createEvents($idBaseEvent, $dateStart, true);
		$result->setCreatedEvents($createdEventIDs);

		return $result;
	}



	/**
	 * Combine date with time of the event
	 * Ex: date: 2012-01-01, time: 15:10:00 => 2012-01-01 15:10:00
	 *
	 * @param	Integer		$date				Base date used for year,month,day
	 * @param	Integer		$idBaseEvent		Event ID. Start date is used for hour,minuts,seconds
	 * @return	Integer		Adjusted timestamp
	 */
	private static function applyEventTimeToDate($date, $idBaseEvent) {
		$baseEvent	= TodoyuCalendarEventStaticManager::getEvent($idBaseEvent);
		$dateStart	= $baseEvent->getDateStart();

		$dateParts	= getdate($date);
		$timeParts	= getdate($dateStart);

		return mktime($timeParts['hours'], $timeParts['minutes'], $timeParts['seconds'], $dateParts['mon'], $dateParts['mday'], $dateParts['year']);
	}



	/**
	 * Update the series ID for an event (assign an event to a series)
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idSeries
	 */
	private static function setSeriesID($idEvent, $idSeries) {
		$idEvent	= intval($idEvent);
		$idSeries	= intval($idSeries);
		$data		= array(
			'id_series'	=> $idSeries
		);

		TodoyuCalendarEventStaticManager::updateEvent($idEvent, $data);
	}



	/**
	 * Delete events of a series which start after $dateStart
	 *
	 * @param	Integer		$idSeries
	 * @param	Integer		$dateStart
	 * @param	Integer		$idEventIgnore			Ignore this event
	 * @param	Boolean		[$allowStartInPast]
	 * @return	Integer[]	IDs of deleted events
	 */
	public static function deleteSeriesEventsAfter($idSeries, $dateStart = 0, $idEventIgnore = 0, $allowStartInPast = false) {
		$idSeries		= intval($idSeries);
		$dateStart		= TodoyuTime::time($dateStart);
		$idEventIgnore	= intval($idEventIgnore);

		if( $dateStart < NOW && !$allowStartInPast ) {
			$dateStart = NOW;
		}

		$field	= 'id';
		$table	= 'ext_calendar_event';
		$where	= '		id_series	 = ' . $idSeries
				. '	AND date_start	 > ' . $dateStart
				. ' AND id			!= ' . $idEventIgnore
				. ' AND deleted		 = 0'
				. ' AND id_series	!= 0';  // Dummy security check to prevent deletion of non series events (just in case of a missing ID)

			// Get IDs of delete event
		$removeIDs	= Todoyu::db()->getColumn($field, $table, $where);
			// Delete the events
		TodoyuRecordManager::deleteRecords($table, $where);

			// Call event delete hook with special series option param
		foreach($removeIDs as $idEvent) {
			TodoyuHookManager::callHook('calendar', 'event.delete', array($idEvent, array('series'=>true, 'batch'=>true)));
		}

		return $removeIDs;
	}



	/**
	 * Hook to toggle series fields in event form
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idEvent
	 * @param	Array			$params
	 * @return	TodoyuForm
	 */
	public static function hookSetSeriesFields(TodoyuForm $form, $idEvent, array $params = array()) {
		$idEvent	= intval($idEvent);
		$formData	= TodoyuArray::assure($params['data']);
		$editSeries	= (boolean)$params['options']['seriesEdit'] || isset($formData['seriesfrequency']);

		if( $idEvent === 0 || $editSeries ) {
			if( $idEvent === 0 ) {
				$newEvent	= sizeof($formData) === 0;
				$series		= self::getSeries(0);
			} else {
				$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);
				$series		= $event->getSeries();
				$newEvent	= $series->hasNoFrequency();
			}

			$series->setFormData($formData);

			$form = $series->addSeriesFields($form, $newEvent);
		}

		if( $editSeries && $idEvent !== 0 ) {
			$form->getFieldset('main')->addFieldElement('seriesinfo', 'comment', array(
				'comment' => 'calendar.series.editMode'
			), 'before:title');
		}

		return $form;
	}
	


	/**
	 * Hook to load series data
	 *
	 * @param	Array		$data
	 * @param	Integer		$idEvent
	 * @param	Array		$params
	 * @return	Array
	 */
	public static function hookLoadSeriesData(array $data, $idEvent, array $params = array()) {
		$idEvent	= intval($idEvent);
		$editSeries	= intval($params['options']['seriesEdit']) === 1;

		if( $idEvent !== 0 ) {
			$event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);

			if( $event->hasSeries() ) {
				$series		= $event->getSeries();
				$seriesData	= $series->getFormData();

					// Inject series data for event
				$data	= array_merge($data, $seriesData);

					// Remove series ID if editing event as standalone
				if( !$editSeries ) {
					$data['id_series'] = 0;
				}
			}
		}

		return $data;
	}




	/**
	 * Get limit for series event creation
	 *
	 * @return	Integer
	 */
	public static function getCreateLimit() {
		return intval(Todoyu::$CONFIG['EXT']['calendar']['series']['maxCreate']);
	}



	/**
	 * Hook which modifies event update data on event move
	 * A moved event is remove from the series
	 *
	 * @param	Array		$data
	 * @param	Integer		$idEvent
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	public static function hookEventMovedDataUpdate($data, $idEvent, $dateStart, $dateEnd) {
		$data['id_series'] = 0;

		return $data;
	}



	/**
	 * Assign the users to the event and update the event reminders for the current user with the settings of the already saved base event
	 *
	 * @param	Integer		$idBaseEvent			Base event which was saved with the correct reminder settings
	 * @param	Integer		$idEvent				New created event which needs to be assigned for all users
	 * @param	Integer[]	$assignedPersonIDs		Assigned users
	 */
	public static function assignEvent($idBaseEvent, $idEvent, array $assignedPersonIDs) {
		$idEvent			= intval($idEvent);
		$idCurrentPerson	= Todoyu::personid();

			// Save assignment for all persons (with their defaults)
		TodoyuCalendarEventStaticManager::saveAssignments($idEvent, $assignedPersonIDs);

		if( in_array($idCurrentPerson, $assignedPersonIDs) ) {
				// Get reminder for current person. So we get the reminder advanced time
			$baseReminder		= TodoyuCalendarReminderManager::getReminderByAssignment($idBaseEvent, $idCurrentPerson);
			$advanceTimeEmail	= $baseReminder->getAdvanceTimeEmail();
			$advanceTimePopup	= $baseReminder->getAdvanceTimePopup();

			TodoyuCalendarEventStaticManager::updateAssignmentRemindersForPerson($idEvent, $advanceTimeEmail, $advanceTimePopup, $idCurrentPerson);
		}
	}

}

?>