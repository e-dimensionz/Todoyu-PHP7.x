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
 * Event Renderer
 *
 * @package			Todoyu
 * @subpackage		Calendar
*/
class TodoyuCalendarEventRenderer {

	/**
	 * Prepare event rendering data array
	 *
	 * @param	Integer		$mode			CALENDAR_MODE_DAY / ..WEEK / ..MONTH
	 * @param	Array		$data			event parameters
	 * @return	Array
	 * @deprecated
	 * @todo	Use event model for this
	 */
	public static function getEventRenderData($mode = CALENDAR_MODE_MONTH, array $data) {
		$idEvent			= intval($data['id']);
		$assignedPersons	= TodoyuCalendarEventStaticManager::getAssignedPersonsOfEvent($idEvent, true, true);

		$data['calendarMode']	= TodoyuCalendarManager::getModeName($mode);
		$data['titleCropLength']= $mode != CALENDAR_MODE_WEEK || TodoyuCalendarPreferences::isWeekendDisplayed() ? 16 : 24;
		$data['assignedPersons']= $assignedPersons;
		$data['timeStart']		= TodoyuCalendarEventStaticManager::getEvent($idEvent)->getStartTime();
		$data['color']			= TodoyuCalendarEventStaticManager::getEventColorData($idEvent);
		$data['eventtypeKey']	= TodoyuCalendarEventTypeManager::getTypeKey($data['eventtype']);

		$assignedPersonIDs	= array_keys($assignedPersons);

			// Hide visible data if event is private and current user not assigned
		$isPrivate	= intval($data['is_private']) === 1;
		if( $isPrivate && ! in_array(Todoyu::personid(), $assignedPersonIDs) ) {
			$data	= self::hidePrivateData($data);
			$data['class'] .= ' noAccess';
		} else {
			if( ! TodoyuCalendarEventRights::isEditAllowed($idEvent) ) {
				$data['class'] .= ' noEdit';
			}
		}

		return $data;
	}




	/**
	 * Render event details view for display inside expanded event in list mode
	 *
	 * @param	Integer		$idEvent
	 * @return	String
	 */
	public static function renderEventDetailsInList($idEvent) {
		$idEvent	= intval($idEvent);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);
		$eventData	= $event->getTemplateData(true, false, true);
		$eventData	= self::getEventRenderData('list', $eventData);

		$eventData['person_create']	= $event->getPersonCreate()->getTemplateData();
		$eventData['persons']		= TodoyuCalendarEventStaticManager::getAssignedPersonsOfEvent($idEvent, true, true);

		$tmpl	= 'ext/calendar/view/event-listmode.tmpl';
		$data	= array(
			'event'	=> $eventData,
		);

		return Todoyu::render($tmpl, $data);
	}




	/**
	 * Render create event form popup
	 *
	 * @param	Integer		$timestamp
	 * @param	Boolean		$isAllDayEvent
	 * @return	String		Form
	 */
	public static function renderCreateQuickEvent($timestamp = 0, $isAllDayEvent = false) {
		$timestamp	= intval($timestamp);
		$timestamp	= TodoyuTime::getRoundedTime($timestamp, 15);

			// Get form object
		$form	= TodoyuCalendarEventStaticManager::getQuickCreateForm();

			// Set event start and ending timestamps
		if( $isAllDayEvent ) {
			$dayRange	= TodoyuTime::getDayRange($timestamp);

			$timeStart	= $dayRange['start'];
			$timeEnd	= $dayRange['end'];
		} else {
			$timeStart	= $timestamp;
			$timeEnd	= $timeStart + TodoyuTime::SECONDS_MIN * 30;
		}

			// Set form data
		$formData	= array(
			'date_start'	=> $timeStart,
			'date_end'		=> $timeEnd,
			'is_dayevent'	=> $isAllDayEvent,
			'persons'		=> array(TodoyuAuth::getPerson()->getTemplateData())
		);

		$form->setFormData($formData);
		$form->setUseRecordID(false);

		return $form->render();
	}



	/**
	 * Hide private data out from event attributes
	 *
	 * @param	Array	$data
	 * @return	Array
	 */
	private static function hidePrivateData(array $data) {
		$data['title']			= '<' . Todoyu::Label('calendar.event.privateEvent.info') . '>';
		$data['description']	= '';

		return $data;
	}



	/**
	 * Render event tabs, including detail viewing tab
	 *
	 * @param	Integer	$idEvent
	 * @return	String
	 */
	public static function renderEventViewTabs($idEvent) {
		$idEvent	= intval($idEvent);
		$activeTab	= 'detail';
		$name		= 'calendar';
		$jsHandler	= 'Todoyu.Ext.calendar.Tabs.onSelect.bind(Todoyu.Ext.calendar.Tabs)';
		$tabs		= TodoyuCalendarManager::getCalendarTabsConfig();

		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);

		$detailTab	= array(
			'id'		=> 'detail',
			'label'		=> 'Detail: ' . $event->getTitle()
		);

		array_unshift($tabs, $detailTab);

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}



	/**
	 * Render event view
	 *
	 * @param	Integer	$idEvent
	 * @return	String
	 */
	public static function renderEventView($idEvent) {
		$idEvent	= intval($idEvent);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);

		$tmpl	= 'ext/calendar/view/event-view.tmpl';
		$data	= array(
			'event'	=> $event->getTemplateData(true, true, true),
			'tabs'	=> self::renderEventViewTabs($idEvent)
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * @param	Array	$personIDs
	 * @return	String
	 */
	public static function renderAutoMailComment(array $personIDs) {
		$tmpl	= 'ext/calendar/view/infocomment-autonotification.tmpl';
		$data	= array(
			'personIDs'	=> $personIDs
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render URL pointing to event in todoyu calendar
	 *
	 * @param	Integer		$idEvent
	 * @param	Boolean		$absolute
	 * @param	Boolean		$encode		Encode HTML entities (use & instead of &amp; as argument separator)
	 * @return	String
	 */
	public static function renderEventLinkURL($idEvent, $absolute = true, $encode = false) {
		$idEvent	= intval($idEvent);

		$urlParams	= array(
			'ext'	=> 'calendar',
			'event'	=> $idEvent
		);

		return TodoyuString::buildUrl($urlParams, '', $absolute, !$encode);
	}

}

?>