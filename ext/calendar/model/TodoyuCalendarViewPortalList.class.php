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
 * Portal list view of calendar (static, holiday and birthday events)
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarViewPortalList extends TodoyuCalendarView {

	/**
	 * Portal config
	 *
	 * @var	Array
	 */
	protected $config;



	/**
	 * Initialize
	 * Without range and filters, because every sub part has its own config
	 */
	public function __construct() {
		$this->config	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['calendar']['appointmentTabConfig']);
	}



	/**
	 * Get title
	 * Not used for this view
	 *
	 */
	public function getTitle() {
		// no op
	}



	/**
	 * Render view
	 *
	 * @return	String
	 */
	public function render() {
		$tmpl	= 'ext/calendar/view/views/portal-list-events.tmpl';
		$data	= array(
			'staticEvents'	=> $this->getStaticEventsData(),
			'holidayEvents'	=> $this->getHolidayEventsData(),
			'birthdayEvents'=> $this->getBirthdayEventsData()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get amount of static events for view
	 *
	 * @return	Integer
	 */
	public function getStaticEventsCount() {
		$filters= array(
			'persons'	=> array(
				Todoyu::personid()
			)
		);
		$weeks	= $this->getWeeks('static');
		$range	= $this->getFutureRange($weeks);

		return TodoyuCalendarDataSourceManager::getDataSourceEventCount('static', $range, $filters);
	}



	/**
	 * Get config (or value)
	 *
	 * @param	Boolean		$key
	 * @return	Mixed|Array
	 */
	protected function getConfig($key = false) {
		if( $key ) {
			return $this->config[$key];
		} else {
			return $this->config;
		}
	}



	/**
	 * Get weeks to search in
	 *
	 * @param	String		$type
	 * @return	Integer
	 */
	protected function getWeeks($type) {
		$key	= 'weeks' . ucfirst($type);

		return intval($this->getConfig($key));
	}



	/**
	 * Get template data for static events
	 *
	 * @return	Array[]
	 */
	protected function getStaticEventsData() {
		$weeks	= $this->getWeeks('static');
		$filters= array(
			'persons'	=> array(
				Todoyu::personid()
			)
		);

		return $this->getEventsTemplateData('static', $weeks, $filters);
	}



	/**
	 * Get events template data for a data source
	 *
	 * @param	String		$sourceName
	 * @param	Integer		$weeks
	 * @param	Array		$filters
	 * @return	Array[]
	 */
	protected function getEventsTemplateData($sourceName, $weeks, array $filters) {
		$events			= $this->getSourceEvents($sourceName, $weeks, $filters);
		$templateData	= array();

		foreach($events as $event) {
			$element		= new TodoyuCalendarEventElementPortalList($event, $this);
			$templateData[]	= $element->getTemplateData();
		}

		return $templateData;
	}



	/**
	 * Get holidays template data
	 *
	 * @return	Array[]
	 */
	protected function getHolidayEventsData() {
		$weeks			= $this->getWeeks('holiday');
		$filters		= array(
			'holidaysets'	=> $this->getHolidaySetIDsOfUsersWorkAddresses()
		);

		return $this->getEventsTemplateData('holiday', $weeks, $filters);
	}



	/**
	 * Get holiday set IDs for current users working addresses
	 *
	 * @return	Integer[]
	 */
	protected function getHolidaySetIDsOfUsersWorkAddresses() {
		$personIDs	= array(Todoyu::personid());
		$addressIDs	= TodoyuContactPersonManager::getWorkaddressIDsOfPersons($personIDs);

		return TodoyuCalendarHolidayManager::getHolidaysetIDsOfAddresses($addressIDs);
	}



	/**
	 * Get birthdays tempalte data
	 *
	 * @return	Array[]
	 */
	protected function getBirthdayEventsData() {
		$weeks	= $this->getWeeks('birthday');
		$filters= array(
			'dayevents'	=> true,
			'eventtypes' => array(EVENTTYPE_BIRTHDAY)
		);

		return $this->getEventsTemplateData('birthday', $weeks, $filters);
	}



	/**
	 * Get events of a data source for the next n weeks
	 *
	 * @param	String		$dataSourceName
	 * @param	Integer		$weeks
	 * @param	Array		$filters
	 * @return	TodoyuCalendarEvent[]
	 */
	protected function getSourceEvents($dataSourceName, $weeks, array $filters = array()) {
		$range	= $this->getFutureRange($weeks);

		return TodoyuCalendarDataSourceManager::getDataSourceEvents($dataSourceName, $range, $filters);
	}



	/**
	 * Get range from now to n weeks in the future
	 *
	 * @param	Integer		$weeks
	 * @return	TodoyuDayRange
	 */
	protected function getFutureRange($weeks) {
		$weeks	= intval($weeks);
		$start	= NOW;
		$end	= $start + $weeks * TodoyuTime::SECONDS_WEEK;

		return new TodoyuDayRange($start, $end);
	}

}

?>