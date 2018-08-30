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
 * Person object
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPerson extends TodoyuBaseObject {

	/**
	 * Initialize person
	 *
	 * @param	Integer		$idPerson
	 */
	public function __construct($idPerson) {
		parent::__construct($idPerson, 'ext_contact_person');
	}



	/**
	 * Get the name label of the person
	 *
	 * @param	Boolean		$showEmail
	 * @param	Boolean		$lastnameFirst
	 * @param	Boolean		$showTitle
	 * @param	Integer		$idRole
	 * @param	Boolean		$showCompanyAbbr
	 * @return	String
	 */
	public function getLabel($showEmail = false, $lastnameFirst = true, $showTitle = false, $idRole = 0, $showCompanyAbbr = false) {
		 $label	= $this->getFullName($lastnameFirst);

		if( $showCompanyAbbr ) {
			$label .= ' (' . $this->getCompany()->getShortname(). ')';
		}

		if( $showTitle ) {
			/** @var	TodoyuRole		$role  */
			$role	= TodoyuRoleManager::getRole($idRole);
			$title	= $role->getTitle();
			$label	.= !empty($title) ? ', ' . $title : '';
		}

		 if( $showEmail ) {
			$email	= $this->getEmail(true);

			if( $email ) {
				$label	.= ' (' . $this->getEmail() . ')';
			}
		}

		return $label;
	}



	/**
	 * Get last name
	 *
	 * @return	String
	 */
	public function getLastName() {
		return $this->get('lastname');
	}



	/**
	 * Get first name
	 *
	 * @return	String
	 */
	public function getFirstName() {
		return $this->get('firstname');
	}



	/**
	 * Check whether person is an admin
	 *
	 * @return	Boolean
	 */
	public function isAdmin() {
		return intval($this->data['is_admin']) === 1;
	}



	/**
	 * Check whether person is a dummy user (e.g. room or other placeholder)
	 *
	 * @return	Boolean
	 */
	public function isDummy() {
		return intval($this->data['is_dummy']) === 1;
	}



	/**
	 * Get username
	 *
	 * @return	String
	 */
	public function getUsername() {
		return $this->get('username');
	}



	/**
	 * Get password
	 *
	 * @return	String
	 */
	public function getPassword() {
		return $this->get('password');
	}



	/**
	 * Check whether a person works in a internal company
	 *
	 * @return	Boolean
	 */
	public function isInternal() {
		if( !isset($this->cache['isInternal']) ) {
			$companies	= $this->getCompanies();
			$isInternal	= false;

			foreach($companies as $company) {
				if( $company->isInternal() ) {
					$isInternal = true;
					break;
				}
			}

			$this->cache['isInternal'] = $isInternal;
		}

		return $this->cache['isInternal'];
	}



	/**
	 * Check whether person is external (not in an internal company)
	 *
	 * @return	Boolean
	 */
	public function isExternal() {
		return !$this->isInternal();
	}



	/**
	 * Get IDs of the roles the person is a member of
	 *
	 * @return	Integer[]
	 */
	public function getRoleIDs() {
		if( !$this->isInCache('roleids') ) {
			$roleIDs	= TodoyuContactPersonManager::getRoleIDs($this->getID());

			$this->addToCache('roleids', $roleIDs);
		}

		return $this->getCacheItem('roleids');
	}



	/**
	 * Check whether user is assigned to role
	 *
	 * @param	Integer		$idRole
	 * @return	Boolean
	 */
	public function hasRole($idRole) {
		$idRole	= intval($idRole);

		return in_array($idRole, $this->getRoleIDs());
	}


	/**
	 * Check whether the user is assigned to any of the roles
	 *
	 * @param	Array	$roleIDs
	 * @return	Boolean
	 */
	public function hasAnyRole(array $roleIDs) {
		return sizeof(array_intersect($roleIDs, $this->getRoleIDs())) > 0;
	}



	/**
	 * Check whether the person has the given extension's right
	 *
	 * @param	String		$ext
	 * @param	String		$right
	 * @return	Boolean
	 */
	public function hasRight($ext, $right) {
		$roleIDs		= $this->getRoleIDs();
		$rolesRights	= TodoyuArray::flatten(TodoyuRightsManager::getExtRoleRights($ext, $roleIDs));

		return in_array($right, $rolesRights);
	}



	/**
	 * Get fullname of the person
	 *
	 * @param	Boolean		$lastnameFirst
	 * @return	String
	 */
	public function getFullName($lastnameFirst = false) {
		if( $this->getID() > 0 ) {
			if( $lastnameFirst ) {
				return $this->getLastname() . ' ' . $this->getFirstname();
			} else {
				return $this->getFirstname() . ' ' . $this->getLastname();
			}
		} else {
			return Todoyu::Label('core.global.system');
		}
	}



	/**
	 * Get contact email address
	 *
	 * @param	Boolean		$checkContactInfo
	 * @return	String|Boolean
	 */
	public function getEmail($checkContactInfo = true) {
		$email	= $this->getAccountEmail();

		if( $email === '' && $checkContactInfo ) {
			$contactInfoMailAddresses	= $this->getEmailContactInfos();

			if( sizeof($contactInfoMailAddresses) > 0 ) {
				$email	= trim($contactInfoMailAddresses[0]['info']);
			}
		}

		return $email === '' ? false : $email;
	}



	/**
	 * Get all contact info email addresses
	 *
	 * @param	String|null		$type
	 * @param	Boolean			$onlyPreferred
	 * @return	Array
	 */
	public function getEmailContactInfos($type = null, $onlyPreferred = false) {
		return TodoyuContactContactInfoManagerPerson::getEmails($this->getID(), $type, $onlyPreferred);
	}



	/**
	 * Get email of user account
	 *
	 * @return	String
	 */
	public function getAccountEmail() {
		return trim($this->get('email'));
	}



	/**
	 * Check whether the user has an email address for his account
	 *
	 * @param	Boolean		$checkContactInfo
	 * @return	Boolean
	 */
	public function hasEmail($checkContactInfo = false) {
		return trim($this->getEmail($checkContactInfo)) !== '';
	}



	/**
	 * Check whether person has an account email address
	 *
	 * @return	Boolean
	 */
	public function hasAccountEmail() {
		return trim($this->getAccountEmail()) !== '';
	}



	/**
	 * Check whether persons account is active
	 *
	 * @return	Boolean
	 */
	public function isActive() {
		return $this->isFlagSet('is_active');
	}





	/**
	 * Get current person's shortname
	 *
	 * @return	String
	 */
	public function getShortname() {
		return $this->get('shortname');
	}



	/**
	 * Get birthday timestamp
	 *
	 * @return	Integer
	 */
	public function getBirthday() {
		if( $this->hasBirthday() ) {
			return intval(strtotime($this->get('birthday')));
		} else {
			return 0;
		}
	}



	/**
	 * Check whether a birthday is set for person
	 *
	 * @return	Boolean
	 */
	public function hasBirthday() {
		return $this->get('birthday') !== '0000-00-00';
	}



	/**
	 * Get person comment
	 *
	 * @return	String
	 */
	public function getComment() {
		return $this->get('comment');
	}



	/**
	 * Get person locale preference
	 * Use system as fallback if not disabled by parameter
	 *
	 * @param	Boolean		$system			Use system locale when user locale not set yet
	 * @return	String
	 */
	public function getLocale($system = true) {
		$locale	= TodoyuContactPreferences::getLocale();

		if( !$locale && $system ) {
			$locale	= Todoyu::getLocale();
		}

		return $locale;
	}



	/**
	 * Get all company IDs of a person (a person can work for multiple companies)
	 *
	 * @return	Integer[]
	 */
	public function getCompanyIDs() {
		$field	= '	mm.id_company';
		$table	= '	ext_contact_mm_company_person mm,
					ext_contact_company c';
		$where	= '		mm.id_person	= ' . $this->getID() . '
					AND	mm.id_company	= c.id
					AND	c.deleted		= 0';

		return Todoyu::db()->getColumn($field, $table, $where, '', '', '', 'id_company');
	}



	/**
	 * Get all companies of the person
	 *
	 * @return	TodoyuContactCompany[]
	 */
	public function getCompanies() {
		$fields	= '	c.id';
		$table	= '	ext_contact_mm_company_person mm,
					ext_contact_company c';
		$where	= '	mm.id_person	= ' . $this->getID() . ' AND
					mm.id_company	= c.id AND
					c.deleted		= 0';

		$companyIDs	= Todoyu::db()->getColumn($fields, $table, $where, '', '', '', 'id');

		return TodoyuRecordManager::getRecordList('TodoyuContactCompany', $companyIDs);
	}



	/**
	 * Get "main" company of person
	 *
	 * @todo	There is not really a main company, we just take the first one
	 * @return	TodoyuContactCompany
	 */
	public function getCompany() {
		$companyIDs		= $this->getCompanyIDs();
		$firstCompanyID	= intval($companyIDs[0]);

		return TodoyuContactCompanyManager::getCompany($firstCompanyID);
	}



	/**
	 * Get timezone of the person
	 * The timezone is defined in the assigned working address of the person's company
	 *
	 * @return	String		Or FALSE if non defined
	 */
	public function getTimezone() {
		if( $this->getID() !== 0 ) {
			$field	= '	tz.timezone';
			$tables	= '	ext_contact_mm_company_person mmcp,
						ext_contact_address a,
						static_timezone tz';
			$where	= '		mmcp.id_person		= ' . $this->getID() .
					  ' AND	mmcp.id_workaddress	= a.id
						AND	a.id_timezone		= tz.id';

			$timezones	= Todoyu::db()->getArray($field, $tables, $where);

			if( sizeof($timezones) > 0 ) {
				return $timezones[0]['timezone'];
			}
		}

		return false;
	}



	/**
	 * loads all employers of a person
	 *
	 * @return	Array
	 */
	public function getEmployers() {
		$employers	= TodoyuContactPersonManager::getPersonCompanyRecords($this->getID());

		foreach($employers as $index => $employer) {
			$employers[$index]['workaddress'] = TodoyuContactAddressManager::getAddress($employer['id_workaddress']);
			$employers[$index]['jobtype'] = TodoyuContactJobTypeManager::getJobType($employer['id_jobtype']);
		}

		return $employers;
	}



	/**
	 * Get contact info records data
	 *
	 * @return	Array
	 */
	public function getContactInfoRecords() {
		return TodoyuContactPersonManager::getContactinfoRecords($this->getID());
	}



	/**
	 * Get addresses
	 *
	 * @return	TodoyuContactAddress[]
	 */
	public function getAddresses() {
		$field	= '	a.id';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_person_address mm';
		$where	= '		a.deleted		= 0'
				. ' AND mm.id_address	= a.id'
				. ' AND	mm.id_person	= ' . $this->getID();

		$addressIDs	= Todoyu::db()->getColumn($field, $tables, $where, '', '', '', 'id');

		return TodoyuRecordManager::getRecordList('TodoyuContactAddress', $addressIDs);
	}



	/**
	 * Get address records data
	 *
	 * @return	Array
	 */
	public function getAddressRecords() {
		return TodoyuContactPersonManager::getAddressRecords($this->getID());
	}



	/**
	 * Get role records data
	 *
	 * @return	Array
	 */
	public function getRoleRecords() {
		return TodoyuContactPersonManager::getRoles($this->getID());
	}



	/**
	 * Get project records data
	 *
	 * @return	Array
	 */
	private function getPersonProjectRecords() {
		return TodoyuContactPersonManager::getProjectOfPerson($this->getID());
	}



	/**
	 * Get salutation key
	 *
	 * @return	String		'm' or 'w'
	 */
	public function getSalutationKey() {
		return $this->get('salutation');
	}



	/**
	 * Parses the salutation to label
	 *
	 * @return	String
	 */
	public function getSalutationLabel() {
		$salutationKey  = $this->getSalutationKey();

		if( !empty($salutationKey) ) {
			return Todoyu::Label('contact.ext.person.attr.salutation.' . $salutationKey);
		}

		return '';
	}



	/**
	 * Get person's phone contact infos: all stored phone numbers
	 *
	 * @return	Array
	 */
	public function getPhones() {
		return TodoyuContactContactInfoManagerPerson::getPhones($this->getID());
	}



	/**
	 * Get first phone number of person
	 *
	 * @return	String|Boolean
	 */
	public function getPhone() {
		$phones	= $this->getPhones();

		if( sizeof($phones) > 0 ) {
			return $phones[0]['info'];
		} else {
			return false;
		}
	}



	/**
	 * @return bool
	 */
	public function getPhoneRecord() {
		$phones	= $this->getPhones();

		if( sizeof($phones) > 0 ) {
			return $phones[0];
		} else {
			return false;
		}
	}



	/**
	 * Get main company (first linked) of the person
	 *
	 * @return	TodoyuContactCompany
	 */
	public function getMainCompany() {
		$field	= 'id_company';
		$table	= ' ext_contact_mm_company_person mm,
					ext_contact_company c';
		$where	= '		mm.id_company	= c.id
					AND c.deleted		= 0
					AND mm.id_person	= ' . $this->getID();
		$limit	= 1;

		$idCompany	= Todoyu::db()->getFieldValue($field, $table, $where, null, null, $limit);

		return TodoyuContactCompanyManager::getCompany($idCompany);
	}



	/**
	 * Get SMTP account ID
	 *
	 * @return	Integer
	 */
	public function getSmtpAccountID() {
		return $this->getInt('id_smtpaccount');
	}



	/**
	 * Check whether person has configured an SMTP account
	 *
	 * @return	Boolean
	 */
	public function hasSmtpAccount() {
		return $this->getSmtpAccountID() !== 0;
	}



	/**
	 * Get SMTP account
	 *
	 * @return TodoyuSysmanagerSmtpAccount
	 */
	public function getSmtpAccount() {
		return TodoyuSysmanagerSmtpAccountManager::getAccount($this->getSmtpAccountID());
	}



	/**
	 * Get mail signature
	 *
	 * @return	String
	 */
	public function getMailSignature() {
		return rtrim($this->get('mail_signature'));
	}



	/**
	 * Get signature as html
	 * Just wrap with <pre> tags to preserve the formatting
	 *
	 * @return	String
	 */
	public function getMailSignatureAsHtml() {
		return '<pre>' . $this->getMailSignature() . '</pre>';
	}



	/**
	 * Check whether person has a mail signature
	 *
	 * @return	Boolean
	 */
	public function hasMailSignature() {
		return trim($this->getMailSignature()) !== '';
	}



	/**
	 * Get correspondence locale
	 *
	 * @return	String
	 */
	public function getLocaleCorrespondence() {
		$localeCorrespondence	= $this->get('locale_correspondence');

		if( empty($localeCorrespondence) ) {
			$localeCorrespondence	= $this->getLocale();
		}

		return $localeCorrespondence;
	}



	/**
	 * Check whether the person has set a correspondence locale
	 *
	 * @return	Boolean
	 */
	public function hasLocaleCorrespondence() {
		return trim($this->get('locale_correspondence')) !== '';
	}



	/**
	 * Get all email addresses which are linked with the person
	 * - Account address
	 * - Contact info addresses
	 * - Smtp address
	 *
	 * @return	String[]
	 */
	public function getAllMailAddresses() {
		$addresses	= array();

		if( $this->isActive() ) {
			$addresses[] = $this->getAccountEmail();
		}

		$mailContactInfos = $this->getEmailContactInfos();
		if( sizeof($mailContactInfos) ) {
			$contactInfoAddresses	= TodoyuArray::getColumn($mailContactInfos, 'info');
			$addresses	= array_merge($addresses, $contactInfoAddresses);
		}

		if( $this->hasSmtpAccount() ) {
			$addresses[] = $this->getSmtpAccount()->getUsername();
		}

		return array_unique($addresses);
	}



	/**
	 * Get mail receiver
	 *
	 * @return	TodoyuMailReceiverInterface
	 */
	public function getMailReceiver() {
		return TodoyuMailReceiverManager::getMailReceiver($this->getID());
	}



	/**
	 * Load all foreign records of a person
	 */
	public function loadForeignData() {
		if( !$this->has('company') ) {
			$this->data['company']		= $this->getEmployers();
			$this->data['contactinfo']	= $this->getContactInfoRecords();
			$this->data['address']		= $this->getAddressRecords();
			$this->data['role']			= $this->getRoleRecords();
			$this->data['projects']		= $this->getPersonProjectRecords();
		}
	}



	/**
	 * Get person template data
	 *
	 * @param	Boolean		$loadForeignData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false) {
		if( $loadForeignData ) {
			self::loadForeignData();
		}

		$this->data['fullname'] = $this->getFullName();

		return parent::getTemplateData();
	}
}

?>