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
 * Ext controller for loginpage
 *
 * @package		Todoyu
 * @subpackage	Loginpage
 */
class TodoyuLoginpageExtActionController extends TodoyuActionController {

	/**
	 * Default action when loading without parameters (normal call of login page)
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
			// Redirect to default view if already logged in
		if( TodoyuAuth::isLoggedIn() ) {
			TodoyuLoginpageManager::redirectToHome();
		}

			// Add login screen main tabs
		TodoyuLoginpageManager::addLoginScreenMainTabs();
			// Set default tab
		TodoyuFrontend::setDefaultTab('login');

		TodoyuPage::init('ext/loginpage/view/ext.tmpl');
		TodoyuPage::setTitle('loginpage.ext.page.title');

		$loginStatus	= $params['status'];

			// Render elements
		$panelWidgets		= TodoyuLoginpageRenderer::renderPanelWidgets();
		$loginForm			= TodoyuLoginpageRenderer::renderLoginForm($loginStatus);
		$extendedContent	= TodoyuLoginpageRenderer::renderExtendedContent($params);

		TodoyuPage::set('panelWidgets', $panelWidgets);
		TodoyuPage::set('loginForm', $loginForm);
		TodoyuPage::set('extendedContent', $extendedContent);

			// Prevent robots to index the login page
		TodoyuPage::addMetatag('robots', 'noindex');

		return TodoyuPage::render();
	}



	/**
	 * Logout request
	 *
	 * @param	Array		$params
	 */
	public function logoutAction($params) {
		TodoyuAuth::logout();
	}



	/**
	 * Login request
	 *
	 * @param	Array			$params
	 * @return	String|Void
	 */
	public function loginAction($params) {
			// If login form not submitted by AJAX, form vars are wrapped in login namespace
		if( !TodoyuRequest::isAjaxRequest() ) {
				// Get login data from login namespace
			$loginData	= $params['login'];
				// Merge login data with params
			$params		= array_merge($params, $loginData);
		}

		$username	= trim(strtolower($params['username']));
		$passHash	= trim(strtolower($params['passhash']));
		$remain		= trim(strtolower($params['remain'])) === 'true';

			// Check whether login is valid
		if( TodoyuAuth::isValidLogin($username, $passHash) ) {
				// Find person-ID by username
			$idPerson = TodoyuContactPersonManager::getPersonIDByUsername($username);

				// Login person
			TodoyuAuth::login($idPerson);

			TodoyuHookManager::callHook('loginpage', 'login', array($idPerson));

				// Set locale cookie
			TodoyuLocaleManager::setLocaleCookie();

				// Build JSON response
			$params	= array(
				'ext'		=> Todoyu::$CONFIG['FE']['DEFAULT']['ext'],
				'controller'=> Todoyu::$CONFIG['FE']['DEFAULT']['controller']
			);

			$response	= array(
				'success'	=> true,
				'redirect'	=> TodoyuString::buildUrl($params)
			);

				// Set remain login cookie
			if( $remain ) {
				TodoyuCookieLogin::setRemainLoginCookie($idPerson);
				TodoyuLoginpageManager::setRemainLoginFlagCookie();
			} else {
				TodoyuLoginpageManager::removeRemainLoginFlagCookie();
			}

		} else {
			TodoyuHookManager::callHook('loginpage', 'login.failed', array($username, $passHash));

				// Log failed login
			TodoyuLogger::logNotice('Login failed for user <' . htmlentities($username) . '>', array('username' => $username, 'passhash' => $passHash));

				// Build JSON response
			$response	= array(
				'success'	=> false,
				'message'	=> Todoyu::Label('loginpage.ext.form.status.loginFailed')
			);

				// Wait at failed log-in
			if( function_exists('sleep') ) {
				$secondsToWait = intval(Todoyu::$CONFIG['EXT']['loginpage']['waitAtFailLogin']);
				sleep($secondsToWait);
			}
		}

			// If AJAX request, send JSON. If normal request, redirect to loginpage
		if( TodoyuRequest::isAjaxRequest() ) {
			TodoyuHeader::sendTypeJSON();

			return json_encode($response);
		} else {
			if( $response['success'] ) {
				TodoyuHeader::location(TODOYU_URL, true);
			} else {
				TodoyuHeader::redirect('loginpage', 'ext');
			}
		}
	}



