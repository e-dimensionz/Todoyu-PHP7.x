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
 * Panel widget: calendar
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarPanelWidgetCalendar extends TodoyuPanelWidget {

	/**
	 * Preference name
	 *
	 * @var string
	 */
	const PREF	= 'panelwidget-calendar';



	/**
	 * Constructor of PanelWidgetCalendar (initialize widget)
	 *
	 * @param	Array	$config
	 * @param	Array	$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'calendar',								// ext. key
			'calendar',								// panel widget ID
			'calendar.panelwidget-calendar.title',	// widget title text
			$config,								// widget config array
			$params									// widget parameters
		);

		$this->addHasIconClass();

			// Init widget JS (observers)
		TodoyuPage::addJsInit($this->getJsInitCode(), 100);
	}



	/**
	 * Get JS init code for widget
	 *
	 * @return	String
	 */
	protected function getJsInitCode() {
		$firstDayOfWeek	= TodoyuSysmanagerSystemConfigManager::getFirstDayOfWeek();
		$date			= date('Y-m-d', $this->getDate());

		return 'Todoyu.Ext.calendar.PanelWidget.Calendar.init(\'' . $date . '\', ' . $firstDayOfWeek . ')';
	}



	/**
	 * Render content
	 * NOTE:	the calender HTML itself is added into the DOM via JS by the jscalendar library
	 *
	 * @return String
	 */
	public function renderContent() {
		$tmpl	= 'ext/calendar/view/panelwidget/calendar.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'class'			=> $this->config['class'],
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get date for area
	 *
	 * @return	Integer
	 */
	public static function getDate() {
		return TodoyuCalendarPreferences::getDate(AREA);
	}



	/**
	 * Save calendar date for area
	 *
	 * @param	Integer		$timestamp
	 */
	public static function saveDate($timestamp) {
		$timestamp	= intval($timestamp);

		TodoyuCalendarPreferences::saveDate($timestamp, AREA);
	}



	/**
	 * Check panelWidget access permission
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return Todoyu::allowed('calendar', 'general:use');
	}

}

?>