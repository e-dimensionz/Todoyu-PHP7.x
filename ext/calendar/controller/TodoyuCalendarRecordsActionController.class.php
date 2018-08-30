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
 * Records action controller
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarRecordsActionController extends TodoyuActionController {

	/**
	 * Init controller: check permission
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('sysmanager', 'general:records');
	}



	/**
	 * Render sub part to calendar admin form for record types added/ used by calendar (holiday, holidayset)
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function addSubformAction(array $params) {
		$xmlBase	= 'ext/calendar/config/form/admin';

		$index		= intval($params['index']);
		$fieldName	= $params['field'];
		$formName	= $params['form'];
		$idRecord	= intval($params['record']);

		switch($fieldName) {
			case 'holidays':
				$xmlPath	= $xmlBase . '/holidayset.xml';
				break;

			case 'holidayset':
			default:
				$xmlPath	= $xmlBase . '/holiday.xml';
				break;
		}

		$form	= TodoyuFormManager::getForm($xmlPath, $idRecord);

			// Load form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $idRecord);

		return TodoyuFormManager::renderSubFormRecord($xmlPath, $fieldName, $formName, $index, $idRecord, $formData);
	}

}

?>