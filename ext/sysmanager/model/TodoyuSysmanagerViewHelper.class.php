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
 * System Manager View Helper
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerViewHelper {

	/**
	 * Get options for extension selector
	 *
	 * @param	TodoyuFormElement		$field
	 * @return	Array
	 */
	public static function getExtensionOptions(TodoyuFormElement $field) {
		$extKeys	= TodoyuExtensions::getInstalledExtKeys();
		$options	= array();

		sort($extKeys);

		foreach($extKeys as $extKey) {
			$options[] = array(
				'value'	=> $extKey,
				'label'	=>  $extKey . ': ' . Todoyu::Label($extKey . '.ext.ext.title')
			);
		}

		return $options;
	}



	/**
	 * Get options for first day of week options (extConf)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getFirstDayOfWeekOptions(TodoyuFormElement $field) {
		$options = array(
			array(
				'value'	=> 0,
				'label' => Todoyu::Label('core.date.weekday.sunday')
			),
			array(
				'value'	=> 1,
				'label' => Todoyu::Label('core.date.weekday.monday')
			)
		);

		return $options;
	}

}

?>