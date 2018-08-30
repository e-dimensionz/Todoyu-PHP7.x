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
 * Rights manager for the bookmark extension
 *
 * @package		Todoyu
 * @subpackage	Bookmark
 */
class TodoyuBookmarkRights {

	/**
	 * Checks whether adding of bookmark is allowed (including visibility check on Element)
	 *
	 * @param	Integer		$idItem
	 * @param	Integer		$idType
	 * @return	Boolean
	 */
	public static function isAddAllowed($idItem, $idType) {
		$idItem	= intval($idItem);

		return TodoyuAuth::isAdmin() ? true : self::isSeeAllowed($idItem, $idType);
	}



	/**
	 * Checks if seeing of bookmark is allowed (including visibility check on Element)
	 *
	 * @param	Integer	$idItem
	 * @param	Integer	$idType
	 * @return	Boolean
	 */
	public static function isSeeAllowed($idItem, $idType) {
		$idItem	= intval($idItem);

		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		switch( $idType ) {
			case BOOKMARK_TYPE_TASK:
				if( TodoyuProjectTaskRights::isSeeAllowed($idItem) ) {
					return Todoyu::allowed('bookmark', 'general:use');
				}
		}

		return false;
	}



	/**
	 * Checks whether removing of bookmark is allowed (inclusive visibility check on Element)
	 *
	 * @param	Integer		$idBookmark
	 * @param	Integer		$type
	 * @return	Boolean
	 */
	public static function isRemoveAllowed($idBookmark, $type) {
		$type		= intval($type);

		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		switch( $type ) {
			case BOOKMARK_TYPE_TASK:
				return Todoyu::allowed('bookmark', 'general:use');
		}

		return false;
	}
}

?>