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

require_once( PATH_LIB . '/php/phpmailer/class.phpmailer.php' );

/**
 * Todoyu mail
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuMail extends PHPMailer {

	/**
	 * Temporary HTML content
	 *
	 * @var	String
	 */
	protected $contentHTML = '';

	/**
	 * Temporary text content
	 *
	 * @var	String
	 */
	protected $contentText = '';

	/**
	 * Additional CSS styles
	 *
	 * @var	Array
	 */
	protected $cssStyles	= array();

	/**
	 * Headline of the todoyu email
	 *
	 * @var	String
	 */
	protected $headline	= null;

	/**
	 * @var	TodoyuMailReceiverInterface[]		To
	 */
	protected $_receivers = array();

	/**
	 * @var	TodoyuMailReceiverInterface[]		Reply to
	 */
	protected $_replyto = array();

	/**
	 * @var	TodoyuMailReceiverInterface[]		CC
	 */
	protected $_cc = array();

	/**
	 * Default config
	 *
	 * @var	Array
	 */
	protected $config = array(
		'exceptions'=> true,
		'mailer'	=> 'mail',
		'charset'	=> 'utf-8'
	);



	/**
	 * Initialize with config
	 *
	 * @param	Array		$config
	 */
	public function __construct(array $config = array()) {
		$config	= TodoyuHookManager::callHookDataModifier('core', 'mail.construct', $config);

		parent::__construct(!!$this->config['exceptions']);

		$this->config	= TodoyuArray::mergeRecursive($this->config, $config);

			// Config
		$this->initConfig();
	}



	/**
	 * Check whether current person has an SMTP account for sending emails
	 *
	 * @return	Boolean
	 */
	protected function hasCurrentPersonSmtpSenderAccount() {
		return Todoyu::person()->hasSmtpAccount();
	}



	/**
	 * Set sender address/name from config and use system mail as fallback
	 *
	 */
	protected function setSenderFromConfig() {
		if( is_array($this->config['from']) ) {
			$this->SetFrom($this->config['from']['email'], $this->config['from']['name'], 0);
		} elseif( is_numeric($this->config['from']) ) {
			$this->setSender($this->config['from']);
		} elseif( TodoyuAuth::isLoggedIn() ) {
			$this->setCurrentUserAsSender();
		} else {
			$this->setSystemAsSender();
		}
	}



	/**
	 * Initialize config
	 *
	 */
	protected function initConfig() {
		$this->CharSet	= $this->config['charset'];

		$this->initMailer();

		if( !$this->hasCurrentPersonSmtpSenderAccount() ) {
			$this->setSenderFromConfig();
		}
	}



	/**
	 * Initialize mailer config
	 * Use persons smtp account if set.
	 * Fallback to system config which can be normal mail() or an smtp account
	 *
	 */
	protected function initMailer() {
		$currentPerson	= TodoyuAuth::getPerson();

		if( $this->hasCurrentPersonSmtpSenderAccount() ) {
			$this->initSmtpConfig($currentPerson->getSmtpAccountID());
		} else {
			$this->initMailerFromSystem();
		}
	}



	/**
	 * Initialize mailer from system configuration
	 */
	protected function initMailerFromSystem() {
		$mailerType	= TodoyuMailManager::getSystemMailerType();

		if( strstr($mailerType, '_') !== false ) {
			list($type, $key) = explode('_', $mailerType, 2);

			switch( $type ) {
				case 'smtp':
					$this->initSmtpConfig($key);
					break;

				default:
					TodoyuLogger::logError('Unknown mailer type <' . $mailerType . '>');
					break;
			}
		} else {
			$this->initPhpMail();
		}
	}



	/**
	 * Init for default mail() sender
	 *
	 */
	protected function initPhpMail() {

	}



	/**
	 * Initialize SMTP configuration: add credentials from given record
	 *
	 * @param	Integer		$idAccount
	 */
	protected function initSmtpConfig($idAccount) {
		$idAccount	= intval($idAccount);
		$account	= TodoyuSysmanagerSmtpAccountManager::getAccount($idAccount);

		$this->Mailer	= 'smtp';
		$this->Host		= $account->getHost();
		$this->Port		= $account->getPort();

		if( $account->isAuthenticationRequired() ) {
			$this->SMTPAuth	= true;
			$this->Username	= $account->getUsername();
			$this->Password	= $account->getPassword();
		}

		$senderName	= $account->hasForcedName() ? $account->getForcedName() : Todoyu::person()->getFullName();

		$this->SetFrom($account->getUsername(), $senderName);

		$this->SMTPDebug  = 0; // 0 = disable debug/ 1 = echo errors+messages/ 2 = messages only
	}



	/**
	 * Render and set email HTML message
	 *
	 * @deprecated
	 * @see	setupHtmlContent
	 */
	protected function renderHtmlContent() {
		$this->setupHtmlContent();
	}



	/**
	 * Setup text and html content
	 *
	 */
	protected function setupContent() {
		$this->setupHtmlContent();
		$this->setupTextContent();
	}



	/**
	 * Render and set email HTML message
	 *
	 */
	protected function setupHtmlContent() {
		$tmpl	= 'core/view/email-html.tmpl';
		$data	= array(
			'content'	=> $this->fullyQualifyLinksInHtml($this->contentHTML),
			'subject'	=> $this->Subject,
			'headline'	=> $this->headline,
			'cssStyles'	=> $this->cssStyles
		);

		if( Todoyu::person()->hasMailSignature() ) {
			$data['signature'] = Todoyu::person()->getMailSignatureAsHtml();
		}

		$html	= Todoyu::render($tmpl, $data);

		$this->MsgHTML($html, PATH);
	}



	/**
	 * Prepare text content before sending
	 *
	 */
	protected function setupTextContent() {
		$text	= $this->contentText;

		if( Todoyu::person()->hasMailSignature() ) {
			$text .= "\n\n" . Todoyu::person()->getMailSignature();
		}

		$this->AltBody = $text;
	}




	/**
	 * Prefix links with TODOYU_URL to make them work in mails
	 *
	 * @param	String		$html
	 * @return	String
	 */
	protected function fullyQualifyLinksInHtml($html) {
		$pattern	= '/href=["\']{1}([^"\']*?)["\']{1}/is';
		$replace	= array();

		preg_match_all($pattern, $html, $matches);

		foreach($matches[1] as $link) {
			if( strncmp('http', $link, 4) === 0 ) {
				continue;
			}
			if( strncmp('javascript', $link, 10) === 0 ) {
				continue;
			}

			$replace[$link] = SERVER_URL . '/' . ltrim($link, '/');
		}

		return str_replace(array_keys($replace), array_values($replace), $html);
	}



	/**
	 * Set email headline (can be a label)
	 *
	 * @param	String		$headline
	 */
	public function setHeadline($headline) {
		$headline	= Todoyu::Label($headline);
		$headline	= TodoyuHookManager::callHookDataModifier('core', 'mail.headline', $headline, array($this));

		$this->headline	= $headline;
	}



	/**
	 * Add CSS style code
	 *
	 * @param	String		$cssStyle
	 */
	public function addCssStyles($cssStyle) {
		$this->cssStyles[] = $cssStyle;
	}



	/**
	 * Task to process before email is sent
	 *
	 */
	protected function beforeSend() {
		TodoyuHookManager::callHook('core', 'mail.beforeSend', array($this));

			// Prepare content
		$this->setupContent();

			// Modify subject
		$this->Subject = TodoyuHookManager::callHookDataModifier('core', 'mail.subject', $this->Subject, array($this));

			// Add all addresses
		$this->addAllAddresses();

			// Raise memory when attachments are sent
		$this->raiseMemoryForAttachments();
	}



	/**
	 * Raise php memory limit when attachments are added.
	 * Because the files are loaded into memory for processing, problems may occure
	 *
	 */
	protected function raiseMemoryForAttachments() {
			// Are there attachments?
		if( sizeof($this->attachment) ) {
			@ini_set('memory_limit', '128M');
		}
	}



	/**
	 * Add all mail receivers as addresses to the phpmailer object
	 *
	 */
	protected function addAllAddresses() {
			// Receivers
		foreach($this->_receivers as $mailReceiver) {
			/**
			 * @var	TodoyuMailReceiverInterface	$mailReceiver
			 */
			$mailReceiver	= TodoyuHookManager::callHookDataModifier('core', 'mail.receiver', $mailReceiver, array('to'));

			if( $mailReceiver instanceof TodoyuMailReceiverInterface ) {
				if( $mailReceiver->isEnabled() ) {
					$this->AddAddress($mailReceiver->getAddress(), $mailReceiver->getName());

					TodoyuLogger::logCore('Add email receiver <' . $mailReceiver->getName() . '><' . $mailReceiver->getAddress() . '>');
					continue;
				}
			}
			TodoyuLogger::logCore('Rejected email receiver <' . $mailReceiver->getName() . '><' . $mailReceiver->getAddress() . '>');
		}

			// Reply to
		foreach($this->_replyto as $mailReceiver) {
			/**
			 * @var	TodoyuMailReceiverInterface	$mailReceiver
			 */
			$mailReceiver	= TodoyuHookManager::callHookDataModifier('core', 'mail.receiver', $mailReceiver, array('replyto'));

			if( $mailReceiver instanceof TodoyuMailReceiverInterface ) {
				if( $mailReceiver->isEnabled() ) {
					parent::AddReplyTo($mailReceiver->getAddress(), $mailReceiver->getName());

					TodoyuLogger::logCore('Add email reply to <' . $mailReceiver->getName() . '><' . $mailReceiver->getAddress() . '>');
					continue;
				}
			}
			TodoyuLogger::logCore('Rejected email reply to <' . $mailReceiver->getName() . '><' . $mailReceiver->getAddress() . '>');
		}


			// CC
		foreach($this->_replyto as $mailReceiver) {
			/**
			 * @var	TodoyuMailReceiverInterface	$mailReceiver
			 */
			$mailReceiver	= TodoyuHookManager::callHookDataModifier('core', 'mail.receiver', $mailReceiver, array('cc'));

			if( $mailReceiver instanceof TodoyuMailReceiverInterface ) {
				if( $mailReceiver->isEnabled() ) {
					parent::AddCC($mailReceiver->getAddress(), $mailReceiver->getName());

					TodoyuLogger::logCore('Add email cc <' . $mailReceiver->getName() . '><' . $mailReceiver->getAddress() . '>');
					continue;
				}
			}
			TodoyuLogger::logCore('Rejected email cc <' . $mailReceiver->getName() . '><' . $mailReceiver->getAddress() . '>');
		}
	}



	/**
	 * Send mail
	 *
	 * @param	Boolean		$catchExceptions		Catch the exceptions and log them automatically. Returns true or false
	 * @return	Boolean		Sending was successful
	 */
	public function send($catchExceptions = true) {
		$this->beforeSend();

		$status = false;

		if( $catchExceptions ) {
			try {
				$status = parent::Send();
				TodoyuLogger::logCore('Sent email - staus: ' . ($status?'ok':'error'));
			} catch(phpmailerException $e) {
				TodoyuLogger::logError($e->getMessage());
			} catch(Exception $e) {
				TodoyuLogger::logError($e->getMessage());
			}
		} else {
			$status = parent::Send();
		}

		return $status;
	}



	/**
	 * Set system as sender of the email (system name and email)
	 */
	public function setSystemAsSender() {
		$this->SetFrom(Todoyu::$CONFIG['SYSTEM']['email'], Todoyu::$CONFIG['SYSTEM']['name'], 0);
	}



	/**
	 * Set currently logged in user as sender
	 * Fallback to system if no user is logged in
	 */
	public function setCurrentUserAsSender() {
		$idPerson	= Todoyu::personid();

		if( $idPerson === 0 ) {
			$this->setSystemAsSender();
		} else {
			$this->setSender($idPerson);
		}
	}




	/**
	 * Set mail subject
	 *
	 * @param	String		$subject
	 */
	public function setSubject($subject) {
		$subject	= Todoyu::Label($subject);

		$this->Subject = $subject;
	}



	/**
	 * Get subject
	 *
	 * @return	String
	 */
	public function getSubject() {
		return $this->Subject;
	}



	/**
	 * Set html content of the mail
	 *
	 * @param	String		$html
	 */
	public function setHtmlContent($html) {
		$this->contentHTML	= $html;
	}



	/**
	 * Set plaintext content of the mail
	 *
	 * @param	String		$text
	 */
	public function setTextContent($text) {
		$this->contentText = $text;
	}



	/**
	 * Add an attachment
	 *
	 * @param	String		$path
	 * @param	String		$name
 	 * @return	Boolean
	 */
	public function addAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {
		$path	= TodoyuFileManager::pathAbsolute($path);

		return parent::AddAttachment($path, $name);
	}



	/**
	 * Set name and email address from mail receiver object of given tuple
	 *
	 * @param	TodoyuMailReceiverInterface		$mailReceiver
	 */
	public function addReceiver(TodoyuMailReceiverInterface $mailReceiver) {
		$this->_receivers[] = $mailReceiver;
	}



	/**
	 * Add a person as reply to
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public function addReplyToPerson($idPerson) {
		$idPerson		= (int) $idPerson;
		$mailReceiver	= new TodoyuContactMailReceiverPerson($idPerson);

		$this->addReplyTo($mailReceiver);
	}



	/**
	 * Add reply to receiver
	 *
	 * @param	TodoyuMailReceiverInterface		$mailReceiver
	 */
	public function addReplyTo($mailReceiver, $name ='') {
		$this->_replyto[] = $mailReceiver;
	}



	/**
	 * Add current person as reply to
	 *
	 * @return	Boolean
	 */
	public function addCurrentPersonAsReplyTo() {
		return $this->addReplyToPerson(Todoyu::personid());
	}



	/**
	 * Add CC receiver
	 *
	 * @param TodoyuMailReceiverInterface $mailReceiver
	 * @return bool|void
	 */
	public function addCC($mailReceiver, $name = '') {
		$this->_cc[] = $mailReceiver;
	}



	/**
	 * Set sender of the email
	 *
	 * @param  Integer		$idPerson
	 * @return Boolean
	 */
	public function setSender($idPerson) {
		$idPerson	= (int) $idPerson;
		$person		= TodoyuContactPersonManager::getPerson($idPerson);

		if( $person->hasSmtpAccount() ) {
			$this->initSmtpConfig($person->getSmtpAccountID());
		} else {
			$email		= $person->getEmail();

			if( !$email ) {
				return false;
			}

			$this->SetFrom($email, $person->getFullName());
		}

		return true;
	}

}

?>