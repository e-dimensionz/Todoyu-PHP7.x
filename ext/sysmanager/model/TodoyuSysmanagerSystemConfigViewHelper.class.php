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
 * System config view helper
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerSystemConfigViewHelper {


	/**
	 * Get grouped options for timezone selector
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getTimezoneOptionsGrouped(TodoyuFormElement $field) {
		return TodoyuTimezoneManager::getTimezonesGroupedOptions(true);
	}



	/**
	 * Get options for password minLength selector
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getPasswordMinLengthOptions(TodoyuFormElement $field) {
		$options	= array();

		for($i = 1; $i <= 12; $i++) {
			$options[] = array(
				'value'	=> $i,
				'label'	=> $i . ' ' . Todoyu::Label('sysmanager.ext.config.tab.passwordstrength.minLenth.characters')
			);
		}

		return $options;
	}



	/**
	 * Get options for log level
	 *
	 * @see		TodoyuLogger
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getLogLevelOptions(TodoyuFormElement $field) {
		return array(
			array(
				'value'	=> TodoyuLogger::LEVEL_CORE,
				'label'	=> TodoyuLogger::LEVEL_CORE . ': ' . Todoyu::Label('core.global.log.core')
			),
			array(
				'value'	=> TodoyuLogger::LEVEL_DEBUG,
				'label'	=> TodoyuLogger::LEVEL_DEBUG . ': ' . Todoyu::Label('core.global.log.debug')
			),
			array(
				'value'	=> TodoyuLogger::LEVEL_NOTICE,
				'label'	=> TodoyuLogger::LEVEL_NOTICE . ': ' . Todoyu::Label('core.global.log.notice')
			),
			array(
				'value'	=> TodoyuLogger::LEVEL_ERROR,
				'label'	=> TodoyuLogger::LEVEL_ERROR . ': ' . Todoyu::Label('core.global.log.error')
			),
			array(
				'value'	=> TodoyuLogger::LEVEL_SECURITY,
				'label'	=> TodoyuLogger::LEVEL_SECURITY . ': ' . Todoyu::Label('core.global.log.security')
			),
			array(
				'value'	=> TodoyuLogger::LEVEL_FATAL,
				'label'	=> TodoyuLogger::LEVEL_FATAL . ': ' . Todoyu::Label('core.global.log.fatal')
			)
		);
	}



	/**
	 * Get options for mailer protocol
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getMailerOptions(TodoyuFormElement $field) {
		return TodoyuSysmanagerSmtpAccountManager::getAllAccountsOptions(true);
	}

}

?>