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
 * Event action controller
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventActionController extends TodoyuActionController {

	/**
	 * Initialize (restrict rights)
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('calendar', 'general:use');
		Todoyu::restrictInternal();
	}



	/**
	 * Edit an event. If event ID is 0, a empty form is rendered to create a new event
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idEvent	= intval($params['event']);
		$options	= TodoyuArray::assureFromJSON($params['options']);
		$date		= strtotime($params['date']);

			// Check rights
		if( $idEvent === 0 ) {
			TodoyuCalendarEventRights::restrictAdd();
			$tabLabel	= Todoyu::Label('calendar.event.new');
		} else {
			TodoyuCalendarEventRights::restrictEdit($idEvent);
			$tabLabel	= TodoyuCalendarEventViewHelper::getEventEditTabLabel($idEvent);
		}
		TodoyuHeader::sendTodoyuHeader('tabLabel', $tabLabel);

		return TodoyuCalendarEventEditRenderer::renderEventForm($idEvent, $date, $options);
	}



	/**
	 * Save event action: validate data and save or return failure feedback
	 *
	 * @param	Array	$params
	 * @return	Void|String			Failure returns re-rendered form with error messages
	 */
	public function saveAction(array $params) {
		$formData		= $params['event'];
		$idEvent		= intval($formData['id']);
		$isNewEvent		= $idEvent === 0;
		$receiverTuples	= TodoyuArray::assure($formData['emailreceivers']);

			// Check rights (new event creation / updating existing event)
		if( $idEvent === 0 ) {
			TodoyuCalendarEventRights::restrictAdd();
		} else {
			TodoyuCalendarEventRights::restrictEdit($idEvent);
		}

		$form	= TodoyuCalendarEventStaticManager::getEventForm($idEvent, $formData);



			// Invalid data detected - re-display the form
		if( ! $form->isValid() ) {
			TodoyuHeader::sendTodoyuErrorHeader();
			TodoyuHeader::sendTodoyuHeader('event', $idEvent);

			$form->setUseRecordID(false);

			return $form->render();
		}
			// Check for warnings and send resp. headers
		$warningHeaders	= TodoyuCalendarEventStaticManager::getOverbookingWarningHeaders($idEvent, $params);
		foreach($warningHeaders as $headerName => $headerValue) {
			TodoyuHeader::sendTodoyuHeader($headerName, $headerValue);
		}

			// Save or update event (and send email if mail-option activated)
		if( sizeof($warningHeaders) === 0 ) {
			$storageData= $form->getStorageData();
			$idEvent	= TodoyuCalendarEventStaticManager::saveEvent($storageData);

				// Send event email to selected receivers
			if( sizeof($receiverTuples) > 0 ) {
				if( TodoyuCalendarEventMailManager::sendEvent($idEvent, $receiverTuples, array('new' => $isNewEvent)) ) {
					TodoyuHeader::sendTodoyuHeader('sentEmail', true);
				}
			}

			TodoyuHeader::sendTodoyuHeader('time', intval($storageData['date_start']));
			TodoyuHeader::sendTodoyuHeader('event', $idEvent);
		}

		return '';
	}



	/**
	 * Save changed starting date when event has been dragged to a new position
	 *
	 * @param	Array		$params
	 * @return	Void|String
	 */
	public function dragDropAction(array $params) {
		$idEvent	= intval($params['event']);
		$timeStart	= strtotime($params['date']);
		$tab		= trim($params['tab']);
		$isConfirmed= intval($params['confirmed']) === 1;

			// Check right
		TodoyuCalendarEventRights::restrictEdit($idEvent);

		$overbookings	= TodoyuCalendarEventStaticManager::moveEvent($idEvent, $timeStart, $tab, $isConfirmed);
		if( is_array($overbookings) && !$isConfirmed ) {
			if( ! TodoyuCalendarManager::isOverbookingAllowed() ) {
					// Overbooking forbidden - reset event to original time, show notification
				TodoyuHeader::sendTodoyuErrorHeader();
				return implode('<br />', $overbookings);
			} else {
					// Overbooking allowed - open popup with warning and confirmation dialog
				$overbookedWarning	= TodoyuCalendarEventStaticManager::getOverbookingWarningAfterDrop($idEvent, $timeStart);
				TodoyuHeader::sendTodoyuHeader('overbookingwarning', $overbookedWarning);
			}
		}
	}



	/**
	 * Delete event
	 *
	 * @param	Array	$params
	 */
	public function deleteAction(array $params) {
		$idEvent= intval($params['event']);

			// Check right
		TodoyuCalendarEventRights::restrictDelete($idEvent);

		if( ! TodoyuCalendarEventStaticManager::deleteEvent($idEvent) ) {
			TodoyuHeader::sendTodoyuErrorHeader();
		}
	}



	/**
	 * Get given event's rendered detail view for list mode
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		$idEvent= intval($params['event']);

		TodoyuCalendarEventRights::restrictSee($idEvent);

		return TodoyuCalendarEventRenderer::renderEventDetailsInList($idEvent);
	}



	/**
	 * Acknowledge an (not seen) event
	 *
	 * @param	Array	$params
	 */
	public function acknowledgeAction(array $params) {
		$idEvent	= intval($params['event']);

		TodoyuCalendarEventRights::restrictSee($idEvent);

		TodoyuCalendarEventAssignmentManager::acknowledgeEvent($idEvent);
	}



	/**
	 * Show event details
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function showAction(array $params) {
		$idEvent	= intval($params['event']);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);

		TodoyuCalendarEventRights::restrictSee($idEvent);

			// Send tab label
		$tabLabel	= TodoyuCalendarEventViewHelper::getEventViewTabLabel($idEvent);
		TodoyuHeader::sendTodoyuHeader('tabLabel', $tabLabel, true);

		return TodoyuCalendarEventRenderer::renderEventView($idEvent);
	}



	/**
	 * Render event form for use as sub form of another form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		Todoyu::restrictIfNone('calendar', 'event:editAll,event:editAssigned');

		$index		= intval($params['index']);
		$fieldName	= $params['field'];
		$formName	= $params['form'];
		$idRecord	= intval($params['record']);

		$xmlPath	= 'ext/calendar/config/form/event.xml';
		$form		= TodoyuFormManager::getForm($xmlPath);
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $idRecord);

		return TodoyuFormManager::renderSubFormRecord($xmlPath, $fieldName, $formName, $index, $idRecord, $formData);
	}



	/**
	 * Update event types options
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateeventtpyesAction(array $params) {
		$isDayEvent = intval($params['isDayEvent']);

		$tmpl	= 'core/view/select-grouped-options.tmpl';
		$data	= array(
			'options'	=> TodoyuCalendarEventViewHelper::getEventTypeOptions($isDayEvent),
			'value'		=> array(intval($params['value']))
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * @param	Array		$params
	 */
	public function validateUserHolidayAction(array $params){
		$personIDs = TodoyuArray::intval(json_decode($params['personIDs']));
		$start		= TodoyuTime::parseDateTime($params['dateStart']);
		$end		= TodoyuTime::parseDateTime($params['dateEnd']);


		$holidaysInRange = TodoyuCalendarHolidayManager::getHolidaysInRange(new TodoyuDayRange($start, $end), TodoyuCalendarHolidaySetManager::getPersonHolidaySets($personIDs));

		if( sizeof($holidaysInRange) > 0 ) {
			TodoyuHeader::sendTodoyuHeader('holidays', true);

			return TodoyuCalendarEventEditRenderer::renderHolidaysInRangeList($holidaysInRange);
		}
	}
}

?>