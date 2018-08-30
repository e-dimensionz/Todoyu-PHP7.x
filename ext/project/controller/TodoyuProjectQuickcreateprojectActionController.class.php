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
 * Quickcreate project controller
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectQuickCreateProjectActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access (project extension must be allowed)
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('project', 'general:use');
		TodoyuProjectProjectRights::restrictAdd();
	}



	/**
	 * Render project form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		return TodoyuProjectProjectRenderer::renderQuickCreateForm($params);
	}



	/**
	 * Save project (new or edit)
	 *
	 * @param	Array		$params
	 * @return	String		Form content if form is invalid
	 */
	public function saveAction(array $params) {
		$data		= $params['project'];
		$idProject	= intval($data['id']);

			// Get form object, call save hooks, set form data
		$form	= TodoyuProjectProjectManager::getQuickCreateForm();

		$form->setFormData($data);

		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

				// Save project
			$idProjectNew	= TodoyuProjectProjectManager::saveProject($storageData);

			TodoyuHeader::sendTodoyuHeader('idProject', $idProjectNew);
			TodoyuHeader::sendTodoyuHeader('idProjectOld', $idProject);
		} else {
			TodoyuHeader::sendTodoyuHeader('idProjectOld', $idProject);
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}

}

?>