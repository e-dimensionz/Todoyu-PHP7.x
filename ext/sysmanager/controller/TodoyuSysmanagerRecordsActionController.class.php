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
 * Records controller
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRecordsActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('sysmanager', 'general:records');
	}



	/**
	 * Update record module content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		return TodoyuSysmanagerExtRecordRenderer::renderModule($params);
	}



	/**
	 * Delete record
	 *
	 * @param	Array	$params
	 */
	public function deleteAction(array $params) {
		$ext		= trim($params['extkey']);
		$type		= trim($params['type']);
		$idRecord	= intval($params['record']);

		TodoyuSysmanagerExtRecordManager::deleteRecord($ext, $type, $idRecord);
	}



	/**
	 * Save record
	 *
	 * @param	Array			$params
	 * @return	Void|String		Failure returns re-rendered form with error messages
	 */
	public function saveAction(array $params) {
		$data	= TodoyuArray::assure($params['record']);
		$ext	= trim($params['extkey']);
		$type	= trim($params['type']);
		$form	= TodoyuSysmanagerExtRecordManager::getRecordForm($ext, $type);

		$form->setFormData($data);

			// Validate, save, render
		if( $form->isValid() ) {
			$storageData= $form->getStorageData();
			TodoyuSysmanagerExtRecordManager::saveRecord($ext, $type, $storageData);
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			$form->addFormData(array(
				'record-extkey'	=> $ext,
				'record-type'	=> $type
			));

			return $form->render();
		}
	}

}

?>