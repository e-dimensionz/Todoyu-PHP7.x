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
 * Register contact search engine types: persons, comments to search headlet
 *
 * @package		Todoyu
 * @subpackage	Project
 */
if( Todoyu::allowed('contact', 'general:use') ) {
	if( Todoyu::allowed('contact', 'general:area') ) {
		TodoyuSearchManager::addEngine('person', 'TodoyuContactPersonSearch::getSuggestions', 'contact.ext.person.search.title', 'contact.ext.person.search.mode', 100);
		TodoyuSearchManager::addEngine('company', 'TodoyuContactCompanySearch::getSuggestions', 'contact.ext.company.search.title', 'contact.ext.company.search.mode', 110);
	}
}

?>