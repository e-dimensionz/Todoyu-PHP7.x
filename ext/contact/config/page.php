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

	// Add main menu area entry for contact
if( /*(Todoyu::person()->isInternal() || TodoyuAuth::isAdmin()) &&*/ Todoyu::allowed('contact', 'general:area') ) {
	TodoyuFrontend::addMenuEntry('contact', 'contact.ext.page.title', 'index.php?ext=contact', 40);

		// Add sub menu entries
	if( Todoyu::allowed('contact', 'general:area') ) {
		$subTabsConfig	= Todoyu::$CONFIG['EXT']['contact']['tabs'];
		TodoyuFrontend::addSubMenuEntriesFromTabsConf('contact', 'contact', $subTabsConfig);
	}

}

?>