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
 * Dwoo plugin to restrict access to template parts to internal persons
 *
 * @example
 * {restrictAdmin}Restricted parts{else}Unrestricted{/restrictAdmin}
 *
 * @package		Todoyu
 * @subpackage	Template
 */
class Dwoo_Plugin_restrictAdmin extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block, Dwoo_IElseable {

	/**
	 * Initialize plugin
	 *
	 */
	public static function init() {

	}



	/**
	 * Before processing the block content
	 *
	 * @param	Dwoo_Compiler	$compiler
	 * @param	Array			$params
	 * @param	String			$prepend		Unknown param
	 * @param	String			$append			Unknown param
	 * @param	String			$type			Unknown param
	 * @return	String
	 */
	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type) {
		return '';
	}



	/**
	 * After processing the block. Create compiled code for template which wraps the processed content
	 *
	 * @param	Dwoo_Compiler	$compiler
	 * @param	Array			$params
	 * @param	String			$prepend		Unknown param
	 * @param	String			$append			Unknown param
	 * @param	String			$content		Unknown param
	 * @return	String
	 */
	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content) {
		$params = $compiler->getCompiledParams($params);

		$pre	= Dwoo_Compiler::PHP_OPEN.'if( TodoyuAuth::isAdmin() ) {'.Dwoo_Compiler::PHP_CLOSE;
		$post	= Dwoo_Compiler::PHP_OPEN."}".Dwoo_Compiler::PHP_CLOSE;

		if (isset($params['hasElse'])) {
			$post .= $params['hasElse'];
		}

		return $pre . $content . $post;
	}

}

?>