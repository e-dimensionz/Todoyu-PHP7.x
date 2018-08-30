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
 * Bookmark action controller
 *
 * @package		Todoyu
 * @subpackage	Bookmark
 */
class TodoyuBookmarkBookmarkActionController extends TodoyuActionController {

	/**
	 * Initialize controller: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('bookmark', 'general:use');
	}



	/**
	 * Add a bookmark
	 *
	 * @param	Array		$params
	 */
	public function addAction(array $params) {
		$idItem	= intval($params['item']);
		$type	= $params['type'];
		$idType	= TodoyuBookmarkBookmarkManager::getTypeIndex($type);

		if( ! TodoyuBookmarkRights::isAddAllowed($idItem, $idType) ) {
			TodoyuRightsManager::deny('bookmark', $type . ':add');
		}

		if ( ! TodoyuBookmarkBookmarkManager::isItemBookmarked($type, $idItem) ) {
			TodoyuBookmarkBookmarkManager::addItemToBookmarks($idType, $idItem);
		}
	}



	/**
	 * Remove an item from bookmarks
	 *
	 * @param	Array		$params
	 */
	public function removeAction(array $params) {
		$type	= $params['type'];
		$idType	= TodoyuBookmarkBookmarkManager::getTypeIndex($type);
		$idItem	= intval($params['item']);

		if( ! TodoyuBookmarkRights::isRemoveAllowed($idItem, $idType) ) {
			TodoyuRightsManager::deny('bookmark', 'general:use');
		}

			// No item ID given? get from bookmark ID
		if( $idItem === 0 ) {
			$idBookmark	= intval($params['bookmark']);
			$idItem		= TodoyuBookmarkBookmarkManager::getItemID($idBookmark);
		}

		TodoyuBookmarkBookmarkManager::removeItemFromBookmarks($idType, $idItem);
	}



	/**
	 * Rename bookmark
	 *
	 * @param	Array	$params
	 */
	public function renameAction(array $params) {
		$type	= trim($params['type']);
		$idItem	= intval($params['item']);
		$label	= trim($params['label']);

		TodoyuBookmarkBookmarkManager::updateItemBookmarkTitle($type, $idItem, $label);
	}

}

?>