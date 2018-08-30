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
 * Export manager for person - records
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonExportManager {

	/**
	 * Exports persons as CSV file
	 *
	 * @param	Array	$searchWords
	 */
	public static function exportCSV(array $searchWords) {
		$persons	= TodoyuContactPersonManager::searchPersons($searchWords, '');
		$exportData	= self::getExportDataByPersonsData($persons);

		self::sendCSVfromData($exportData);
	}



	/**
	 * Exports data of companies of given IDs as CSV file
	 *
	 * @param	Integer[]	$personIDs
	 */
	public static function exportCSVfromIDs(array $personIDs) {
		$personIDs	= TodoyuArray::intval($personIDs);
		$exportData	= self::getExportDataByPersonIDs($personIDs);

		self::sendCSVfromData($exportData);
	}



	/**
	 * Send CSV for download
	 *
	 * @param	Array	$exportData
	 */
	public static function sendCSVfromData(array $exportData) {
		$export = new TodoyuExportCSV($exportData);
		$export->download('todoyu_person_export_' . date('YmdHis') . '.csv');
	}



	/**
	 * Prepares the given persons to be exported
	 *
	 * @param	Array	$personsData
	 * @return	Array
	 */
	protected static function getExportDataByPersonsData(array $personsData) {
		$exportData = array();

		TodoyuCache::disable();
		foreach($personsData as $personData) {
			$exportData[]	= self::getPersonExportData($personData['id']);
		}
		TodoyuCache::enable();

		return $exportData;
	}



	/**
	 * Prepares data of given persons of given IDs for export
	 *
	 * @param	Array	$personIDs
	 * @return	Array
	 */
	public static function getExportDataByPersonIDs(array $personIDs) {
		$personIDs = TodoyuArray::intval($personIDs, true, true);
		TodoyuCache::disable();
		$exportData = array();
		foreach($personIDs as $idPerson) {
			$exportData[]	= self::getPersonExportData($idPerson);
		}
		TodoyuCache::enable();
		return $exportData;
	}



	/**
	 * Parses person-data for export
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	protected static function getPersonExportData($idPerson) {
		$person			= TodoyuContactPersonManager::getPerson($idPerson);
		$companyPrefix	= Todoyu::Label('contact.ext.person.attr.company');
		$rolePrefix		= Todoyu::Label('core.global.role');
		$addressPrefix	= Todoyu::Label('contact.ext.address');
		$labelYes		= Todoyu::Label('core.global.yes');
		$labelNo		= Todoyu::Label('core.global.no');

		$exportData = array(
			Todoyu::Label('contact.ext.person.attr.id')			=> $person->getID(),
			Todoyu::Label('core.global.date_create')			=> TodoyuTime::format($person->getDateCreate()),
			Todoyu::Label('core.global.date_update')			=> TodoyuTime::format($person->getDateUpdate()),
			Todoyu::Label('core.global.id_person_create')		=> $person->getPersonCreate()->getFullName(),

			Todoyu::Label('contact.ext.person.attr.lastname')	=> $person->getLastName(),
			Todoyu::Label('contact.ext.person.attr.firstname')	=> $person->getFirstName(),
			Todoyu::Label('contact.ext.person.attr.salutation')	=> $person->getSalutationLabel(),
			Todoyu::Label('contact.ext.person.attr.shortname')	=> $person->getShortname(),

			Todoyu::Label('contact.ext.person.attr.username')	=> $person->getUsername(),
			Todoyu::Label('contact.ext.person.attr.email')		=> $person->getEmail(),
			Todoyu::Label('contact.ext.person.attr.is_admin')	=> $person->isAdmin() ? $labelYes : $labelNo,
			Todoyu::Label('contact.ext.person.attr.is_active')	=> $person->isActive() ? $labelYes : $labelNo,
			Todoyu::Label('contact.ext.person.attr.birthday')	=> $person->hasBirthday() ? TodoyuTime::format($person->getBirthday(), 'date') : '',
		);

			// Map & prepare company records of person
		$counter = 1;
		foreach($person->getCompanies() as $company) {
			$exportData[$companyPrefix . '_' . $counter]	= $company->getTitle();

			$counter++;
		}

			// Map & prepare contactinfo records of person
		$counter = 1;
		foreach($person->getContactInfoRecords() as $contactinfo) {
			$prefix			= Todoyu::Label('contact.ext.contactinfo') . '_' . $counter . '_';
			$contactinfoObj	= TodoyuContactContactInfoManager::getContactinfo($contactinfo['id']);

			$exportData[$prefix . Todoyu::Label('contact.ext.contactinfo.attr.type')]	= $contactinfoObj->getTypeLabel();
			$exportData[$prefix . Todoyu::Label('contact.ext.contactinfo.attr.info')]	= $contactinfo['info'];
			$exportData[$prefix . Todoyu::Label('core.form.is_preferred')]				= $contactinfo['preferred'] ? $labelYes : $labelNo;

			$counter++;
		}

			// Map & prepare address records of person
		$counter = 1;
		foreach($person->getAddresses() as $address) {
			$prefix			= $addressPrefix . '_' . $counter . '_';

			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.addresstype')]= $address->getAddressTypeLabel();
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.street')]		= $address->getStreet();
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.postbox')]	= $address->getPostbox();
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.zip')]		= $address->getZip();
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.city')]		= $address->getCity();
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.region')]		= $address->getRegionLabel();
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.country')]	= $address->getCountry()->getLabel();
			$exportData[$prefix . Todoyu::Label('core.form.is_preferred')]				= $address->isPreferred() ? $labelYes : $labelNo;
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.comment')]	= $address->getComment();

			$counter++;
		}

			// Map & prepare role records of person
		foreach($person->getRoleRecords() as $index => $role) {
			$exportData[$rolePrefix . '_' . ($index + 1)]	= $role['title'];
		}

		$exportData = TodoyuHookManager::callHookDataModifier('contact', 'personCSVExportParseData', $exportData, array('person'=>$person));

		return $exportData;
	}
}

?>