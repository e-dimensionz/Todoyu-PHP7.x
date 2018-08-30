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
 * Password mail for loginpage (abstract)
 *
 * @package		Todoyu
 * @subpackage	Loginpage
 * @abstract
 */
abstract class TodoyuLoginpagePasswordMail extends TodoyuMail {

	/**
	 * Email receiver
	 *
	 * @var	TodoyuMailReceiverInterface
	 */
	protected $mailReceiver;



	/**
	 * Initialize mail
	 *
	 * @param	String		$idPerson		Tuple like 'type:ID' or just 'ID' which defaults the type to 'contactperson'
	 * @param	Array		$config
	 */
	public function __construct($idPerson, array $config = array()) {
		parent::__construct($config);

		$this->mailReceiver	= TodoyuMailReceiverManager::getMailReceiver($idPerson);

		$this->init();
	}



	/**
	 * Init mail
	 */
	private function init() {
		$this->setSystemAsSender();
		$this->addReceiver($this->mailReceiver);

		$this->setHtmlContent($this->getContent(true));
		$this->setTextContent($this->getContent(false));
	}



	/**
	 * Get mail content
	 *
	 * @param	Boolean		$asHtml
	 * @return	String
	 */
	private function getContent($asHtml = true) {
		$tmpl	= $this->getTemplate($asHtml);
		$data	= $this->getData();

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get template path
	 *
	 * @param	String		$type
	 * @param	Boolean		$asHtml
	 * @return	String
	 */
	protected function getTemplatePath($type, $asHtml) {
		$basePath	= 'ext/loginpage/view/email';
		$format		= $asHtml ? 'html' : 'text';
		$template	= $basePath . '/password-' . $type . '-' . $format . '.tmpl';

		return TodoyuFileManager::pathAbsolute($template);
	}



	/**
	 * Get mail template
	 *
	 * @abstract
	 * @param	Boolean		$asHtml
	 * @return	String
	 */
	abstract protected function getTemplate($asHtml = true);



	/**
	 * Get data
	 *
	 * @return	Array
	 */
	abstract protected function getData();

}

?>