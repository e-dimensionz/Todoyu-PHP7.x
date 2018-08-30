<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 *  Preference action controller for contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPreferenceActionController extends TodoyuActionController {

	/**
	 * Controller init
	 */
	public function init() {
		Todoyu::restrict('contact', 'general:use');
	}



	/**
	 * Save staffselector panelwidget preferences
	 *
	 * @param	Array	$params
	 */
	public function panelwidgetstaffselectorAction(array $params) {
		$prefs	= json_decode($params['value'], true);

		TodoyuContactPanelWidgetStaffSelector::savePrefs($prefs);
	}



	/**
	 * Save stafflist panelwidget preference (selected person)
	 *
	 * @param	Array	$params
	 */
	public function panelwidgetstafflistAction(array $params) {
		$prefs	= json_decode($params['value'], true);

		TodoyuContactPreferences::savePref('panelwidget-stafflist', $prefs, 0, true, AREA);
	}



	/**
	 * General panelWidget action, saves collapse status
	 *
	 * @param	Array	$params
	 */
	public function pwidgetAction(array $params) {
		$idWidget	= $params['item'];
		$value		= $params['value'];

		TodoyuPanelWidgetManager::saveCollapsedStatus(EXTID_CONTACT, $idWidget, $value);
	}
}

?>