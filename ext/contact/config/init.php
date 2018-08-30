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

/* -------------------------------
	Content Tabs Configuration
   ------------------------------- */
Todoyu::$CONFIG['EXT']['contact'] = array(
	'defaultTypeTab' => 'person',
	'numFavoriteCountries' => 5,
	'tabs' => array(
		'person'	=> array(
			'key'		=> 'person',
			'id'		=> 'person',
			'label'		=> 'contact.ext.persons',
			'require'	=> 'contact.general:area',
			'position'	=> 105
		),
		'company'	=> array(
			'key'		=> 'company',
			'id'		=> 'company',
			'label'		=> 'contact.ext.companies',
			'require'	=> 'contact.general:area',
			'position'	=> 110
		)
	),
	/* ---------------------------------------------
		Categories of dynamic contact info types
	   --------------------------------------------- */
	'contactinfotypecategories' => array(
		array(	// Email
			'index'	=> CONTACT_INFOTYPE_CATEGORY_EMAIL,
			'label'	=> 'contact.ext.record.contactinfotype.email'
		),
		array(	// Phone
			'index'	=> CONTACT_INFOTYPE_CATEGORY_PHONE,
			'label'	=> 'contact.ext.record.contactinfotype.phone'
		),
		array(	// Other
			'index'	=> CONTACT_INFOTYPE_CATEGORY_OTHER,
			'label'	=> 'contact.ext.record.contactinfotype.other'
		)
	),
	/* -----------------------
		Types of addresses
	   ----------------------- */
	'addresstypes' => array(
		array(	// Home address
			'index'	=> CONTACT_ADDRESSTYPE_HOME,
			'label'	=> 'contact.ext.address.attr.addresstype.1'
		),
		array(	// Business address
			'index'	=> CONTACT_ADDRESSTYPE_BUSINESS,
			'label'	=> 'contact.ext.address.attr.addresstype.2'
		),
		array(	// Billing address
			'index'	=> CONTACT_ADDRESSTYPE_INVOICE,
			'label'	=> 'contact.ext.address.attr.addresstype.3'
		)
	),
	'listing' => array(
		'person' => array(
			'name'		=> 'person',
			'update'	=> 'contact/person/listing',
			'dataFunc'	=> 'TodoyuContactPersonSearch::getPersonListingData',
			'size'		=> Todoyu::$CONFIG['LIST']['size'],
			'columns'	=> array(
				'icon'		=> '',
				'lastname'	=> 'contact.ext.person.attr.lastname',
				'firstname'	=> 'contact.ext.person.attr.firstname',
				'company'	=> 'contact.ext.company',
				'email'	=> 'contact.ext.person.attr.email',
				'phone'		=> 'contact.ext.phone',
				'actions'	=> '',
			)
		),
		'company' => array(
			'name'		=> 'company',
			'update'	=> 'contact/company/listing',
			'dataFunc'	=> 'TodoyuContactCompanySearch::getCompanyListingData',
			'size'		=> Todoyu::$CONFIG['LIST']['size'],
			'columns'	=> array(
				'icon'		=> '',
				'title'		=> 'contact.ext.company.attr.title',
				'email'		=> 'contact.ext.person.attr.email',
				'phone'		=> 'contact.ext.phone',
				'address'	=> 'contact.ext.address',
				'actions'	=> ''
			)
		),
		'employee' => array(
			'name'		=> 'person',
			'update'	=> 'contact/empoyee/listing',
			'dataFunc'	=> 'TodoyuContactCompanySearch::getEmployeeListingData',
			'size'		=> 999,
			'columns'	=> array(
				'name'		=> 'contact.ext.person',
				'jobtype'	=> 'contact.ext.jobtype',
			)
		)
	),
	'panelWidgetProjectList' => array(
		'maxPersons'	=> 30 // Maximum persons in staff listing widget
	),
	'panelWidgetStaffSelector'	=> array(
		'maxListSize'	=> 15 // Max size of person selector widget
	),
	/* ----------------------------
		Configure Contact Images
	   ---------------------------- */
	'contactimage' => array(
		'pathperson'	=> 'files/contact/person',
		'pathcompany'	=> 'files/contact/company',
		'max_file_size'	=> 512000,
		'dimension'		=> array(
			'x'	=> 120,
			'y'	=> 120
		),
		'allowedTypes'	=> array(
			'image/png',
			'image/jpeg',
			'image/gif'
		)
	),
	'avatar' => array(
		'pathperson'	=> 'files/contact/person/avatar',
		'pathcompany'	=> 'files/contact/company/avatar',
		'max_file_size'	=> 512000,
		'dimension'		=> array(
			'x'	=> 80,
			'y'	=> 80
		),
		'allowedTypes'	=> array(
			'image/png',
			'image/jpeg',
			'image/gif'
		)
	)
);




/* -------------------------------------
	Add contact module to profile
   ------------------------------------- */
if( TodoyuExtensions::isInstalled('profile') && Todoyu::allowed('contact', 'general:use') ) {
	TodoyuProfileManager::addModule('contact', array(
		'position'	=> 2,
		'tabs'		=> 'TodoyuContactProfileRenderer::renderTabs',
		'content'	=> 'TodoyuContactProfileRenderer::renderContent',
		'label'		=> 'contact.ext.profile.module',
		'class'		=> 'contact'
	));
}

	// Tabs for contact section in profile: "personal data"
