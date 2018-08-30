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
 * Event modification mail popup
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventMailPopup {

	/**
	 *
	 * @var	TodoyuCalendarEventStatic
	 */
	protected $event;

	/**
	 *
	 * @var	String
	 */
	protected $operation;

	/**
	 *
	 * @var	Boolean
	 */
	protected $asSeries;



	/**
	 * Initialize
	 *
	 * @param	Integer		$idEvent
	 * @param	String		$operation
	 * @param	Boolean		$asSeries
	 */
	public function __construct($idEvent, $operation, $asSeries = false) {
		$this->event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);
		$this->operation= trim($operation);
		$this->asSeries	= $asSeries;
	}



	/**
	 * Get event
	 *
	 * @return	TodoyuCalendarEventStatic
	 */
	public function getEvent() {
		return $this->event;
	}



	/**
	 * Get event ID
	 *
	 * @return	Integer
	 */
	public function getEventID() {
		return $this->getEvent()->getID();
	}



	/**
	 * Get operation key
	 *
	 * @return	String
	 */
	public function getOperation() {
		return $this->operation;
	}



	/**
	 * Check whether operation is delete
	 *
	 * @return	Boolean
	 */
	public function isDeleteOperation() {
		return $this->getOperation() === 'delete';
	}



	/**
	 * Check whether operation is update
	 *
	 * @return	Boolean
	 */
	public function isUpdateOperation() {
		return $this->getOperation() === 'update';
	}



	/**
	 * Check whether popup is for a series event
	 *
	 * @return	Boolean
	 */
	public function isAsSeries() {
		return $this->asSeries;
	}



	/**
	 * Check whether its necessary to render the popup
	 *
	 * @return	Boolean
	 */
	public function isRequired() {
		if( $this->getEvent()->areOtherPersonsAssigned() ) {
			if( $this->isDeleteOperation() ) {
				return true;
			} elseif( TodoyuCalendarPreferences::isMailPopupDisabled() ) {
				return false;
			} else {
				return $this->getEvent()->hasAnyAssignedPersonAnEmailAddress();
			}
		}

		return false;
	}



	/**
	 * Render popup
	 *
	 * @return	String
	 */
	public function render() {
		$tmpl	= 'ext/calendar/view/popup/email.tmpl';
		$data	= array(
			'subject'		=> $this->getSubject(),
			'event'			=> $this->getEvent()->getTemplateData(true, true, true),
			'mailingForm'	=> TodoyuRightsManager::isAllowed('calendar', 'mailing:sendAsEmail') ? $this->getForm()->render() : ''
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get popup subject label
	 *
	 * @return	String
	 */
	protected function getSubject() {
		return Todoyu::Label('calendar.event.mail.popup.subject.' . $this->getOperation());
	}



	/**
	 * Get the prepared mail popup form
	 *
	 * @return	TodoyuForm
	 */
	protected function getForm() {
		$xmlPath	= 'ext/calendar/config/form/update-mailinfo.xml';

		$receivers	= TodoyuCalendarEventMailManager::getOtherAssignedUserIDs($this->getEventID());
		$formData	= array(
			'id_event'		=> $this->getEventID(),
			'emailreceivers'=> $receivers,
		);
		$formParams	= array(
			'data'		=> $formData,
			'operation'	=> $this->getOperation()
		);
		$form		= TodoyuFormManager::getForm($xmlPath, $this->getEventID(), $formParams);

			// Set mail form data
		$form->setFormData($formData);

			// Remove "don't ask again" button in form of deleted events
		if( $this->isDeleteOperation() ) {
			$form->removeField('dontaskagain', true);
		}

		return $form;
	}

}

?>