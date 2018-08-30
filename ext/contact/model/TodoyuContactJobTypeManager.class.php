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
 * Manager for jobtypes
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactJobTypeManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_jobtype';



	/**
	 * Get jobtype object
	 *
	 * @param	Integer		$idJobType
	 * @return	TodoyuContactJobType
	 */
	public static function getJobType($idJobType) {
		return TodoyuRecordManager::getRecord('TodoyuContactJobType', $idJobType);
	}



	/**
	 * Get all jobtypes
	 *
	 * @return	Array
	 */
	public static function getAllJobTypes() {
		return TodoyuRecordManager::getAllRecords(self::TABLE);
	}



	/**
	 * Get job types
	 *
	 * @param	Array	$typeIDs	optional
	 * @return	Array
	 */
	public static function getJobTypes($typeIDs = array()) {
		$fields	= 'id, title';
		$table	= self::TABLE;

		$where	= 'deleted = 0';
		if( count($typeIDs) > 0 ) {
			$where		.= ' AND ' . TodoyuSql::buildInListQueryPart($typeIDs, 'id');
		}

//		$order	= 'title';

		$jobTypes	= Todoyu::db()->getIndexedArray('id', $fields, $table, $where, '', '');
		foreach($jobTypes as $id => $jobType) {
			$jobTypes[$id]['title'] = Todoyu::Label($jobType['title']);
			$jobTypes[$id]['id']	= $id;
		}

		return $jobTypes;
	}



	/**
	 * Get all job type records
	 *
	 * @return	Array
	 */
	public static function getRecords() {
		$jobTypes		= self::getAllJobTypes();
		$reformConfig	= array(
			'title'	=> 'label'
		);

		return TodoyuArray::reform($jobTypes, $reformConfig, true);
	}



	/**
	 * Get job type options
	 *
	 * @return	Array
	 */
	public static function getJobTypeOptions() {
		$jobTypes		= self::getJobTypes();
		$reformConfig	= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);
		$options	= TodoyuArray::reform($jobTypes, $reformConfig);

		return TodoyuArray::sortByLabel($options, 'label');
	}



	/**
	 * Search in job types
	 *
	 * @param	String		$search
	 * @return	Array
	 */
	public static function searchJobtypes($search) {
		$fields		= '*';
		$table		= self::TABLE;
		$order		= 'title';
		$search		= TodoyuArray::trimExplode(' ', $search, true);
		$searchFields = array('title');

		if( sizeof($search) > 0 ) {
			$where = TodoyuSql::buildLikeQueryPart($search, $searchFields);
		} else {
			$where = '1';
		}

		$where .= ' AND deleted = 0';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Save jobtype
	 *
	 * @param	Array	$data		Data of jobtype
	 * @return	Integer
	 */
	public static function saveJobtype(array $data) {
		$idJobtype	= intval($data['id']);
		$xmlPath	= 'ext/contact/config/form/admin/jobtype.xml';

		if( $idJobtype === 0 ) {
			$idJobtype = self::addJobtype();
		}

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idJobtype);

		self::updateJobtype($idJobtype, $data);


		return $idJobtype;
	}



	/**
	 * Add new jobtype
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addJobtype(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update jobtype record
	 *
	 * @param	Integer		$idJobtype
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateJobtype($idJobtype, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idJobtype, $data);
	}



	/**
	 * Remove jobtype record
	 *
	 * @param	Integer	$idJobtype
	 * @return	Boolean
	 */
	public static function deleteJobtype($idJobtype) {
		$idJobtype	= intval($idJobtype);

		return TodoyuRecordManager::deleteRecord(self::TABLE, $idJobtype);
	}



	/**
	 * Get auto-complete list for job type
	 *
	 * @param	String		$input
	 * @param	Array		$formData
	 * @param	String		$name
	 * @return	Array
	 */
	public static function autocompleteJobtypes($input, array $formData = array(), $name = '') {
		$data		= array();
		$jobTypes	= self::searchJobtypes($input);

		foreach($jobTypes as $jobType) {
			$data[$jobType['id']] = $jobType['title'];
		}

		return $data;
	}



	/**
	 * Get person IDs for a job type
	 *
	 * @param	Integer		$idJobType
	 * @param	Boolean		$onlyInternal
	 * @return	Integer[]
	 */
	public static function getPersonIDsWithJobtype($idJobType = 0, $onlyInternal = true) {
		$idJobType	= intval($idJobType);

		$fields		= '	mmcp.id_person';
		$tables		= '	ext_contact_mm_company_person mmcp,
						ext_contact_person p';
		$where		= '		mmcp.id_jobtype	= ' . $idJobType
					. ' AND mmcp.id_person	= p.id'
					. ' AND p.deleted		= 0';
		$group		= '	mmcp.id_person';

		if( $onlyInternal ) {
			$tables	.= ', ext_contact_company c';
			$where	.= ' AND mmcp.id_company= c.id'
					. ' AND c.deleted		= 0'
					. ' AND c.is_internal	= 1';
		}
		$field		= 'id_person';

		return Todoyu::db()->getColumn($fields, $tables, $where, $group, '', '', $field);
	}

}

?>