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
 * Sysmanager specific dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 */



/**
 * Checks whether current extension has records registered
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$extKey
 * @return	String
 */
function Dwoo_Plugin_extMgr_hasRecords_compile(Dwoo_Compiler $compiler, $extKey) {
	return 'sizeof(TodoyuSysmanagerExtManager::getRecordTypes(' . $extKey . ')) > 0';
}



/**
 * Checks whether extension has rights config registered
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$extKey
 * @return	Boolean
 */
function Dwoo_Plugin_extMgr_hasRighsConfig_compile(Dwoo_Compiler $compiler, $extKey) {
	return 'TodoyuSysmanagerRightsEditorManager::hasRightsConfig(' . $extKey . ')';
}



/**
 * Checks whether extension has something to configure
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$extKey
 * @return	String
 */
function Dwoo_Plugin_extMgr_hasConfig_compile(Dwoo_Compiler $compiler, $extKey) {
	return 'TodoyuSysmanagerExtManager::extensionHasConfig(' . $extKey . ')';
}



/**
 * Checks whether extension has informations registered
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$extKey
 * @return	String
 */
function Dwoo_Plugin_extMgr_hasExtInfo_compile(Dwoo_Compiler $compiler, $extKey) {
	return 'TodoyuSysmanagerExtManager::getExtInfos(' . $extKey . ') !== false';
}


/**
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$extKey
 * @return	String
 */
function Dwoo_Plugin_extMgr_isSysExt_compile(Dwoo_Compiler $compiler, $extKey) {
	return 'TodoyuSysmanagerExtManager::isSysExt(' . $extKey . ')';
}



/**
 * Render extension icon image tag (if exists)
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$extKey
 * @return	String
 */
function Dwoo_Plugin_extIcon_compile(Dwoo_Compiler $compiler, $extKey) {
	return "'<img src=\"ext/' . " . $extKey . " . '/asset/img/exticon.png\" width=\"16\" height=\"16\" />'";
}



/**
 * Convert the state key into an state image
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$state
 * @return	String
 */
function Dwoo_Plugin_ExtensionStatusIcon(Dwoo $dwoo, $state) {
	$state	= trim(strtolower($state));
	$states	= array(
		1		=> 'stable',
		2		=> 'beta',
		3		=> 'alpha',
		'alpha'	=> 'alpha',
		'beta'	=> 'beta',
		'stable'=> 'stable'
	);

	if( ! array_key_exists($state, $states) ) {
		$state	= 'alpha';
	} else {
		$state	= $states[$state];
	}

	return '<span class="extensionstate ' . $state . '"></span>';
}



/**
 * Returns a wrapped label tag of a right, evoking right-info tooltip on rollOver
 *
 * @param	Dwoo			$dwoo
 * @param	String			$extension
 * @param	String			$sectionName
 * @param	String			$right
 * @param	String			$prefix	descriptive string: 'ext'_'recordtype'
 * @param	String			$tag
 * @param	String			$class
 * @return	String
 */
function Dwoo_Plugin_rightLabel(Dwoo $dwoo, $extension, $sectionName, $right, $prefix = 'right', $tag = 'span', $class = '') {
	$htmlID		= $prefix . '-' . $extension . '-' . $sectionName . '-' . $right;
	$attributes	= array(
		'id'	=> $htmlID,
		'class'	=> 'require ' . trim('quickInfoRight ' . $class)
	);

	$rightTag		= TodoyuString::buildHtmlTag($tag, $attributes, '');
	$quickInfoScript= TodoyuString::wrapScript('Todoyu.Ext.sysmanager.QuickInfoRight.add(\'' . $htmlID . '\');');

	return $rightTag . $quickInfoScript;
}

?>