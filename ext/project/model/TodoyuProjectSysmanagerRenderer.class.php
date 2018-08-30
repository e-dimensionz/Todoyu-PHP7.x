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

/**
 * Renderer for project related pages in sysmanager
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectSysmanagerRenderer {

	/**
	 * Hooked-in when rendering taskpreset records list in sysmanager
	 *
	 * @param	Integer		$idTaskpreset
	 * @param	String		$body
	 * @return	String
	 */
	public static function onRenderTaskpresetRecordsBody($idTaskpreset, $body = '') {
		$idTaskpreset	= intval($idTaskpreset);

		if( $idTaskpreset > 0 ) {
			$body .= TodoyuString::wrapScript('Todoyu.Ext.project.TaskPreset.initForm(' . $idTaskpreset . ')');
		}

		return $body;
	}

}

?>