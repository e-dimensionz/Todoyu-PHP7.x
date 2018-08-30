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
class TodoyuContactPersonFilterDataSource {

	/**
	 * Get auto-complete suggestions list for person
	 *
	 * @param	String		$searchWord
	 * @param	Array		$formData
	 * @param	String		$name
	 * @return	Array
	 */
	public static function autocompletePersons($searchWord, array $formData = array(), $name = '') {
		$searchWords= TodoyuArray::trimExplode(' ', $searchWord, true);
		$persons	= TodoyuContactPersonManager::searchPersons($searchWords);
		$data 		= array();

		foreach($persons as $person) {
			$data[$person['id']] = TodoyuContactPersonManager::getLabel($person['id'], false, true, true);
		}

		return $data;
	}



	/**
	 * Get person filter definition label
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getLabel($definitions) {
		$definitions['value_label'] = TodoyuContactPersonManager::getLabel($definitions['value']);

		return $definitions;
	}



	/**
	 * Prepare options of salutations for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getSystemRoleOptions(array $definitions) {
		$options	= array();
		$roles	= TodoyuRoleManager::getAllRoles();

		foreach($roles as $role) {
			$label  = $role['title'] . ($role['is_active'] ? '' : ' (inactive)');

			$options[] = array(
				'label'		=> $label,
				'value'		=> $role['id']
			);
		}

		$definitions['options'] = $options;

		return $definitions;
	}



	/**
	 * Prepare options of salutations for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getJobTypeOptions(array $definitions) {
		$options	= array();
		$jobtypes	= TodoyuContactJobTypeManager::getAllJobTypes();
		foreach($jobtypes as $jobtype) {
			$label	= $jobtype['title'];

			$options[] = array(
				'label'		=> $label,
				'value'		=> $jobtype['id']
			);
		}

		$definitions['options'] = $options;

		return $definitions;
	}



	/**
	 * Prepare options of salutations for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getSalutationOptions(array $definitions) {
		$definitions['options'] = array(
			array(
				'index'	=> '1',
				'value'	=> 'm',
				'key'	=> 'mr',
				'class'	=> 'salutationMale',
				'label'	=> Todoyu::Label('contact.ext.person.attr.salutation.m')
			),
			array(
				'index'	=> '2',
				'value'	=> 'f',
				'key'	=> 'mrs',
				'class'	=> 'salutationFemale',
				'label'	=> Todoyu::Label('contact.ext.person.attr.salutation.f')
			)
		);

		return $definitions;
	}



	/**
	 * Get person label
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getPersonLabel($definitions) {
		$idPerson	= intval($definitions['value']);
		$person	= TodoyuContactPersonManager::getPerson($idPerson);

		$definitions['value_label'] = $person->getLabel();

		return $definitions;
	}



	/**
	 * Get options config of countries related to any person address
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getCountryOptions(array $definitions) {
		$definitions['options'] = TodoyuContactManager::getUsedCountryOptions('ext_contact_mm_person_address');

		return $definitions;
	}

}

?>