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
 * Asset upload action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsUploadActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('assets', 'general:use');
	}



	/**
	 * Upload an asset
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		$idRecord		= intval($params['asset']['id_record']);
		$recordType		= $params['asset']['recordType'];
		$file	= TodoyuRequest::getUploadFile('file', 'asset');

		if( strlen($file['name']) > Todoyu::$CONFIG['EXT']['assets']['max_length_filename'] ) {
			$file['error']	= 3;
		}

			// Render frame content. Success or error
		if( !$file || $file['error'] !== UPLOAD_ERR_OK ) {
			TodoyuLogger::logError('File upload failed: ' . $file['name'] . ' (ERROR:' . $file['error'] . ')');

			return TodoyuAssetsAssetRenderer::renderUploadframeContentFailed($idRecord, $file['error'], $file['name']);
		} else {
			$methodName = 'add' . ucfirst($recordType) . 'Asset';
			TodoyuAssetsAssetManager::$methodName($idRecord, $file['tmp_name'], $file['name'], $file['type']);

			return TodoyuAssetsAssetRenderer::renderUploadframeContent($idRecord, $recordType, $file['name']);
		}
	}

}

?>