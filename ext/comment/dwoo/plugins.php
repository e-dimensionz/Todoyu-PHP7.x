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
 * Comment specific Dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 */



/**
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$text
 * @return	String
 */
function Dwoo_Plugin_linkComments_compile(Dwoo_Compiler $compiler, $text) {
	return 'TodoyuCommentCommentManager::linkCommentIDsInText(' . $text . ')';
}



/**
 * @param	Dwoo		$dwoo
 * @param	Integer		$idTask
 * @return	Boolean
 */
function Dwoo_Plugin_isAddInTaskAllowed(Dwoo $dwoo, $idTask) {
	$idTask	= intval($idTask);
	return TodoyuCommentRights::isAddInTaskAllowed($idTask);
}



/**
 * @param	Dwoo $dwoo
 * @return	Boolean
 */
function Dwoo_Plugin_isSeeAllCommentsAllowed(Dwoo $dwoo) {
	return Todoyu::allowed('comment', 'comment:seeAll');
}

?>