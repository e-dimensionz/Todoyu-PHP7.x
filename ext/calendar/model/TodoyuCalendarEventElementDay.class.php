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
 * Event element for day view
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventElementDay extends TodoyuCalendarEventElementDayWeek {

	/**
	 * Get view
	 * Just a wrapper to set the correct return type in this doc
	 *
	 * @return	TodoyuCalendarViewDay
	 */
	public function getView() {
		return parent::getView();
	}



	/**
	 * Get view width
	 *
	 * @return	Integer
	 */
	protected function getViewWidth() {
		return CALENDAR_DAY_EVENT_WIDTH;
	}



	/**
	 * Get element template data
	 *
	 * @return	Array
	 */
	protected function getElementTemplateData($date = 0) {
		$data	= parent::getElementTemplateData(0);

		$data['titleCropLength']= 200;

		return $data;
	}



	/**
	 * Get position styles
	 * Add top position data
	 *
	 * @return	String[]
	 */
	protected function getPositionStyles($date) {
		$styles	= parent::getPositionStyles($this->getEvent()->getDateStart());

		$styles['top']	= $this->getTopOffset($this->getEvent()->getDateStart())  . 'px';

		return $styles;
	}

}

?>