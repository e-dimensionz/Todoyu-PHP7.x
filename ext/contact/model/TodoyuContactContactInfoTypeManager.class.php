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
 * Contactinfotype manager
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactContactInfoTypeManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_contactinfotype';



	/**
	 * Get contact info type
	 *
	 * @param	Integer		$idContactInfoType
	 * @return	TodoyuContactContactInfoType
	 */
	public static function getContactInfoType($idContactInfoType) {
		$idContactInfoType	= intval($idContactInfoType);

		return TodoyuRecordManager::getRecord('TodoyuContactContactInfoType', $idContactInfoType);
	}



	/**
	 * Get all contact info types, optionally parse title labels
	 *
	 * @param	Boolean		$parseLabels
	 * @return	Array
	 */
	public static function getContactInfoTypes($parseLabels = true) {
		$where	= 'deleted = 0';

		$types	= TodoyuRecordManager::getAllRecords(self::TABLE, $where, '');

		if( $parseLabels ) {
			foreach($types as $index => $type) {
				$types[$index]['title'] = Todoyu::Label($type['title']);
			}
		}

		return $types;
	}



	/**
	 * Get list of existing contactinfotype records
	 *
	 * @return	Array
	 */
	public static function getRecords() {
		$contactInfoTypes	= self::getContactInfoTypes(true);

		$reformConfig		= array(
			'id'	=> 'id',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($contactInfoTypes, $reformConfig);
	}



	/**
	 * Create new contactinfotype record with given data
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addContactInfoType(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update contactinfo type of given ID with given data
	 *
	 * @param	Integer		$idContactTypeInfo
	 * @param	Array		$data
	 */
	public static function updateContactInfoType($idContactTypeInfo, array $data) {
		TodoyuRecordManager::updateRecord(self::TABLE, $idContactTypeInfo, $data);
	}



	/**
	 * Delete given contact type info record of given ID
	 *
	 * @param	Integer		$idContactTypeInfo
	 */
	public static function deleteContactTypeInfo($idContactTypeInfo) {
		TodoyuRecordManager::deleteRecord(self::TABLE, $idContactTypeInfo);
	}



	/**
	 * Save given data into contactinfo type (update if ID given or add new record)
	 *
	 * @param	Array	$data
	 * @return	Integer
	 */
	public static function saveContactInfoType(array $data) {
		$idContactInfoType	= intval($data['id']);
		$xmlPath			= 'ext/contact/config/form/admin/contactinfotype.xml';

		if( $idContactInfoType === 0 ) {
			$idContactInfoType = self::addContactInfoType();
		}

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idContactInfoType);

		self::updateContactInfoType($idContactInfoType, $data);

		return $idContactInfoType;
	}

}

?>