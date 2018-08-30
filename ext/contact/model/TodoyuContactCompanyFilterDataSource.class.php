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
 * Person filter data source
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyFilterDataSource {

	/**
	 * Get company filter definition label
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getLabel($definitions) {
		$definitions['value_label'] = TodoyuContactCompanyManager::getLabel($definitions['value']);

		return $definitions;
	}



	/**
	 * Get company autocompletion data
	 *
	 * @param	String	$input
	 * @param	Array	$formData
	 * @param	String	$name
	 * @return	Array
	 */
	public static function autocompleteCompanies($input, array $formData = array(), $name = '') {
		$result		= array();
		$searchWords= TodoyuArray::trimExplode(' ', $input, true);
		$companies	= TodoyuContactCompanyManager::searchCompany($searchWords);

		foreach($companies as $companyData) {
			$company	= TodoyuContactCompanyManager::getCompany($companyData['id']);
			$result[$companyData['id']] = $company->getTitle();
		}

		return $result;
	}



	/**
	 * Reduce company autocompletion to active company (is_notactive = 0) companies
	 *
	 * @param	String		$input
	 * @param	Array		$formData
	 * @param	String		$name
	 * @return	Array
	 */
	public static function autocompleteActiveCompanies($input, array $formData = array(), $name = ''){
		$result 	= array();
		$tempResult = self::autocompleteCompanies($input, $formData, $name);

		foreach($tempResult as $idCompany => $companyTitle) {
			$company	= TodoyuContactCompanyManager::getCompany($idCompany);
			if( !$company->isNotActive() ) {
				$result[$idCompany] = $companyTitle;
			}
		}

		return $result;
	}



	/**
	 * Get company label
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getCompanyLabel($definitions) {
		$idCompany	= intval($definitions['value']);
		$company	= TodoyuContactCompanyManager::getCompany($idCompany);

		$definitions['value_label'] = $company->getTitle();

		return $definitions;
	}



	/**
	 * Get options config of countries related to any company address
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getCountryOptions(array $definitions) {
		$definitions['options'] = TodoyuContactManager::getUsedCountryOptions('ext_contact_mm_company_address');

		return $definitions;
	}

}
?>