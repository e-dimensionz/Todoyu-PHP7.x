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
 * Manager for asset previews
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsPreviewManager {

	/**
	 * Create scaled version of given (image) asset for preview, return it's path
	 *
	 * @param	Integer		$idAsset
	 * @return	Array		Attributes of the preview image: path, width, height
	 */
	public static function getPreviewImage($idAsset) {
		$idAsset= intval($idAsset);
		$width = 0;
		$height = 0;

		$asset		= TodoyuAssetsAssetManager::getAsset($idAsset);
		$idParent	= $asset->getParentID();
		$basePath	= self::getStorageBasePath();
		$folder		= TodoyuAssetsAssetManager::getFolderNameByParentType($asset->getParentType(), $idParent);

		$storagePath= TodoyuFileManager::pathAbsolute($basePath . DIR_SEP . ($folder ? $folder . DIR_SEP : '') . $idParent);

			// Create storage folder if it doesn't exist
		TodoyuFileManager::makeDirDeep($storagePath);

		$extension			= TodoyuFileManager::getFileExtension($asset->getFilename());
		$previewImagePath	= $storagePath . DIR_SEP . $idAsset . '.' . $extension;

			// Render preview image if it doesn't exist yet
		$pathAsset	= $asset->getFileStoragePath();

		if( !file_exists($previewImagePath) ) {
			$Resize	= new TodoyuAssetsImageResizer($pathAsset);
			$extConf= TodoyuAssetsManager::getExtConf();

			if( $Resize->isValidImage() ) {
				$Resize->resizeImage($extConf['preview_max_width'], $extConf['preview_max_height']);
				$Resize->saveImage($previewImagePath, $extConf['preview_quality']);
				$width	= $Resize->getScaledWidth();
				$height = $Resize->getScaledHeight();
			}
		} else {
			$Resize	= new TodoyuAssetsImageResizer($previewImagePath);
			$width	= $Resize->getWidth();
			$height = $Resize->getHeight();
		}

		return array(
			'path' 	=> TodoyuFilemanager::pathWeb($previewImagePath),
			'width'	=> $width,
			'height'=> $height,
		);
	}



	/**
	 * Get base path (absolute) of preview storage
	 *
	 * @return	String
	 */
	public static function getStorageBasePath() {
		return TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['assets']['previewPath']);
	}

}

?>