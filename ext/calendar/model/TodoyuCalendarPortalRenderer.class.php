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
 * Calendar Portal Renderer
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarPortalRenderer {

	/**
	 * Get tab label for appointment tab in portal
	 *
	 * @param	Boolean		$count		Add count in brackets
	 * @return	String
	 */
	public static function getAppointmentTabLabel($count = true) {
		$label		= Todoyu::Label('calendar.ext.portal.tab.appointments');

		if( $count ) {
			$view	= new TodoyuCalendarViewPortalList();
			$count	= $view->getStaticEventsCount();
			$label	= $label . ' (' . $count . ')';
		}

		return $label;
	}



	/**
	 * Get tab content for appointment tab in portal
	 *
	 * @return	String
	 */
	public static function getAppointmentTabContent() {
		$view	= new TodoyuCalendarViewPortalList();
		$count	= $view->getStaticEventsCount();

		TodoyuHeader::sendTodoyuHeader('items', $count);

		return $view->render();
	}

}

?>