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
 * Configure panel widgets to be shown in Calendar area
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */

	// Add default (for all persons) panel widgets
TodoyuPanelWidgetManager::addPanelWidget('calendar', 'calendar', 'Calendar',			10, array('class' => 'todoyuskin'));
TodoyuPanelWidgetManager::addPanelWidget('calendar', 'contact', 'StaffSelector',		20, array('group' => true));
TodoyuPanelWidgetManager::addPanelWidget('calendar', 'calendar', 'EventTypeSelector',	40,	array('selectAllOnFirstRun'	=> true));
//TodoyuPanelWidgetManager::addPanelWidget('calendar', 'calendar', 'HolidaySetSelector',	50); @todo remove

?>