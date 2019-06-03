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
 * Address filter data source
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactAddressFilterDataSource {

	/**
	 * Search for (stored) cities by given search string from the auto-completion
	 *
	 * @param	String	$input
	 * @param	Array	$formData
	 * @param	String	$name
	 * @return	Array				array (id => label)
	 */
	public function autocompleteCities($input, array $formData = array(), $name = '') {
		$data = array();

		$cityNames		= TodoyuContactAddressManager::searchStoredCities($input);
		if( !empty($cityNames) ) {
			foreach($cityNames as $cityName) {
				$data[$cityName] = $cityName;
			}
		}

		return $data;
	}



	/**
	 * Prepare options of countries for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getCountryOptions(array $definitions) {
		$countries	= TodoyuStaticRecords::getCountryOptions();

		$options	= array();
		foreach($countries as $country) {
			$options[] = array(
				'label'		=> $country['label'],
				'value'		=> $country['value']
			);
		}

		$definitions['options'] = $options;

		return $definitions;
	}



	/**
	 * Prepare options of countries of any company for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getCompanyCountryOptions(array $definitions) {
		return TodoyuContactCompanyFilterDataSource::getCountryOptions($definitions);
	}



	/**
	 * Prepare options of countries of any person for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getPersonCountryOptions(array $definitions) {
		return TodoyuContactPersonFilterDataSource::getCountryOptions($definitions);
	}

}

?>