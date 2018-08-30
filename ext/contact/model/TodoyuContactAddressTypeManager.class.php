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
 * Address type manager
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactAddressTypeManager {

	/**
	 * Get all configured contact address types
	 *
	 * @return	Array
	 */
	public static function getAddressTypes() {
		return Todoyu::$CONFIG['EXT']['contact']['addressTypes'];
	}



	/**
	 * Get configuration of contact address type with given ID
	 *
	 * @param	Integer		$idAddressType
	 * @return	Array
	 */
	public static function getAddressType($idAddressType) {
		$idAddressType	= intval($idAddressType);

		return Todoyu::$CONFIG['EXT']['contact']['addresstypes'][($idAddressType-1)];
	}



	/**
	 * @param	Integer	$idAddressType
	 * @return	String
	 */
	public static function getAddressTypeLabel($idAddressType) {
		$addressType = self::getAddressType($idAddressType);

		return Todoyu::Label($addressType['label']);
	}

}