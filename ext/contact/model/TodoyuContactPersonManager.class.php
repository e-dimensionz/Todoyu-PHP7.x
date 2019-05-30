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
 * Manage persons
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_person';

	/**
	 * @var	String
	 */
	const contactTypeKey	= 'person';



	/**
	 * Get form object for person quick creation
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuForm
	 */
	public static function getQuickCreateForm($idPerson = 0) {
		$idPerson	= intval($idPerson);

			// Construct form object
		$xmlPath	= 'ext/contact/config/form/person.xml';
		$form		= TodoyuFormManager::getForm($xmlPath, $idPerson);

			// Adjust form to needs of quick creation wizard
		$form->setAttribute('action', 'index.php?ext=contact&amp;controller=quickcreateperson');
		$form->setAttribute('onsubmit', 'return false');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.QuickCreatePerson.save(this.form)');
		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.removeUnusedImages(this.form);Todoyu.Popups.close(\'quickcreate\')');

			// Make sure that birthday field isn't set to default
		$form->setFieldFormData('birthday', false);

		return $form;
	}



	/**
	 * Get a person object. This functions uses the cache to
	 * prevent double object initialisation
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuContactPerson
	 */
	public static function getPerson($idPerson = null) {
		return TodoyuRecordManager::getRecord('TodoyuContactPerson', $idPerson);
	}



	/**
	 * Form hook to load persons foreign record data
	 * Load: company, contactinfo, address
	 *
	 * @param	Array		$data
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function hookPersonLoadFormData(array $data, $idPerson) {
		$idPerson	= intval($idPerson);

			// Set salutation for new persons
		if( ! isset($data['salutation']) ) {
			$data['salutation'] = 'm';
		}

		$data['company']	= TodoyuContactPersonManager::getPersonCompanyRecords($idPerson);
		$data['contactinfo']= TodoyuContactPersonManager::getContactinfoRecords($idPerson);
		$data['address']	= TodoyuContactPersonManager::getAddressRecords($idPerson);

		return $data;
	}



	/**
	 * Get all active persons
	 *
	 * @param	Array		$fields			By default, all fields are selected. You can provide a field list instead
	 * @param	Boolean		$showInactive	Also show inactive persons
	 * @return	Array
	 */
	public static function getAllActivePersons(array $fields = array(), $showInactive = false) {
		$fields	= sizeof($fields) === 0 ? '*' : implode(',', TodoyuSql::escapeArray($fields));
		$table	= self::TABLE;
		$where	= 'deleted = 0';
		$order	= 'lastname, firstname';

		if( $showInactive !== true ) {
			$where .= ' AND is_active	= 1';
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get all persons with an active login
	 *
	 * @return	Array
	 */
	public static function getAllLoginPersons() {
		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		deleted		= 0'
				. ' AND is_active	= 1'
				. ' AND username	!= \'\''
				. ' AND password	!= \'\'';
		$order	= 'lastname, firstname';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Check whether $username and $password are a valid login
	 *
	 * @param	String		$username		Username
	 * @param	String		$password		Password as sha1
	 * @return	Boolean
	 */
	public static function isValidLogin($username, $password) {
		$username	= trim($username);
		$password	= trim($password);

			// Prevent empty login data
		if( $username === '' || $password === '' ) {
			return false;
		}

		$field	= 'id';
		$table	= self::TABLE;
		$where	= '		`username`	= ' . TodoyuSql::quote($username, true) .
				  ' AND	`password`	= ' . TodoyuSql::quote($password, true) .
				  ' AND	`is_active`	= 1
					AND	`deleted`	= 0';

		return Todoyu::db()->hasResult($field, $table, $where);
	}



	/**
	 * Check whether $idPerson is a valid person ID
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isPerson($idPerson) {
		return TodoyuRecordManager::isRecord(self::TABLE, $idPerson);
	}



	/**
	 * Check whether a person with the username $username exists
	 *
	 * @param	String		$username
	 * @return	Boolean
	 */
	public static function personExists($username) {
		return self::getPersonIDByUsername($username) !== 0;
	}



	/**
	 * Get person ID by username
	 *
	 * @param	String		$username
	 * @return	Integer
	 */
	public static function getPersonIDByUsername($username) {
		$fields	= 'id';
		$table	= self::TABLE;
		$where	= '		username	= ' . TodoyuSql::quote($username, true)
				. ' AND is_active	= 1'
				. ' AND deleted		= 0';
		$limit	= '1';

		$row	= Todoyu::db()->doSelectRow($fields, $table, $where, '', '', $limit);

		return intval($row['id']);
	}



	/**
	 * Get person by username
	 *
	 * @param	String		$username
	 * @return	TodoyuContactPerson
	 */
	public static function getPersonByUsername($username) {
		$idPerson	= self::getPersonIDByUsername($username);

		return self::getPerson($idPerson);
	}



	/**
	 * Get person by email address
	 *
	 * @param	String		$email
	 * @return	TodoyuContactPerson|Boolean
	 */
	public static function getPersonByEmail($email) {
		if( !TodoyuString::isValidEmail($email) ) {
			return false;
		}

		$fields = 'id';
		$table	= self::TABLE;
		$where	= ' deleted = 0 AND ' . TodoyuSql::buildLikeQueryPart(array($email), array('email'));

		$personIDs	= Todoyu::db()->getArray($fields, $table, $where, '', '', '');

		return count($personIDs) === 1 ? self::getPerson($personIDs[0]['id']) : false;
	}



	/**
	 * Add a new person
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addPerson(array $data = array()) {
		$idPerson = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('contact', 'person.add', array($idPerson));

		return $idPerson;
	}



	/**
	 * Delete a person in the database (set deleted flag to 1)
	 *
	 * @param	Integer		$idPerson
	 */
	public static function deletePerson($idPerson) {
		$idPerson	= intval($idPerson);

		$data	= array(
			'deleted'	=> 1
		);

		self::updatePerson($idPerson, $data);

		TodoyuHookManager::callHook('contact', 'person.delete', array($idPerson));
	}



	/**
	 * Update a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$data
	 */
	public static function updatePerson($idPerson, array $data) {
		TodoyuRecordManager::removeRecordCache('TodoyuContactPerson', $idPerson);

		TodoyuRecordManager::updateRecord(self::TABLE, $idPerson, $data);

		TodoyuHookManager::callHook('contact', 'person.update', array($idPerson, $data));
	}



	/**
	 * Save person
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function savePerson(array $data) {
		$idPerson	= intval($data['id']);
		$xmlPath	= 'ext/contact/config/form/person.xml';

			// Create person in database if not existing
		if( $idPerson === 0 ) {
			$idPerson = self::addPerson();
		}

		if( $data['image_id'] != 0 ) {
			TodoyuContactImageManager::renameStorageFolder('person', $data['image_id'], $idPerson);
		}

		unset($data['image_id']);

			// Update/set password?
		if( strlen($data['password']) > 0 ) {
			$data['password'] = md5($data['password']);
		} else {
			unset($data['password']);
		}

			// Call internal save function
		$data	= self::savePersonForeignRecords($data, $idPerson);
			// Call hooked save functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idPerson);

		self::updatePerson($idPerson, $data);

		return $idPerson;
	}



	/**
	 * Update current persons password
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$password
	 * @param	Boolean		$alreadyHashed		Is password already a md5 hash?
	 */
	public static function updatePassword($idPerson, $password, $alreadyHashed = true) {
		$idPerson	=	intval($idPerson);

		if( ! $alreadyHashed ) {
			$password = md5($password);
		}

		$data		= array(
			'password'	=> $password
		);

		self::updatePerson($idPerson, $data);
	}



	/**
	 * Sort given person IDs alphabetical or by given sorting flags
	 *
	 * @param	Integer[]	$personIDs
	 * @param	String		$sorting
	 * @return	Integer[]
	 */
	public static function sortPersonIDs(array $personIDs, $sorting = 'lastname,firstname') {
		$field		= 'id';
		$table		= self::TABLE;
		$where		= TodoyuSql::buildInListQueryPart($personIDs, $field);
		$group		= 'id';

		return Todoyu::db()->getColumn($field, $table, $where, $group, $sorting);
	}



	/**
	 * Get role IDs of a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Integer[]
	 */
	public static function getRoleIDs($idPerson) {
		$idPerson	= Todoyu::personid($idPerson);

		$field	= 'mm.id_role';
		$tables	= ' ext_contact_mm_person_role mm,
					system_role r';
		$where	= 'mm.id_person		= ' . $idPerson
				. ' AND r.id		= mm.id_role'
				. ' AND r.deleted	= 0';

		return Todoyu::db()->getColumn($field, $tables, $where, '', '', '', 'id_role');
	}



	/**
	 * Get roles of a person
	 *
	 * @param	Integer	$idPerson
	 * @return	Array
	 */
	public static function getRoles($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	r.*';
		$tables	= '	ext_contact_mm_person_role mm,
					system_role r';
		$where	= '		mm.id_person= ' . $idPerson
				. ' AND	r.id		= mm.id_role'
				. ' AND r.deleted	= 0';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get labels of person's roles
	 *
	 * @param	$idPerson
	 * @return	Array
	 */
	public static function getPersonRoleLabels($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);
		$roleIDs	= self::getPerson($idPerson)->getRoleIDs();

		$roles	= array();
		foreach($roleIDs as $idRole) {
			$roles[]= TodoyuRoleManager::getRole($idRole)->getTitle();
		}

		return $roles;
	}



	/**
	 * Check whether the given person belongs to any of the given roles
	 *
	 * @param	Array		$roles
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function hasAnyRole(array $roles, $idPerson = 0) {
		$personRoles	= TodoyuContactPersonManager::getRoleIDs($idPerson);

		return sizeof(array_intersect($roles, $personRoles)) > 0;
	}



	/**
	 * Check whether person of given ID has any image in profile
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function hasImage($idPerson) {
		return TodoyuContactImageManager::hasImage($idPerson, self::contactTypeKey, 'contactimage');
	}



	/**
	 * Check whether person of given ID has any image in profile
	 *
	 * @param		$idPerson
	 * @return		Boolean
	 */
	public static function hasAvatar($idPerson) {
		return TodoyuContactImageManager::hasImage($idPerson, self::contactTypeKey, 'avatar');
	}



	/**
	 * Check whether given person has given right
	 *
	 * @param	String	$extKey
	 * @param	String	$right
	 * @param	Integer	$idPerson
	 * @return	Boolean
	 */
	public static function isAllowed($extKey, $right, $idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);
		$person		= self::getPerson($idPerson);

		if( $person->isAdmin() ) {
			return true;
		}

			// Get all rights of any role of person
		$roleIDs		= self::getRoleIDs($idPerson);
		$personRights	= TodoyuArray::flatten(TodoyuRightsManager::getExtRoleRights($extKey, $roleIDs));

			// Check whether the given right is there
		return in_array($right, $personRights);
	}



	/**
	 * Get IDs of internal persons (staff)
	 *
	 * @return	Integer[]
	 */
	public static function getInternalPersonIDs() {
		$persons	= self::getInternalPersons();

		return TodoyuArray::getColumn($persons, 'id');
	}



	/**
	 * Get internal persons (staff)
	 *
	 * @param	Boolean		$getJobType
	 * @param	Boolean		$getWorkAddress
	 * @param	Boolean		$onlyWithEmail		Filter out records w/o account email address?
	 * @return	Array
	 */
	public static function getInternalPersons($getJobType = false, $getWorkAddress = false, $onlyWithEmail = false) {
			// Fetch persons data
		$fields	=	'p.*';

		if( $getJobType ) {
			$fields	.= ', mm.id_jobtype';
		}
		if( $getWorkAddress ) {
			$fields .= ', mm.id_workaddress';
		}

		$table	=	self::TABLE . ' p,
					ext_contact_company c,
					ext_contact_mm_company_person mm';
		$where	= '		p.id			= mm.id_person
					AND	mm.id_company	= c.id
					AND	c.is_internal	= 1
					AND	p.deleted		= 0	';
		$order	= '	p.lastname,
					p.firstname';

		$persons	= Todoyu::db()->getIndexedArray('id', $fields, $table, $where, '', $order);

			// Remove persons w/o email address
		if( $onlyWithEmail ) {
			foreach($persons as $index => $personData) {
				if ( !self::getPerson($personData['id'])->hasEmail() ) {
					unset($persons[$index]);
				}
			}
		}

		return $persons;
	}



	/**
	 * Search for person
	 *
	 * @param	String[]	$searchWords
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @param	Integer[]	$ignoreIDs			Ignore records with this IDs
	 * @param	Boolean		$onlyActiveUsers
	 * @return	Array
	 */
	public static function searchPersons(array $searchWords = array(), $size = 100, $offset = 0, array $ignoreIDs = array(), $onlyActiveUsers = false) {
		$ignoreIDs	= TodoyuArray::intval($ignoreIDs, true, true);

		$fields	= 'SQL_CALC_FOUND_ROWS *';
		$table	= self::TABLE;
		$where	= ' deleted = 0';
		$order	= 'lastname';
		$limit	= ($size != '') ? intval($offset) . ',' . intval($size) : '';

		if( sizeof($searchWords) > 0 ) {
			$searchFields	= array('username', 'email', 'firstname', 'lastname', 'shortname');
			$where			.= ' AND ' . TodoyuSql::buildLikeQueryPart($searchWords, $searchFields);
		}

			// Add ignore IDs
		if( sizeof($ignoreIDs) > 0 ) {
			$where .= ' AND ' . TodoyuSql::buildInListQueryPart($ignoreIDs, 'id', true, true);
		}

			// Limit results to allowed person records
		if( ! Todoyu::allowed('contact', 'person:seeAllPersons') ) {
			$where .= ' AND ' . TodoyuContactPersonRights::getAllowedToBeSeenPersonsWhereClause();
		}

		if( $onlyActiveUsers ) {
			$where .= ' AND is_active = 1 AND username != \'\'';
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit);
	}



	/**
	 * Search persons which match the search words
	 *
	 * @param	Array		$searchWords
	 * @param	Integer[]	$ignoreUserIDs
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function searchStaff(array $searchWords, array $ignoreUserIDs = array(), $limit = 10) {
		$ignoreUserIDs	= TodoyuArray::intval($ignoreUserIDs, true, true);

		if( sizeof($searchWords) === 0 ) {
			return array();
		}

		$searchFieldsPerson	= array(
			'p.username',
			'p.email',
			'p.firstname',
			'p.lastname',
			'p.shortname',
			'p.title',
			'p.comment'
		);
		$searchFieldsJobtype = array(
			'jt.title'
		);
		$likePerson		= TodoyuSql::buildLikeQueryPart($searchWords, $searchFieldsPerson);
		$likeJobtypes	= TodoyuSql::buildLikeQueryPart($searchWords, $searchFieldsJobtype);

		$fields	= '	p.id,
					p.firstname,
					p.lastname,
					p.username,
					CONCAT(p.lastname, \' \', p.firstname) as label';
		$table	= '	ext_contact_person p
						LEFT JOIN ext_contact_mm_company_person mmcp
							ON p.id			= mmcp.id_person
						LEFT JOIN ext_contact_company c
							ON mmcp.id_company	= c.id
						LEFT JOIN ext_contact_jobtype jt
							ON mmcp.id_jobtype	= jt.id';
		$where	= '		c.is_internal	= 1'
				. ' AND c.deleted		= 0'
				. ' AND p.deleted		= 0'
				. ' AND (jt.deleted		= 0 OR jt.deleted IS NULL)'
				. '	AND	('
				. $likePerson
				. ' OR '
				. $likeJobtypes
				. ')';
		$group	= '	p.id';
		$order	= '	p.lastname,
					p.firstname';
		$limit	= intval($limit);

		if( sizeof($ignoreUserIDs) > 0 ) {
			$where .= ' AND p.id NOT IN(' . implode(',', $ignoreUserIDs) . ')';
		}

		return Todoyu::db()->getArray($fields, $table, $where, $group, $order, $limit);
	}



	/**
	 * Get label of database relation
	 *
	 * @param	TodoyuFormElement		$field
	 * @param	Array					$record
	 * @return	String
	 */
	public static function getDatabaseRelationLabel(TodoyuFormElement $field, array $record) {
		$idPerson	= intval($record['id']);

		if( $idPerson === 0 ) {
			$label	= 'New person';
		} else {
			$label	= self::getLabel($idPerson);
		}

		return $label;
	}



	/**
	 * Get person label
	 *
	 * @param	Integer		$idPerson
	 * @param	Boolean		$showEmail
	 * @param	Boolean		$lastnameFirst
	 * @param	Boolean		$showCompanyAbbr
	 * @return	String
	 */
	public static function getLabel($idPerson, $showEmail = false, $lastnameFirst = true, $showCompanyAbbr = false) {
		$idPerson	= intval($idPerson);
		$label		= '';

		if( $idPerson !== 0 ) {
			$label	= self::getPerson($idPerson)->getLabel($showEmail, $lastnameFirst, false, 0, $showCompanyAbbr);
		}

		return  $label;
	}



	/**
	 * When person form is saved, extract roles from data and save them
	 *
	 * @param	Array		$data
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function savePersonForeignRecords(array $data, $idPerson) {
		$idPerson	= intval($idPerson);

			// Save contact info
		if( isset($data['contactinfo']) ) {
			$contactInfoIDs	= TodoyuArray::getColumn($data['contactinfo'], 'id');

				// Delete all contact infos which are no longer linked
			self::deleteRemovedContactInfos($idPerson, $contactInfoIDs);

				// If contact infos submitted
			if( sizeof($data['contactinfo']) > 0 ) {
				$infoIDs	= array();
				foreach($data['contactinfo'] as $contactInfo) {
					$infoIDs[] = TodoyuContactContactInfoManager::saveContactInfos($contactInfo);
				}

				self::linkContactInfos($idPerson, $infoIDs);
			}

			unset($data['contactinfo']);
		}



			// Save address
		if( isset($data['address']) ) {
			$addressIDs	= TodoyuArray::getColumn($data['address'], 'id');

				// Delete all addresses which are no longer linked
			self::deleteRemovedAddresses($idPerson, $addressIDs);

				// If addresses submitted
			if( is_array($data['address']) ) {
				$addressIDs	= array();
				foreach($data['address'] as $address) {
					$addressIDs[] =  TodoyuContactAddressManager::saveAddress($address);
				}

				self::linkAddresses($idPerson, $addressIDs);
			}

			unset($data['address']);
		}



			// Save company
		if( isset($data['company']) ) {
			$companyIDs	= TodoyuArray::getColumn($data['company'], 'id');

				// Remove all person links which are no longer active
			self::removeRemovedCompanies($idPerson, $companyIDs);

			if( sizeof($data['company']) > 0 ) {
				foreach($data['company'] as $index => $company) {
						// Prepare data form mm-table
					$data['company'][$index]['id_company']	= $company['id'];
					$data['company'][$index]['id_person']	= $idPerson;
					unset($data['company'][$index]['id']);
				}

				self::saveCompanyLinks($idPerson, $data['company']);
			}

			unset($data['company']);
		}



			// Save roles
		if( isset($data['role']) ) {
			$roleIDs	= TodoyuArray::getColumn($data['role'], 'id');

				// Remove all role links which are no longer active
			self::removeRemovedRoles($idPerson, $roleIDs);

				// Save roles
			if( sizeof($roleIDs) > 0 ) {
				TodoyuRoleManager::addPersonToRoles($idPerson, $roleIDs);
			}

			unset($data['role']);
		}

		return $data;
	}



	/**
	 * Remove person object from cache
	 *
	 * @param	Integer		$idPerson
	 */
	public static function removeFromCache($idPerson) {
		$idPerson		= intval($idPerson);

		TodoyuRecordManager::removeRecordCache('TodoyuContactPerson', $idPerson);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idPerson);
	}



	/**
	 * Get IDs of working addresses of given person(s)
	 *
	 * @param	Array		$personIDs
	 * @return	Integer[]
	 */
	public static function getWorkaddressIDsOfPersons(array $personIDs) {
		$addressIDs	= array();

		if( sizeof($personIDs) > 0) {
			$field	= 'id_workaddress';
			$table	= 'ext_contact_mm_company_person';
			$where	= TodoyuSql::buildInListQueryPart($personIDs, 'id_person')
					. ' AND id_workaddress != 0';

			$addressIDs	= Todoyu::db()->getColumn($field, $table, $where);
		}

		return $addressIDs;
	}



	/**
	 * Get persons which celebrate birthday in the given range
	 * Gets the person records with some extra keys:
	 * - date: date of the birthday in this view (this year)
	 * - age: new age on this birthday
	 *
	 * @param	TodoyuDayRange	$range
	 * @return	Array
	 */
	public static function getBirthdayPersons(TodoyuDayRange $range) {
		$dateStart	= $range->getStart();
		$dateEnd	= $range->getEnd();

		$monthStart	= date('n', $dateStart);
		$monthEnd	= date('n', $dateEnd);
		$dayStart	= date('j', $dateStart);
		$dayEnd		= date('j', $dateEnd);


		if( $range->isInOneDay() ) { // One day range
			$rangeWhere	= 'MONTH(birthday) = ' . $monthStart . ' AND DAY(birthday) = ' . $dayStart;
		} elseif( $range->isInOneMonth() ) { // One month range
			$rangeWhere = 'MONTH(birthday) = ' . $monthStart . ' AND DAY(birthday) BETWEEN ' . $dayStart . ' AND ' . $dayEnd;
		} else { // all the rest
			if( $monthEnd < $monthStart ) { // Range overlaps two years
				$months			= array();
				$shiftedMonthEnd= $monthEnd + 12;

				for($monthCounter = $monthStart; $monthCounter <= $shiftedMonthEnd; $monthCounter++) {
					$month		= $monthCounter % 12;
					$months[]	= $month === 0 ? 12 : $month;
				}
			} else {
				$months = range($monthStart, $monthEnd);
			}
				// Fetch first and last month for day checks
			$firstMonth	= array_shift($months);
			$lastMonth	= array_pop($months);

			$rangeWhere	= '		MONTH(birthday) = ' . $firstMonth . ' AND DAY(birthday)	>= ' . $dayStart
						. ' OR	MONTH(birthday)	= ' . $lastMonth .  ' AND DAY(birthday)	<= ' . $dayEnd;

				// All months between
			if( sizeof($months) > 0 ) {
				$rangeWhere .= ' OR MONTH(birthday) IN(' . implode(',', $months) . ')';
			}
		}


		$fields	= '	id,
					email,
					firstname,
					lastname,
					shortname,
					salutation,
					title,
					birthday';
		$where	= '		deleted	= 0'
				. '	AND	(' . $rangeWhere . ')';
		$order	= '	IF(MONTH(birthday) >= ' . $monthStart . ',1,0) DESC,
					IF(MONTH(birthday) > ' . $monthStart . ',1,0) ASC,
					MONTH(birthday) ASC,
					DAY(birthday) ASC';

		$birthdayPersons	= Todoyu::db()->getArray($fields, self::TABLE, $where, '', $order);

			// Enrich data with date and age of persons
		$birthdayPersons	= self::addBirthdayPersonsDateAndAge($birthdayPersons, $dateStart, $dateEnd);

		return $birthdayPersons;
	}



	/**
	 * Enrich data array of persons and birthdays with resp. age and date
	 *
	 * @param	Array		$birthdayPersons
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	private static function addBirthdayPersonsDateAndAge(array $birthdayPersons, $dateStart, $dateEnd) {
		foreach($birthdayPersons as $index => $birthdayPerson) {
			$dateParts	= explode('-', $birthdayPerson['birthday']);
			$birthday	= mktime(0, 0, 0, $dateParts[1], $dateParts[2], date('Y', $dateStart));

				// If a persons birthday is in the next year, use $dateEnd for year information
			if( $birthday < $dateStart ) {
				$birthday = mktime(0, 0, 0, $dateParts[1], $dateParts[2], date('Y', $dateEnd));

				$birthdayPersons[$index]['age']	= floor(date('Y', $dateStart) - date('Y', strtotime($birthdayPerson['birthday'])) + 1);
			} else {
				$birthdayPersons[$index]['age']	= floor(date('Y', $dateStart) - date('Y', strtotime($birthdayPerson['birthday'])));
			}

				// Set date of the upcoming birthday
			$birthdayPersons[$index]['date']	= $birthday;
		}

		return $birthdayPersons;
	}



	/**
	 * Get color IDs to given person id's (persons are given enumerated colors by their position in the list)
	 *
	 * @param	Array	$personIDs
	 * @return	Array
	 */
	public static function getSelectedPersonColor(array $personIDs) {
		$personIDs	= TodoyuArray::intval($personIDs, true, true);
		$cacheKey	= 'personcolors:' . md5(serialize($personIDs));

		if( ! TodoyuCache::isIn($cacheKey) ) {
			$colors	= array();

				// Enumerate persons by system specific color to resp. list position
			foreach($personIDs as $idPerson) {
				$colors[$idPerson]	= TodoyuColors::getColorArray($idPerson);
			}

			TodoyuCache::set($cacheKey, $colors);
		}

		return TodoyuCache::get($cacheKey);
	}



	/**
	 * Get company records for a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPersonCompanyRecords($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	mm.*,
					c.*';
		$tables	= '	ext_contact_company c,
					ext_contact_mm_company_person mm';
		$where	= '		mm.id_company	= c.id
					AND	mm.id_person	= ' . $idPerson .
				  ' AND c.deleted		= 0 ';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Remove company links which are no longer active
	 * Companies stays untouched, only the link with the extra data will be removed
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$companyIDs
	 */
	public static function removeRemovedCompanies($idPerson, array $companyIDs) {
		TodoyuDbHelper::deleteOtherMmLinks('ext_contact_mm_company_person', 'id_person', 'id_company', $idPerson, $companyIDs);
	}



	/**
	 * Save linked person and linking data
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$linkData
	 */
	public static function saveCompanyLinks($idPerson, array $linkData) {
		TodoyuDbHelper::saveExtendedMMLinks('ext_contact_mm_company_person', 'id_person', 'id_company', $idPerson, $linkData);
	}



	/**
	 * Remove role links which are no longer active
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$roleIDs
	 */
	public static function removeRemovedRoles($idPerson, array $roleIDs) {
		TodoyuDbHelper::deleteOtherMmLinks('ext_contact_mm_person_role', 'id_person', 'id_role', $idPerson, $roleIDs);
	}



	/**
	 * Delete all contactinfos except the given ones
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$currentContactInfoIDs
	 * @return	Integer		Deleted records
	 */
	public static function deleteRemovedContactInfos($idPerson, array $currentContactInfoIDs) {
		return TodoyuContactContactInfoManager::deleteLinkedContactInfos('person', $idPerson, $currentContactInfoIDs, 'id_person');
	}



	/**
	 * Link a person with contactinfos
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$contactinfoIDs
	 */
	public static function linkContactinfos($idPerson, array $contactinfoIDs) {
		TodoyuDbHelper::addMMLinks('ext_contact_mm_person_contactinfo', 'id_person', 'id_contactinfo', $idPerson, $contactinfoIDs);
	}



	/**
	 * Delete all company addresses which are no longer active
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$currentAddressIDs	Active addresses which will not be deleted
	 * @return	Integer
	 */
	public static function deleteRemovedAddresses($idPerson, array $currentAddressIDs) {
		return TodoyuContactAddressManager::deleteLinkedAddresses('ext_contact_mm_person_address', $idPerson, $currentAddressIDs, 'id_person');
	}



	/**
	 * Link a person with addresses
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$addressIDs
	 */
	public static function linkAddresses($idPerson, array $addressIDs) {
		TodoyuDbHelper::addMMLinks('ext_contact_mm_person_address', 'id_person', 'id_address', $idPerson, $addressIDs);
	}



	/**
	 * Get address records for a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getAddressRecords($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	a.*';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_person_address mm';
		$where	= '		a.deleted		= 0'
				. ' AND mm.id_address	= a.id'
				. ' AND	mm.id_person	= ' . $idPerson;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get contact records for a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getContactinfoRecords($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	c.*, t.category infotype_category';
		$tables	= '	ext_contact_contactinfotype t,
					ext_contact_contactinfo c,
					ext_contact_mm_person_contactinfo mm';
		$where	= '		mm.id_contactinfo	= c.id
					AND	mm.id_person		= ' . $idPerson .
				  ' AND c.deleted			= 0' .
				  ' AND t.id				= c.id_contactinfotype';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Gets the preview image for the form
	 *
	 * @param	TodoyuFormElement_Comment	$formElement
	 * @return	String
	 */
	public static function getPreviewImageForm(TodoyuFormElement_Comment $formElement) {
		return TodoyuContactImageManager::renderImageForm($formElement, self::contactTypeKey);
	}




	/**
	 * Get link to detail view of a person
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getDetailLink($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= self::getPerson($idPerson);

		$linkParams	= array(
			'ext'		=> 'contact',
			'controller'=> 'person',
			'action'	=> 'detail',
			'person'	=> $idPerson,
		);

		return TodoyuString::wrapTodoyuLink($person->getLabel(), 'contact', $linkParams);
	}



	/**
	 * Get matching staff persons
	 *
	 * @param 	String[]	$searchWords
	 * @param	Integer[]	$ignoreIDs
	 * @param	Array		$params
	 * @param	String		$type
	 * @return	Array[]
	 */
	public static function getMatchingStaffPersons(array $searchWords, array $ignoreIDs = array(), array $params = array(), $type = null) {
		$ignore			= TodoyuArray::intval($ignoreIDs, true, true);
		$staffPersons	= self::searchStaff($searchWords, $ignore);
		$staffItems		= array();

		foreach($staffPersons as $person) {
			$staffItems[] = array(
				'id'		=> $person['id'],
				'label'		=> $person['label'],
				'className'	=> 'typeStaff'
			);
		}

		return $staffItems;
	}



	/**
	 * Get matching persons as list with id and label key
	 *
	 * @param	String[]	$searchWords
	 * @param	Integer[]	$ignoreIDs
	 * @return	Array[]
	 */
	public static function getMatchingPersons(array $searchWords, array $ignoreIDs = array()) {
		$persons	= self::searchPersons($searchWords, 30, 0, $ignoreIDs);
		$personItems= array();

		foreach($persons as $personData) {
			$person	= self::getPerson($personData['id']);

			$personItems[] = array(
				'id'		=> $person['id'],
				'label'		=> $person->getLabel(),
				'className'	=> 'typePerson'
			);
		}

		return $personItems;
	}



	/**
	 * Get matching persons with email
	 *
	 * @param	Array	$searchWords
	 * @param	Array	$ignoreIDs
	 * @return	Array
	 */
	public static function getMatchingEmailPersons(array $searchWords, array $ignoreIDs = array()) {
		$persons	= self::searchPersons($searchWords, 30, 0, $ignoreIDs, true);
		$personItems= array();

		foreach($persons as $personData) {
			$person	= self::getPerson($personData['id']);

			$personItems[] = array(
				'id'		=> $person['id'],
				'label'		=> $person->getLabel(true),
				'className'	=> 'typeEmailPerson'
			);
		}

		return $personItems;
	}



	/**
	 * Get matching email receiver tuples for active persons
	 *
	 * @param	String[]		$searchWords
	 * @param	String[]		$ignoreTuples
	 * @param	Array			$params
	 * @return	String[]
	 */
	public static function getMatchingEmailReceiversActivePersons(array $searchWords, array $ignoreTuples = array(), array $params = array()) {
		$ignoreIDs	= array();
		$tuples		= array();

		foreach($ignoreTuples as $ignoreTuple) {
			list($type, $idRecord) = explode(':', $ignoreTuple, 2);

			if( $type === 'contactperson' ) {
				$ignoreIDs[] = $idRecord;
			}
		}

		$persons	= self::searchPersons($searchWords, 30, 0, $ignoreIDs, true);

		foreach($persons as $person) {
			$tuples[] = 'contactperson:' . $person['id'];
		}

		return $tuples;
	}



	/**
	 * Get matching email receiver tuples for contact infos
	 *
	 * @param	String[]		$searchWords
	 * @param	String[]		$ignoreTuples
	 * @param	Array			$params
	 * @return	String[]
	 */
	public static function getMatchingEmailReceiversContactInfo(array $searchWords, array $ignoreTuples = array(), array $params = array()) {
		$ignoreIDs	= array();
		$tuples		= array();

		foreach($ignoreTuples as $ignoreTuple) {
			list($type, $idRecord) = explode(':', $ignoreTuple, 2);

			if( $type === 'contactinfo' ) {
				$ignoreIDs[] = $idRecord;
			}
		}

		$contactInfoIDs	= TodoyuContactContactInfoManagerPerson::getMatchingEmailContactInfoIDs($searchWords, $ignoreIDs, $params);

		foreach($contactInfoIDs as $idContactInfo) {
			$tuples[] = 'contactinfo:' . $idContactInfo;
		}

		return $tuples;
	}



	/**
	 * Link a person to a company
	 *
	 * @param	Integer		$idPerson
	 * @param	Integer		$idCompany
	 * @param	Integer		$idWorkAddress
	 * @param	Integer		$idJobType
	 * @param	Array		$extraData
	 */
	public static function addCompanyLink($idPerson, $idCompany, $idWorkAddress = 0, $idJobType = 0, array $extraData = array()) {
		$extraData['id_workaddress']= intval($idWorkAddress);
		$extraData['id_jobtype'] 	= intval($idJobType);

		TodoyuDbHelper::addMMLink('ext_contact_mm_company_person', 'id_person', 'id_company', $idPerson, $idCompany, $extraData);
	}



	/**
	 * Project records which the given person is assigned to
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getProjectOfPerson($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= 'p.*, mm.id_role';
		$table	= 'ext_project_project as p,
					ext_project_mm_project_person as mm';
		$where	= 'p.id = mm.id_project AND mm.id_person = ' . $idPerson
					. ' AND p.deleted = 0';

		$group	= 'p.id';

		return Todoyu::db()->getArray($fields, $table, $where, $group);
	}
}
?>