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
 * Loginpage renderer
 *
 * @package		Todoyu
 * @subpackage	Loginpage
 */
class TodoyuLoginpageRenderer {

	/**
	 * @var string		Extension key
	 */
	const EXTKEY = 'loginpage';



	/**
	 * Render login-page panel widgets
	 *
	 * @return	String
	 */
	public static function renderPanelWidgets() {
		return TodoyuPanelWidgetRenderer::renderPanelWidgets(self::EXTKEY);
	}



	/**
	 * Render login mask (form)
	 *
	 * @param	String		$status
	 * @return	String
	 */
	public static function renderLoginForm($status = null) {
		$xml	= 'ext/loginpage/config/form/login.xml';
		$form	= TodoyuFormManager::getForm($xml);
		$form->setUseRecordID(false);

			// If status is failed, show error message
		if( $status === 'failed' ) {
			$config	= array(
				'default'	=> 'loginpage.ext.form.loginFailed',
				'class'		=> 'error'
			);
			$form->getFieldset('message')->addFieldElement('info', 'comment', $config);
		}

			// Check remain login checkbox if last time was checked
		if( TodoyuLoginpageManager::hasRemainLoginFlagCookie() ) {
			/** @var	$fieldLoginRemain	TodoyuFormElement_Checkbox */
			$fieldLoginRemain	= $form->getField('loginremain');
			$fieldLoginRemain->setChecked();
		}

		return $form->render();
	}



	/**
	 * Render extended content (from registered function hooks)
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public static function renderExtendedContent(array $params) {
		$content	= '';

		$funcRefs	= TodoyuArray::assure(Todoyu::$CONFIG['EXT']['loginpage']['extendedContentHooks']);

		foreach($funcRefs as $funcRef) {
			if( TodoyuFunction::isFunctionReference($funcRef) ) {
				list($obj, $method) = explode('::', $funcRef);
				$obj 		= new $obj();
				$content	.= $obj->$method($params);
			}
		}

		return $content;
	}



	/**
	 * @return	String
	 */
	public static function renderForgotPasswordLink() {
		return Todoyu::render('ext/loginpage/view/forgotpasswordlink.tmpl', array());
	}



	/**
	 * Render form for requesting email with forgotten password
	 *
	 * @param	String	$username
	 * @return	String
	 */
	public static function renderForgotPasswordForm($username = '') {
		$xmlPath	= 'ext/loginpage/config/form/forgotpassword.xml';

		$form		= TodoyuFormManager::getForm($xmlPath);
		$form->setUseRecordID(false);
		$form->addFormData(array('username' => $username));

		return $form->render();
	}



	/**
	 * Renders the noscript check and sets a cookie.
	 * To check if javaScript & cookies are enabled
	 *
	 * @return	String
	 */
	public static function renderJavascriptAndCookieCheck() {
		$tmpl	= 'ext/loginpage/view/javascriptcheck.tmpl';

		$data	= array(
			'javaScriptManual'	=>	Todoyu::$CONFIG['EXT']['loginpage']['manuallinks']['javascript']
		);

		setcookie("check", 1, 0);

		return Todoyu::render($tmpl, $data);
	}

}

?>