Todoyu::$CONFIG['EXT']['profile']['contactTabs'] = array(
	array(
		'id'	=> 'contact',
		'label'	=> 'contact.ext.profile.module'
	)
);



/* ----------------------------------------
	Configure search + results listing
   --------------------------------------- */
			// Person search
Todoyu::$CONFIG['EXT']['contact']['listing']['personSearch'] = Todoyu::$CONFIG['EXT']['contact']['listing']['person'];
Todoyu::$CONFIG['EXT']['contact']['listing']['personSearch']['dataFunc']	= 'TodoyuContactPersonSearch::getPersonListingDataSearch';

	// Company search
Todoyu::$CONFIG['EXT']['contact']['listing']['companySearch'] = Todoyu::$CONFIG['EXT']['contact']['listing']['company'];
Todoyu::$CONFIG['EXT']['contact']['listing']['companySearch']['dataFunc']	= 'TodoyuContactCompanySearch::getCompanyListingDataSearch';



/* ---------------------------------------------
	Add autocompleters for contact data types
   --------------------------------------------- */
	// Company
TodoyuAutocompleter::addAutocompleter('company', 'TodoyuContactCompanyFilterDataSource::autocompleteCompanies', array('contact', 'general:use'));
TodoyuAutocompleter::addAutocompleter('activecompany', 'TodoyuContactCompanyFilterDataSource::autocompleteActiveCompanies', array('contact', 'general:use'));
	// Person
TodoyuAutocompleter::addAutocompleter('person', 'TodoyuContactPersonFilterDataSource::autocompletePersons', array('contact', 'general:use'));
	// Jobtype
TodoyuAutocompleter::addAutocompleter('jobtype', 'TodoyuContactJobTypeManager::autocompleteJobtypes', array('contact', 'general:use'));



/* ---------------------------------------------
	Add quickInfo callback for person labels
   --------------------------------------------- */
TodoyuQuickinfoManager::addFunction('person', 'TodoyuContactPersonQuickinfoManager::addPersonInfos');


/* ----------------------------------------------
	Add exports to search area action panel
   ---------------------------------------------- */
TodoyuSearchActionPanelManager::addExport('company', 'csvexport', 'TodoyuContactCompanyExportManager::exportCSVfromIDs', 'contact.ext.export.companycsv', 'exportCsv', 'contactcompanysearch:export:companycsv');
TodoyuSearchActionPanelManager::addExport('person', 'csvexport', 'TodoyuContactPersonExportManager::exportCSVfromIDs', 'contact.ext.export.personcsv', 'exportCsv', 'contactpersonsearch:export:personcsv');





TodoyuCreateWizardManager::addWizard('person', array(
	'ext'		=> 'contact',
	'controller'=> 'person',
	'action'	=> 'createWizard',
	'title'		=> 'contact.ext.person.create',
	'restrict'	=> array(
		array(
			'contact',
			'person:add'
		)
	)
));

TodoyuCreateWizardManager::addWizard('company', array(
	'ext'		=> 'contact',
	'controller'=> 'company',
	'action'	=> 'createWizard',
	'title'		=> 'contact.ext.create.company.label',
	'restrict'	=> array(
		array(
			'contact',
			'company:add'
		)
	)
));



	// Add email receiver type: 'contactperson'
TodoyuMailReceiverManager::addType('contactperson', 'TodoyuContactMailReceiverPerson');
TodoyuMailReceiverManager::addType('contactinfo', 'TodoyuContactMailReceiverContactInfo');


	// Records selector: staff (can be person, role or group)
TodoyuFormRecordsManager::addType('staff', 'TodoyuContactFormElement_RecordsStaff', 'TodoyuContactPersonManager::getMatchingStaffPersons');
	// Records selector: person
TodoyuFormRecordsManager::addType('person', 'TodoyuContactFormElement_RecordsPerson', 'TodoyuContactPersonManager::getMatchingPersons');
	// Records selector: email person
TodoyuFormRecordsManager::addType('emailPerson', 'TodoyuContactFormElement_RecordsEmailPerson', 'TodoyuContactPersonManager::getMatchingEmailPersons');


TodoyuMailReceiverManager::addSearchCallback('TodoyuContactPersonManager::getMatchingEmailReceiversActivePersons');
TodoyuMailReceiverManager::addSearchCallback('TodoyuContactPersonManager::getMatchingEmailReceiversContactInfo');

TodoyuQuickinfoManager::addFunction('companyEmail', 'TodoyuContactCompanyQuickInfoManager::addQuickInfoEmail');
TodoyuQuickinfoManager::addFunction('companyPhone', 'TodoyuContactCompanyQuickInfoManager::addQuickInfoPhone');
TodoyuQuickinfoManager::addFunction('companyAddress', 'TodoyuContactCompanyQuickInfoManager::addQuickInfoAddress');

TodoyuQuickinfoManager::addFunction('personEmail', 'TodoyuContactPersonQuickInfoManager::addQuickInfoEmail');
TodoyuQuickinfoManager::addFunction('personPhone', 'TodoyuContactPersonQuickInfoManager::addQuickInfoPhone');

?>