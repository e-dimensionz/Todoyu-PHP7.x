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
 * Event object
 *
 * @package		Todoyu
 * @subpackage	Calendar
 *
 */
class TodoyuCalendarEventStatic extends TodoyuBaseObject implements TodoyuCalendarEvent {

	/**
	 * Initialize event
	 *
	 * @param	Integer		$idEvent
	 */
	public function __construct($idEvent) {
		parent::__construct($idEvent, 'ext_calendar_event');
	}



	/**
	 * Get start date
	 *
	 * @return	Integer
	 */
	public function getDateStart() {
		return $this->getInt('date_start');
	}



	/**
	 * Get start time (at start day)
	 *
	 * @return	Integer
	 */
	public function getStartTime() {
		return TodoyuTime::getTimeOfDay($this->get('date_start'));
	}



	/**
	 * Get end date of event
	 *
	 * @return	Integer
	 */
	public function getDateEnd() {
		return $this->getInt('date_end');
	}



	/**
	 * Get duration of event in seconds
	 *
	 * @return	Integer
	 */
	public function getDuration() {
		return $this->getDateEnd() - $this->getDateStart();
	}



	/**
	 * Get amount of hours of event duration
	 *
	 * @param	Integer		$precision
	 * @return	Integer
	 */
	public function getDurationHours($precision = 1) {
		return round($this->getDuration() / TodoyuTime::SECONDS_HOUR, $precision);
	}



	/**
	 * Get amount of minutes of event duration
	 *
	 * @param	Integer		$precision
	 * @return	Integer
	 */
	public function getDurationMinutes($precision = 0) {
		return round(($this->getDateEnd() - $this->getDateStart()) / TodoyuTime::SECONDS_MIN, $precision);
	}



	/**
	 * Get duration as string
	 *
	 * @param	Boolean	$withDuration
	 * @return	String
	 */
	public function getRangeLabel($withDuration = false) {
		$rangeLabel	= TodoyuTime::formatRange($this->getDateStart(), $this->getDateEnd());

		if( $withDuration && $this->getDuration() > 0 ) {
			$rangeLabel .= ' (' . TodoyuTime::formatDuration($this->getDuration()) . ')';
		}

		return $rangeLabel;
	}



	/**
	 * Get event range
	 *
	 * @param	Integer		$minLength
	 * @return	TodoyuDateRange
	 */
	public function getRange($minLength = 0) {
		return new TodoyuDateRange($this->getDateStart(), $this->getDateEnd());
	}



	/**
	 * Get place of event
	 *
	 * @return	String
	 */
	public function getPlace() {
		return $this->get('place');
	}



	/**
	 * Get title
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->get('title');
	}



	/**
	 * Get description
	 *
	 * @return	String
	 */
	public function getDescription() {
		return $this->get('description');
	}



