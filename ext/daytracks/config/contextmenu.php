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
 *	Context menu for hours sheet panel widget. Use the same items, change behaviour.
 */


	// Copy context menu items from project/task
Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget'] = array(
	'showinproject' => Todoyu::$CONFIG['EXT']['project']['ContextMenu']['Task']['showinproject'],
	'status'		=> Todoyu::$CONFIG['EXT']['project']['ContextMenu']['Task']['status']
);

Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['showinproject']['jsAction']	= 'Todoyu.Ext.project.goToTaskInProject(#ID#)';

Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['planning']['jsAction']	= 'Todoyu.Ext.daytracks.PanelWidget.Daytracks.updateTaskStatus(#ID#, ' . STATUS_PLANNING . ')';
Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['open']['jsAction']		= 'Todoyu.Ext.daytracks.PanelWidget.Daytracks.updateTaskStatus(#ID#, ' . STATUS_OPEN . ')';
Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['progress']['jsAction']	= 'Todoyu.Ext.daytracks.PanelWidget.Daytracks.updateTaskStatus(#ID#, ' . STATUS_PROGRESS . ')';
Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['confirm']['jsAction']	= 'Todoyu.Ext.daytracks.PanelWidget.Daytracks.updateTaskStatus(#ID#, ' . STATUS_CONFIRM . ')';
Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['done']['jsAction']		= 'Todoyu.Ext.daytracks.PanelWidget.Daytracks.updateTaskStatus(#ID#, ' . STATUS_DONE . ')';
Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['accepted']['jsAction']	= 'Todoyu.Ext.daytracks.PanelWidget.Daytracks.updateTaskStatus(#ID#, ' . STATUS_ACCEPTED . ')';
Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['rejected']['jsAction']	= 'Todoyu.Ext.daytracks.PanelWidget.Daytracks.updateTaskStatus(#ID#, ' . STATUS_REJECTED . ')';

	// Only update status if available
if( Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['cleared'] ) {
	Todoyu::$CONFIG['EXT']['daytracks']['ContextMenu']['PanelWidget']['status']['submenu']['cleared']['jsAction']	= 'Todoyu.Ext.daytracks.PanelWidget.Daytracks.updateTaskStatus(#ID#, ' . STATUS_CLEARED . ')';
}

?>