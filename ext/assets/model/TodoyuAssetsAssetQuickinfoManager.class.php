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
 * Manage asset quickinfo
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsAssetQuickInfoManager {

	/**
	 * Add items to asset quickinfo
	 *
	 * @param	TodoyuQuickinfo		$quickInfo
	 * @param	Integer				$idAsset
	 */
	public static function addAssetInfos(TodoyuQuickinfo $quickInfo, $idAsset) {
		$idAsset= intval($idAsset);
		$asset 	= TodoyuAssetsAssetManager::getAsset($idAsset);

		$fileInfo	= $asset->getFilename() . ' (' . $asset->getFilesizeFormatted(). ')';
		$quickInfo->addInfo('fileinfo', $fileInfo, 10, false);

		$preview	= TodoyuAssetsAssetRenderer::renderPreview($idAsset);
		$quickInfo->addInfo('image', $preview, 10, false);
	}



	/**
	 * Add JS onload function to page (hooked into TodoyuPage::render())
	 */
	public static function addJSonloadFunction() {
		TodoyuPage::addJsInit('Todoyu.Ext.assets.QuickinfoAsset.init()', 100);
	}

}

?>