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
 * Export manager for companies
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyExportManager {

	/**
	 * Exports companies as CSV file
	 *
	 * @param	Array	$searchWords
	 */
	public static function exportCSV(array $searchWords) {
		$companies	= TodoyuContactCompanyManager::searchCompany($searchWords, '');
		$csvData	= self::getExportDataByCompaniesData($companies);

		self::sendCSVfromData($csvData);
	}



	/**
	 * Exports data of companies of given IDs as CSV file
	 *
	 * @param	Integer[]	$companyIDs
	 */
	public static function exportCSVfromIDs(array $companyIDs) {
		$companyIDs = TodoyuArray::intval($companyIDs);
		$exportData	= self::getExportDataByCompanyIDs($companyIDs);

		self::sendCSVfromData($exportData);
	}



	/**
	 * Send CSV for download
	 *
	 * @param	Array	$exportData
	 */
	public static function sendCSVfromData(array $exportData) {
		$export = new TodoyuExportCSV($exportData);
		$export->download('todoyu_company_export_' . date('YmdHis') . '.csv');
	}



	/**
	 * Prepares data of given companies for export
	 *
	 * @param	Array	$companiesData
	 * @return	Array
	 */
	public static function getExportDataByCompaniesData(array $companiesData) {
		$exportData = array();

		TodoyuCache::disable();
		foreach($companiesData as $companyData) {
			if( intval($companyData['id']) !== 0 ) {
				$exportData[]	= self::getCompanyExportData($companyData['id']);
			}

		}
		TodoyuCache::enable();
		return $exportData;
	}



	/**
	 * Prepares data of given companies for export
	 *
	 * @param	Array	$companyIDs
	 * @return	Array
	 */
	public static function getExportDataByCompanyIDs(array $companyIDs) {
		$companyIDs = TodoyuArray::intval($companyIDs);

		$exportData = array();
		TodoyuCache::disable();
		foreach($companyIDs as $count => $idCompany) {
			if( $idCompany !== 0 ) {
				$exportData[]	= self::getCompanyExportData($idCompany);
			}
		}
		TodoyuCache::enable();
		return $exportData;
	}



	/**
	 * Parses company data for export
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyExportData($idCompany) {
		$company			= TodoyuContactCompanyManager::getCompany($idCompany);
		$creator			= $company->getPersonCreate();
		$contactInfoPrefix	= Todoyu::Label('contact.ext.contactinfo');
		$addressPrefix		= Todoyu::Label('contact.ext.address');
		$employeePrefix		= Todoyu::Label('contact.ext.company.attr.person');
		$labelYes			= Todoyu::Label('core.global.yes');
		$labelNo			= Todoyu::Label('core.global.no');

		$exportData = array(
			Todoyu::Label('contact.ext.company.attr.id')			=> $company->getID(),
			Todoyu::Label('core.global.date_create')				=> TodoyuTime::format($company->getDateCreate()),
			Todoyu::Label('core.global.date_update')				=> TodoyuTime::format($company->getDateUpdate()),
			Todoyu::Label('core.global.id_person_create')			=> $creator ? $creator->getFullName() : '',
			Todoyu::Label('contact.ext.company.attr.title')			=> $company->getTitle(),
			Todoyu::Label('contact.ext.company.attr.shortname')		=> $company->getShortname(),
			Todoyu::Label('contact.ext.company.attr.is_internal')	=> $company->isInternal() ? $labelYes : $labelNo,
			Todoyu::Label('contact.ext.company.attr.is_notactive')	=> $company->isNotActive() ? $labelYes : $labelNo
		);

			// Map & prepare contactinfo records of company
		$counter = 1;
		foreach($company->getContactInfoRecords() as $index => $contactinfo) {
			$prefix			= $contactInfoPrefix . '_' . $counter . '_';
			$contactinfoObj	= TodoyuContactContactInfoManager::getContactinfo($contactinfo['id']);

			$exportData[$prefix . Todoyu::Label('contact.ext.contactinfo.attr.type')]	= $contactinfoObj->getTypeLabel();
			$exportData[$prefix . Todoyu::Label('contact.ext.contactinfo.attr.info')]	= $contactinfo['info'];
			$exportData[$prefix . Todoyu::Label('core.form.is_preferred')]				= $contactinfo['is_preferred'] ? $labelYes : $labelNo;

			$counter++;
		}

			// Map & prepare address records of company
		$counter = 1;
		foreach($company->getAddresses() as $address) {
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

			// Map & prepare employee records of company
		$counter = 1;
		$contactInfoTypes	= TodoyuContactContactInfoTypeManager::getContactInfoTypes(true);

		foreach($company->getEmployeesRecords() as $personData) {
			$exportData[$employeePrefix . '_' . $counter]	= $personData['firstname'] . ' ' . $personData['lastname'];

				// Add contact infos of categories "email" and "phone" per employee
			$contactInfos	= TodoyuContactPersonManager::getPerson($personData['id'])->getContactInfoRecords();
			foreach($contactInfos as $contactInfo) {
				$labelContactInfoType	= $contactInfoTypes[$contactInfo['id_contactinfotype']] ['title'];
				$exportData[$employeePrefix . '_' . $counter . '_' . $labelContactInfoType] = $contactInfo['info'];
			}

			$counter++;
		}

		$exportData = TodoyuHookManager::callHookDataModifier('contact', 'companyCSVExportParseData', $exportData, array('company'=>$company));

		return $exportData;
	}

}

?>