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
 * Authentication class
 * Get access to the current person, check rights and handle login
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuAuth {

	/**
	 * Instance of the logged in person
	 *
	 * @var	TodoyuContactPerson
	 */
	private static $person = null;



	/**
	 * Check if current person is logged in
	 *
	 * @return	Boolean
	 */
	public static function isLoggedIn() {
		return self::getPersonID() !== 0;
	}



	/**
	 * Get person object of current person
	 *
	 * @param	Boolean		$reload		Force to reinit person from current session value
	 * @return	TodoyuContactPerson
	 */
	public static function getPerson($reload = false) {
		if( is_null(self::$person) || $reload ) {
			if( self::getPersonID() !== 0 ) {
				self::$person = TodoyuContactPersonManager::getPerson(self::getPersonID());
			} else {
				self::$person = TodoyuContactPersonManager::getPerson(0);
			}
		}

		return self::$person;
	}



	/**
	 * Get role IDs of the current person
	 *
	 * @return	Array
	 */
	public static function getRoleIDs() {
		return self::getPerson()->getRoleIDs();
	}



	/**
	 * Get ID of the currently logged in person
	 * 0 means there is no person logged in
	 *
	 * @return	Integer
	 */
	public static function getPersonID() {
		return (int) TodoyuSession::get('person');
	}



	/**
	 * Set ID of currently logged in person
	 *
	 * @param	Integer		$idPerson
	 */
	public static function setPersonID($idPerson) {
		TodoyuSession::set('person', (int) $idPerson);
	}



	/**
	 * Register person as logged in
	 *
	 * @param	Integer		$idPerson
	 */
	public static function login($idPerson) {
			// Log successful login
		TodoyuLogger::logNotice('Login person (' . $idPerson . ')', $idPerson);
			// Generate a new session ID for the logged in person
		session_regenerate_id(true);
			// Set current person id
		self::setPersonID($idPerson);
			// Set new person in Todoyu object
		Todoyu::reset();
			// Reload rights
		TodoyuRightsManager::reloadRights();
			// Call login hook
		TodoyuHookManager::callHook('core', 'login', array($idPerson));
	}



	/**
	 * Logout current person
	 */
	public static function logout() {
			// Call logout hook
		TodoyuHookManager::callHook('core', 'logout');
			// Clear session
		TodoyuSession::clear();
			// Delete relogin cookie
		TodoyuCookieLogin::removeRemainLoginCookie();
			// Generate a new session ID for the logged out person
		session_regenerate_id(true);
	}



	/**
	 * Check whether $username and $password are a valid login
	 *
	 * @param	String		$username		Username
	 * @param	String		$passwordHash	Password as md5
	 * @return	Boolean
	 */
	public static function isValidLogin($username, $passwordHash) {
		return TodoyuContactPersonManager::isValidLogin($username, $passwordHash);
	}



	/**
	 * Check whether an action is allowed
	 *
	 * @param	Integer		$extKey
	 * @param	Integer		$right
	 * @return	Boolean
	 */
	public static function isAllowed($extKey, $right) {
		return TodoyuRightsManager::isAllowed($extKey, $right);
	}



	/**
	 * Check whether current person is admin
	 *
	 * @return	Boolean
	 */
	public static function isAdmin() {
		return self::getPerson()->isAdmin();
	}



	/**
	 * Check whether the current person is working for the internal company
	 *
	 * @return  Boolean
	 */
	public static function isInternal() {
		return self::getPerson()->isInternal();
	}



	/**
	 * Check whether the current person is NOT working for the internal company
	 *
	 * @return  Boolean
	 */
	public static function isExternal() {
		return self::getPerson()->isExternal();
	}



	/**
	 * There can be actions of extensions defined, which do not require a login
	 * (I know, this can be a security problem!)
	 * This is for example used to verfiy login data by loginpage (which is an normal action)
	 *
	 * Extensions can add their own actions to the config array Todoyu::$CONFIG['AUTH']['noLoginRequired'][EXTNAME][] = ACTION
	 *
	 * @param	String		$extension
	 * @param	String		$controller
	 * @return	Boolean
	 */
	public static function isNoLoginRequired($extension, $controller) {
		$extension	= strtolower($extension);
		$controller	= strtolower($controller);

			// Check if for this extension an array is defined
		if( is_array(Todoyu::$CONFIG['AUTH']['noLoginRequired'][$extension]) ) {
			if( in_array($controller, Todoyu::$CONFIG['AUTH']['noLoginRequired'][$extension]) ) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Send not logged in message for AJAX requests
	 *
	 * @param	Array		$requestVars
	 * @param	Array		$originalRequestVars
	 * @return	Array
	 */
	public static function hookSendNotLoggedInForAjaxRequest(array $requestVars, array $originalRequestVars) {
		if( ! self::isLoggedIn() && ! self::isNoLoginRequired($requestVars['ext'], $requestVars['ctrl']) ) {
			if( TodoyuRequest::isAjaxRequest() ) {
				self::sendNotLoggedInHeader();
				echo "NOT LOGGED IN";
				exit();
			}
		}

		return $requestVars;
	}



	/**
	 * Override request vars, if person is not logged in
	 *
	 * @param	Array		$requestVars
	 * @param	Array		$originalRequestVars
	 * @return	Array
	 */
	public static function hookRedirectToLoginIfNotLoggedIn(array $requestVars, array $originalRequestVars) {
		if( ! self::isLoggedIn() && ! self::isNoLoginRequired($requestVars['ext'], $requestVars['ctrl']) ) {
				// On normal request, change controller to login page
			$requestVars['ext']		= Todoyu::$CONFIG['AUTH']['login']['ext'];
			$requestVars['ctrl']	= Todoyu::$CONFIG['AUTH']['login']['controller'];
			$requestVars['action']	= 'default';
		}

		return $requestVars;
	}



	/**
	 * Send header to inform the user that the AJAX request failed because of not being logged-in
	 */
	private static function sendNotLoggedInHeader() {
		TodoyuHeader::sendTodoyuHeader('notLoggedIn', 1);
	}


}

?>