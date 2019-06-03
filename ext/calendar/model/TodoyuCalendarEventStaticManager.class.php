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
 * Event Manager
 *
 * @package			Todoyu
 * @subpackage		Calendar
 */
class TodoyuCalendarEventStaticManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE	= 'ext_calendar_event';


	/**
	 * Get event form
	 *
	 * @param	Integer		$idEvent
	 * @param	Array		$formData
	 * @param	Array		$params
	 * @return	TodoyuForm
	 */
	public static function getEventForm($idEvent, array $formData = array(), array $params = array()) {
		$xmlPath	= 'ext/calendar/config/form/event.xml';
		$params['data'] = $formData;
		$form	= TodoyuFormManager::getForm($xmlPath, $idEvent, $params);
		$form->setUseRecordID(false);
//		$form->setVars(array(
//			'eventData'	=> $formData
//		));

		if( sizeof($formData) ) {
			$form->setFormData($formData);
		}

		return $form;
	}



	/**
	 * Get form object form quick create
	 *
	 * @param	Integer		$idEvent
	 * @return	TodoyuForm
	 */
	public static function getQuickCreateForm($idEvent = 0) {
		$idEvent	= intval($idEvent);

		TodoyuCalendarEventStaticManager::createNewEventWithDefaultsInCache(NOW);
		$event	= TodoyuCalendarEventStaticManager::getEvent(0);
		$data	= $event->getTemplateData(true, false, true);

		// Create form object
		$xmlPath= 'ext/calendar/config/form/event.xml';
//		$form	= TodoyuFormManager::getForm($xmlPath, $idEvent)

		$form	= self::getEventForm($idEvent, $data);

			// Call hooked load functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idEvent);
		$form->setFormData($data);

			// Adjust form to needs of quick creation wizard
		$form->setAttribute('action', 'index.php?ext=calendar&amp;controller=quickcreateevent');
		$form->setAttribute('onsubmit', 'return false');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.calendar.QuickCreateEvent.save(this.form)');
		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Popups.close(\'quickcreate\')');

		return $form;
	}



	/**
	 * Get event object
	 *
	 * @param	Integer		$idEvent
	 * @return	TodoyuCalendarEventStatic
	 */
	public static function getEvent($idEvent) {
		$idEvent	= intval($idEvent);

		return TodoyuRecordManager::getRecord('TodoyuCalendarEventStatic', $idEvent);
	}



	/**
	 * Get full label of event
	 *
	 * @param	Integer		$idEvent
	 * @param	Boolean		$withType
	 * @return	String
	 */
	public static function getEventFullLabel($idEvent, $withType = true) {
		return self::getEvent($idEvent)->getFullLabelHTML($withType);
	}



	/**
	 * Get event record from database
	 *
	 * @param	Integer		$idEvent
	 * @return	Array
	 */
	public static function getEventRecord($idEvent) {
		$idEvent	= intval($idEvent);

		return Todoyu::db()->getRecord(self::TABLE, $idEvent);
	}



	/**
	 * Get all events within given timestamps
	 *
	 * @param	Integer		$dateStart		timestamp at beginning of timespan
	 * @param	Integer		$dateEnd		timestamp at end of timespan	(optionally 0, will be set to 5 years after today than)
	 * @param	Array		$persons
	 * @param	Array		$eventTypes
	 * @param	Mixed		$dayEvents				null = both types, true = only full-day events, false = only non full-day events
	 * @param	String		$indexField
	 * @return	Array
	 */
	public static function getEventsInTimespan($dateStart, $dateEnd, array $persons = array(), array $eventTypes = array(), $dayEvents = null, $indexField = 'id') {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$persons	= TodoyuArray::intval($persons, true, true);

		$tables	=	self::TABLE  . ' e,
					ext_calendar_mm_event_person mmep';

		$fields	= '	e.*,
					mmep.id_person,
					mmep.is_acknowledged,
					mmep.is_updated,
					e.date_end - e.date_start as duration';

			// We add or subtract 1 second to prevent direct overlapping collision
			// Ex: event1: 10-11, event2: 11-12 - BETWEEN would find both event at 11:00:00
		$where	= '		e.id		= mmep.id_event
					AND e.deleted	= 0
					AND (
							e.date_start	BETWEEN ' . ($dateStart + 1) . ' AND ' . ($dateEnd - 1) . '
						OR	e.date_end		BETWEEN ' . ($dateStart + 1) . ' AND ' . ($dateEnd - 1) . '
						OR (e.date_start < ' . ($dateStart + 1) . ' AND e.date_end > ' . ($dateEnd - 1) . ')
					)';

		$group	= '';
		$order	= 'e.date_start, duration DESC';
		$limit	= '';

			// DayEvents: null = both, true = only, false = without
		if( ! is_null($dayEvents) ) {
			$where .= ' AND e.is_dayevent = ' . ($dayEvents ? 1 : 0);
		}

			// Limit to given event types
		if( sizeof($eventTypes) > 0 ) {
			$where .= ' AND e.eventtype IN(' . implode(',', $eventTypes) . ')';
		}

			// Not allowed to see all events? Limit to own events!
		if( ! Todoyu::allowed('calendar', 'event:seeAll') ) {
			$where .= ' AND mmep.id_person IN(' . Todoyu::personid() . ')';
		} elseif( sizeof($persons) > 0 ) {
				// Limit to given assigned persons
			$where	.= ' AND mmep.id_person IN(' . implode(',', $persons) . ')';
		}

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order, $limit, $indexField);
	}



	/**
	 * Get all persons assigned to an event
	 *
	 * @param	Integer		$idEvent
	 * @param	Boolean		$getPersonData		Get also person data?
	 * @param	Boolean		$getRemindersData	Get also persons reminders data?
	 * @return	Array
	 */
	public static function getAssignedPersonsOfEvent($idEvent, $getPersonData = false, $getRemindersData = false) {
		$idEvent	= intval($idEvent);

		$fields		= '	mm.id_person,
						mm.is_acknowledged';
		$tables		= '	ext_calendar_mm_event_person mm';
		$where		= '	mm.id_event = ' . $idEvent;
		$group		= ' mm.id_person';
		$indexField	= 'id_person';

		if( $getPersonData ) {
			$fields .= ', p.*';
			$tables	.= ', ext_contact_person p';
			$where	.= ' AND mm.id_person = p.id';
		}

		if( $getRemindersData ) {
			$fields	.= ', mm.date_remindemail
						, mm.date_remindpopup';
		}

		return Todoyu::db()->getArray($fields, $tables, $where, $group, '', '', $indexField);
	}



	/**
	 * Get all persons assigned to given array of events
	 *
	 * @param	Array $eventIDs
	 * @return	Array
	 */
	public static function getAssignedPersonsOfEvents(array $eventIDs) {
		$persons	= array();

		if( sizeof($eventIDs) > 0 ) {
			$fields	= 'id_event, id_person';
			$tables	= 'ext_calendar_mm_event_person';
			$where	= TodoyuSql::buildInListQueryPart($eventIDs, 'id_event');

			$epLinks= Todoyu::db()->getArray($fields, $tables, $where, '', 'id_event', '');

			foreach($epLinks as $epLink) {
				$persons[ $epLink['id_event'] ][]	= $epLink['id_person'];
			}
		}

		return $persons;
	}



	/**
	 * Get details of persons which could receive an event email
	 *
	 * @param	Integer		$idEvent
	 * @param	Boolean		$getPersonsDetails		(false: get only ID and email)
	 * @return	Array
	 */
	public static function getEmailReceivers($idEvent, $getPersonsDetails = true) {
		$idEvent	= intval($idEvent);

		$persons	= self::getAssignedPersonsOfEvent($idEvent, true);

			// Reduce persons data to contain only ID and email, use id_person as new key
		$reformConfig	= array(
			'id_person'	=> 'id_person',
			'email'		=> 'email'
		);
		$persons	= TodoyuArray::reformWithFieldAsIndex($persons, $reformConfig, $getPersonsDetails, 'id_person');

			// Remove all persons w/o email address
		foreach($persons as $idPerson => $personData) {
			if( empty($personData['email']) ) {
				unset($persons[$idPerson]);
			}
		}

		return $persons;
	}



	/**
	 * Check for conflicts with other events (of non-overbookable type) for the assigned persons if overbooking is not allowed
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @param	Array		$personIDs
	 * @param	Integer		$idEvent
	 * @return	Array		empty if no conflicts, information if conflicted
	 */
	public static function getOverbookingInfos($dateStart, $dateEnd, array $personIDs, $idEvent = 0) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$personIDs	= TodoyuArray::intval($personIDs, true, true);
		$idEvent	= intval($idEvent);

			// Make empty overbooking data
		$overbooked	= array();

		if( $dateEnd >= $dateStart ) {
				// Get all (not-overbookable / conflicting) events in the duration of the event
			$eventTypes	= TodoyuCalendarEventTypeManager::getNotOverbookableTypeIndexes();
			$otherEvents= TodoyuCalendarEventStaticManager::getEventsInTimespan($dateStart, $dateEnd, $personIDs, $eventTypes);
				// Remove current event
			unset($otherEvents[$idEvent]);

			foreach($otherEvents as $otherEvent) {
					// Don't check for conflicts if is all-day event as long its not an absence
				$absenceEventTypes	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['calendar']['EVENTTYPES_ABSENCE']);

				if( $otherEvent['is_dayevent'] == 1 && ! in_array($otherEvent['eventtype'], $absenceEventTypes)) {
					continue;
				}

				$assignedPersons	= TodoyuCalendarEventStaticManager::getAssignedPersonsOfEvent($otherEvent['id']);
				$assignedPersonIDs	= TodoyuArray::getColumn($assignedPersons, 'id_person');
				$conflictedPersonIDs= array_intersect($personIDs, $assignedPersonIDs);

				foreach($conflictedPersonIDs as $idPerson) {
					if( ! isset($overbooked[$idPerson]['person']) ) {
						$overbooked[$idPerson]['person']	= $idPerson;
					}

					if(empty($overbooked[$idPerson]['events'])) $overbooked[$idPerson]['events'] = array();
					if( count($overbooked[$idPerson]['events']) < Todoyu::$CONFIG['EXT']['calendar']['maxShownOverbookingsPerPerson'] ) {
						$overbooked[$idPerson]['events'][]	= $otherEvent;
					}
				}
			}
		}

		return $overbooked;
	}



	/**
	 * Delete event
	 *
	 * @param	Integer		$idEvent
	 * @return	Boolean
	 */
	public static function deleteEvent($idEvent) {
		$idEvent	= intval($idEvent);

		$deleted	= TodoyuRecordManager::deleteRecord(self::TABLE, $idEvent);

		TodoyuHookManager::callHook('calendar', 'event.delete', array($idEvent, array('series'=>false)));

		return $deleted;
	}



	/**
	 * Save a new event
	 *
	 * @param	Array	$data		event data
	 * @return	Integer				ID of event
	 */
	public static function saveEvent(array $data) {
		$xmlPath= 'ext/calendar/config/form/event.xml';

		$idEvent			= intval($data['id']);
		$isNewEvent			= $idEvent === 0;
		$advanceTimeEmail	= intval($data['reminder_email']);
		$advanceTimePopup	= intval($data['reminder_popup']);
		$personIDs			= TodoyuArray::intval($data['persons'], true, true);

			// Add empty event
		if( $idEvent === 0 ) {
			$idEvent		= self::addEvent();
			$dateStartOld	= 0;
		} else {
			$event			= self::getEvent($idEvent);
			$dateStartOld	= $event->getDateStart();
		}

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idEvent, array('newEvent'	=> $isNewEvent));

			// Extract data for series
		$seriesData	= self::extractSeriesData($data);

			// Remove not needed fields
		unset($data['persons']);
		unset($data['reminder_email']);
		unset($data['reminder_popup']);
		unset($data['sendasemail']);
		unset($data['emailreceivers']);

			// Update the event with the definitive data
		self::updateEvent($idEvent, $data);
			// Remove record and query from cache
		self::removeEventFromCache($idEvent);
			// Save person assignments
		self::saveAssignments($idEvent, $personIDs, $dateStartOld);
			// Set reminder for all users
		self::updateAssignmentRemindersForPerson($idEvent, $advanceTimeEmail, $advanceTimePopup);

		if( $seriesData !== false ) {
			$idEvent = TodoyuCalendarEventSeriesManager::saveSeries($seriesData, $idEvent);
		}

		self::removeEventFromCache($idEvent);

		TodoyuHookManager::callHook('calendar', 'event.save', array(
			$idEvent,
			array(
				'new' 		=> $isNewEvent
			)
		));

		return $idEvent;
	}



	/**
	 * Extract series data
	 * Return series data and remove it from event data
	 *
	 * @param	Array	$eventData
	 * @return	Array
	 */
	private static function extractSeriesData(array &$eventData) {
		$idFrequency= intval($eventData['seriesfrequency']);
		$seriesData	= false;

		unset($eventData['seriesfrequency']);

			// Is a series selected?
		if( $idFrequency !== 0 ) {
			$seriesData = array(
				'id'		=> intval($eventData['id_series']),
				'frequency'	=> $idFrequency,
				'interval'	=> intval($eventData['seriesinterval']),
				'date_end'	=> intval($eventData['seriesdate_end']),
				'editfuture'=> intval($eventData['serieseditfuture']) === 1,
				'config'	=> ''
			);

			unset($eventData['seriesinterval']);
			unset($eventData['seriesdate_end']);

			if( isset($eventData['seriesweekdays']) ) {
				$seriesData['weekdays'] = TodoyuArray::trimExplode(',', $eventData['seriesweekdays']);
				unset($eventData['seriesweekdays']);
			}

			if( $idFrequency === CALENDAR_SERIES_FREQUENCY_WEEK ) {
				$seriesData['config'] = json_encode($seriesData['weekdays']);
				unset($seriesData['weekdays']);
			}
		}

		unset($eventData['serieseditfuture']);

		return $seriesData;
	}







	/**
	 * Add an event to database. Add date_create and id_person_create values
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addEvent(array $data = array()) {
		$idEvent	= TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('calendar', 'event.add', array($idEvent, $data));

		return $idEvent;
	}



	/**
	 * Update an event in the database
	 *
	 * @param	Integer		$idEvent
	 * @param	Array		$data
	 */
	public static function updateEvent($idEvent, array $data) {
		TodoyuRecordManager::updateRecord(self::TABLE, $idEvent, $data);

		self::removeEventFromCache($idEvent);

		TodoyuHookManager::callHook('calendar', 'event.update', array($idEvent, $data));
	}



	/**
	 * Move an event to a new start date
	 *
	 * @param	Integer				$idEvent
	 * @param	Integer				$newStartDate
	 * @param	String				$mode
	 * @param	Boolean|Array		$overbookingConfirmed	True or array of overbooking infos
	 * @return	Array|Boolean
	 */
	public static function moveEvent($idEvent, $newStartDate, $mode, $overbookingConfirmed = false) {
		$event	= self::getEvent($idEvent);

		if( $mode === 'month' ) {
			$newStart	= TodoyuTime::getDayStart($newStartDate);
			$startDay	= TodoyuTime::getDayStart($event->getDateStart());
			$offset		= $newStart - $startDay;
			$dateStart	= $event->getDateStart() + $offset;
			$dateEnd	= $event->getDateEnd() + $offset;
		} else {
			$offset		= $newStartDate - $event->getDateStart();
			$dateStart	= $newStartDate;
			$dateEnd	= $event->getDateEnd() + $offset;
		}

		if( !TodoyuCalendarEventTypeManager::isOverbookable($event->getTypeIndex(), $event->isDayevent()) ) {
			if( !$overbookingConfirmed || !TodoyuCalendarManager::isOverbookingAllowed() ) {
					// Collect overbookings of assigned persons (to request confirmation or resetting event)
				$overbookingPersonsErrors	= self::getOverbookedPersonsErrors($idEvent, $dateStart, $dateEnd);
				if( $overbookingPersonsErrors !== false ) {
					return $overbookingPersonsErrors;
				}
			}
		}

			// Update event record data
		$data	= array(
			'date_start'=> $dateStart,
			'date_end'	=> $dateEnd
		);


		$data	= TodoyuHookManager::callHookDataModifier('calendar', 'event.move.data', $data, array($idEvent, $dateStart, $dateEnd));

		self::updateEvent($idEvent, $data);

			// Update scheduled reminders relative to shifted time of event
		TodoyuCalendarReminderManager::shiftReminderDates($idEvent, $offset);

		TodoyuHookManager::callHook('calendar', 'event.move', array($idEvent, $dateStart, $dateEnd));

		return true;
	}



	/**
	 * Collect overbooking errors of affected persons
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	String[]|Boolean
	 */
	public static function getOverbookedPersonsErrors($idEvent, $dateStart, $dateEnd) {
		$idEvent	= intval($idEvent);

		$event			= TodoyuCalendarEventStaticManager::getEvent($idEvent);
		$assignedPersons= $event->getAssignedPersonIDs();
		$overbookedInfos= self::getOverbookingInfos($dateStart, $dateEnd, $assignedPersons, $idEvent);

		if( sizeof($overbookedInfos) > 0 ) {
			$errorMessages	= array();
			foreach($overbookedInfos as $idPerson => $infos) {
				$errorMessages[]	= Todoyu::Label('calendar.event.error.personsOverbooked') . ' ' . TodoyuContactPersonManager::getPerson($idPerson)->getFullName();
			}

			return array_unique($errorMessages);
		}

		return false;
	}



	/**
	 * Assign multiple persons to an event
	 *
	 * @param	Integer		$idEvent
	 * @param	Array		$personIDs
	 * @param	Integer		$dateStartOld
	 */
	public static function saveAssignments($idEvent, array $personIDs, $dateStartOld = 0) {
		$idEvent			= intval($idEvent);
		$personIDs			= TodoyuArray::intval($personIDs, true, true);
		$assignedPersonIDs	= TodoyuCalendarEventAssignmentManager::getAssignedPersonIDs($idEvent);
		$newAssignments		= TodoyuArray::diffLeft($personIDs, $assignedPersonIDs);
		$removedAssignments	= TodoyuArray::diffLeft($assignedPersonIDs, $personIDs);
		$keptAssignments	= array_intersect($personIDs, $assignedPersonIDs);

			// Add new assignments
		foreach($newAssignments as $idPerson) {
			self::addAssignment($idEvent, $idPerson);
		}

			// Remove deleted assignments
		foreach($removedAssignments as $idPerson) {
			TodoyuCalendarEventAssignmentManager::removeAssignment($idEvent, $idPerson);
		}

			// Update untouched assignments
		foreach($keptAssignments as $idPerson) {
			self::updateAssignment($idEvent, $idPerson, $dateStartOld);
		}
	}



	/**
	 * Assign person to the event
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 */
	private static function addAssignment($idEvent, $idPerson) {
		$idEvent		= intval($idEvent);
		$event			= self::getEvent($idEvent);
		$idPerson		= intval($idPerson);
		$acknowledged	= Todoyu::personid() == $idPerson ? 1 : 0;

		$dateStart			= $event->getDateStart();
		$advanceTimeEmail	= TodoyuCalendarReminderManager::getAdvanceTimeEmail($idPerson);
		$advanceTimePopup	= TodoyuCalendarReminderManager::getAdvanceTimePopup($idPerson);
		$dateRemindEmail	= $advanceTimeEmail === 0 ? 0 : $dateStart - $advanceTimeEmail;
		$dateRemindPopup	= $advanceTimePopup === 0 ? 0 : $dateStart - $advanceTimePopup;

		$table	= 'ext_calendar_mm_event_person';
		$data	= array(
			'id_event'			=> $idEvent,
			'id_person'			=> $idPerson,
			'is_acknowledged'	=> $acknowledged,
			'is_updated'		=> 0,
			'date_remindemail'	=> $dateRemindEmail,
			'date_remindpopup'	=> $dateRemindPopup
		);

		Todoyu::db()->addRecord($table, $data);

		TodoyuHookManager::callHook('calendar', 'event.assign', array($idEvent, $idPerson));
	}



	/**
	 * Update assignment for a person
	 * Update reminders if set
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @param	Integer		$dateStartOld
	 */
	public static function updateAssignment($idEvent, $idPerson, $dateStartOld) {
		$event		= self::getEvent($idEvent);
		$reminder	= $event->getReminder($idPerson);
		$diff		= $event->getDateStart() - $dateStartOld;

		$dateEmail	= false;
		$datePopup	= false;

		if( $reminder->hasEmailReminder() ) {
			$dateEmail	= $reminder->getDateRemindEmail() + $diff;
		}
		if( $reminder->hasPopupReminder() ) {
			$datePopup	= $reminder->getDateRemindPopup() + $diff;
		}

		TodoyuCalendarEventAssignmentManager::updateReminderDates($idEvent, $idPerson, $dateEmail, $datePopup);
		TodoyuCalendarReminderManager::removeReminderFromCache($reminder->getID());
	}



	/**
	 * Update reminders in assignment for current person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$advanceTimeEmail		Reminder time before event start for email
	 * @param	Integer		$advanceTimePopup		Reminder time before event start for popup
	 * @param	Integer		$idPerson
	 */
	public static function updateAssignmentRemindersForPerson($idEvent, $advanceTimeEmail, $advanceTimePopup, $idPerson = 0) {
		$idEvent			= intval($idEvent);
		$idPerson			= Todoyu::personid($idPerson);
		$advanceTimeEmail	= intval($advanceTimeEmail);
		$advanceTimePopup	= intval($advanceTimePopup);
		$event				= self::getEvent($idEvent);
		$dateStart			= $event->getDateStart();

		$data	= array(
			'is_remindpopupdismissed'	=> 0,
			'date_remindemail'			=> $advanceTimeEmail === 0 ? 0 : ($dateStart - $advanceTimeEmail),
			'date_remindpopup'			=> $advanceTimePopup === 0 ? 0 : ($dateStart - $advanceTimePopup)
		);

		TodoyuCalendarReminderManager::updateReminderByAssignment($idEvent, $idPerson, $data);
	}



	/**
	 * Check whether person is assigned
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isPersonAssigned($idEvent, $idPerson) {
		$idEvent	= intval($idEvent);
		$idPerson	= intval($idPerson);
		$personIDs	= TodoyuCalendarEventAssignmentManager::getAssignedPersonIDs($idEvent);

		return in_array($idPerson, $personIDs);
	}



	/**
	 * Remove event from cache
	 *
	 * @param	Integer	$idEvent
	 */
	public static function removeEventFromCache($idEvent) {
		$idEvent	= intval($idEvent);

		TodoyuRecordManager::removeRecordCache('TodoyuCalendarEventStatic', $idEvent);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idEvent);
	}



	/**
	 * Set given event acknowledged by given person
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$idPerson
	 */
	public static function acknowledgeEvent($idEvent, $idPerson = 0) {
		$idEvent	= intval($idEvent);
		$idPerson	= Todoyu::personid($idPerson);

		$where	= '		id_event	= ' . $idEvent .
				  ' AND	id_person	= ' . $idPerson;

			// Store also timestamp to be able to detect unacknowledged modifications of events
		$update	= array(
			'is_acknowledged'	=> 1
		);

		Todoyu::db()->doUpdate('ext_calendar_mm_event_person', $where, $update);

		TodoyuHookManager::callHook('calendar', 'event.acknowledge', array($idEvent, $idPerson));
	}



	/**
	 * Create new event object with default data
	 *
	 * @param	Integer	$timestamp
	 */
	public static function createNewEventWithDefaultsInCache($timestamp) {
		$timestamp	= intval($timestamp);
		$defaultData= self::getEventDefaultData($timestamp);

		$idCache	= TodoyuRecordManager::makeClassKey('TodoyuCalendarEventStatic', 0);
		$event		= self::getEvent(0);
		$event->injectData($defaultData);
		TodoyuCache::set($idCache, $event);
	}



	/**
	 * Create event default data
	 *
	 * @param	Integer		$timestamp
	 * @return	Array
	 */
	protected static function getEventDefaultData($timestamp) {
		$timestamp	= $timestamp == 0 ? NOW : intval($timestamp);

		if( date('Hi', $timestamp) === '0000' ) {
			$dateStart	= $timestamp + intval(Todoyu::$CONFIG['EXT']['calendar']['default']['timeStart']);
		} else {
			$dateStart	= $timestamp;
		}

		$dateEnd	= $dateStart + intval(Todoyu::$CONFIG['EXT']['calendar']['default']['eventDuration']);

		$defaultData	= array(
			'id'			=>	0,
			'date_start'	=>	$dateStart,
			'date_end'		=>	$dateEnd,
			'eventtype'		=> EVENTTYPE_GENERAL,
			'persons'		=> array(
				TodoyuAuth::getPerson()->getTemplateData()
			)
		);

		return $defaultData;
	}



	/**
	 * Add default context menu item for event
	 *
	 * @param	Integer		$idEvent
	 * @param	Array		$items
	 * @return	Array
	 */
	public static function getContextMenuItems($idEvent, array $items) {
		$idEvent= intval($idEvent);

		$allowed= array();
		$own	= Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['Event'];

			// Option: show event
		if( TodoyuCalendarEventRights::isSeeDetailsAllowed($idEvent) ) {
			$allowed['show']	= $own['show'];
		}
			// Options: edit event, delete event
			// Edit event: right:editAll OR is assigned and right editAssigned OR is creator
		if( TodoyuCalendarEventRights::isEditAllowed($idEvent) ) {
			$allowed['edit']	= $own['edit'];
		}
		if( TodoyuCalendarEventRights::isDeleteAllowed($idEvent) ) {
			$allowed['delete']	= $own['remove'];
		}

			// Option: add event
		if( TodoyuCalendarEventRights::isAddAllowed() ) {
			$allowed['add']		= $own['add'];
		}

		$items	= array_merge_recursive($items, $allowed);

		return $items;
	}



	/**
	 * Get event context menu items for display in portal
	 *
	 * @param	Integer		$idEvent
	 * @param	Array		$items
	 * @return	Array
	 */
	public static function getContextMenuItemsPortal($idEvent, array $items) {
		$idEvent	= intval($idEvent);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);
		$dateStart	= $event->getDateStart();

		$ownItems			= Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['Event'];
		$ownItems['show']	= Todoyu::$CONFIG['EXT']['calendar']['ContextMenu']['EventPortal']['show'];
		$ownItems['edit']['jsAction']	= 'Todoyu.Ext.calendar.EventPortal.edit(#ID#)';

		unset($ownItems['add']);

		if( ! TodoyuCalendarEventRights::isEditAllowed($idEvent) ) {
			unset($ownItems['edit']);
		}

		if( ! TodoyuCalendarEventRights::isDeleteAllowed($idEvent) ) {
			unset($ownItems['delete']);
		}

		$ownItems['show']['jsAction']	= str_replace('#DATE#', $dateStart, $ownItems['show']['jsAction']);

		foreach($ownItems['show']['submenu'] as $key => $config) {
			$ownItems['show']['submenu'][$key]['jsAction']	= str_replace('#DATE#', $dateStart, $config['jsAction']);
		}

		return array_merge_recursive($items, $ownItems);
	}



	/**
	 * Hook when saving event data. Modify data looking at the event type
	 *
	 * @param	Array		$data
	 * @param	Integer		$idEvent
	 * @return	Array
	 */
	public static function hookSaveEvent(array $data, $idEvent) {
		switch( $data['eventtype'] ) {
				// Birthday
			case EVENTTYPE_BIRTHDAY:
				$data['is_dayevent']= 1;
				$data['date_end'] 	= $data['date_start']; // Fix, so event is in day period
				break;

				// Reminder
			case EVENTTYPE_REMINDER:
				$data['date_end'] = $data['date_start'];
				break;
		}

			// Make sure date end is set. Same as date start of not set
		if( empty($data['date_end']) ) {
			$data['date_end'] = $data['date_start'];
		}

			// Expand to maximal hours for day events 00:00-23:59
		if( $data['is_dayevent'] == 1 ) {
			$data['date_start']	= TodoyuTime::getDayStart($data['date_start']);
			$data['date_end']	= TodoyuTime::getDayEnd($data['date_end']);
		}

		return $data;
	}



	/**
	 * Check if user has access to view or edit tab
	 * If not, change tab to "day"
	 *
	 * @param	String		$tab
	 * @param	Integer		$idEvent
	 * @return	String		Allowed tab
	 */
	public static function checkTabAccess($tab, $idEvent) {
		$tab	= trim($tab);
		$idEvent= intval($idEvent);

			// Check for edit rights
		if( $tab === 'edit' && ! TodoyuCalendarEventRights::isEditAllowed($idEvent) ) {
			$tab	= 'day';
		}

			// Check for view rights
		if( $tab === 'view' && ! TodoyuCalendarEventRights::isSeeDetailsAllowed($idEvent) ) {
			$tab	= 'day';
		}

		return $tab;
	}



	/**
	 * Check event for overbookings (regardless whether allowed) and render warning message content if any found
	 *
	 * @param	Integer		$idEvent
	 * @param	Array		$formData
	 * @param	Boolean		$forPopup			For popup or annotation inside the form?
	 * @param	Boolean		$convertDates		Dates (start/end) needed to be parsed from string, or are timestamps already?
	 * @param	Boolean		$isDragAndDrop
	 * @return	String
	 */
	public static function getOverbookingWarning($idEvent, array $formData, $forPopup = true, $convertDates = true, $isDragAndDrop = false) {
		$idEvent	= intval($idEvent);
		$dateStart	= ( $convertDates ) ? TodoyuTime::parseDate($formData['date_start']) : $formData['date_start'];
		$dateEnd	= ( $convertDates ) ? TodoyuTime::parseDate($formData['date_end']) : $formData['date_end'];
		$personIDs	= TodoyuArray::intval($formData['persons'], true, true);

		$warning		= '';
		$overbookedInfos= TodoyuCalendarEventStaticManager::getOverbookingInfos($dateStart, $dateEnd, $personIDs, $idEvent);

		if( sizeof($overbookedInfos) > 0 ) {
			$tmpl	= 'ext/calendar/view/overbooking-info.tmpl';
			$formData	= array(
				'idEvent'		=> $idEvent,
				'overbooked'	=> $overbookedInfos
			);

			if( $forPopup ) {
					// Render for display in popup
				if( ! $isDragAndDrop ) {
						// Regular edit of event
					$xmlPath= 'ext/calendar/config/form/overbooking-warning.xml';
					$form	= TodoyuFormManager::getForm($xmlPath);
				} else {
						// Modification via drag and drop
					$xmlPath= 'ext/calendar/config/form/overbooking-warning-drop.xml';
					$form	= TodoyuFormManager::getForm($xmlPath);
				}
				$buttonsForm	= $form->render();

				$tmpl	= 'ext/calendar/view/overbooking-warning.tmpl';
				$formData['buttonsFieldset']	= $buttonsForm;
			}

			$warning	= Todoyu::render($tmpl, $formData);
		}

		return $warning;
	}



	/**
	 * Check overbooking warning for dragged & dropped event
	 *
	 * @param	Integer		$idEvent
	 * @param	Integer		$dateStart
	 * @return	String
	 */
	public static function getOverbookingWarningAfterDrop($idEvent, $dateStart) {
		$idEvent	= intval($idEvent);
		$dateStart	= intval($dateStart);

			// Fetch original event data
		$event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);

		$eventData				= $event->getData();
		$eventData['persons']	= $event->getAssignedPersonsData();
			// Set modified time data
		$eventData['date_start']= $dateStart;
		$eventData['date_end']	= $dateStart + $event->getDuration();

		return self::getOverbookingWarning($idEvent, $eventData, true, false, true);
	}



	/**
	 * Get colors for event type
	 *
	 * @return	Array
	 */
	public static function getEventTypeColors() {
		return array(
			0						=> '',
			EVENTTYPE_GENERAL		=> "#7f007f",
			EVENTTYPE_AWAY			=> "#FF0000",
			EVENTTYPE_BIRTHDAY		=> "#FFAC00",
			EVENTTYPE_VACATION		=> "#FFFC00",
			EVENTTYPE_EDUCATION		=> "#77DC00",
			EVENTTYPE_MEETING		=> "green",
			EVENTTYPE_AWAYOFFICIAL	=> "#A60000",
			EVENTTYPE_HOMEOFFICE	=> "grey",
			9						=> "#2335e0",
			10						=> "pink"
		);
	}



	/**
	 * Get color data for event item via assigned person, if there are multiple/no persons assigned it's colored neutral
	 *
	 * @param	Integer		$idEvent
	 * @return	Array
	 */
	public static function getEventColorData($idEvent) {
		$idEvent		= intval($idEvent);
		$eventPersons	= TodoyuCalendarEventStaticManager::getAssignedPersonsOfEvent($idEvent, true, true);

		if( count($eventPersons) === 1 ) {
				// Single person assigned, set event color accordingly
			$idPerson		= $eventPersons[key($eventPersons)]['id_person'];
			$personColors	= TodoyuContactPersonManager::getSelectedPersonColor(array($idPerson));

			return $personColors[$idPerson];
		} else {
				// None or multiple persons assigned to event, no unique coloring possible
			return array(
				'id'	=> 'multiOrNone'
			);
		}
	}



	/**
	 * Check for warnings (overbookings) to be shown prior to saving
	 *
	 * @param	Integer		$idEvent
	 * @param	Array		$params
	 * @return	Array
	 */
	public static function getOverbookingWarningHeaders($idEvent, array $params) {
		$warnings	= array();

		$isOverbookingConfirmed	= intval($params['isOverbookingConfirmed']);
		if( TodoyuCalendarManager::isOverbookingAllowed() && ! $isOverbookingConfirmed ) {
			$overbookedWarning	= self::getOverbookingWarning($idEvent, $params['event']);
			if( ! empty($overbookedWarning) ) {
				$warnings['overbookingwarning']			= $overbookedWarning;
				$warnings['overbookingwarningInline']	= self::getOverbookingWarning($idEvent, $params['event'], false);
			}
		}

		return $warnings;
	}

}

?>