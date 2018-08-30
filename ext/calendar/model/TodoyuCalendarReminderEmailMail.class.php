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
 * Email for reminders
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarReminderEmailMail extends TodoyuMail {

	/**
	 * Reminder which is sent per email
	 *
	 * @var	TodoyuCalendarReminderEmail
	 */
	private $reminder;



	/**
	 * Initialize with reminder
	 *
	 * @param	Integer		$idReminder
	 * @param	Array		$config
	 */
	public function __construct($idReminder, array $config = array()) {
		parent::__construct($config);

		$this->reminder	= TodoyuCalendarReminderEmailManager::getReminder($idReminder);

		$this->init();
	}



	/**
	 * Init email settings
	 */
	private function init() {
		$event	= $this->reminder->getEvent();

			// Render and set email subject
		$prefix	= Todoyu::Label('calendar.reminder.email.subject');
		$subject= $prefix . ': ' . $event->getTitle() . ' - ' . $event->getRangeLabel(true);

		$this->setSubject($subject);

			// Add receivers, headline
		$mailReceiver	= TodoyuMailReceiverManager::getMailReceiver($this->getPerson()->getID());
		$this->addReceiver($mailReceiver);
		$this->setHeadline('calendar.reminder.email.title');

			// Add CSS
		$this->addCssStyles('td.label{font-weight:bold;width:120px;}');
			// Set person timezone + locale
		Todoyu::setEnvironmentForPerson($this->getPerson()->getID());

			// Set content
		$this->setHtmlContent($this->getContent(true));
		$this->setTextContent($this->getContent(false));

		Todoyu::resetEnvironment();
	}



	/**
	 * Get event of the reminder
	 *
	 * @return	TodoyuCalendarEventStatic
	 */
	private function getEvent() {
		return $this->reminder->getEvent();
	}



	/**
	 * Get person of the reminder
	 *
	 * @return	TodoyuContactPerson
	 */
	private function getPerson() {
		return $this->reminder->getPerson();
	}



	/**
	 * Render content for HTML/plaintext mail
	 *
	 * @param	Boolean		$asHTML
	 * @return	String
	 */
	private function getContent($asHTML = true) {
		$tmpl	= $this->getTemplate($asHTML);
		$data	= TodoyuCalendarEventMailManager::getMailData($this->getEvent()->getID(), $this->getPerson()->getID(), true);

		$data['hideEmails']	= false;
		$data['colors']		= TodoyuCalendarEventStaticManager::getEventTypeColors();

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get filename of event reminder email template to current mode (text/HTML)
	 *
	 * @param	Boolean		$asHTML
	 * @return	String
	 */
	private function getTemplate($asHTML = true) {
		$type	= $asHTML ? 'html' : 'text';

		 return 'ext/calendar/view/emails/event-reminder-' . $type . '.tmpl';
	}

}

?>