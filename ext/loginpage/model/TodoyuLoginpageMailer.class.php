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
 * @package		Todoyu
 * @subpackage	Loginpage
 */
class TodoyuLoginpageMailer {

	/**
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$password
	 * @return	Boolean
	 */
	public static function sendNewPasswordMail($idPerson, $password) {
		$mail	= new TodoyuLoginpagePasswordResetMail($idPerson, $password);

		return $mail->send();
	}



	/**
	 * Send confirmation email
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$hash
	 * @return	Boolean
	 */
	public static function sendConfirmationMail($idPerson, $hash) {
		$mail	= new TodoyuLoginpagePasswordConfirmMail($idPerson, $hash);

		return $mail->send();
	}

}

?>