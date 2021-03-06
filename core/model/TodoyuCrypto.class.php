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
	 * Initialize process cryptkey
	 */
	private static function cryptKey() {
		// Generate initialisation vector
		$method = 'AES-128-CBC'; // Or whatever you want

		$vector	= openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
		// Get the expected key size based on mode and cipher

		$ivSize = openssl_cipher_iv_length($method);

		if ($ivSize > 0) {
			/*
			 * This will fit will with most.
			 * A few might get a larger key than required, but larger is better than smaller
			 * since larger keys just get's downsized rather than padded.
			 *
			 */
			$expectedKeySize = $ivSize * 2;

		} else {
			// Defaults to 128 when IV is not used
			$expectedKeySize = 16;
		}



		// Get a key in the needed length (use encryption key)
		return substr(Todoyu::$CONFIG['SYSTEM']['encryptionKey'], 0, $expectedKeySize);

	}



	/**
	 * Encrypt element
	 *
	 * @param	Mixed		$input		String,Array,Object,... (will be serialized)
	 * @return	String
	 */
	public static function encrypt($input) {
		$key = self::cryptKey();

		$stringToEncrypt	= serialize($input);
		$encryptedString	= openssl_encrypt($stringToEncrypt, 'DES-EDE3', $key, OPENSSL_RAW_DATA);

		return base64_encode($encryptedString);
	}



	/**
	 * Decrypt string to element
	 *
	 * @param	String		$encryptedString
	 * @return	Mixed		With unserialize
	 */
	public static function decrypt($encryptedString) {
		$key = self::cryptKey();

		$encryptedString	= base64_decode($encryptedString);
		$decryptedString	= openssl_decrypt($encryptedString, 'DES-EDE3', $key, OPENSSL_RAW_DATA);

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