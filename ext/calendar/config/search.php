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
 * Register search engine type of calendar events to search headlet
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
if( (Todoyu::person()->isInternal() || TodoyuAuth::isAdmin()) && Todoyu::allowed('calendar', 'general:use') ) {
	TodoyuSearchManager::addEngine('event', 'TodoyuCalendarEventSearch::getSuggestions', 'calendar.ext.search.label', 'calendar.ext.search.mode.label', 50);
}

?>