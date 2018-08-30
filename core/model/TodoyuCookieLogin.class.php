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
 * Cookie login functions to handle the remain login cookie
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCookieLogin {

	/**
	 * Hook to process the "remain login" cookie if not yet logged in
	 * Called by the core->onload hook
	 *
	 * @param	Array		$requestVars
	 * @param	Array		$originalRequestVars
	 * @return	Array
	 */
	public static function hookTryCookieLogin(array $requestVars, array $originalRequestVars) {
			// Only make cookie login, if not already done
		if( ! TodoyuAuth::isLoggedIn() ) {

				// Check for cookie login data
			$cookieName	= Todoyu::$CONFIG['AUTH']['loginCookieName'];
			$cookieValue= $_COOKIE[$cookieName];

			if( ! empty($cookieValue) ) {
					// Decrypt cookie data
				$cookieData	= TodoyuCrypto::decrypt($cookieValue);

				TodoyuLogger::logDebug('Try to login with cookie, is cookie data valid?');

					// If
				if( is_array($cookieData) ) {
					$userAgentHash	= self::getUserAgentShortHash();

					if( $cookieData['useragentHash'] === $userAgentHash ) {
						if( TodoyuAuth::isValidLogin($cookieData['username'], $cookieData['passhash']) ) {
							$idPerson = TodoyuContactPersonManager::getPersonIDByUsername($cookieData['username']);
							TodoyuAuth::login($idPerson);
							self::setRemainLoginCookie($idPerson);

							TodoyuLogger::logCore('Logged in with cookie, proceed request');

							// Proceed with the request, because we're logged in and it's all ok
						} else {
							TodoyuLogger::logNotice('Cookie login failed. username/password mismatch', TodoyuLogger::LEVEL_SECURITY);
							self::removeRemainLoginCookie();
						}
					} else {
						TodoyuLogger::logNotice('Cookie login failed for user [' . $cookieData['username'] . '] (useragent is different than in the encrypted login cookie)', TodoyuLogger::LEVEL_SECURITY);
						TodoyuLogger::logDebug('Current user agent <' . $_SERVER['HTTP_USER_AGENT'] . '>');
						TodoyuLogger::logDebug('User Agent Hash Compare: expect: <' . $cookieData['useragentHash'] . '>, actual <' . $userAgentHash . '>');
					}
				} else {
					TodoyuLogger::logNotice('Decrypted cookie date is not an array', TodoyuLogger::LEVEL_ERROR, $cookieData);
				}
			}
		}

		return $requestVars;
	}



	/**
	 * Set encrypted login cookie for direct login
	 *
	 * @param	Integer		$idPerson
	 */
	public static function setRemainLoginCookie($idPerson) {
		$cookieName	= Todoyu::$CONFIG['AUTH']['loginCookieName'];
		$value		= self::generateRemainLoginCode($idPerson);
		$expires	= NOW + TodoyuTime::SECONDS_WEEK;

		setcookie($cookieName, $value, $expires, PATH_WEB, null, false, true);
	}



	/**
	 * Remove remain login cookie
	 */
	public static function removeRemainLoginCookie() {
		$cookieName	= Todoyu::$CONFIG['AUTH']['loginCookieName'];
		$expire		= NOW - 10000;

		setcookie($cookieName, '', $expire, PATH_WEB, null, false, true);
	}



	/**
	 * Generate the encrypted content for the remain login cookie
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function generateRemainLoginCode($idPerson) {
		$idPerson	= (int) $idPerson;
		$person		= TodoyuContactPersonManager::getPerson($idPerson);
		$data		= array(
			'username'		=> $person->getUsername(),
			'passhash'		=> $person->getPassword(),
			'useragentHash'	=> self::getUserAgentShortHash()
		);

		return TodoyuCrypto::encrypt($data);
	}



	/**
	 * Get a short hash of the current user agent
	 *
	 * @return	String
	 */
	public static function getUserAgentShortHash() {
		return substr(md5($_SERVER['HTTP_USER_AGENT']), 10, 10);
	}

}

?>