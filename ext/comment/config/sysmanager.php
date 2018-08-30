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

TodoyuSysmanagerExtManager::addRecordConfig('comment', 'fallback', array(
	'label'			=> 'comment.ext.records.fallback',
	'description'	=> 'comment.ext.records.fallback.desc',
	'form'			=> 'ext/comment/config/form/admin/fallback.xml',
	'list'			=> 'TodoyuCommentFallbackManager::getRecords',
	'save'			=> 'TodoyuCommentFallbackManager::saveFallback',
	'delete'		=> 'TodoyuCommentFallbackManager::deleteFallback',
	'object'		=> 'TodoyuCommentFallback',
	'table'			=> 'ext_comment_fallback',
	'isDeletable'	=> 'TodoyuCommentFallbackManager::isDeletable'
));

?>