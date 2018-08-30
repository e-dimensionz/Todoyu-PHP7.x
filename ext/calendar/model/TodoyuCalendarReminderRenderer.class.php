
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
 * Reminder Renderer
 *
 * @package			Todoyu
 * @subpackage		Calendar
 */
class TodoyuCalendarReminderRenderer {

	/**
	 * Render reminders of given event for it's details view
	 *
	 * @param	Integer		$idEvent
	 * @return	String
	 */
	public static function renderEventDetailsReminders($idEvent) {
		$idEvent	= intval($idEvent);

		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);
		$eventData	= $event->getTemplateData(true, false, true);
		$eventData	= TodoyuCalendarEventRenderer::getEventRenderData('list', $eventData);

		$eventData['person_create']	= $event->getPersonCreate()->getTemplateData();
//		$eventData['persons']		= TodoyuCalendarEventStaticManager::getAssignedPersonsOfEvent($idEvent, true, true);
		$eventData['persons']		= $eventData['assignedPersons'];

		$tmpl	= 'ext/calendar/view/event-view-details-reminders.tmpl';
		$data	= array(
			'event'		=> $eventData,
			'personID'	=> Todoyu::personid()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render content of event reminder popup
	 *
	 * @param	Integer		$idEvent
	 * @return	String
	 */
	public static function renderEventReminderPopup($idEvent) {
		$idEvent= intval($idEvent);
		$event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);

			// Construct form object for inline form
		$xmlPath		= 'ext/calendar/config/form/reminder-delay.xml';
		$reminderForm	= TodoyuFormManager::getForm($xmlPath, $idEvent);
		$reminderForm->setFormData(array(
			'id_event'	=> $idEvent
		));

			// Reminder label
		$startDateFormat = date('ymd', $event->getDateStart()) === date('ymd') ? 'time' : 'D2MshortTime';

		$tmpl	= 'ext/calendar/view/popup/reminder.tmpl';
		$data	= array(
			'event'				=> $event->getTemplateData(true, true, true),
			'datetime'			=> TodoyuTime::format($event->getDateStart(), $startDateFormat),
			'reminderFormHtml'	=> $reminderForm->render()
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>