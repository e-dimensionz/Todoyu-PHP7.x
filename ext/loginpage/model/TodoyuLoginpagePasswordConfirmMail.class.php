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
 * Password confirm reset mail for loginpage
 *
 * @package		Todoyu
 * @subpackage	Loginpage
 */
class TodoyuLoginpagePasswordConfirmMail extends TodoyuLoginpagePasswordMail {

	/**
	 * Reset hash
	 *
	 * @var	String
	 */
	private $hash;



	/**
	 * Initialize mail
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$hash
	 * @param	Array		$config
	 */
	public function __construct($idPerson, $hash, array $config = array()) {
		$this->hash		= $hash;

		parent::__construct($idPerson, $config);

		$this->init();
	}



	/**
	 * Init mail
	 */
	private function init() {
		$this->setSubject('loginpage.ext.forgotpassword.mail.confirmation.title');
		$this->setHeadline('loginpage.ext.forgotpassword.mail.confirmation.title');
	}



	/**
	 * Get mail data
	 *
	 * @return	Array
	 */
	protected function getData() {
		return array(
			'confirmationlink'	=> $this->getConfirmationLink()
		);
	}



	/**
	 * Get mail template
	 *
	 * @param	Boolean		$asHtml
	 * @return	String
	 */
	protected function getTemplate($asHtml = true) {
		return $this->getTemplatePath('confirm', $asHtml);
	}



	/**
	 * Get link to confirm password reset
	 *
	 * @return	String
	 */
	private function getConfirmationLink() {
		return TodoyuString::buildUrl(
			array(
				'ext'			=> 'loginpage',
				'controller'	=> 'ext',
				'action'		=> 'confirmationmail',
				'hash'			=> $this->hash,
				'userName'		=> $this->mailReceiver->getPerson()->getUsername()
			)
		);
	}

}

?>