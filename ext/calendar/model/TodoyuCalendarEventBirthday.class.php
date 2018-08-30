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
 * Birthday event
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventBirthday implements TodoyuCalendarEvent {

	/**
	 * Range for birthday in current search range
	 *
	 * @var	TodoyuCalendarRangeDay
	 */
	protected $birthdayRange;

	/**
	 * ID person
	 *
	 * @var	Integer
	 */
	protected $idPerson	= 0;



	/**
	 * Initialize with person and search range
	 *
	 * @param	Integer					$idPerson
	 * @param	TodoyuDayRange|null		$searchRange		Range in which the birthday occurs this year
	 */
	public function __construct($idPerson, TodoyuDayRange $searchRange = null) {
		$this->idPerson	= intval($idPerson);

		if( !is_null($searchRange) ) {
			$this->birthdayRange	= new TodoyuCalendarRangeDay($this->getBirthdayDateForRange($searchRange));
		}
	}



	/**
	 * Get the date at which the birthday occurs in the search range
	 *
	 * @param	TodoyuDayRange		$searchRange
	 * @return	Integer
	 */
	protected function getBirthdayDateForRange(TodoyuDayRange $searchRange) {
		$dateBirthday	= $this->getPerson()->getBirthday();
		$bornMonth		= date('n', $dateBirthday);
		$bornDay		= date('j', $dateBirthday);

		if( $bornMonth >= date('n', $searchRange->getStart()) ) {
			$yearRefDate	= $searchRange->getStart();
		} else  {
			$yearRefDate	= $searchRange->getEnd();
		}

		$year	= date('Y', $yearRefDate);

		return mktime(0, 0, 0, $bornMonth, $bornDay, $year);
	}



	/**
	 * Get ID (of the person)
	 *
	 * @return	Integer
	 */
	public function getID() {
		return $this->getPerson()->getID();
	}



	/**
	 * Get title (name of the person and age)
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->getPerson()->getFullName() . ' (' . $this->getAge() . ' ' . Todoyu::Label('calendar.ext.yearsold') . ')';
	}



	/**
	 * Get description
	 *
	 * @return	String
	 */
	public function getDescription() {
		return $this->getTitle();
	}



	/**
	 * Get person
	 *
	 * @return	TodoyuContactPerson
	 */
	public function getPerson() {
		return TodoyuContactPersonManager::getPerson($this->idPerson);
	}



	/**
	 * Get start date of the birthday (morning)
	 *
	 * @return	Integer
	 */
	public function getDateStart() {
		return $this->getRange()->getStart();
	}



	/**
	 * Get end date of the birthday (night)
	 *
	 * @return	Integer
	 */
	public function getDateEnd() {
		return $this->getRange()->getEnd();
	}



	/**
	 * Get duration of the birthday
	 *
	 * @return	Integer
	 */
	public function getDuration() {
		return TodoyuTime::SECONDS_DAY;
	}



	/**
	 * Get range
	 *
	 * @return	TodoyuCalendarRangeDay|null
	 */
	public function getRange() {
		return $this->birthdayRange;
	}



	/**
	 * Birthday are always day events
	 *
	 * @return	Boolean
	 */
	public function isDayEvent() {
		return true;
	}



	/**
	 * Birthdays are never private
	 *
	 * @return	Boolean
	 */
	public function isPrivate() {
		return false;
	}



	/**
	 * No one is assigned to a birthday, it just happens ;-)
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public function isPersonAssigned($idPerson = 0) {
		return false;
	}



	/**
	 * No persons assigned, so empty
	 *
	 * @return	Array
	 */
	public function getAssignedPersons() {
		return array();
	}



	/**
	 * Birthdays don't overlap
	 *
	 * @param	TodoyuCalendarEvent $event
	 * @return	Boolean
	 */
	public function isOverlapping(TodoyuCalendarEvent $event) {
		return false;
	}



	/**
	 * Source name
	 *
	 * @return	String
	 */
	public function getSource() {
		return 'birthday';
	}



	/**
	 * Type is birthday as for static events
	 *
	 * @return	String
	 */
	public function getType() {
		return 'birthday';
	}



	/**
	 * No one has access, because the are no details for this event
	 *
	 * @return	Boolean
	 */
	public function hasAccess() {
		return false;
	}



	/**
	 * No one can edit/change a birthday ;-)
	 *
	 * @return	Boolean
	 */
	public function canEdit() {
		return false;
	}



	/**
	 * No users are assigned
	 *
	 * @return	Boolean
	 */
	public function isCurrentPersonAssigned() {
		return false;
	}



	/**
	 * Get age
	 * An additional search range must be provided, if not set on initialization
	 *
	 * @param	TodoyuDayRange|null		$searchRange
	 * @return	Integer
	 */
	public function getAge(TodoyuDayRange $searchRange = null) {
		if( !is_null($searchRange) ) {
			$date	= $this->getBirthdayDateForRange($searchRange);
		} elseif( !is_null($this->birthdayRange) ) {
			$date	= $this->birthdayRange->getStart();
		} else {
			$date	= 0;
		}

		$yearBorn	= date('Y', $this->getPerson()->getBirthday());
		$yearRange	= date('Y', $date);

		return intval($yearRange-$yearBorn);
	}



	/**
	 * Get template data
	 *
	 * @param	Boolean		$loadForeignData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false) {
		return array(
			'id'				=> $this->getID(),
			'isPrivate'			=> $this->isPrivate(),
			'assignedPersons'	=> array(),
			'isAssigned'		=> $this->isCurrentPersonAssigned(),
			'dateStart'			=> $this->getRange()->getStart(),
			'dateEnd'			=> $this->getRange()->getEnd(),
			'title'				=> $this->getTitle(),
			'person'			=> $this->getPerson()->getTemplateData(),
			'age'				=> $this->getAge(),
			'date'				=> $this->birthdayRange->getStart()
		);
	}



	/**
	 * Add data to quick info
	 *
	 * @param	TodoyuQuickinfo			$quickInfo
	 * @param	TodoyuDayRange|null		$currentRange
	 */
	public function addQuickInfos(TodoyuQuickinfo $quickInfo, TodoyuDayRange $currentRange = null) {
		$age		= $this->getAge($currentRange);
		$name		= $this->getPerson()->getFullName();

		$quickInfo->addHTML('name', $this->getPersonDetailLink($name));
		$quickInfo->addInfo('date',		TodoyuTime::format($this->getPerson()->getBirthday(), 'date'));
		$quickInfo->addInfo('birthday',	$age . ' ' . Todoyu::Label('calendar.ext.yearsold'));
	}

	

	/**
	 * Get person link
	 *
	 * @param	String		$name
	 * @return	String
	 */
	protected function getPersonDetailLink($name) {
		$params	= array(
			'controller'	=> 'person',
			'action'		=> 'detail',
			'person'		=> $this->getPerson()->getID()
		);

		return TodoyuString::wrapTodoyuLink($name, 'contact', $params);
	}



	/**
	 * Get class names
	 *
	 * @return	String[]
	 */
	public function getClassNames() {
		return array();
	}

}

?>