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
 *  Person quick creation action controller for contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactQuickCreatePersonActionController extends TodoyuActionController {

	/**
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		TodoyuContactPersonRights::restrictAdd();
	}


	
	/**
	 * Get quick person creation form rendered
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		return TodoyuContactPersonRenderer::renderPersonQuickCreateForm();
	}



	/**
	 * Save person record
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function saveAction(array $params) {
		$data		= $params['person'];
		$idPerson	= intval($data['id']);

			// Get form, call save hooks, set data
		$form	= TodoyuContactPersonManager::getQuickCreateForm($idPerson);
		$data	= TodoyuFormHook::callSaveData('ext/contact/config/form/person.xml', $data, 0);
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

			$idPerson	= TodoyuContactPersonManager::savePerson($storageData);

			TodoyuHeader::sendTodoyuHeader('idRecord', $idPerson);
			TodoyuHeader::sendTodoyuHeader('recordLabel', $storageData['lastname'] . ' ' . $storageData['firstname']);

			return $idPerson;
		} else {
			TodoyuHeader::sendTodoyuHeader('idRecord', 0);
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}

}

?>