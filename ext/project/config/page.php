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

		// Add menu entry
if(  Todoyu::allowed('project', 'general:area') ) {
	TodoyuFrontend::addMenuEntry('project', 'project.ext.tab.label', 'index.php?ext=project', 20);

		// Register quick task headlet
	if( Todoyu::allowed('project', 'addtask:addViaQuickCreateHeadlet') ) {
		TodoyuHeadManager::addHeadlet('TodoyuProjectHeadletQuickTask', 55);
	}
}

if( TodoyuExtensions::isInstalled('portal') && Todoyu::allowed('portal', 'general:use') ) {
		// Add portal tab: 'todos'
	TodoyuPortalManager::addTab('todo', 'TodoyuProjectPortalRenderer::getTodoTabLabel', 'TodoyuProjectPortalRenderer::renderTodoTabContent', 20);
}

?>