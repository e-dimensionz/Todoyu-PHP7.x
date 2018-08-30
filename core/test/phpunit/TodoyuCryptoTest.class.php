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
 * TodoyuCrypto Tests
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCryptoTest extends PHPUnit_Framework_TestCase {

	private static $oldKey;

	public static function setUpBeforeClass() {
		self::$oldKey = Todoyu::$CONFIG['SYSTEM']['encryptionKey'];

		Todoyu::$CONFIG['SYSTEM']['encryptionKey'] = '1234567890';
	}

	public static function tearDownAfterClass() {
		Todoyu::$CONFIG['SYSTEM']['encryptionKey'] = self::$oldKey;
	}

	public function testencrypt() {
		$encrypted	= TodoyuCrypto::encrypt('secret');
		$expected	= '8NL25+4LC+nDThoPYx1GNg==';

		$this->assertEquals($expected, $encrypted);
	}

	public function testdecrypt() {
		$decrypted	= TodoyuCrypto::decrypt('8NL25+4LC+nDThoPYx1GNg==');
		$expected	= 'secret';

		$this->assertEquals($expected, $decrypted);
	}

	public function testmakeencryptionkey() {
		$key	= TodoyuCrypto::makeEncryptionKey();

		$this->assertInternalType('string', $key);
		$this->assertEquals(43, strlen($key));
	}

}

?>
