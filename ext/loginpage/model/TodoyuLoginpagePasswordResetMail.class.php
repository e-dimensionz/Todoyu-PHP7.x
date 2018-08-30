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
 * Password reset mail for loginpage
 *
 * @package		Todoyu
 * @subpackage	Loginpage
 */
class TodoyuLoginpagePasswordResetMail extends TodoyuLoginpagePasswordMail {

	/**
	 * Username
	 *
	 * @var	String
	 */
	private $username;

	/**
	 * New password
	 *
	 * @var	String
	 */
	private $password;


	/**
	 * Initialize mail
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$password
	 * @param	Array		$config
	 */
	public function __construct($idPerson, $password, array $config = array()) {
		$idPerson	= intval($idPerson);
		$person	= TodoyuContactPersonManager::getPerson($idPerson);

		$this->username	= $person->getUsername();
		$this->password	= $password;

		parent::__construct($idPerson, $config);

		$this->init();
	}



	/**
	 * Init mail
	 */
	private function init() {
		$this->setSubject('loginpage.ext.forgotpassword.mail.subject.newpassword');
		$this->setHeadline('loginpage.ext.forgotpassword.mail.subject.newpassword');
	}



	/**
	 * Get mail data
	 *
	 * @return	Array
	 */
	protected function getData() {
		return array(
			'username'		=> $this->username,
			'newPassword'	=> $this->password,
			'loginlink'		=> TODOYU_URL
		);
	}



	/**
	 * Get mail template
	 *
	 * @param	Boolean		$asHtml
	 * @return	String
	 */
	protected function getTemplate($asHtml = true) {
		return $this->getTemplatePath('reset', $asHtml);
	}

}

?>