	/**
	 * Get full label of event
	 *
	 * @param	Boolean		$withType
	 * @return	String
	 */
	public function getFullLabelHTML($withType = true) {
		$tmpl = 'ext/calendar/view/event-header.tmpl';

		$data = array(
			'date'	=>	TodoyuTime::format($this->getDateStart(), 'DshortD2MlongY4'),
			'title'	=>	$this->getTitle(),
			'type'	=>	$this->getTypeLabel(),
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get event type (ID)
	 *
	 * @return	Integer
	 */
	public function getTypeIndex() {
		return $this->getInt('eventtype');
	}



	/**
	 * Get type key
	 *
	 * @return	String
	 */
	public function getTypeKey() {
		return TodoyuCalendarEventTypeManager::getTypeKey($this->getTypeIndex());
	}



	/**
	 * Get string to identify type
	 *
	 * @see		getTypeKey
	 * @return	String
	 */
	public function getType() {
		return $this->getTypeKey();
	}



	/**
	 * Get type label
	 *
	 * @return	String
	 */
	public function getTypeLabel() {
		return TodoyuCalendarEventTypeManager::getEventTypeLabel($this->getTypeIndex(), true);
	}



	/**
	 * Get the IDs if assigned persons of event
	 *
	 * @return	Integer[]
	 */
	public function getAssignedPersonIDs() {
		$assignedPersons	= TodoyuCalendarEventStaticManager::getAssignedPersonsOfEvent($this->getID(), false);

		return TodoyuArray::getColumn($assignedPersons, 'id_person');
	}



	/**
	 * Get assigned persons
	 *
	 * @return	TodoyuContactPerson[]
	 */
	public function getAssignedPersons() {
		$personIDs	= $this->getAssignedPersonIDs();

		return TodoyuRecordManager::getRecordList('TodoyuContactPerson', $personIDs);
	}



	/**
	 * Get data of the assigned persons
	 *
	 * @param	Boolean		$getRemindersData
	 * @return	Array
	 */
	public function getAssignedPersonsData($getRemindersData = false) {
		return TodoyuCalendarEventStaticManager::getAssignedPersonsOfEvent($this->getID(), true, $getRemindersData);
	}



	/**
	 * Get assignment for person
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuCalendarEventAssignment
	 */
	public function getAssignment($idPerson) {
		$idPerson	= intval($idPerson);

		return TodoyuCalendarEventAssignmentManager::getAssignmentByEventPerson($this->getID(), $idPerson);
	}



	/**
	 * Get all assignments
	 *
	 * @return	TodoyuCalendarEventAssignment[]
	 */
	public function getAssignments() {
		$assignedPersonIDs	= $this->getAssignedPersonIDs();
		$assignments		= array();

		foreach($assignedPersonIDs as $idPerson) {
			$assignments[] = TodoyuCalendarEventAssignmentManager::getAssignmentByEventPerson($this->getID(), $idPerson);
		}

		return $assignments;
	}



	/**
	 * Get reminder for person
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuCalendarReminder
	 */
	public function getReminder($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		return TodoyuCalendarReminderManager::getReminderByAssignment($this->getID(), $idPerson);
	}



	/**
	 * Get popup reminder
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuCalendarReminderPopup
	 */
	public function getReminderPopup($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		return TodoyuCalendarReminderPopupManager::getReminderByAssignment($this->getID(), $idPerson);
	}



	/**
	 * @param	Integer							$idPerson
	 * @return	TodoyuCalendarReminderEmail
	 */
	public function getReminderEmail($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		return TodoyuCalendarReminderEmailManager::getReminderByAssignment($this->getID(), $idPerson);
	}



	/**
	 * Get reminder time for email
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public function getReminderTimeEmail($idPerson = 0) {
		return $this->getReminderTime('email', $idPerson);
	}



	/**
	 * Get reminder time for popup
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public function getReminderTimePopup($idPerson = 0) {
		return $this->getReminderTime('popup', $idPerson);
	}



	/**
	 * Get reminder time for type
	 *
	 * @param	String		$type
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	private function getReminderTime($type, $idPerson = 0) {
		$idPerson		= Todoyu::personid($idPerson);
		$assignedPersons= $this->getAssignedPersonsData(true);

		if( array_key_exists($idPerson, $assignedPersons) ) {
			$key	= 'date_remind' . $type;
			return intval($assignedPersons[$idPerson][$key]);
		} else {
			return 0;
		}
	}



	/**
	 * Get reminder advance time for email (in seconds)
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public function getReminderAdvanceTimeEmail($idPerson = 0) {
		return $this->getReminderAdvanceTime('email', $idPerson);
	}



	/**
	 * Get reminder advance time for popup (in seconds)
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	public function getReminderAdvanceTimePopup($idPerson = 0) {
		return $this->getReminderAdvanceTime('popup', $idPerson);
	}



	/**
	 * Get reminder advance time for type
	 *
	 * @param	String		$type
	 * @param	Integer		$idPerson
	 * @return	Integer
	 */
	private function getReminderAdvanceTime($type, $idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);
		$remindTime	= $this->getReminderTime($type, $idPerson);

		if( $remindTime === 0 ) {
			return 0;
		} else {
			return $this->getDateStart() - $remindTime;
		}
	}



	/**
	 * Check whether a person is assigned
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public function isPersonAssigned($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);
		$personIDs	= $this->getAssignedPersonIDs();

		return in_array($idPerson, $personIDs);
	}



	/**
	 * Check whether event start and end is on different days
	 *
	 * @return	Boolean
	 */
	public function isMultiDay() {
		return !$this->isSingleDay();
	}



	/**
	 * Check whether event start and end is on the same day
	 *
	 * @return	Boolean
	 */
	public function isSingleDay() {
		return date('Ymd', $this->getDateStart()) === date('Ymd', $this->getDateEnd());
	}



	/**
	 * Check whether event is a full-day event
	 *
	 * @return	Boolean
	 */
	public function isDayevent() {
		return intval($this->data['is_dayevent']) === 1;
	}



	/**
	 * Check whether current person is assigned
	 *
	 * @return	Boolean
	 */
	public function isCurrentPersonAssigned() {
		return $this->isPersonAssigned(Todoyu::personid());
	}



	/**
	 * Check whether event is private
	 *
	 * @return	Boolean
	 */
	public function isPrivate() {
		return intval($this->data['is_private']) === 1;
	}



	/**
	 * Check whether event is overbookable (generally allowed or type not overbooking relevant)
	 *
	 * @return Boolean
	 */
	public function isOverbookable() {
		return TodoyuCalendarEventTypeManager::isOverbookable($this->getTypeIndex());
	}



	/**
	 * Load event foreign data
	 *
	 * @param	Boolean	$getRemindersData
	 */
	protected function loadForeignData($getRemindersData = false) {
			// Add assigned persons of event
		if( ! isset($this->data['persons']) ) {
			$this->data['persons'] 	= $this->getAssignedPersonsData($getRemindersData);
		}

			// Add email receivers infos
		$emailPersons	= TodoyuMailManager::getEmailReceivers(EXTID_CALENDAR, CALENDAR_TYPE_EVENT, $this->data['id']);
		$this->data['persons_email']	= $emailPersons;
	}



	/**
	 * Get template data
	 *
	 * @param	Boolean		$loadForeignData
	 * @param	Boolean		$loadCreatorPersonData
	 * @param	Boolean		$loadRemindersData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false, $loadCreatorPersonData = false, $loadRemindersData = false) {
		if( $loadForeignData ) {
			$this->loadForeignData($loadRemindersData);
		}

		if( $loadCreatorPersonData ) {
			$this->data['person_create']	= $this->getPersonCreate()->getTemplateData(false);
		}

		$this->data['rangeLabel']		= $this->getRangeLabel(true);
		$this->data['isPrivate']		= $this->isPrivate();
		$this->data['isAcknowledged']	= $this->isAcknowledged();
		$this->data['isUpdated']		= $this->isUpdated();
		$this->data['isAssigned']		= $this->isCurrentPersonAssigned();

		if( $this->hasSeries() ) {
			$this->data['series'] = $this->getSeries()->getTemplateData();
		}

		if( $loadRemindersData ) {
			$this->prepareReminderTemplateData();
		}

		return parent::getTemplateData();
	}



	/**
	 * Add reminder labels to template data
	 */
	protected function prepareReminderTemplateData() {
		$idCurrentUser	= TodoyuAuth::getPersonID();
		$dateStartEvent	= $this->getDateStart();

			// Prepare labels with disabled status
		$reminders = array(
			'mail'	=> Todoyu::Label('calendar.reminder.deactivated'),
			'popup'	=> Todoyu::Label('calendar.reminder.deactivated')
		);

			// Get reminder dates
		$dateRemindMail	= intval($this->data['persons'][$idCurrentUser]['date_remindemail']);
		$dateRemindPopup= intval($this->data['persons'][$idCurrentUser]['date_remindpopup']);

			// Add mail reminder
		if( $dateRemindMail > 0 ) {
			$diffMail	= $dateStartEvent - $dateRemindMail;
			if( $diffMail === 1 ) {
				$reminders['mail'] = Todoyu::Label('calendar.reminder.atDateStart');
			} else {
				$reminders['mail'] = $this->getReminderLabel($diffMail, $dateRemindMail);
			}
		}

			// Add popup reminder
		if( $dateRemindPopup > 0 ) {
			$diffPopup	= $dateStartEvent - $dateRemindPopup;
			if( $diffPopup === 1 ) {
				$reminders['popup'] = Todoyu::Label('calendar.reminder.atDateStart');
			} else {
				$reminders['popup'] = $this->getReminderLabel($diffPopup, $dateRemindPopup);
			}
		}

		$this->data['reminders'] = $reminders;
	}



	/**
	 * Build reminder label
	 *
	 * @param	Integer		$duration		Advanced time before event
	 * @param	Integer		$dateRemind		Date of the reminder
	 * @return	String
	 */
	protected function getReminderLabel($duration, $dateRemind) {
		return TodoyuTime::formatDuration($duration) . ' ' . Todoyu::Label('calendar.reminder.beforeDateStart') . ' - ' . TodoyuTime::format($dateRemind, 'datetime');
	}



	/**
	 * Check whether other persons than the current are assigned to the event
	 *
	 * @return	Boolean
	 */
	public function areOtherPersonsAssigned() {
		$assignedPersonIDs	= $this->getAssignedPersonIDs();
		$others				= array_diff($assignedPersonIDs, array(Todoyu::personid()));

		return !empty($others);
	}



	/**
	 * Check whether any of the assigned person has an email address
	 *
	 * @return	Boolean
	 */
	public function hasAnyAssignedPersonAnEmailAddress() {
		$personsData	= $this->getAssignedPersonsData(false);

		foreach($personsData as $personData) {
			if( trim($personData['email']) !== '' ) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Check whether event is overlapping with another
	 *
	 * @param	TodoyuCalendarEvent		$event
	 * @return	Boolean
	 */
	public function isOverlapping(TodoyuCalendarEvent $event) {
		return $this->getRange()->isOverlapping($event->getRange());
	}



	/**
	 * Check whether current user can edit the event
	 *
	 * @return	Boolean
	 */
	public function canEdit() {
		return TodoyuCalendarEventRights::isEditAllowed($this->getID());
	}



	/**
	 * Check whether current user has access to the event
	 *
	 * @return	Boolean
	 */
	public function hasAccess() {
		return !$this->isPrivate() || $this->isCurrentPersonAssigned();
	}



	/**
	 * Get source
	 *
	 * @return	String
	 */
	public function getSource() {
		return 'static';
	}



	/**
	 * Check whether event is acknowledged for person
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public function isAcknowledged($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		return $this->getAssignment($idPerson)->isAcknowledged();
	}



	/**
	 * Check whether event is update for person
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public function isUpdated($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		return $this->getAssignment($idPerson)->isUpdated();
	}



	/**
	 * Add data to static event quick info
	 *
	 * @param	TodoyuQuickinfo			$quickInfo
	 * @param	TodoyuDayRange|null		$currentRange
	 */
	public function addQuickInfos(TodoyuQuickinfo $quickInfo, TodoyuDayRange $currentRange = null) {
		$canSeeDetails	= TodoyuCalendarEventRights::isSeeDetailsAllowed($this->getID());

			// Private event or no access?
		if( $canSeeDetails ) {
			$quickInfo->addInfo('title', $this->getTitle(), 10);

				// Add conditionally displayed (only if set) infos
			if( $this->getPlace() !== '' ) {
				if( $canSeeDetails ) {
					$quickInfo->addInfo('place', $this->getPlace(), 40);
				} else {
					$quickInfo->addInfo('place', Todoyu::Label('calendar.event.privateEvent.info'), 40);
				}
			}
		} else {
			$quickInfo->addInfo('title', '<' . Todoyu::Label('calendar.event.privateEvent.info') . '>', 10);
		}

			// Type
		$typeInfo	= $this->getQuickinfoTypeInfo();
		$quickInfo->addInfo('type' . ucfirst($this->getTypeKey()),	$typeInfo, 20);

			// Part of a series? (only displayed if yes)
		if( $this->getSeriesID() ) {
			$quickInfo->addInfo('isSeriesEvent', Todoyu::Label('calendar.event.isseriesevent.info'), 25);
		}

			// Date
		$dateInfo	= TodoyuTime::formatRange($this->getDateStart(), $this->getDateEnd());
		$quickInfo->addInfo('date',	$dateInfo, 30);

			// Duration
		if( $this->getDuration() > 0 ) {
			$durationInfo	= $this->getDurationFormatted();
			$quickInfo->addInfo('duration',	$durationInfo, 35);
		}

			// Persons
		$amountAssignedPersons	= sizeof($this->getAssignedPersonIDs());
		if( $amountAssignedPersons > 0 ) {
			$personInfo	= $this->getQuickinfoPersonInfo();
			$quickInfo->addInfo('persons', $personInfo, 50, false);
		}
	}



	/**
	 * Get formatted duration
	 * Handle case when event overlaps multiple days, but does not use the full range (00:00-23:59)
	 *
	 * @return	String
	 */
	public function getDurationFormatted() {
		if( $this->isSingleDay() || $this->getDuration() < TodoyuTime::SECONDS_HOUR*12 ) {
			$duration	= $this->getDuration();
		} else {
			$duration	= TodoyuTime::getDayEnd($this->getDateEnd()) - TodoyuTime::getDayStart($this->getDateStart());
		}

		return TodoyuTime::formatDuration($duration);
	}



	/**
	 * Build pre-formatted person(s) info for event quickinfo tooltip
	 *
	 * @return	String
	 */
	protected function getQuickinfoPersonInfo() {
		$persons	= $this->getAssignedPersonsData();
		$personInfo	= array();

		foreach($persons as $person) {
			$label	= TodoyuContactPersonManager::getLabel($person['id']);

				// Add person label, linked to contacts detail view if allowed to be seen
			if( Todoyu::allowed('contact', 'general:area') ) {
				$linkParams	= array(
					'ext'		=> 'contact',
					'controller'=> 'person',
					'action'	=> 'detail',
					'person'	=> $person['id'],
				);
				$linkedLabel		= TodoyuString::wrapTodoyuLink($label, 'contact', $linkParams);
				$personInfo[]	= $linkedLabel;
			} else {
				$personInfo[]	= $label;
			}
		}

		return implode("\n", $personInfo);
	}



	/**
	 * Build pre formatted type info for event quickinfo tooltip
	 *
	 * @return	String
	 */
	protected function getQuickinfoTypeInfo() {
		$typeInfo	= $this->getTypeLabel();

		if( $this->isPrivate() ) {
			$typeInfo	.= ', ' . Todoyu::Label('calendar.event.attr.is_private');
		}

		return $typeInfo;
	}



	/**
	 * Check whether event is based on a series
	 *
	 * @return	Boolean
	 */
	public function hasSeries() {
		return $this->getSeriesID() !== 0;
	}



	/**
	 * Get ID of the series
	 *
	 * @return	Integer
	 */
	public function getSeriesID() {
		return $this->getInt('id_series');
	}



	/**
	 *
	 *
	 * @return	TodoyuCalendarEventSeries
	 */
	public function getSeries() {
		return TodoyuRecordManager::getRecord('TodoyuCalendarEventSeries', $this->getSeriesID());
	}



	/**
	 * Get class names
	 *
	 * @return	String[]
	 */
	public function getClassNames() {
		$classNames	= array();

		if( $this->hasSeries() ) {
			$classNames[] = 'series' . $this->getSeriesID();
		}

		return $classNames;
	}



	/**
	 * Is the event in the future
	 *
	 * @return	Boolean
	 */
	public function isInFuture() {
		return $this->getDateStart() > NOW;
	}



	/**
	 * Is the event in the past
	 *
	 * @return	Boolean
	 */
	public function isInPast() {
		return $this->getDateEnd() < NOW;
	}

}

?>