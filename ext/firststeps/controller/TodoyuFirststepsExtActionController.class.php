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
 *  Action controller for Firststeps
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirststepsExtActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
	 */
	public function init(array $params = array()) {
		Todoyu::restrictAdmin();
	}



	/**
	 * Delete employee person
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function removeEmployeeAction(array $params) {
		$idPerson	= intval($params['person']);

		TodoyuContactPersonManager::deletePerson($idPerson);
	}



	/**
	 * Remove a company
	 *
	 * @param	Array	$params
	 */
	public function removeCompanyAction(array $params) {
		$idCompany	= intval($params['company']);

		TodoyuContactCompanyManager::deleteCompany($idCompany);
	}



	/**
	 * Remove an assigned person from a project
	 *
	 * @param	Array	$params
	 */
	public function removeAssignedPersonAction(array $params) {
		$idPerson	= intval($params['person']);
		$idProject	= intval($params['project']);

		TodoyuProjectProjectManager::removeProjectPerson($idProject, $idPerson);
	}



	/**
	 * Disable the wizard. Save in extConf
	 *
	 * @param	Array	$params

	 */
	public function saveDisableAction(array $params) {
		TodoyuFirstStepsManager::disableWizard();

		TodoyuNotification::notifyInfo('firststeps.ext.wizardDisabledInfo');
	}

}

?>