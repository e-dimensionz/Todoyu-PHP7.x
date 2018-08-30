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
 * Rescale images
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsImageResizer {

	/**
	 * Square dimension: as wide as high
	 *
	 * @var	Integer
	 */
	const DIMENSION_SQUARE	= 0;

	/**
	 * Portrait dimension: higher than wide
	 *
	 * @var	Integer
	 */
	const DIMENSION_PORTRAIT	= 1;

	/**
	 * Landscape dimension: wider than high
	 *
	 * @var	Integer
	 */
	const DIMENSION_LANDSCAPE	= 2;

	/**
	 * @var Boolean|Resource
	 */
	private $image;

	/**
	 * @var	Integer
	 */
	private $width;

	/**
	 * @var	Integer
	 */
	private $height;

	/**
	 * @var	Integer
	 */
	private $newWidth;

	/**
	 * @var	Integer
	 */
	private $newHeight;

	/**
	 * @var	Resource
	 */
	private $imageScaled;


	/**
	 * Constructor: set original image properties
	 *
	 * @param	String	$filename		Path to image file to be scaled
	 */
	function __construct($filename) {
		$this->image	= $this->openImage($filename);
		$this->initSize();
	}



	/**
	 * @return	Boolean
	 */
	public function isValidImage() {
		return gettype($this->image) == 'resource';
	}



	/**
	 * Create image resource from given image file
	 *
	 * @param	String	$filename
	 * @return	Boolean|Resource
	 */
	private function openImage($filename) {
		$extension	= strtolower(TodoyuFileManager::getFileExtension($filename));

		switch($extension) {
			case 'jpg':
			case 'jpeg':
				// Disable error handler, to suppress confusing error-messages.
				// Sometimes the library can not handle not well formed jpeg-files.
				TodoyuErrorHandler::setActive(false);
				$image	= @imagecreatefromjpeg($filename);
				TodoyuErrorHandler::setActive(true);
				break;
			
			case 'gif':
				$image	= @imagecreatefromgif($filename);
				break;
			
			case 'png':
				$image	= @imagecreatefrompng($filename);
				break;

			default:
				$image	= false;
				break;
		}

		return $image;
	}



	/**
	 * @param	Integer		$width
	 * @param	Integer		$height
	 * @param	Boolean		[$crop]
	 * @return	Boolean
	 */
	public function resizeImage($width, $height, $crop= false) {
		if( !$this->isValidImage() ) {
			return false;
		}

			// Get optimal width and height - based on $option
		$optionArray	= $this->getDimensions($width, $height, $crop ? 'crop' : 'auto');

		$this->newWidth 	= intval($optionArray['optimalWidth']);
		$this->newHeight	= intval($optionArray['optimalHeight']);

		if( $this->newWidth === 0 || $this->newHeight === 0 ) {
			return false;
		}

			// Resample - create image canvas of x, y size
		$this->imageScaled	= imagecreatetruecolor($this->newWidth, $this->newHeight);

		if( gettype($this->imageScaled) !== 'resource' ) {
			return false;
		}

		imagecopyresampled($this->imageScaled, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);

		if( $crop ) {
			$this->crop($this->newWidth, $this->newHeight, $width, $height);
		}

		return true;
	}



	/**
	 * @return	Integer
	 */
	public function getWidth() {
		return $this->width;
	}



	/**
	 * @return	Integer
	 */
	public function getHeight() {
		return $this->height;
	}



	/**
	 * @return	Integer
	 */
	public function getScaledWidth() {
		return $this->newWidth;
	}



	/**
	 * @return	Integer
	 */
	public function getScaledHeight() {
		return $this->newHeight;
	}



	/**
	 * @param	Integer		$newWidth
	 * @param	Integer		$newHeight
	 * @param	String		$option
	 * @return	Array
	 */
	private function getDimensions($newWidth, $newHeight, $option) {
			// Prevent upscale
		if( $newWidth >= $this->width || $newHeight >= $this->height) {
			return array(
				'optimalWidth'	=> $this->width,
				'optimalHeight'	=> $this->height
			);
		}

		switch( $option ) {
			case 'exact':
				return array(
					'optimalWidth'	=> $newWidth,
					'optimalHeight'	=> $newHeight
				);

			case 'portrait':
				return array(
					'optimalWidth'	=> $this->getSizeByFixedHeight($newHeight),
					'optimalHeight'	=> $newHeight
				);

			case 'landscape':
				return array(
					'optimalWidth'	=> $newWidth,
					'optimalHeight'	=> $this->getSizeByFixedWidth($newWidth)
				);

			case 'crop':
				$optionArray	= $this->getOptimalCrop($newWidth, $newHeight);
				return array(
					'optimalWidth'	=> $optionArray['optimalWidth'],
					'optimalHeight'	=> $optionArray['optimalHeight']
				);

			case 'auto':
			default:
				$optionArray	= $this->getSizeByAuto($newWidth, $newHeight);
				return array(
					'optimalWidth'	=> $optionArray['optimalWidth'],
					'optimalHeight'	=> $optionArray['optimalHeight']
				);
		}
	}



	/**
	 * @param	Integer	$newHeight
	 * @return	Mixed
	 */
	private function getSizeByFixedHeight($newHeight) {
		$ratio		= $this->width / $this->height;
		$newWidth	= $newHeight * $ratio;

		return $newWidth;
	}



	/**
	 * @param	Integer		$newWidth
	 * @return	Mixed
	 */
	private function getSizeByFixedWidth($newWidth) {
		$ratio		= $this->height / $this->width;

		return $newWidth * $ratio;
	}



	/**
	 * @param	Integer		$newWidth
	 * @param	Integer		$newHeight
	 * @return	Array
	 */
	private function getSizeByAuto($newWidth, $newHeight) {
		switch( $this->getImageDimensionType($this->width, $this->height) ) {
			case self::DIMENSION_LANDSCAPE:
				$optimalWidth	= $newWidth;
				$optimalHeight	= $this->getSizeByFixedWidth($newWidth);
				break;

			case self::DIMENSION_PORTRAIT:
				$optimalWidth	= $this->getSizeByFixedHeight($newHeight);
				$optimalHeight	= $newHeight;
				break;

			default:	// Square
				switch( $this->getImageDimensionType($newWidth, $newHeight) ) {
					case self::DIMENSION_LANDSCAPE:
						$optimalWidth	= $newWidth;
						$optimalHeight	= $this->getSizeByFixedWidth($newWidth);
						break;

					case self::DIMENSION_PORTRAIT:
						$optimalWidth	= $this->getSizeByFixedHeight($newHeight);
						$optimalHeight	= $newHeight;
						break;

					default:	// Square being scaled to a square
						$optimalWidth	= $newWidth;
						$optimalHeight	= $newHeight;
						break;
				}
				break;
			}

		return array(
			'optimalWidth'	=> $optimalWidth,
			'optimalHeight'	=> $optimalHeight
		);
	}



	/**
	 * Detect dimension type: square, landscape or portrait.
	 * Tests given values or original image
	 *
	 * @param	Integer		$width
	 * @param	Integer		$height
	 * @return	Integer
	 */
	private function getImageDimensionType($width, $height) {
		$width	= intval($width);
		$height	= intval($height);

		if( $height > $width ) {
			return self::DIMENSION_PORTRAIT;
		} elseif( $height < $width ) {
			return self::DIMENSION_LANDSCAPE;
		} else {
			return self::DIMENSION_SQUARE;
		}
	}



	/**
	 * @param	Integer		$newWidth
	 * @param	Integer		$newHeight
	 * @return	Array
	 */
	private function getOptimalCrop($newWidth, $newHeight) {
		$heightRatio	= $this->height / $newHeight;
		$widthRatio 	= $this->width /  $newWidth;

		if( $heightRatio < $widthRatio ) {
			$optimalRatio	= $heightRatio;
		} else {
			$optimalRatio	= $widthRatio;
		}

		return array(
			'optimalWidth'	=> $this->width  / $optimalRatio,
			'optimalHeight'	=> $this->height / $optimalRatio
		);
	}



	/**
	 * @param	Integer		$optimalWidth
	 * @param	Integer		$optimalHeight
	 * @param	Integer		$newWidth
	 * @param	Integer		$newHeight
	 */
	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight) {
			// Find center - this will be used for the crop
		$cropStartX	= ( $optimalWidth / 2) - ( $newWidth /2 );
		$cropStartY	= ( $optimalHeight/ 2) - ( $newHeight/2 );

		$crop	= $this->imageScaled;
//		imagedestroy($this->imagescaled);

			// Crop from center to exact requested size
		$this->imageScaled	= imagecreatetruecolor($newWidth , $newHeight);
		imagecopyresampled($this->imageScaled, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
	}



	/**
	 * Save scaled image in given image format (jpg / gif / png)
	 *
	 * @param	String	$savePath			Store path including filename of image to be saved
	 * @param	String	[$imageQuality]
	 * @return	Boolean
	 */
	public function saveImage($savePath, $imageQuality = '100') {
		if( gettype($this->imageScaled) !== 'resource' ) {
			return false;
		}

		$extension	= strtolower(TodoyuFilemanager::getFileExtension($savePath));
		$imageTypes	= imagetypes();

			// Create image file (if GD supports the image format)
		switch($extension) {
			case 'jpg':
			case 'jpeg':
				if( $imageTypes & IMG_JPG ) {
					imagejpeg($this->imageScaled, $savePath, $imageQuality);
				}
				break;

			case 'gif':
				if( $imageTypes & IMG_GIF ) {
					imagegif($this->imageScaled, $savePath);
				}
				break;

			case 'png':
				if( $imageTypes & IMG_PNG ) {
					imagepng($this->imageScaled, $savePath, $this->getImageQualityForPng($imageQuality));
				}
				break;

			default:
					// No extension - No save
				break;
		}

		imagedestroy($this->imageScaled);

		return true;
	}



	/**
	 * Convert percentage value (100% to 0%) to PNG quality format (0 compression to 9)
	 *
	 * @param	$qualityInPercent
	 * @return	Integer
	 */
	private static function getImageQualityForPng($qualityInPercent) {
			// Scale quality from 0-100 to 0-9,
		$scaleQuality	= round(($qualityInPercent / 100) * 9);

			// Invert quality as 0 is best, not 9
		return 9 - $scaleQuality;
	}



	/**
	 * Check whether the given asset is an image than GD lib can handle
	 *
	 * @param	Integer		$idAsset
	 * @return	Boolean
	 */
	public static function isGDcompatibleImage($idAsset) {
		$idAsset		= intval($idAsset);
		$asset 			= TodoyuAssetsAssetManager::getAsset($idAsset);
		$fileExtension	= strtolower(TodoyuFileManager::getFileExtension($asset->getFilename()));

		return in_array($fileExtension, array('gif', 'jpeg', 'jpg', 'png'));
	}



	/**
	 *
	 */
	protected function initSize() {
		if( $this->isValidImage() ) {
			$this->width = imagesx($this->image);
			$this->height = imagesy($this->image);
		}
	}

}
