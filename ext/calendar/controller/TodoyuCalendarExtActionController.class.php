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
 * Ext action controller
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarExtActionController extends TodoyuActionController {

	/**
	 * Init controller: check permission
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('calendar', 'general:area');
		Todoyu::restrictInternal();
	}



	/**
	 * Render default view of calendar when full page is reloaded
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
			// Generate colors CSS and sprite
		TodoyuColors::generate();

			// Activate FE tab and tab sub menu entry
		TodoyuFrontend::setActiveTab('planning');
		TodoyuFrontend::setActiveSubmenuTab('planning', 'calendar');

			// Initialise page template, title
		TodoyuPage::init('ext/calendar/view/ext.tmpl');
		TodoyuPage::setTitle('calendar.ext.page.title');


		$idEvent	= intval($params['event']);

		$activeTab	= $params['tab'];
		if( empty($activeTab) ) {
			$activeTab	= $idEvent === 0 ? TodoyuCalendarPreferences::getActiveTab() : 'view';
		}

			// Verify access rights. If not, change it to day
		$activeTab	= TodoyuCalendarEventStaticManager::checkTabAccess($activeTab, $params['event']);

			// Set date in preferences when given as parameter
		if( is_numeric($params['date']) ) {
			TodoyuCalendarPanelWidgetCalendar::saveDate($params['date']);
		}

			// Render the calendar
		$panelWidgets	= TodoyuCalendarRenderer::renderPanelWidgets();
		$calendarTabs	= TodoyuCalendarRenderer::renderTabs($activeTab, $idEvent);
		$calendar		= TodoyuCalendarRenderer::renderContent($activeTab, $params);

			// Set calendar as active page
		TodoyuPage::set('calendarTabs', $calendarTabs);
		TodoyuPage::set('calendar', $calendar);
		TodoyuPage::set('panelWidgets', $panelWidgets);

		return TodoyuPage::render();
	}

}

?>