	/**
	 * Render forgot-password form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function loadForgotPasswordFormAction($params) {
		$username	= $params['username'];

		return TodoyuLoginpageRenderer::renderForgotPasswordForm($username);
	}



	/**
	 * Render login form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function loadLoginFormAction($params) {
		return TodoyuLoginpageRenderer::renderLoginForm();
	}



	/**
	 * Get password-forgotten form
	 *
	 * @param	Array	$params
	 * @return	String	JSON encoded password-forgotten form
	 * @todo	Remove the json_encode part, refactor method!
	 */
	public function forgotPasswordAction($params) {
		$xmlPath	= 'ext/loginpage/config/form/forgotpassword.xml';

		$forgotPasswordData	= $params['forgotpassword'];

		$form	= TodoyuFormManager::getForm($xmlPath);
		$form->addFormData($forgotPasswordData);
		$form->setRecordID(false);

		if( $form->isValid() ) {
			$userName	= $forgotPasswordData['usernameoremail'];
			if( TodoyuString::isValidEmail($userName) ) {
				$person	= TodoyuContactPersonManager::getPersonByEmail($userName);
				if( $person !== false ) {
					$userName	= $person->getUsername();
				}
			}

			if( TodoyuContactPersonManager::personExists($userName) ) {
				TodoyuLoginpageManager::sendConfirmationMail($userName);
				$response['form'] = TodoyuLoginpageRenderer::renderLoginForm();
			} else {
				TodoyuHeader::sendTodoyuErrorHeader();

//				$message = Todoyu::Label('loginpage.ext.forgotpassword.invalidusernameoremail')
//				.  '<br /><br />'
//				.  TodoyuString::buildMailtoATag(Todoyu::$CONFIG['SYSTEM']['email'], Todoyu::Label('loginpage.ext.forgotpassword.invalidusername.adminlink'));

				$response['message'] = Todoyu::Label('loginpage.ext.forgotpassword.invalidusernameoremail');
				$response['form'] = $form->render();
			}
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();
			$response['form'] = $form->render();
		}

		TodoyuHeader::sendTypeJSON();
		return json_encode($response);
	}



	/**
	 * Render confirmation email screen
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function confirmationmailAction($params) {
		$userName	= $params['userName'];
		$hash		= $params['hash'];

		$idPerson	= TodoyuContactPersonManager::getPersonIDByUsername($userName);

		$person		= TodoyuContactPersonManager::getPerson($idPerson);

		TodoyuPage::init('ext/loginpage/view/confirmationpage.tmpl');

				// Add login screen main tabs
		TodoyuLoginpageManager::addLoginScreenMainTabs();
			// Set default tab
		TodoyuFrontend::setDefaultTab('login');

		$panelWidgets		= TodoyuLoginpageRenderer::renderPanelWidgets();
		TodoyuPage::set('panelWidgets', $panelWidgets);

		if( $hash === md5($person->getUsername() . $person->get('password')) ) {
				// Hash is valid
			TodoyuLoginpageManager::createAndSendNewPassword($userName);

			TodoyuPage::set('class', 'successful');
			TodoyuPage::setTitle(Todoyu::Label('loginpage.ext.forgotpassword.confirmpage.successful.title'));
			TodoyuPage::set('title', Todoyu::Label('loginpage.ext.forgotpassword.confirmpage.successful.title'));
			TodoyuPage::set('confirmationpagetext', Todoyu::Label('loginpage.ext.forgotpassword.confirmpage.successful.text'));
		} else {
				// Hash validation failed
			TodoyuPage::set('class', 'failure');
			TodoyuPage::setTitle(Todoyu::Label('loginpage.ext.forgotpassword.confirmpage.failure.title'));
			TodoyuPage::set('title', Todoyu::Label('loginpage.ext.forgotpassword.confirmpage.failure.title'));

			$replaceArray	=  TodoyuString::buildMailtoATag(Todoyu::$CONFIG['SYSTEM']['email'], '', true);

			$label = str_replace(array('%s', '%e'), $replaceArray, Todoyu::Label('loginpage.ext.forgotpassword.confirmpage.failure.text'));

			TodoyuPage::set('confirmationpagetext', $label);
		}

		return TodoyuPage::render();
	}



	/**
	 * Render re-login popup message with form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function reloginPopupAction(array $params) {
		$tmpl	= 'ext/loginpage/view/reloginpopup.tmpl';
		$xmlPath= 'ext/loginpage/config/form/login.xml';

		$form	= TodoyuFormManager::getForm($xmlPath);
		$form->setRecordID(false);

		$data	= array(
			'message'	=> Todoyu::Label('loginpage.ext.loginexpired.message'),
			'form'		=> $form->render()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * If the cookie check is empty, cookies are not enabled in the browser and todoyu has to show an warning message.
	 * if the cookie exists delete it
	 *
	 * @param	Array	$params
	 * @return String
	 */
	public function nocookieAction(array $params) {
		TodoyuHeader::sendTodoyuErrorHeader();
		$labelCookieCheck	= Todoyu::Label('loginpage.ext.form.cookiecheck');
		$labelLink			= Todoyu::Label('loginpage.ext.form.cookiecheck.linklabel');
		$urlManual			= Todoyu::$CONFIG['EXT']['loginpage']['manuallinks']['cookies'];

		return $labelCookieCheck . '<br />'	. TodoyuString::buildATag($urlManual, $labelLink, '_blank');
	}

}

?>