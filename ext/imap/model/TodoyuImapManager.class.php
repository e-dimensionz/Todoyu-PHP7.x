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
 * Imap Manager
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapManager {

	/**
	 * Get IDs of all active accounts
	 *
	 * @return	Integer[]
	 */
	public static function getActiveAccountIDs() {
		$accounts	= self::getAllAccounts();

		$accountIDs	= array();
		foreach($accounts as $account) {
			$accountIDs[]	= $account->getID();
		}

		return $accountIDs;
	}



	/**
	 * Get all configured mailbox accounts
	 *
	 * @param	Boolean		$onlyActive
	 * @return	TodoyuImapImapAccount[]
	 */
	public static function getAllAccounts($onlyActive = true) {
		return TodoyuImapImapAccountManager::getAllAccounts($onlyActive);
	}



	/**
	 * Get matching items: persons with email
	 *
	 * @param	String[]	$searchWords
	 * @param	Integer[]	$ignoreIDs
	 * @return	Array
	 */
	public static function getMatchingEmailPersons($searchWords, $ignoreIDs) {
		$persons	= TodoyuContactPersonManager::getMatchingEmailPersons($searchWords, $ignoreIDs);

			// Prefix all items' IDs with mail-receiver type key and add resp. classname
		$typeKeyMailReceiver	= 'contactperson';
		foreach($persons as $index => $personData) {
			$persons[$index]['id']			= $typeKeyMailReceiver . ':' . $personData['id'];
			$persons[$index]['className']	= $typeKeyMailReceiver;
		}

		return $persons;
	}

}

?>