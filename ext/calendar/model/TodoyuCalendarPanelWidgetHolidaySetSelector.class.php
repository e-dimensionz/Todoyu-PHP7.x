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
 * Panel widget: holidaySet selector
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarPanelWidgetHolidaySetSelector extends TodoyuPanelWidget {

	/**
	 * Preference name
	 *
	 * @var string
	 */
	const PREF	= 'panelwidget-holidaysetselector';


	/**
	 * Constructor (init widget)
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'calendar',											// ext key
			'holidaysetselector',								// panel widget ID
			'calendar.panelwidget-holidaysetselector.title',	// widget title text
			$config,											// widget config array
			$params												// widget parameters
		);

		$this->addHasIconClass();

			// Init widget JS (observers)
		TodoyuPage::addJsInit('Todoyu.Ext.calendar.PanelWidget.HolidaySetSelector.init()', 100);
	}



	/**
	 * Render the whole widget
	 *
	 * @return	String
	 */
	public function render() {
		$this->setContent($this->renderContent());

		return parent::render();
	}



	/**
	 * Render widget content
	 *
	 * @return	String
	 */
	public function renderContent() {
		$tmpl	= 'ext/calendar/view/panelwidget/holidaysetselector.tmpl';

		$data	= array(
			'id'		=> $this->getID(),
			'config'	=> $this->config,
			'options'	=> self::getHolidaySetsOptions(),
			'selected'	=> self::getSelectedHolidaySetIDs()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get <option>s config array of all holidaysets
	 *
	 * @return	Array
	 */
	public static function getHolidaySetsOptions() {
		$holidaySets= TodoyuCalendarHolidaySetManager::getAllHolidaySets();
		$selected	= self::getSelectedHolidaySetIDs();
		$options	= array();

		foreach($holidaySets as $holidaySet) {
			$options[] = array(
				'value'		=> $holidaySet['id'],
				'label'		=> $holidaySet['title'],
				'selected'	=> in_array($holidaySet['id'], $selected)
			);
		}

		return $options;
	}



	/**
	 * Get IDs of selected holidaySets
	 *
	 * @param	String	$area
	 * @return	Integer[]
	 */
	public static function getSelectedHolidaySetIDs($area = AREA) {
		$selectorPref	= TodoyuCalendarPreferences::getPref('panelwidget-holidaysetselector', 0, $area);

		return TodoyuArray::intExplode(',', $selectorPref);
	}



	/**
	 * Store prefs of the holidaySet selector panel widget
	 *
	 * @param	Integer	$idArea
	 * @param	String	$prefVals
	 */
	public function savePreference($idArea = 0, $prefVals = '') {
		TodoyuCalendarPreferences::savePref('panelwidget-holidaysetselector', $prefVals, 0, true);
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