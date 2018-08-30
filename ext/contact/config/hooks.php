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


	// Load person foreign records data
TodoyuFormHook::registerLoadData('ext/contact/config/form/person.xml', 'TodoyuContactPersonManager::hookPersonLoadFormData');
TodoyuFormHook::registerLoadData('ext/contact/config/form/address.xml', 'TodoyuContactAddressManager::hookAddressLoadFormData');

	// Add person quickInfos to task persons
if( Todoyu::allowed('contact', 'general:use') ) {
	TodoyuHookManager::registerHook('project', 'taskdata', 'TodoyuContactTaskManager::hookModifyTaskPersonAttributes', 200);
}

TodoyuHookManager::registerHook('contact', 'contactinfotype.render', 'TodoyuContactContactInfoManager::renderContactInformation');
?>