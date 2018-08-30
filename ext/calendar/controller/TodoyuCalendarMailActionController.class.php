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
class TodoyuCalendarMailActionController extends TodoyuActionController {

	/**
	 * Initialize (restrict rights)
	 */
	public function init() {
		Todoyu::restrict('calendar', 'general:use');
		Todoyu::restrictInternal();
	}


	/**
	 * Show mail popup
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		$idEvent	= intval($params['event']);
		$operation	= trim($params['operation']);
		$options	= TodoyuArray::assureFromJSON($params['options']);
		$asSeries	= (boolean)$options['series'];
		$popup		= new TodoyuCalendarEventMailPopup($idEvent, $operation, $asSeries);

		if( $popup->isRequired() ) {
			TodoyuHeader::sendTodoyuHeader('show', true);

			return $popup->render();
		} else {
			return '';
		}
	}



	/**
	 * Deactivate showing of mailing popup after drag and drop change of events
	 *
	 * @param	Array	$params
	 */
	public function disablePopupAction(array $params) {
		$prefName				= 'is_mailpopupdeactivated';
		$isRequestDeactivated	= '1';

		TodoyuCalendarPreferences::savePref($prefName, $isRequestDeactivated, 0, true);
	}



	/**
	 * Send event mail to selected persons
	 *
	 * @param	Array	$params
	 */
	public function sendAction(array $params) {
		$idEvent	= intval($params['event']);
		$personIDs	= TodoyuArray::intExplode(',', $params['persons'], true, true);
		$options	= TodoyuArray::assureFromJSON($params['options']);

		if( sizeof($personIDs) > 0 ) {
			$sent	= TodoyuCalendarEventMailManager::sendEmails($idEvent, $personIDs, $options);
			if( $sent ) {
				TodoyuHeader::sendTodoyuHeader('sentEmail', 1);
			}
		}
	}



	/**
	 * Update auto-notification comment: list of auto-notified participants
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function autoMailCommentAction(array $params) {
		$personIDs			= TodoyuArray::intExplode(',', $params['persons']);
		$autoMailPersonsIDs	= TodoyuCalendarEventMailManager::extractAutoNotifiedPersonIDs($personIDs);

		TodoyuHeader::sendTodoyuHeader('autoMailPersons', $autoMailPersonsIDs);

		return TodoyuCalendarEventRenderer::renderAutoMailComment($autoMailPersonsIDs);
	}



}

?>