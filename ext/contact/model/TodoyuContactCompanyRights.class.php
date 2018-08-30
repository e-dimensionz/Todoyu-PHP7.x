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
 * Company rights functions
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyRights {

	/**
	 * Deny access
	 * Shortcut for contact
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('contact', $right);
	}



	/**
	 * Checks if see given company is allowed for current person
	 *
	 * @param	Integer		$idCompany
	 * @return	Boolean
	 */
	public static function isSeeAllowed($idCompany) {
		$idCompany	= intval($idCompany);

		if( TodoyuAuth::isAdmin() || Todoyu::allowed('contact', 'company:seeAllCompanies') ) {
			return true;
		}

		return in_array($idCompany, self::getCompanyIDsAllowedToBeSeen());
	}



	/**
	 * Checks if edit given company is allowed for current person
	 *
	 * @param	Integer		$idCompany
	 * @return	Boolean
	 */
	public static function isEditAllowed($idCompany) {
		$idCompany	= intval($idCompany);

		if( ! self::isSeeAllowed($idCompany) ) {
			return false;
		}

		if( TodoyuAuth::isAdmin() || Todoyu::allowed('contact', 'company:editAndDeleteAll')) {
			return true;
		}

		if( Todoyu::allowed('contact', 'company:editOwn') ) {
			$person				= TodoyuContactPersonManager::getPerson(Todoyu::personid());
			$personsCompanies	= $person->getCompanyIDs();

			return in_array($idCompany, $personsCompanies);
		}

		return false;
	}



	/**
	 * Checks whether deletion of given company is allowed for current person
	 *
	 * @param	Integer		$idCompany
	 * @return	Boolean
	 */
	public static function isDeleteAllowed($idCompany) {
		$idCompany	= intval($idCompany);
		$hasProjects= TodoyuContactCompanyManager::hasProjects($idCompany);

		if( $hasProjects ) {
			return false;
		}

		return Todoyu::allowed('contact', 'company:editAndDeleteAll');
	}



	/**
	 * Returns all company IDs which are allowed to be seen by the current person
	 *
	 * @return	Integer[]
	 */
	public static function getCompanyIDsAllowedToBeSeen() {
		$fields	= 'id';
		$table	= TodoyuContactCompanyManager::TABLE;
		$where	= self::getAllowedToBeSeenCompaniesWhereClause();

		return Todoyu::db()->getColumn($fields, $table, $where, '', '', '', 'id');
	}



	/**
	 * Get WHERE clause for all companies the current user is allowed to see
	 *
	 * @return	String
	 */
	public static function getAllowedToBeSeenCompaniesWhereClause() {
		$allowedCompanyIDs	= TodoyuContactCompanyManager::getInternalCompanyIDs();

		if( TodoyuAuth::isAdmin() || Todoyu::allowed('contact', 'company:seeAllCompanies') ) {
			return ' 1';
		}

			// Get all companies the user belongs to
		$person			= TodoyuContactPersonManager::getPerson(Todoyu::personid());
		$ownCompanyIDs	= $person->getCompanyIDs();

		$allowedCompanyIDs	= array_unique(array_merge($allowedCompanyIDs, $ownCompanyIDs));

		return TodoyuSql::buildInListQueryPart($allowedCompanyIDs, 'id');
	}



	/**
	 * Restrict access to persons who are allowed to see the given company
	 *
	 * @param	$idCompany
	 */
	public static function restrictSee($idCompany) {
		if( ! self::isSeeAllowed($idCompany) ) {
			self::deny('company:see');
		}
	}


	/**
	 * Restrict access to persons who are allowed to add a new company
	 *
	 * @return void
	 */
	public static function restrictAdd() {
		if( ! Todoyu::allowed('contact', 'company:add') ) {
			self::deny('company:add');
		}
	}



	/**
	 * Restrict access to persons who are allowed to edit the given company
	 *
	 * @param	$idCompany
	 */
	public static function restrictEdit($idCompany) {
		if( ! self::isEditAllowed($idCompany) ) {
			self::deny('company:edit');
		}
	}



	/**
	 * Restrict access to persons who are allowed to delete the given company
	 *
	 * @param	Integer		$idCompany
	 */
	public static function restrictDelete($idCompany) {
		if( ! self::isDeleteAllowed($idCompany) ) {
			self::deny('company:delete');
		}
	}
}
?>