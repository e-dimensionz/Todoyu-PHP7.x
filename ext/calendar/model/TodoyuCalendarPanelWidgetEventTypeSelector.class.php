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
 * Panel widget: event type selector
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarPanelWidgetEventTypeSelector extends TodoyuPanelWidget {

	/**
	 * Preference name
	 *
	 * @var string
	 */
	const PREF	= 'panelwidget-eventtypeselector';


	/**
	 * Constructor (init widget)
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'calendar',										// ext key
			'eventtypeselector',							// panel widget ID
			'calendar.panelwidget-eventtypeselector.title',	// widget title text
			$config,										// widget config array
			$params											// widget parameters
		);

		$this->addHasIconClass();

			// Init widget JS (observers)
		TodoyuPage::addJsInit('Todoyu.Ext.calendar.PanelWidget.EventTypeSelector.init()', 100);
	}



	/**
	 * Render panel content (event type selector)
	 *
	 * @return	String
	 */
	public function renderContent() {
		$selectedEventTypes	= $this->getSelectedEventTypes();
		$eventTypes			= TodoyuCalendarEventTypeManager::getEventTypes(true);

		$tmpl	= 'ext/calendar/view/panelwidget/eventtypeselector.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'eventtypes'	=> $eventTypes,
			'selected'		=> $selectedEventTypes,
			'config'		=> $this->config
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render widget (get evoked)
	 *
	 * @return	String
	 */
	public function render() {
		$this->setContent($this->renderContent());

		return parent::render();
	}



	/**
	 * Get current event types selection (from prefs)
	 *
	 * @return	Array
	 */
	public static function getSelectedEventTypes() {
		$eventTypes	= TodoyuCalendarPreferences::getPref('panelwidget-eventtypeselector', 0, AREA);

		if( !$eventTypes ) {
			$eventTypes	= TodoyuCalendarEventTypeManager::getEventTypeIndexes();
		} else {
			$eventTypes	= TodoyuArray::intExplode(',', $eventTypes, true, true);
		}

		return $eventTypes;
	}



	/**
	 * Store prefs of the event type selector panel widget
	 *
	 * @param	Integer	$idArea
	 * @param	String	$prefVals
	 */
	public function savePreference($idArea = 0, $prefVals = '') {
		TodoyuPreferenceManager::savePreference(
			EXTID_CALENDAR,						// ext ID
			'panelwidget-eventtypeselector', 	// preference
			$prefVals, 							// value
			0,									// item ID
			true								// unique?
		);
	}



	/**
	 * Shortcut for rights check - calendar 'general:use'
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return Todoyu::allowed('calendar', 'general:use');
	}
}

?>