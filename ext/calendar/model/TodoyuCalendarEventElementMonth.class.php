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
 * Event element for month
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarEventElementMonth extends TodoyuCalendarEventElement {

	/**
	 * Get view
	 * Just a wrapper to set the correct return type in this doc
	 *
	 * @return	TodoyuCalendarViewMonth
	 */
	public function getView() {
		return parent::getView();
	}



	/**
	 * Get template
	 *
	 * @return	String
	 */
	public function getTemplate() {
		return 'ext/calendar/view/event/month.tmpl';
	}



	/**
	 *
	 *
	 * @param	Integer		$date
	 * @return	Array
	 */
	protected function getElementTemplateData($date = 0) {
		$data	= parent::getElementTemplateData($date);

		$data['titleCropLength']	= 11;

		return $data;
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