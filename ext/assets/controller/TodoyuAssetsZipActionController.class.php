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
 * Asset ZIP download action controller
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsZipActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('assets', 'general:use');
	}



	/**
	 * Download multiple assets in a ZIP archive
	 *
	 * @param	Array		$params
	 */
	public function downloadAction(array $params) {
		$idRecord		= intval($params['record']);
		$recordType		= $params['recordType'];
		$assetIDs	= TodoyuArray::intExplode(',', $params['assets'], true, true);

		if( !empty($assetIDs) ) {
			foreach( $assetIDs as $idAsset) {
				if( ! TodoyuAssetsRights::isSeeAllowed( $idAsset )) {
					TodoyuAssetsRights::restrictSee($idAsset);
				}
			}

			TodoyuAssetsAssetManager::downloadAssetsZipped($idRecord, $recordType, $assetIDs);
		} else {
			die("NO ASSETS SELECTED");
		}
	}

}

?>