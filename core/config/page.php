<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * Core config for page rendering
 *
 * @package		Todoyu
 * @subpackage	Core
 */

if( TodoyuAuth::isLoggedIn() ) {
		// Register AJAX loader headlet which indicated active AJAX requests
	TodoyuHeadManager::addHeadlet('TodoyuHeadletAjaxLoader', 0);
	TodoyuHeadManager::addHeadlet('TodoyuHeadletAbout', 10);
	TodoyuHeadManager::addHeadlet('TodoyuHeadletQuickCreate', 50);

		// Add JSCalendar language file suiting to system language
	TodoyuJSCalendar::addLangFile();
}

?>