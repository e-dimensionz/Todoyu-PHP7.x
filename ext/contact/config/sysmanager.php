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

	// Add job types to records area of sysadmin
TodoyuSysmanagerExtManager::addRecordConfig('contact', 'jobtype', array(
	'label'			=> 'contact.ext.record.jobtype',
	'description'	=> 'contact.ext.record.jobtype.desc',
	'list'			=> 'TodoyuContactJobTypeManager::getRecords',
	'form'			=> 'ext/contact/config/form/admin/jobtype.xml',
	'object'		=> 'TodoyuContactJobType',
	'delete'		=> 'TodoyuContactJobTypeManager::deleteJobType',
	'save'			=> 'TodoyuContactJobTypeManager::saveJobType',
	'table'			=> 'ext_contact_jobtype'
));

	// Add contact info types to records area of sysadmin
TodoyuSysmanagerExtManager::addRecordConfig('contact', 'contactinfotype', array(
	'label'			=> 'contact.ext.record.contactinfotype',
	'description'	=> 'contact.ext.record.contactinfotype.desc',
	'list'			=> 'TodoyuContactContactInfoTypeManager::getRecords',
	'form'			=> 'ext/contact/config/form/admin/contactinfotype.xml',
	'object'		=> 'TodoyuContactContactInfoType',
	'delete'		=> 'TodoyuContactContactInfoTypeManager::deleteContactTypeInfo',
	'save'			=> 'TodoyuContactContactInfoTypeManager::saveContactInfoType',
	'table'			=> 'ext_contact_contactinfotype'
));

?>