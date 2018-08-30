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
 * Company Manager
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyManager {

	/**
	 * Default ext table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_company';

	/**
	 * @var	String
	 */
	const contactTypeKey	= 'company';



	/**
	 * Get form object for company quick creation
	 *
	 * @param	Integer		$idCompany
	 * @return	TodoyuForm
	 */
	public static function getQuickCreateForm($idCompany = 0) {
		$idCompany	= intval($idCompany);

			// Construct form object
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$form		= TodoyuFormManager::getForm($xmlPath, $idCompany);

		$form->getField('date_enter')->setValue(NOW);
		$form->getField('date_enter')->setValue(NOW);

			// Adjust form to needs of quick creation wizard
		$form->setAttribute('action', 'index.php?ext=contact&amp;controller=quickcreatecompany');
		$form->setAttribute('onsubmit', 'return false');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.QuickCreateCompany.save(this.form)');
		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.removeUnusedImages(this.form);Todoyu.Popups.close(\'quickcreate\')');

		return $form;
	}



	/**
	 * Get IDs of all companies marked as "internal"
	 *
	 * @return	Array
	 */
	public static function getInternalCompanyIDs() {
		$fields	= 'id';
		$table	= self::TABLE;
		$where	= '		deleted		= 0
					AND	is_internal	= 1';

		return Todoyu::db()->getColumn($fields, $table, $where, '', '', '', 'id');
	}



	/**
	 * Get a company object
	 *
	 * @param	Integer					$idCompany
	 * @return	TodoyuContactCompany
	 */
	public static function getCompany($idCompany) {
		$idCompany	= intval($idCompany);

		return TodoyuRecordManager::getRecord('TodoyuContactCompany', $idCompany);
	}



	/**
	 * Get all company records
	 *
	 * @param	Array		$fields			Custom field list (instead of *)
	 * @param	String		$where			Extra WHERE clause
	 * @return	Array
	 */
	public static function getAllCompanies(array $fields = array(), $where = '') {
		$fields	= sizeof($fields) === 0 ? '*' : implode(',', TodoyuSql::escapeArray($fields));
		$table	= self::TABLE;
		$where	= ($where === '' ? '' : $where . ' AND ') . 'deleted = 0';
		$order	= 'title';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Save company data as record
	 *
	 * @param	Array		$data
	 * @return	Integer		Company ID
	 */
	public static function saveCompany(array $data) {
		$idCompany	= intval($data['id']);

		if( $idCompany === 0 ) {
			$idCompany	= self::addCompany();
		}

		if( $data['image_id'] != 0 ) {
			TodoyuContactImageManager::renameStorageFolder(self::contactTypeKey, $data['image_id'], $idCompany);
		}

		unset($data['image_id']);

			// Save own external fields
		$data	= self::saveCompanyForeignRecords($data, $idCompany);

			// Call hooked save functions
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$data		= TodoyuFormHook::callSaveData($xmlPath, $data, $idCompany);

			// Update company data with basic field data
		self::updateCompany($idCompany, $data);
			// Remove company record from cache
		self::removeFromCache($idCompany);

		return $idCompany;
	}



	/**
	 * Add a company record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addCompany(array $data = array()) {
		$idCompany = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('contact', 'company.add', array($idCompany));

		return $idCompany;
	}



	/**
	 * Update a company record
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$data
	 */
	public static function updateCompany($idCompany, array $data) {
		$idCompany	= intval($idCompany);

		TodoyuRecordManager::updateRecord(self::TABLE, $idCompany, $data);

		TodoyuHookManager::callHook('contact', 'company.update', array($idCompany, $data));
	}



	/**
	 * Delete a company in the database (set deleted flag to 1)
	 *
	 * @param	Integer		$idCompany
	 */
	public static function deleteCompany($idCompany) {
		$idCompany	= intval($idCompany);

		$data	= array(
			'deleted'	=> 1
		);

		self::updateCompany($idCompany, $data);

		TodoyuHookManager::callHook('contact', 'company.delete', array($idCompany));
	}



	/**
	 * Save company foreign records form hook
	 * Extract:
	 * - contactinfo
	 * - address
	 * - person
	 *
	 * @param	Array		$data			Company form data
	 * @param	Integer		$idCompany		Company ID
	 * @return	Array
	 */
	public static function saveCompanyForeignRecords(array $data, $idCompany) {
		$idCompany = intval($idCompany);

			// Contact info
		if( isset($data['contactinfo']) ) {
			$contactInfoIDs	= TodoyuArray::getColumn($data['contactinfo'], 'id');

				// Delete all contact infos which are no longer linked
			self::deleteRemovedContactInfos($idCompany, $contactInfoIDs);

				// If contact infos submitted
			if( sizeof($data['contactinfo']) > 0 ) {
				$infoIDs	= array();
				foreach($data['contactinfo'] as $contactInfo) {
					$infoIDs[] = TodoyuContactContactInfoManager::saveContactInfos($contactInfo);
				}

				self::linkContactInfos($idCompany, $infoIDs);
			}

			unset($data['contactinfo']);
		}

			// Address
		if( isset($data['address']) ) {
			$addressIDs	= TodoyuArray::getColumn($data['address'], 'id');

				// Delete all addresses which are no longer linked
			self::deleteRemovedAddresses($idCompany, $addressIDs);

				// If addresses submitted
			if( is_array($data['address']) ) {
				$addressIDs	= array();
				foreach($data['address'] as $address) {
					$addressIDs[] =  TodoyuContactAddressManager::saveAddress($address);
				}

				self::linkAddresses($idCompany, $addressIDs);
			}

			unset($data['address']);
		}

			// Person
		if( isset($data['person']) ) {
				// Remove all person links
			self::removeAllPersons($idCompany);

				// Save person links
			if( sizeof($data['person']) > 0 ) {
				foreach($data['person'] as $index => $person) {
						// Prepare data form mm-table
					$data['person'][$index]['id_person']	= $person['id'];
					$data['person'][$index]['id_company']	= $idCompany;
					unset($data['person'][$index]['id']);
				}

				self::savePersonLinks($idCompany, $data['person']);
			}

			unset($data['person']);
		}

		return $data;
	}



	/**
	 * Delete all contactinfos except the given ones
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$currentContactInfoIDs
	 * @return	Integer		Deleted records
	 */
	public static function deleteRemovedContactInfos($idCompany, array $currentContactInfoIDs) {
		return TodoyuContactContactInfoManager::deleteLinkedContactInfos(self::contactTypeKey, $idCompany, $currentContactInfoIDs, 'id_company');
	}



	/**
	 * Link contact infos to company
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$contactInfoIDs
	 */
	public static function linkContactInfos($idCompany, array $contactInfoIDs) {
		TodoyuDbHelper::addMMLinks('ext_contact_mm_company_contactinfo', 'id_company', 'id_contactinfo', $idCompany, $contactInfoIDs);
	}



	/**
	 * Delete all company addresses which are no longer active
	 *
	 * @param	String		$idCompany
	 * @param	Array		$currentAddressIDs	Active addresses which will not be deleted
	 * @return	Integer
	 */
	public static function deleteRemovedAddresses($idCompany, array $currentAddressIDs) {
		return TodoyuContactAddressManager::deleteLinkedAddresses('ext_contact_mm_company_address', $idCompany, $currentAddressIDs, 'id_company');
	}



	/**
	 * Link addresses to company
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$addressIDs
	 * @return	Integer
	 */
	public static function linkAddresses($idCompany, array $addressIDs) {
		return TodoyuDbHelper::addMMLinks('ext_contact_mm_company_address', 'id_company', 'id_address', $idCompany, $addressIDs);
	}



	/**
	 * Save linked person and linking data
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$linkData
	 */
	public static function savePersonLinks($idCompany, array $linkData) {
		TodoyuDbHelper::saveExtendedMMLinks('ext_contact_mm_company_person', 'id_company', 'id_person', $idCompany, $linkData);
	}



	/**
	 * Remove person links which are no longer active
	 * Person stays untouched, only the link with the extra data will be removed
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$personIDs
	 */
	public static function removeRemovedPersons($idCompany, array $personIDs) {
		TodoyuDbHelper::deleteOtherMmLinks('ext_contact_mm_company_person', 'id_company', 'id_person', $idCompany, $personIDs);
	}



	/**
	 * Add a person to a company (linked)
	 * Additional data are the working address and the job type
	 * @deprecated
	 *
	 * @param	Integer		$idCompany
	 * @param	Integer		$idPerson
	 * @param	Integer		$idWorkAddress
	 * @param	Integer		$idJobType
	 */
	public static function addPerson($idCompany, $idPerson, $idWorkAddress = 0, $idJobType = 0) {
		$data	= array(
			'id_company'	=> intval($idCompany) ,
			'id_person'		=> intval($idPerson),
			'id_workaddress'=> intval($idWorkAddress),
			'id_jobtype'	=> intval($idJobType)
		);

		Todoyu::db()->addRecord('ext_contact_mm_company_person', $data);
	}



	/**
	 * Remove person from company
	 *
	 * @param	Integer		$idCompany
	 * @param	Integer		$idPerson
	 */
	public static function removePerson($idCompany, $idPerson) {
		$idCompany	= intval($idCompany);
		$idPerson	= intval($idPerson);

		TodoyuDbHelper::removeMMrelation('ext_contact_mm_company_person', 'id_company', 'id_person', $idCompany, $idPerson);
	}



	/**
	 * Delete all persons of a company (only the link will be removed, not the persons)
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeAllPersons($idCompany) {
		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_person', 'id_company', $idCompany);
	}



	/**
	 * Remove all contact information of a company
	 *
	 * @todo	Keep the contact information, or delete? Dead records at the moment. See also next functions for same problem
	 * @param	Integer		$idCompany
	 */
	public static function removeContactinfoLinks($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_contactinfo', 'id_company', $idCompany);
	}



	/**
	 * Delete all linked contact info records of given company
	 *
	 * @todo	see comment in above function 'removeContactinfoLinks'
	 *
	 * @param	Integer		$idCompany
	 */
	public static function deleteContactinfos($idCompany) {
		TodoyuContactContactInfoManagerCompany::deleteContactInfos($idCompany);
	}



	/**
	 * Remove all address information of a company
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeAddressesLinks($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_address', 'id_company', $idCompany);
	}



	/**
	 * Get company label
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function getLabel($idCompany) {
		return self::getCompany($idCompany)->getLabel();
	}



	/**
	 * Search companies
	 *
	 * @param	String[]	$searchWords
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @return	Array
	 */
	public static function searchCompany(array $searchWords, $size = 100, $offset = 0) {
		$fields	= 'SQL_CALC_FOUND_ROWS *';
		$where	= ' deleted = 0';
		$table	= self::TABLE;
		$order	= 'title';
		$limit	= ($size != '') ? intval($offset) . ',' . intval($size) : '';

		if( sizeof($searchWords) ) {
			$searchFields	= array('title', 'shortname');

			$where	.= ' AND ' . TodoyuSql::buildLikeQueryPart($searchWords, $searchFields);
		}

			// Limit results to allowed person records
		if( ! TodoyuAuth::isAdmin() && ! Todoyu::allowed('contact', 'company:seeAllCompanies') ) {
			$allowedWhere	= TodoyuContactCompanyRights::getAllowedToBeSeenCompaniesWhereClause();

			$where .= ' AND ' . $allowedWhere;
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit);
	}



	/**
	 * Remove company object from cache
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeFromCache($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuRecordManager::removeRecordCache('TodoyuContactCompany', $idCompany);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idCompany);
	}



	/**
	 * Get person records of a company
	 *
	 * @param	Integer		$idCompany
	 * @param	String		$order
	 * @return	Array
	 */
	public static function getCompanyPersonRecords($idCompany, $order = '') {
		$idCompany	= intval($idCompany);

		$fields	= '	mm.*,
					p.*';
		$tables	= '	ext_contact_person p,
					ext_contact_mm_company_person mm';
		$where	= '		mm.id_person	= p.id
					AND	mm.id_company	= ' . $idCompany .
				  ' AND	p.deleted		= 0';
		$order	= empty($order) ? 'mm.id' : $order;

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * Get contact info records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyContactinfoRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	c.*, t.category infotype_category';
		$tables	= '	ext_contact_contactinfotype t,
					ext_contact_contactinfo c,
					ext_contact_mm_company_contactinfo mm';
		$where	= '		mm.id_contactinfo	= c.id
					AND	mm.id_company		= ' . $idCompany .
				  ' AND	c.deleted			= 0' .
				  ' AND t.id				= c.id_contactinfotype';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get address records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyAddressRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	a.*';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_company_address mm';
		$where	= '		mm.id_address	= a.id
					AND	mm.id_company	= ' . $idCompany .
				  ' AND	a.deleted		= 0';
		$order	= ' a.is_preferred DESC';

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * Get project records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyProjectRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= 'p.*';
		$tables	= ' ext_project_project as p';
		$where	= ' p.id_company = ' . $idCompany
					. ' AND p.deleted = 0';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get the number of persons on a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Integer
	 */
	public static function getNumPersons($idCompany) {
		$idCompany	= intval($idCompany);

		$field	= 'id_person';
		$table	= 'ext_contact_mm_company_person';
		$where	= 'id_company = ' . $idCompany;

		$persons= Todoyu::db()->getArray($field, $table, $where);

		return sizeof($persons);
	}



	/**
	 * Get address label of the company
	 * Compiled from the first address record. Using, street, zip and city
	 *
	 * @param	Integer		$idCompany
	 * @return	String
	 */
	public static function getCompanyAddressLabel($idCompany) {
		$idCompany	= intval($idCompany);

		$labelStreet= self::getCompanyStreetLabel($idCompany) .
		$labelPlace	=	  self::getCompanyPlaceLabel($idCompany);

		return $labelStreet . (!empty($labelStreet) ? ', ' : '') . $labelPlace;
	}




	/**
	 * Get place (zip and city) label of the company from the first address record.
	 *
	 * @param	Integer		$idCompany
	 * @return	String
	 */
	public static function getCompanyPlaceLabel($idCompany) {
		$idCompany	= intval($idCompany);

		$addresses	= self::getCompanyAddressRecords($idCompany);
		$label		= '';

		if( sizeof($addresses) > 0 ) {
			$address = $addresses[0];

			$label	= ($address['zip']	!== '' ? $address['zip'] . ' ' : '')
					. $address['city'];
		}

		return $label;
	}



	/**
	 * Get street of first related address record of company
	 *
	 * @param	Integer		$idCompany
	 * @param	Boolean		$includeStreet
	 * @return	String
	 */
	public static function getCompanyStreetLabel($idCompany, $includeStreet = true) {
		$idCompany	= intval($idCompany);

		$addresses	= self::getCompanyAddressRecords($idCompany);
		$street	= '';

		if( sizeof($addresses) > 0 ) {
			$address = $addresses[0];
			$street	=	  ($address['street'] !== '' ? $address['street'] : '');
		}

		return $street;
	}



	/**
	 * Check whether a company has projects
	 *
	 * @param	Integer		$idCompany
	 * @return	Boolean
	 */
	public static function hasProjects($idCompany) {
		$idCompany	= intval($idCompany);

		return sizeof(self::getProjectIDs($idCompany)) > 0;
	}



	/**
	 * Get project IDs of the company
	 *
	 * @param	Integer		$idCompany
	 * @return	Integer[]
	 */
	public static function getProjectIDs($idCompany) {
		$idCompany	= intval($idCompany);

		$field	= 'id';
		$table	= 'ext_project_project';
		$where	= '		id_company	= ' . $idCompany .
				  ' AND	deleted		= 0';

		return Todoyu::db()->getColumn($field, $table, $where);
	}



	/**
	 * Gets the preview image for the form
	 *
	 * @param	TodoyuFormElement_Comment		$formElement
	 * @return	String
	 */
	public static function getPreviewImageForm(TodoyuFormElement_Comment $formElement) {
		return TodoyuContactImageManager::renderImageForm($formElement, self::contactTypeKey);
	}



	/**
	 * Get assigned project records of given company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getPhones($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	c.*';
		$tables	=  'ext_contact_contactinfo c,
					ext_contact_mm_company_contactinfo mm,
					ext_contact_contactinfotype t';
		$where	= '		mm.id_contactinfo	= c.id
					AND	mm.id_company		= ' . $idCompany .
				  ' AND	c.deleted			= 0' .
				  ' AND t.id = c.id_contactinfotype' .
				  ' AND t.category	= ' . CONTACT_INFOTYPE_CATEGORY_PHONE;
		$orderBy	= 'is_preferred DESC';

		return Todoyu::db()->getArray($fields, $tables, $where, '', $orderBy);
	}
}

?>