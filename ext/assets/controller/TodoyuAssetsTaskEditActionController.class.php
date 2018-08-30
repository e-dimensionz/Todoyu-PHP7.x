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
 * Assets file upload action controller for task edit form inline
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTaskEditActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('assets', 'general:use');
	}



	/**
	 * Default action: upload an asset
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		return $this->uploadassetfileAction($params);
	}



	/**
	 * Render <option> tags for uploaded files in this session
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function sessionFilesAction(array $params) {
		$idTask	= intval($params['record']);

		return TodoyuAssetsTaskEditRenderer::renderSessionFileOptions($idTask);
	}



	/**
	 * Upload an asset file
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function uploadassetfileAction(array $params) {
		$idTask	= intval($params['task']['id']);
		$file	= TodoyuRequest::getUploadFile('file', 'task');
		$error	= intval($file['error']);

			// Check again for file limit
		$maxFileSize	= intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']);
		if( $file['size'] > $maxFileSize ) {
			$error	= UPLOAD_ERR_FORM_SIZE;
		}
			// Check length of file name
		if( strlen($file['name']) > Todoyu::$CONFIG['EXT']['assets']['max_length_filename'] ) {
			$file['error']	= 3;
		}

			// Render frame content. Success or error
		if( $error === UPLOAD_ERR_OK && is_array($file) && !$file['error'] ) {
			$uploader	= new TodoyuAssetsTempUploaderTask($idTask);
			$uploader->addFile($file);

			return TodoyuAssetsTaskEditRenderer::renderUploadframeContent($file['name'], $idTask);
		} else {
				// Notify upload failure
			TodoyuLogger::logError('File upload failed: ' . $file['name'] . ' (ERROR:' . $error . ')');

			return TodoyuAssetsTaskEditRenderer::renderUploadframeContentFailed($error, $file['name'], $idTask);
		}
	}



	/**
	 * Delete temporary (uploaded prior to creation of task) asset file
	 *
	 * @param	Array	$params
	 * @return	String	Session file option elements
	 */
	public static function deletesessionfileAction(array $params) {
		$fileKey	= trim($params['filekey']);
		$idTask		= intval($params['record']);

		$uploader	= new TodoyuAssetsTempUploaderTask($idTask);
		$uploader->removeFile($fileKey);

		return TodoyuAssetsTaskEditRenderer::renderSessionFileOptions($idTask);
	}



	/**
	 * Delete all temporary (uploaded prior to creation of task) asset files
	 *
	 * @param	Array	$params
	 */
	public static function deleteuploadsAction(array $params) {
		$idTask		= intval($params['record']);

		$uploader	= new TodoyuAssetsTempUploaderTask($idTask);
		$uploader->clear();
	}

}

?>