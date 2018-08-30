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
class TodoyuCalendarPortalActionController extends TodoyuActionController {

	/**
	 * Initialize (restrict rights)
	 */
	public function init() {
		Todoyu::restrict('calendar', 'general:use');
		Todoyu::restrictInternal();
	}



	/**
	 * 'detail' action method
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		$idEvent	= intval($params['event']);

		TodoyuCalendarEventRights::restrictSee($idEvent);

		TodoyuCalendarPreferences::savePortalEventExpandedStatus($idEvent);
		TodoyuCalendarEventAssignmentManager::acknowledgeEvent($idEvent);

		return $this->forward('calendar', 'event', 'detail', $params);
	}



	/**
	 * Acknowledge an (not seen) event
	 *
	 * @param	Array	$params
	 */
	public function acknowledgeAction(array $params) {
		$idEvent	= intval($params['event']);
		$idPerson	= intval($params['person']);

		TodoyuCalendarEventRights::restrictSee($idEvent);

		TodoyuCalendarEventAssignmentManager::acknowledgeEvent($idEvent, $idPerson);
	}

}

?>