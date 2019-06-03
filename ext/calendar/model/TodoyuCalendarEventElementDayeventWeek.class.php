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
 * Dayevent element for week view
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventElementDayeventWeek extends TodoyuCalendarEventElementWeek {

	/**
	 * Base event element
	 *
	 * @var	TodoyuCalendarEventElementWeek
	 */
	protected $eventElement;



	/**
	 * Initialize
	 *
	 * @param	TodoyuCalendarEventElementWeek	$eventElement
	 */
	public function __construct(TodoyuCalendarEventElementWeek $eventElement) {
		parent::__construct($eventElement->getEvent(), $eventElement->getView());

		$this->eventElement	= $eventElement;

		$this->addClass('dayevent');
	}



	/**
	 * Get event element templating data
	 *
	 * @param $date
	 * @return Array
	 */
	protected function getElementTemplateData($date = 0) {
		$data	= parent::getElementTemplateData($date);

		$data['titleCropLength']= TodoyuCalendarPreferences::isWeekendDisplayed() ? 11 : 16;

		$view		= $this->getView();
		$viewRange	= $view->getRange();
		$eventRange	= $this->getEvent()->getRange();

		$overlappingRange	= $viewRange->getOverlappingRange($eventRange, true);
		$days	= $overlappingRange->getAmountOfDays();

		$dayWidth	= $view->isWeekendDisplayed() ? CALENDAR_WEEK_DAYEVENT_WIDTH : CALENDAR_WEEK_FIVEDAY_DAYEVENT_WIDTH;
		$realWidth	= ($days * $dayWidth) - 6;

		$data['width']	= $realWidth;
		return $data;
	}



	/**
	 * Get template path
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return 'ext/calendar/view/event/dayevent.tmpl';
	}



	/**
	 * Get template data
	 *
	 * @param	TodoyuDayRange		$range		Week in where the elements is rendered
	 * @return	Array
	 */
	public function getTemplateData($date = 0) {
		$elementTemplateData= $this->getElementTemplateData($date);
		$eventTemplateData	= $this->getEvent()->getTemplateData(true);

		return array_merge($eventTemplateData, $elementTemplateData);
	}



	/**
	 * Render dayevent for a range
	 *
	 * @param	TodoyuDayRange		$range
	 * @return	String
	 */
	public function render(TodoyuDayRange $range = null) {
		$data	= $this->getTemplateData($range);
		$tmpl	= $this->getTemplate();

		return Todoyu::render($tmpl, $data);
	}

}

?>