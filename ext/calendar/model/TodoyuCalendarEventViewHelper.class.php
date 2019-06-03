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
 * Event view helper
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventViewHelper {

	/**
	 * Get event types (grouped by non-/blocking) in a form-readable format
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getEventTypeOptions($isDayEvent) {
		$groupLabelOverbookable	= Todoyu::Label('calendar.event.eventtype.optgroup.overbookable');
		$groupLabelBlocking		= Todoyu::Label('calendar.event.eventtype.optgroup.blocking');

			// Add ordered optgroups, group for "blocking" only if overbooking protection is activated
		$options	= array();
		if( ! TodoyuCalendarManager::isOverbookingAllowed() ) {
			$options[$groupLabelBlocking]	= array();
		}

		$options[$groupLabelOverbookable]	= array();

			// Add event types to resp. optgroup
		$eventTypes	= TodoyuCalendarEventTypeManager::getEventTypes(true);
		foreach($eventTypes as $eventType) {
			if( TodoyuCalendarEventTypeManager::isOverbookable($eventType['value'], $isDayEvent) ) {
				$options[$groupLabelOverbookable][]	= $eventType;
			} else {
				$options[$groupLabelBlocking][]	= $eventType;
			}
		}

			// Reform and sort options alphabetically
		$reformConfig	= array(
			'index'	=> 'value',
			'label'	=> 'label'
		);
		$options[$groupLabelOverbookable]	= TodoyuArray::sortByLabel(TodoyuArray::reform($options[$groupLabelOverbookable], $reformConfig, false), 'label');

		if( ! TodoyuCalendarManager::isOverbookingAllowed() ) {
			$options[$groupLabelBlocking]		= TodoyuArray::sortByLabel(TodoyuArray::reform($options[$groupLabelBlocking], $reformConfig, false), 'label');
		}

		return $options;
	}



	/**
	 * Get option array of persons which can receive the event email (participant with an email address)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getEmailReceiverOptions(TodoyuFormElement $field) {
		$idEvent	= intval($field->getForm()->getHiddenField('id_event'));
		$options	= array();
		$persons	= TodoyuCalendarEventStaticManager::getEmailReceivers($idEvent, true);

		foreach($persons as $person) {
			$options[]	= array(
				'value'		=> $person['id'],
				'label'		=> TodoyuContactPersonManager::getLabel($person['id'], true, true),
			);
		}

		return $options;
	}



	/**
	 * Get option array of persons which can receive the event email (participant with an email address)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getEmailReceiverGroupedOptions(TodoyuFormElement $field) {
		$options	= array();
		$idEvent	= $field->getForm()->getRecordID();

			// Disable auto notified persons in normal email list
		$autoNotifiedPersonIDs	= TodoyuCalendarEventMailManager::getAutoNotifiedPersonIDs($idEvent);

			// Event attending persons
		$groupLabel	= Todoyu::Label('calendar.event.group.attendees');
		$options[$groupLabel]	= self::getEmailReceiverOptions($field);

			// Get staff persons (employees of internal company) having an email
		$groupLabel	= Todoyu::Label('comment.ext.group.employees');
		$options[$groupLabel]	= TodoyuContactViewHelper::getInternalPersonOptions($field, true, true, true);

			// Deselect + disable options of persons receiving an automatic notification email
		if( !empty($autoNotifiedPersonIDs)  ) {
			foreach($options as $groupLabel => $groupOptions) {
				if( !empty($groupOptions) ) {
					foreach($options[$groupLabel] as $optionKey => $option) {
						if( in_array($option['value'], $autoNotifiedPersonIDs)  ) {
							$options[$groupLabel][$optionKey]['disabled']	= 1;
						}
					}
				}
			}
		}

		return $options;
	}



	/**
	 * Get auto-notification information comment: preset roles' persons
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	String
	 */
	public static function getAutoNotificationComment(TodoyuFormElement $field) {
		$idEvent				= $field->getForm()->getRecordID();
		$autoNotifiedPersonIDs	= TodoyuCalendarEventMailManager::getAutoNotifiedPersonIDs($idEvent, true);

		return TodoyuCalendarEventRenderer::renderAutoMailComment($autoNotifiedPersonIDs);
	}



//	/**
//	 * Get person IDs of participants receiving auto-notification event emails
//	 *
//	 * @param	TodoyuFormElement	$field
//	 * @return	Array|Integer[]
//	 */
//	private static function getAutoNotifiedPersonIDs(TodoyuFormElement $field) {
//		$form	= $field->getForm();
//		$data	= $form->getFormData();
//
//		$participantIDs	= array();
//
//		if( isset($data['id_event']) ) {
//				// Event form inside mailing popup, after change per drag&drop/delete
//			$idEvent	= intval($data['id_event']);
//			$isNewEvent	= $idEvent === 0;
//			if( ! $isNewEvent ) {
//				$participantIDs	= TodoyuCalendarEventStaticManager::getEvent($idEvent)->getAssignedPersonIDs();
//			}
//		} else {
//				// Edit event form
//			$eventPersons	= $form->getField('persons')->getValue();
//			$participantIDs	= TodoyuArray::intval(array_keys($eventPersons));
//		}
//
//		return TodoyuCalendarEventMailManager::getAutoNotifiedPersonIDs($participantIDs);
//	}



	/**
	 * Get autocomplete persons for events (only staff)
	 *
	 * @param	String		$input
	 * @param	Array		$formData
	 * @param	String		$name
	 * @return	Array
	 */
	public static function autocompleteEventPersons($input, array $formData, $name) {
		$items	= array();

		$fieldsToSearchIn	= array(
			'p.firstname',
			'p.lastname',
			'p.shortname'
		);
		$searchWords	= TodoyuArray::trimExplode(' ', $input, true);

		if( !empty($searchWords)  ) {
			$fields	= '	p.id';
			$table	= '	ext_contact_person p,
						ext_contact_mm_company_person mmcp,
						ext_contact_company c';
			$where	= '		c.is_internal	= 1
						AND	c.id			= mmcp.id_company
						AND p.id			= mmcp.id_person';
			$like	= TodoyuSql::buildLikeQueryPart($searchWords, $fieldsToSearchIn);
			$where	.= ' AND ' . $like;
			$order	= '	p.lastname, p.firstname';

			$personIDs	= Todoyu::db()->getColumn($fields, $table, $where, '', $order, '', 'id');

			foreach($personIDs as $idPerson) {
				$items[$idPerson]	= TodoyuContactPersonManager::getLabel($idPerson);
			}
		}

		return $items;
	}



	/**
	 * Get edit tab label
	 *
	 * @param	Integer		$idEvent
	 * @return	String
	 */
	public static function getEventEditTabLabel($idEvent) {
		$event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);

		return Todoyu::Label('calendar.event.edit') . ': ' . $event->getTitle();
	}



	/**
	 * Get detail view tab label
	 *
	 * @param	Integer		$idEvent
	 * @return	String
	 */
	public static function getEventViewTabLabel($idEvent) {
		$event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);

		return $event->getTitle();
	}

}

?>