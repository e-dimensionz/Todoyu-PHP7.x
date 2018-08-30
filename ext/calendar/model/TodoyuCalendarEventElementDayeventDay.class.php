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
 * Day event element for day view
 * Extends a normal event element for day
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventElementDayeventDay extends TodoyuCalendarEventElementDay {

	/**
	 * Base event element
	 *
	 * @var	TodoyuCalendarEventElementWeek
	 */
	protected $eventElement;



	/**
	 * Initialize with event element
	 *
	 * @param	TodoyuCalendarEventElementDay	$eventElement
	 */
	public function __construct(TodoyuCalendarEventElementDay $eventElement) {
		parent::__construct($eventElement->getEvent(), $eventElement->getView());

		$this->eventElement	= $eventElement;

		$this->addClass('dayevent');
	}



	/**
	 * Get template path
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return  'ext/calendar/view/event/dayevent.tmpl';
	}



	/**
	 * Get element template data
	 *
	 * @return	Array
	 */
	protected function getElementTemplateData($date = 0) {
		$data	= parent::getElementTemplateData();

		$data['titleCropLength']	= 80;

		return $data;
	}

}

?>