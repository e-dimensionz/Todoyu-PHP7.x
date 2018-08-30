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
 * Event element for week view
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventElementWeek extends TodoyuCalendarEventElementDayWeek {

	/**
	 * Get view
	 * Just a wrapper to set the correct return type in this doc
	 *
	 * @return	TodoyuCalendarViewWeek
	 */
	public function getView() {
		return parent::getView();
	}



	/**
	 * Get width of the total space which is available for the events of a day
	 *
	 * @return	Integer
	 */
	protected function getViewWidth() {
		return $this->getView()->isWeekendDisplayed() ? CALENDAR_WEEK_EVENT_WIDTH : CALENDAR_WEEK_FIVEDAY_EVENT_WIDTH;
	}



	/**
	 * Get name of the view
	 *
	 * @return	String
	 */
	public function getViewName() {
		return $this->getView()->getName();
	}



	/**
	 *
	 *
	 * @param	Integer		$date
	 * @return	Array
	 */
	protected function getElementTemplateData($date = 0) {
		$data	= parent::getElementTemplateData($date);

		$data['titleCropLength']	= $this->getView()->isWeekendDisplayed() ? 10 : 16;

		return $data;
	}



	/**
	 * Get position styles
	 * Add top position key
	 *
	 * @param	Integer		$date
	 * @return	String[]
	 */
	protected function getPositionStyles($date) {
		$styles	= parent::getPositionStyles($date);

		$styles['top']	= $this->getTopOffset($date) . 'px';

		return $styles;
	}



	/**
	 * Render event for a day in week view
	 *
	 * @param	Integer		$date
	 * @return	String
	 */
	public function render($date = null) {
		$tmpl	= $this->getTemplate();
		$data	= $this->getTemplateData($date);

		return Todoyu::render($tmpl, $data);
	}

}

?>