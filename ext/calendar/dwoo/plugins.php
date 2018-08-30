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
 * Calendar specific Dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 */

/**
 * Check right of current person to see given event
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idEvent
 * @return	Boolean
 */
function Dwoo_Plugin_isAllowedSeeEvent(Dwoo $dwoo, $idEvent) {
	return TodoyuCalendarEventRights::isSeeAllowed($idEvent);
}



/**
 * Check right of current person to add events
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @return	Boolean
 */
function Dwoo_Plugin_isAllowedAddEvent(Dwoo $dwoo) {
	return TodoyuCalendarEventRights::isAddAllowed();
}



/**
 * Check right of current person to edit given event
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idEvent
 * @return	Boolean
 */
function Dwoo_Plugin_isAllowedEditEvent(Dwoo $dwoo, $idEvent) {
	return TodoyuCalendarEventRights::isEditAllowed($idEvent);
}



/**
 * Get full label of event
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler $compiler
 * @param	Integer		$idEvent
 * @return	String
 */
function Dwoo_Plugin_EventFullLabel_compile(Dwoo_Compiler $compiler, $idEvent) {
	return 'TodoyuCalendarEventStaticManager::getEventFullLabel(' . $idEvent . ')';
}



/**
 * Get label of event type
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idEventIndex
 * @return	String
 */
function Dwoo_Plugin_EventTypeLabel_compile(Dwoo_Compiler $compiler, $idEventIndex) {
	return 'TodoyuCalendarEventTypeManager::getEventTypeLabel(' . $idEventIndex . ')';
}



/**
 * Get key of event type
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param		Dwoo_Compiler	$compiler
 * @param		Integer			$idEventIndex
 * @return		String
 */
function Dwoo_Plugin_EventTypeKey_compile(Dwoo_Compiler $compiler, $idEventIndex) {
	return 'TodoyuCalendarEventTypeManager::getEventTypeKey(' . $idEventIndex . ')';
}



/**
 * Get short name label of day name, e.g: 'Mon'
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param		Dwoo		$dwoo
 * @param		Integer		$timestamp
 * @return		String
 */
function Dwoo_Plugin_weekdayName(Dwoo $dwoo, $timestamp) {
	$timestamp	= intval($timestamp);

	return Todoyu::Label('core.date.weekday.' . strtolower(date('l', $timestamp)));
}


/**
 * Get short name label of day name, e.g: 'Mon'
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$timestamp
 * @return	String
 */
function Dwoo_Plugin_weekdayNameShort(Dwoo $dwoo, $timestamp) {
	$timestamp	= intval($timestamp);

	return Todoyu::Label('core.date.weekday.' . strtolower(date('D', $timestamp)));
}



/**
 * Check right of current person to schedule popup reminder of given event
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idEvent
 * @return	Boolean
 */
function Dwoo_Plugin_isAllowedEventReminderPopup(Dwoo $dwoo, $idEvent) {
	return TodoyuCalendarEventReminderRights::isPopupSchedulingAllowed($idEvent);
}



/**
 * Check right of current person to schedule email reminder of given event
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idEvent
 * @return	Boolean
 */
function Dwoo_Plugin_isAllowedEventReminderEmail(Dwoo $dwoo, $idEvent) {
	return TodoyuCalendarEventReminderRights::isEmailSchedulingAllowed($idEvent);
}

?>