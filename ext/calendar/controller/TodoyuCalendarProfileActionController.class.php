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
 * Calendar profile action controller
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarProfileActionController extends TodoyuActionController {

	/**
	 * Init controller: check permission
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('calendar', 'general:use');
		Todoyu::restrictInternal();
	}



	/**
	 * Load tab content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function tabAction(array $params) {
		return TodoyuCalendarProfileRenderer::renderContent($params);
	}



	/**
	 * Save calendar general preference from main tab of profile
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function saveMainAction(array $params) {
		Todoyu::restrict('calendar', 'mailing:sendAsEmail');

		$data	= TodoyuArray::assure($params['general']);

			// Construct form object for validation
		$xmlPath	= 'ext/calendar/config/form/profile-main.xml';
		$form		= TodoyuFormManager::getForm($xmlPath);
		$form->setFormData($data);

		if( $form->isValid() ) {
				// Save
			TodoyuCalendarPreferences::savePref('is_mailpopupdeactivated', intval($data['is_mailpopupdeactivated']), 0, true);
			TodoyuCalendarPreferences::savePref('range_start', intval($data['range_start']), 0, true);
			TodoyuCalendarPreferences::savePref('range_end', intval($data['range_end']), 0, true);
		} else {
				// Re-display with error message(s)
			TodoyuHeader::sendTodoyuErrorHeader();
			return $form->render();
		}
	}



	/**
	 * Save calendar preference from reminders tab of profile
	 *
	 * @param	Array		$params
	 */
	public function saveRemindersAction(array $params) {
			// Email reminder prefs
		$prefName	= 'reminderemail_advancetime';
		$timeEmail	= intval($params['reminders'][$prefName]);
			// Popup reminder prefs
		$prefName	= 'reminderpopup_advancetime';
		$timePopup	= intval($params['reminders'][$prefName]);

		TodoyuCalendarPreferences::saveReminderEmailTime($timeEmail);
		TodoyuCalendarPreferences::saveReminderPopupTime($timePopup);
	}

}

?>