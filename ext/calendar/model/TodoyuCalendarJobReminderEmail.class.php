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
 * Event email reminder cronjob
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarJobReminderEmail extends TodoyuSchedulerJob {

	/**
	 * Executed from TodoyuScheduler: send scheduled event reminder emails
	 */
	public function execute() {
			// Get unsent emails with scheduled timestamp <= NOW
		$reminderIDs	= $this->getUnsentDueReminderIDs();
		
			// Send emails
		foreach($reminderIDs as $idReminder) {
			$reminder	= TodoyuCalendarReminderEmailManager::getReminder($idReminder);

			$reminder->sendAsEmail();

			TodoyuHookManager::callHook('calendar', 'email.reminder', array($idReminder));
		}
	}



	/**
	 * Get records of unsent reminders (from ext_calendar_mm_event_person) which are due
	 *
	 * @return	Array
	 */
	private function getUnsentDueReminderIDs() {
		$field		= 'id';
		$table		= 'ext_calendar_mm_event_person';
		$where		= '		is_remindemailsent	= 0'
					. ' AND	date_remindemail	> 0'
					. ' AND	date_remindemail	< ' . $this->getNextExecutionTime();

		return Todoyu::db()->getColumn($field, $table, $where);
	}

}

?>