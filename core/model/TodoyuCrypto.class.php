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
 * Crypto functions
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCrypto {

	/**
	 * Mcrypt instance
	 *
	 * @var	Object
	 */
	private static $mcrypt = null;



	/**
	 * Initialize mcrypt
	 */
	private static function initMcrypt() {
		if( is_null(self::$mcrypt) ) {
				// Open module
			self::$mcrypt = mcrypt_module_open('tripledes', '', 'ecb', '');
				// Random seed
			$random = 596328;
				// Generate initialisation vector
			$vector	= mcrypt_create_iv(mcrypt_enc_get_iv_size(self::$mcrypt), $random);
				// Get the expected key size based on mode and cipher
			$expectedKeySize = mcrypt_enc_get_key_size(self::$mcrypt);
				// Get a key in the needed length (use encryption key)
			$key = substr(Todoyu::$CONFIG['SYSTEM']['encryptionKey'], 0, $expectedKeySize);
				// Initialize mcrypt library with mode/cipher, encryption key, and random initialization vector
			mcrypt_generic_init(self::$mcrypt, $key, $vector);
		}
	}



	/**
	 * Encrypt element
	 *
	 * @param	Mixed		$input		String,Array,Object,... (will be serialized)
	 * @return	String
	 */
	public static function encrypt($input) {
		self::initMcrypt();

		$stringToEncrypt	= serialize($input);
		$encryptedString	= mcrypt_generic(self::$mcrypt, $stringToEncrypt);

		return base64_encode($encryptedString);
	}



	/**
	 * Decrypt string to element
	 *
	 * @param	String		$encryptedString
	 * @return	Mixed		With unserialize
	 */
	public static function decrypt($encryptedString) {
		self::initMcrypt();

		$encryptedString	= base64_decode($encryptedString);
		$decryptedString	= mdecrypt_generic(self::$mcrypt, $encryptedString);

		return unserialize($decryptedString);
	}



	/**
	 * Generate encryption key
	 *
	 * @return	String
	 */
	public static function makeEncryptionKey() {
		return str_replace('=', '', base64_encode(md5(NOW . serialize($_SERVER) . session_id() . rand(1000, 30000))));
	}

}

?>