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
 * Form handling action controller for contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactFormhandlingActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('contact', 'general:use');
	}

	/**
	 * Get additional sub form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		$formName	= $params['form'];
		$fieldName	= $params['field'];
		$xmlPath	= TodoyuContactManager::getContactTypeFromXml($formName);
		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);

		if( $idRecord === 0 ) {
			TodoyuContactRights::restrictRecordAdd($formName);
		} else {
			TodoyuContactRights::restrictRecordEdit($formName, $idRecord);
		}

			// Construct form object
		$form	= TodoyuFormManager::getForm($xmlPath, $index);

			// Load (/preset) form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $index);

			// Set form data
		$form->setFormData($formData);
		$form->setRecordID($idRecord);

		$field			= $form->getField($fieldName);
		$form['name']	= $formName;

		return $field->renderNewRecord($index);
	}



	/**
	 * Render contact image upload form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function contactimageuploadformAction(array $params) {
		$idRecord	= intval($params['idRecord']);
		$recordType	= $params['recordType'];

		if( $idRecord > 0 ) {
			TodoyuContactRights::restrictRecordEdit($recordType, $idRecord);
		} else {
			TodoyuContactRights::restrictRecordAdd($recordType);
		}

		return TodoyuContactRenderer::renderContactImageUploadForm($idRecord, $recordType);
	}



	/**
	 * Upload contact image file
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function uploadcontactimageAction(array $params) {
		$file		= TodoyuRequest::getUploadFile('file', 'uploadcontactimage');
		$error		= intval($file['error']);
		$data		= $params['uploadcontactimage'];
		$idContact	= intval($data['idContact']);
		$recordType	= $data['recordType'];

		if( $idContact > 0 ) {
			TodoyuContactRights::restrictRecordEdit($recordType, $idContact);
		} else {
			TodoyuContactRights::restrictRecordAdd($recordType);
		}

			// Check against the file mime type
		if( $error === UPLOAD_ERR_OK && !TodoyuContactImageManager::checkFileType($file['type']) ) {
			$error	= UPLOAD_ERR_EXTENSION;
		}

			// Render frame content. Success or error
		if( $error === UPLOAD_ERR_OK ) {
			$idReplace	= TodoyuContactImageManager::storeImage($file['tmp_name'], $file['name'], $file['type'], $idContact, $recordType);

			return TodoyuContactRenderer::renderUploadFormFinished($recordType, $idContact, $idReplace);
		} else {
				// Notify upload failure
			TodoyuLogger::logError('File upload failed: ' . $file['name'] . ' (ERROR:' . $error . ')');

			return TodoyuContactRenderer::renderUploadframeContentFailed($file['error'], $file['name']);
		}
	}



	/**
	 * Removes Image form file-system
	 *
	 * @param	Array	$params
	 */
	public function removeimageAction(array $params) {
		$idRecord	= $params['record'];
		$recordType	= $params['recordType'];

		if( $idRecord > 0 ) {
			TodoyuContactRights::restrictRecordEdit($recordType, $idRecord);
		} else {
			TodoyuContactRights::restrictRecordAdd($recordType);
		}

		TodoyuContactImageManager::removeImage($idRecord, $recordType);
	}

}

?>