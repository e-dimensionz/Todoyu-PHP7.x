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
 * Event rights functions
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventRights {

	/**
	 * Deny access
	 * Shortcut for calendar
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('calendar', $right);
	}



	/**
	 * Check whether a person is allowed to see an event
	 *
	 * @param	Integer		$idEvent
	 * @return	Boolean
	 */
	public static function isSeeAllowed($idEvent) {
		$idEvent= intval($idEvent);
		$event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);

			// Private event are only available for assigned users
		if( $event->isPrivate() && !$event->isCurrentPersonAssigned() ) {
			return false;
		}

			// Admin sees all events.
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

			// Person is assigned to event
		if( $event->isCurrentPersonAssigned() ) {
			return true;
		}

			// Person can see all events and event is not private,
		if( Todoyu::allowed('calendar', 'event:seeAll') && ! $event->isPrivate() ) {
			return true;
		}

			// Create can see task
		if( $event->isCurrentPersonCreator() ) {
			return true;
		}

		return false;
	}



	/**
	 * Check whether person can see details of an event
	 *
	 * @param	Integer		$idEvent
	 * @return	Boolean
	 */
	public static function isSeeDetailsAllowed($idEvent) {
		$idEvent= intval($idEvent);

		if( ! self::isSeeAllowed($idEvent) ) {
			return false;
		}

		$event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);

		if( $event->isPrivate() ) {
			return $event->isCurrentPersonAssigned();
		} else {
			return true;
		}
	}



	/**
	 * Check whether person is allowed to add new events
	 *
	 * @return	Boolean
	 */
	public static function isAddAllowed() {
		return Todoyu::allowed('calendar', 'event:add');
	}



	/**
	 * Check whether person can edit an event
	 *
	 *
	 * @param	Integer		$idEvent
	 * @return	Boolean
	 */
	public static function isEditAllowed($idEvent) {
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		$idEvent	= intval($idEvent);

		if( !Todoyu::allowed('calendar', 'event:editInPast') && TodoyuCalendarEventStaticManager::getEvent($idEvent)->isInPast() ) {
			return false;
		}

		return self::isEditOrDeleteAllowed('edit', $idEvent);
	}



	/**
	 * Check whether person is allowed to delete an event
	 *
	 * @param	Integer		$idEvent
	 * @return	Boolean
	 */
	public static function isDeleteAllowed($idEvent) {
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		$idEvent	= intval($idEvent);

		if( !Todoyu::allowed('calendar', 'event:deleteInPast') && TodoyuCalendarEventStaticManager::getEvent($idEvent)->isInPast() ) {
			return false;
		}

		return self::isEditOrDeleteAllowed('delete', $idEvent);
	}



	/**
	 * Check whether person is allowed to do the requested action (delete / edit) for an event
	 * Check whether person has edit rights and is assigned if necessary
	 *
	 * @param	String		$action
	 * @param	Integer		$idEvent
	 * @return	Boolean
	 */
	private static function isEditOrDeleteAllowed($action, $idEvent) {
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		$idEvent	= intval($idEvent);
		$event		= TodoyuCalendarEventStaticManager::getEvent($idEvent);

			// Creator can edit event
		if( $event->isCurrentPersonCreator() ) {
			return true;
		}

			// Person is assigned to event and has right to edit/delete events it's assigned to
		if( $event->isCurrentPersonAssigned() ) {
			if( Todoyu::allowed('calendar', 'event:' . $action . 'Assigned') ) {
				return true;
			}
		}

			// Person can edit/delete all events and event is not private,
		if( Todoyu::allowed('calendar', 'event:' . $action . 'All') && !$event->isPrivate() ) {
			return true;
		}

		return false;
	}



	/**
	 * Check whether person is allowed to see birthdays listing in portal (admin or internal)
	 *
	 * @return	Boolean
	 */
	public static function isAllowedSeeBirthdaysInPortal() {
		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		$person	= TodoyuContactPersonManager::getPerson(Todoyu::personid());

		return $person->isInternal();
	}



	/**
	 * Restrict access to persons who are allowed to see the event
	 *
	 * @param	Integer		$idEvent
	 */
	public static function restrictSee($idEvent) {
		if( ! self::isSeeAllowed($idEvent) ) {
			self::deny('event:seeAll');
		}
	}



	/**
	 * Restrict access to persons who are allowed to add events
	 */
	public static function restrictAdd() {
		if( ! self::isAddAllowed() ) {
			self::deny('event:add');
		}
	}



	/**
	 * Restrict access to persons who are allowed to edit events
	 *
	 * @param	Integer		$idEvent
	 */
	public static function restrictEdit($idEvent) {
		if( ! self::isEditAllowed($idEvent) ) {
			self::deny('event:editAssigned');
		}
	}



	/**
	 * Restrict access to persons who are allowed to delete events
	 *
	 * @param	Integer		$idEvent
	 */
	public static function restrictDelete($idEvent) {
		if( ! self::isDeleteAllowed($idEvent) ) {
			self::deny('event:deleteAssigned');
}
	}

}
?>