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
 * View helper for IMAP account
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuImapViewHelper {

	/**
	 * Get config array for one IMAP account option
	 *
	 * @param	Integer		$index
	 * @param	String		$label
	 * @return	Array
	 */
	public static function getAccountOption($index, $label = '') {
		$index	= intval($index);

		if( empty($label) ) {
			$label	= TodoyuImapImapAccountManager::getAccount($index)->getLabel();
		}

		return array(
			'index'		=> $index,
			'value'		=> $index,
			'label'		=> $label,
			'class'		=> ''
		);
	}



	/**
	 * Get options config for all active accounts
	 *
	 * @return	Array
	 */
	public static function getActiveAccountsOptions() {
		$options	= array();

		$accounts	= TodoyuImapManager::getAllAccounts();
		foreach($accounts as $account) {
			$options[] = array(
				'value'	=> $account->getID(),
				'label'	=> $account->getLabel(false)
			);
		}

		return $options;
	}
}

?>