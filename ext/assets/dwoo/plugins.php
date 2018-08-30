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
 * Asset specific Dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 */

/**
 * Check right of current person to see given asset
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idAsset
 * @return	Boolean
 */
function Dwoo_Plugin_isAssetSeeAllowed(Dwoo $dwoo, $idAsset) {
	return TodoyuAssetsRights::isSeeAllowed($idAsset);
}



/**
 * Check right of current person to delete given asset
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idAsset
 * @return	Boolean
 */
function Dwoo_Plugin_isAssetDeleteAllowed(Dwoo $dwoo, $idAsset) {
	return TodoyuAssetsRights::isDeleteAllowed($idAsset);
}



/**
 * Check whether the given asset is an image type that GD lib can handle
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idAsset
 * @return	Boolean
 */
function Dwoo_Plugin_isAssetGDcompatibleImage(Dwoo $dwoo, $idAsset) {
	return TodoyuAssetsImageResizer::isGDcompatibleImage($idAsset);
}


