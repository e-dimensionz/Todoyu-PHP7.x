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
 * Calendar Ext Renderer
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarRenderer {

	/**
	 * Extension key
	 *
	 * @var	String
	 */
	const EXTKEY	= 'calendar';



	/**
	 * Render the whole calendar (header, tabs and the actual calendar grid)
	 *
	 * @param	String	$tab		Active tab
	 * @param	Array	$params		Request parameters sub functions
	 * @return	String	Code of the calendar
	 */
	public static function renderContent($tab, array $params = array()) {
		$date	= TodoyuCalendarPanelWidgetCalendar::getDate();

		$tmpl	= 'ext/calendar/view/main.tmpl';
		$data	= array(
			'tab'			=> $tab,
			'content'		=> TodoyuCalendarCalendarRenderer::renderBody($tab, $date, $params),
			'showCalendar'	=> in_array($tab, array('day', 'week', 'month')),
			'rangeStart'	=> TodoyuCalendarPreferences::getCompactViewRangeStart(),
			'rangeEnd'		=> TodoyuCalendarPreferences::getCompactViewRangeEnd(),
			'idEvent'		=> intval($params['event'])
		);

			// If event view is selected, set date and add it to data array
		if( $tab === 'view' ) {
			$idEvent= intval($params['event']);
			$event	= TodoyuCalendarEventStaticManager::getEvent($idEvent);
			TodoyuCalendarPanelWidgetCalendar::saveDate($event->getDateStart());
			$data['date']	= $event->getDateStart();
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render calendar panel widgets
	 *
	 * @return	String	HTML
	 */
	public static function renderPanelWidgets() {
		return TodoyuPanelWidgetRenderer::renderPanelWidgets(self::EXTKEY);
	}



	/**
	 * Renders the calendar tabs (day, week, month)
	 *
	 * @param	String		$activeTab
	 * @param	Integer		$idEvent
	 * @return	String
	 */
	public static function renderTabs($activeTab = '', $idEvent = 0) {
		if( empty($activeTab) ) {
			$activeTab	= TodoyuCalendarPreferences::getActiveTab();
		}
		$idEvent	= intval($idEvent);

		$name		= 'calendar';
		$tabs		= TodoyuCalendarManager::getCalendarTabsConfig();
		$jsHandler	= 'Todoyu.Ext.calendar.Tabs.onSelect.bind(Todoyu.Ext.calendar.Tabs)';

		if( $activeTab === 'view' && $idEvent > 0 ) {
			$tabs[]	= array(
				'id'	=> 'view',
				'label'	=> TodoyuCalendarEventViewHelper::getEventViewTabLabel($idEvent)
			);
		}
		if( $activeTab === 'edit' && $idEvent > 0 ) {
			$tabs[]	= array(
				'id'	=> 'edit',
				'label'	=> TodoyuCalendarEventViewHelper::getEventEditTabLabel($idEvent)
			);
		}

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}

}

?>