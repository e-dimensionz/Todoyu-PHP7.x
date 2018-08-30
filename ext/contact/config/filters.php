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
 * Filter configurations for the contact extension
 * Search widgets over persons, companies
 *
 * @package		Todoyu
 * @subpackage	Contact
 */

/**
 * Person filters
 */
Todoyu::$CONFIG['FILTERS']['PERSON'] = array(
	'key'	=> 'person',
	'config'	=> array(
		'label'				=> 'contact.ext.persons',
		'position'			=> 30,
		'resultsRenderer'	=> 'TodoyuContactPersonRenderer::renderPersonListingSearch',
		'class'				=> 'TodoyuContactPersonFilter',
		'defaultSorting'	=> 'ext_contact_person.lastname',
		'require'			=> 'contact.general:area'
	),
	'widgets' => array(
			// Optgroup persons
		'fulltext' => array(
			'label'		=> 'contact.filter.fulltext',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> false
			)
		),
		'name' => array(
			'label'		=> 'contact.filter.person.name',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> false
			)
		),
		'salutation' => array(
			'label'		=> 'contact.filter.person.salutation',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> false,
				'size'		=> 2,
				'FuncRef'	=> 'TodoyuContactPersonFilterDataSource::getSalutationOptions',
				'negation'	=> false
			)
		),
		'contactinformation' => array(
			'label'		=> 'contact.filter.contactinformation',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> false
			)
		),
		'systemrole' => array(
			'label'		=> 'contact.filter.person.system_role',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 8,
				'FuncRef'	=> 'TodoyuContactPersonFilterDataSource::getSystemRoleOptions',
				'negation'	=> 'default'
			)
		),
//		'isActive'	=> array(
//			'label'		=> 'contact.filter.person.isActive',
//			'optgroup'	=> 'contact.filter.person.label',
//			'widget'	=> 'checkbox',
//			'internal'	=> true,
//			'wConf'		=> array(
//				'checked'	=> true,
//				'negation'	=> false
//			)
//		),

			// Optgroup companies
		'company' => array(
			'label'		=> 'contact.filter.person.company',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactCompanyFilterDataSource::autocompleteCompanies',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactCompanyFilterDataSource::getCompanyLabel',
				'negation'		=> 'default'
			)
		),
		'isInternal'	=> array(
			'label'		=> 'contact.filter.person.company.isInternal',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'checkbox',
			'internal'	=> true,
			'wConf'		=> array(
				'checked'	=> true
			)
		),
		'jobtype'		=> array(
			'label'		=> 'contact.ext.jobtype',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> false,
				'FuncRef'	=> 'TodoyuContactPersonFilterDataSource::getJobTypeOptions',
				'negation'	=> 'default'
			)
		),

			// Optgroup addresses
		'country'		=> array(
			'label'		=> 'contact.filter.address.country',
			'optgroup'	=> 'contact.filter.addresses.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> false,
				'FuncRef'	=> 'TodoyuContactAddressFilterDataSource::getPersonCountryOptions',
				'negation'	=> 'default'
			)
		),
		'zip'		=> array(
			'label'		=> 'contact.filter.address.zip',
			'optgroup'	=> 'contact.filter.addresses.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> false,
				'negation'		=> false
			)
		),
		'city'		=> array(
			'label'		=> 'contact.filter.address.city',
			'optgroup'	=> 'contact.filter.addresses.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactAddressFilterDataSource::autocompleteCities',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactAddressFilterDataSource::getCityLabel',
				'negation'		=> 'default'
			)
		),
		'street'		=> array(
			'label'		=> 'contact.filter.address.street',
			'optgroup'	=> 'contact.filter.addresses.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> false,
				'negation'		=> false
			)
		)
	)
);



/**
 *  Company filters
 */
Todoyu::$CONFIG['FILTERS']['COMPANY'] = array(
	'key'		=> 'company',
	'config'	=> array(
		'label'				=> 'contact.ext.companies',
		'position'			=> 35,
		'resultsRenderer'	=> 'TodoyuContactCompanyRenderer::renderCompanyListingSearch',
		'class'				=> 'TodoyuContactCompanyFilter',
		'defaultSorting'	=> 'ext_contact_company.title',
		'require'			=> 'contact.general:area'
	),
	'widgets' => array(

			// Company optgroup
		'fulltext' => array(
			'label'		=> 'contact.filter.fulltext',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'text',
			'wConf' => array(
				'negation'		=> 'default'
			)
		),
		'name' => array(
				'label'		=> 'contact.filter.company.name',
				'optgroup'	=> 'contact.filter.company.label',
				'widget'	=> 'text',
				'wConf' => array(
					'negation'		=> 'default'
				)
			),
		'contactinformation' => array(
			'label'		=> 'contact.filter.contactinformation',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'text',
			'wConf' => array(
				'negation'		=> false
			)
		),
		'isInternal'	=> array(
			'funcRef'	=> 'TodoyuContactCompanyFilter::Filter_isInternal',
			'label'		=> 'contact.filter.company.isInternal',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'checkbox',
			'wConf'		=> array(
				'negation'	=> false
			)
		),
		'dateEnter'		=> array(
			'label'		=> 'contact.filter.company.dateEnter',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'date',
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'notActive'		=> array(
			'funcRef'	=> 'TodoyuContactCompanyFilter::Filter_isNotActive',
			'label'		=> 'contact.ext.company.attr.is_notactive',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'checkbox',
			'wConf'		=> array(
				'negation'	=> false
			)
		),

			// Person optgroup
		'person' => array(
			'label'		=> 'contact.filter.company.person',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
				'negation'		=> 'default'
			)
		),

			// Address optgroup
		'country'		=> array(
			'label'		=> 'contact.filter.address.country',
			'optgroup'	=> 'contact.filter.addresses.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> false,
				'FuncRef'	=> 'TodoyuContactAddressFilterDataSource::getCompanyCountryOptions',
				'negation'	=> 'default'
			)
		),
		'zip'		=> array(
			'label'		=> 'contact.filter.address.zip',
			'optgroup'	=> 'contact.filter.addresses.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> false,
				'negation'		=> false
			)
		),
		'city'		=> array(
			'label'		=> 'contact.filter.address.city',
			'optgroup'	=> 'contact.filter.addresses.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactAddressFilterDataSource::autocompleteCities',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactAddressFilterDataSource::getCityLabel',
				'negation'		=> 'default'
			)
		),
		'street'		=> array(
			'label'		=> 'contact.filter.address.street',
			'optgroup'	=> 'contact.filter.addresses.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> false,
				'negation'		=> false
			)
		),
	)
);

?>