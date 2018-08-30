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
 * Send notification to browser
 * Currently, the number of notes per response is limited to one (1)!
 *
 * @package		Todoyu
 * @subpackage	Core
 * @see			core/asset/js/Notification.js
 */
class TodoyuNotification {

	/**
	 * Notification type: success
	 *
	 * @var	Integer
	 */
	const TYPE_SUCCESS	= 1;

	/**
	 * Notification type: error
	 *
	 * @var	Integer
	 */
	const TYPE_ERROR	= 2;

	/**
	 * Notification type: info
	 *
	 * @var	Integer
	 */
	const TYPE_INFO		= 3;



	/**
	 * Get key of given notification type
	 *
	 * @param	Integer		$type
	 * @return	String
	 */
	public static function getNotificationTypeKey($type) {
		$typeKeys	= array(
			self::TYPE_SUCCESS	=> 'success',
			self::TYPE_ERROR	=> 'error',
			self::TYPE_INFO		=> 'info',
		);

		return $typeKeys[$type];
	}



	/**
	 * Send notification over HTTP header
	 *
	 * @param	Integer		$type
	 * @param	String		$message
	 * @param	Integer		$countdown
	 * @param	String		$identifier			Notification hash to identify and possibly remove error message of same event
	 */
	private static function notify($type, $message, $countdown = 3, $identifier = '') {
		$info	= array(
			'type'			=> self::getNotificationTypeKey($type),
			'message'		=> Todoyu::Label($message),
			'countdown'		=> $countdown,
			'identifier'	=> $identifier
		);

		TodoyuHeader::sendTodoyuHeader('note', json_encode($info));
	}



	/**
	 * Send success notification
	 *
	 * @param	String		$message
	 * @param	String		$identifier
	 */
	public static function notifySuccess($message, $identifier = '') {
		self::notify(self::TYPE_SUCCESS, $message, 3, $identifier);
	}



	/**
	 * Send error notification
	 *
	 * @param	String		$message
	 * @param	String		$identifier
	 */
	public static function notifyError($message, $identifier = '') {
		self::notify(self::TYPE_ERROR, $message, 3, $identifier);
	}



	/**
	 * Send info notification
	 *
	 * @param	String		$message
	 */
	public static function notifyInfo($message) {
		self::notify(self::TYPE_INFO, $message);
	}

}

?>