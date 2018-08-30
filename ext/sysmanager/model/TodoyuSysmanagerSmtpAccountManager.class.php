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
 * Manager for SMTP account records
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerSmtpAccountManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_sysmanager_smtpaccount';



	/**
	 * Get IMAP account object
	 *
	 * @param	Integer				$idAccount
	 * @return	TodoyuSysmanagerSmtpAccount
	 */
	public static function getAccount($idAccount) {
		return TodoyuRecordManager::getRecord('TodoyuSysmanagerSmtpAccount', $idAccount);
	}



	/**
	 * Get SMTP account objects for given IDs
	 *
	 * @param	Integer[]				$accountIDs
	 * @return	TodoyuSysmanagerSmtpAccount[]
	 */
	public static function getAccountsByID(array $accountIDs) {
		$accountIDs	= TodoyuArray::intval($accountIDs);

		return TodoyuRecordManager::getRecordList('TodoyuSysmanagerSmtpAccount', $accountIDs);
	}



	/**
	 * Get all SMTP accounts from the database
	 *
	 * @return	Array	admin readable array of entries
	 */
	public static function getRecordsListingItems() {
		$items 			= array();
		$imapAccounts	= self::getAllAccounts(false);

		foreach($imapAccounts as $imapAccount) {
			$items[] = array(
				'id'	=> $imapAccount->getID(),
				'label'	=> $imapAccount->getLabel()
			);
		}

		return $items;
	}



	/**
	 * Get all (optionally only active ones) SMTP accounts
	 *
	 * @return	TodoyuSysmanagerSmtpAccount[]
	 */
	public static function getAllAccounts() {
		$field	= 'id';
		$where	= 'deleted = 0';

		$accountIDs	= Todoyu::db()->getColumn($field, self::TABLE, $where);

		return TodoyuRecordManager::getRecordList('TodoyuSysmanagerSmtpAccount', $accountIDs);
	}



	/**
	 * Get options configuration for all SMTP account records
	 *
	 * @param	Boolean		$withPrefix
	 * @return	Array
	 */
	public static function getAllAccountsOptions($withPrefix = false) {
		$smtpAccounts	= TodoyuSysmanagerSmtpAccountManager::getAllAccounts();
		$options		= array();
		$prefix			= $withPrefix ? 'smtp_' : '';

		foreach($smtpAccounts as $idAccount => $smtpAccount) {
			$options[]	= array(
				'value'	=> $prefix . $idAccount,
				'label'	=> 'SMTP: ' . $smtpAccount->getLabel()
			);
		}

		return $options;
	}



	/**
	 * Removes given IMAP account record from the database
	 *
	 * @param	Integer	$idAccount
	 */
	public static function removeAccount($idAccount) {
		$idAccount	= intval($idAccount);

		TodoyuHookManager::callHook('sysmanager', 'smtp-account.remove', array($idAccount));

		TodoyuRecordManager::deleteRecord(self::TABLE, $idAccount);
	}



	/**
	 * Save SMTP account record from given data
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function saveAccount(array $data) {
		$idAccount	= intval($data['id']);

		if( $idAccount == 0 ) {
			$idAccount = TodoyuRecordManager::addRecord(self::TABLE, array());
		}

		$xmlPath= 'ext/sysmanager/config/form/admin/smtp-account.xml';

		if( !empty($data['password']) ) {
			$data['password'] = TodoyuCrypto::encrypt($data['password']);
		} else {
			unset($data['password']);
		}

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idAccount);

		TodoyuRecordManager::updateRecord(self::TABLE, $idAccount, $data);

		return $idAccount;
	}

}

?>