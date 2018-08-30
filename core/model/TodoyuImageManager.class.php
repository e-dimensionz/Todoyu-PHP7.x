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
 * Image functions
 * 
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuImageManager {

	/**
	 * Save an image resized on the server
	 *
	 * @param	String		$pathSource
	 * @param	String		$pathDestination
	 * @param	Integer		$newMaxWidth
	 * @param	Integer		$newMaxHeight
	 * @param	String		$typeSource
	 * @param	Boolean		$upscale
	 * @return	Boolean
	 */
	public static function saveResizedImage($pathSource, $pathDestination, $newMaxWidth, $newMaxHeight, $typeSource = null, $upscale = false) {
		$pathSource		= TodoyuFileManager::pathAbsolute($pathSource);
		$pathDestination= TodoyuFileManager::pathAbsolute($pathDestination);
		$sourceInfo		= getimagesize($pathSource);
		$typeDest		= pathinfo($pathDestination, PATHINFO_EXTENSION);

		if( is_null($typeSource) ) {
			$typeSource	= $sourceInfo['mime'];
		}

			// Load image based on file type
		$image	= self::loadImage($pathSource, $typeSource);

		if( $image !== false ) {
			$newDimensions =	self::getDimensions($sourceInfo[0], $sourceInfo[1], $newMaxWidth, $newMaxHeight, $upscale);

			$newImage	= imagecreatetruecolor($newDimensions['width'], $newDimensions['height']);
			imagealphablending($newImage, false);

			imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newDimensions['width'], $newDimensions['height'], $sourceInfo[0], $sourceInfo[1]);

			return self::saveImage($newImage, $typeDest, $pathDestination);
		}

		return false;
	}



	/**
	 * Create an image resource from a file. PNG, JPEG and GIF are supported
	 *
	 * @param	String		$pathFile
	 * @param	String		$type
	 * @return	Boolean|Resource
	 */
	public static function loadImage($pathFile, $type) {
		$ext		= self::getExt($type);
		$pathFile	= TodoyuFileManager::pathAbsolute($pathFile);

		switch($ext) {
			case 'x-png':
			case 'png':
				$image	= imagecreatefrompng($pathFile);
				break;

			case 'pjpeg':
			case 'jpeg':
				$image	= imagecreatefromjpeg($pathFile);
				break;

			case 'gif':
				$image	= imagecreatefromgif($pathFile);
				break;

			default:
				$image	= false;
		}

		return $image;
	}



	/**
	 * Save an image resource to a file
	 *
	 * @param	Resource	$image				Image resource
	 * @param	String		$type				Image type (mime or extension)
	 * @param	String		$pathFile			Path to destination file
	 * @param	Boolean		$destroy			Destroy image resource
	 * @return	Boolean		Saving status
	 */
	public static function saveImage($image, $type, $pathFile, $destroy = true) {
		$ext		= self::getExt($type);
		$pathFile	= TodoyuFileManager::pathAbsolute($pathFile);

			// Create folder to the image
		TodoyuFileManager::makeDirDeep(dirname($pathFile));

		switch($ext) {
			case 'png':
				imagesavealpha($image, true);
				$success	= imagepng($image, $pathFile, 7);
				break;

			case 'jpeg';
				$success	= imagejpeg($image, $pathFile, 80);
				break;

			case 'gif':
				$success	= imagegif($image, $pathFile);
				break;

			default:
				$success	= false;
		}

			// Destroy image resource to save memory
		if( $destroy ) {
			imagedestroy($image);
		}

		return $success;
	}



	/**
	 * Get image extension type from mime type or extension
	 *
	 * @param	String		$type		image/gif or gif
	 * @return	String
	 */
	public static function getExt($type) {
		if( strstr($type, '/') !== false ) {
			list($dummy, $ext) = explode('/', $type);
		} else {
			$ext	= $type;
		}

		return $ext;
	}



	/**
	 * Get resize factor for image resizing
	 *
	 * @param	Integer		$sourceWidth
	 * @param	Integer		$sourceHeight
	 * @param	Integer		$newWidth
	 * @param	Integer		$newHeight
	 * @param	Boolean		$upscale			Upscale image if source is smaller than destination
	 * @return	Array		[width,height]
	 */
	public static function getDimensions($sourceWidth, $sourceHeight, $newWidth, $newHeight, $upscale = false) {
		$factorHeight	= $newHeight / $sourceHeight;
		$factorWidth	= $newWidth / $sourceWidth;
		$factor			= min($factorHeight, $factorWidth);

		if( !$upscale && $factor > 1.0 ) {
			$factor	= 1.0;
		}

		return array(
			'width'	=> round($sourceWidth * $factor,  0),
			'height'=> round($sourceHeight * $factor, 0)
		);
	}
}

?>