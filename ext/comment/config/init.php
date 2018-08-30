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


require_once( PATH_EXT_COMMENT . '/config/hooks.php' );


/* ------------------------------------------------
	Configuration for 'feedback' tab in portal
   ------------------------------------------------ */
Todoyu::$CONFIG['EXT']['comment'] = array(
	'allowedTags'		=> '<p><strong><em><span><i><ol><ul><li><br><pre><a><hr>',
	'feedbackTabFilters'=> array(
		array(
			'filter' => 'unseenFeedbackPerson'
		),
		array(
			'filter' => 'commentIsPublicForExternals'
		)
	)
);



/* --------------------------------------------
	Add comment content tab and context menu
   -------------------------------------------- */
if( Todoyu::allowed('comment', 'general:use') ) {
		// Add task tab for comments
	TodoyuContentItemTabManager::registerTab('project', 'task', 'comment', 'TodoyuCommentTaskManager::getTaskTabLabel', 'TodoyuCommentTaskManager::getTaskTabContent', 30);
		// Add "Add New > Comment" to task context menu
	TodoyuContextMenuManager::addFunction('Task', 'TodoyuCommentCommentManager::getTaskContextMenuItems', 150);
}

?>