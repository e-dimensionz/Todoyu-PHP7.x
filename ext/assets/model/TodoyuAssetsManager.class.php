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
 * Manager for assets extension
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsManager {

	/**
	 * Get extension config
	 *
	 * @return	Array
	 */
	public static function getExtConf() {
		if( TodoyuSysmanagerExtConfManager::hasExtConf('assets') ) {
			$extConf	= TodoyuSysmanagerExtConfManager::getExtConf('assets');
		} else {
			$extConf	= array();
		}

			// Set defaults
		if( !array_key_exists('preview_max_width', $extConf) ) {
			$extConf['preview_max_width']	= 600;
		}
		if( !array_key_exists('preview_max_height', $extConf) ) {
			$extConf['preview_max_height']	= 450;
		}
		if( !array_key_exists('preview_quality', $extConf) ) {
			$extConf['preview_quality']	= 100;
		}

		return $extConf;
	}

}

?>