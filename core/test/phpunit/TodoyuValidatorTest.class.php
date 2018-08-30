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
 * [Enter Class Description]
 *
 * @package		Todoyu
 * @subpackage	[Subpackage]
 */
class TodoyuValidatorTest extends PHPUnit_Framework_TestCase {

	public function testIsEmail() {
		$this->assertTrue(TodoyuValidator::isEmail('team@todoyu.com'));
		$this->assertTrue(TodoyuValidator::isEmail('a@bc.de'));
		$this->assertTrue(TodoyuValidator::isEmail('with-dash@sub.domain.com'));
		$this->assertFalse(TodoyuValidator::isEmail('with space@sub.domain.com'));
	}

	public function testIsDigit() {
		$this->assertTrue(TodoyuValidator::isDigit(1));
		$this->assertTrue(TodoyuValidator::isDigit('0'));
		$this->assertTrue(TodoyuValidator::isDigit(-2342342));
		$this->assertFalse(TodoyuValidator::isDigit('a10'));
	}

	public function testIsNumber() {
		$this->assertTrue(TodoyuValidator::isNumber(1));
		$this->assertTrue(TodoyuValidator::isNumber(-234));
		$this->assertTrue(TodoyuValidator::isNumber(0));
		$this->assertTrue(TodoyuValidator::isNumber('123'));
		$this->assertFalse(TodoyuValidator::isNumber('1d'));
	}


	public function testIsDecimal() {
		$this->assertTrue(TodoyuValidator::isDecimal(1));
		$this->assertTrue(TodoyuValidator::isDecimal(2.32423));
	}


	public function testisnotempty() {
		$this->assertTrue(TodoyuValidator::isNotEmpty('a'));
		$this->assertFalse(TodoyuValidator::isNotEmpty(' '));
		$this->assertFalse(TodoyuValidator::isNotEmpty(null));
	}

	public function testisinrange() {
		$this->assertTrue(TodoyuValidator::isInRange(2, 1, 3));
		$this->assertFalse(TodoyuValidator::isInRange(2, 1, 2, false));
		$this->assertTrue(TodoyuValidator::isInRange(2, 1, 2));
		$this->assertFalse(TodoyuValidator::isInRange(7, 1, 2));
		$this->assertTrue(TodoyuValidator::isInRange('1', 1, 2));
	}

	public function testismin() {
		$this->assertTrue(TodoyuValidator::isMin(1, 1));
		$this->assertTrue(TodoyuValidator::isMin(2, 1));
		$this->assertFalse(TodoyuValidator::isMin(2, 2, false));
		$this->assertFalse(TodoyuValidator::isMin(-2, 2));
		$this->assertTrue(TodoyuValidator::isMin(-2, -3));
	}


	public function testismax() {
		$this->assertTrue(TodoyuValidator::isMax(1, 1));
		$this->assertTrue(TodoyuValidator::isMax(2, 3));
		$this->assertFalse(TodoyuValidator::isMax(2, 2, false));
		$this->assertFalse(TodoyuValidator::isMax(2, -2));
		$this->assertTrue(TodoyuValidator::isMax(-3, -2));
	}


	public function testhasminlength() {
		$this->assertTrue(TodoyuValidator::hasMinLength('test', 2));
		$this->assertTrue(TodoyuValidator::hasMinLength('test', 4));
		$this->assertFalse(TodoyuValidator::hasMinLength('tes ', 4));
	}

	public function testhasmaxlength() {
		$this->assertTrue(TodoyuValidator::hasMaxLength('test', 6));
		$this->assertTrue(TodoyuValidator::hasMaxLength('test', 4));
		$this->assertTrue(TodoyuValidator::hasMaxLength('tes ', 3));
		$this->assertFalse(TodoyuValidator::hasMaxLength('tes ', 2));
	}


	public function testisnotzerotime() {
		$this->assertTrue(TodoyuValidator::isNotZerotime('00:10'));
		$this->assertTrue(TodoyuValidator::isNotZerotime(':10'));
		$this->assertFalse(TodoyuValidator::isNotZerotime('00:00'));
		$this->assertFalse(TodoyuValidator::isNotZerotime(''));
		$this->assertTrue(TodoyuValidator::isNotZerotime('1:'));
	}


	public function testillegalchars() {
		// Method is not correct yet, no testing until fixed
	}


}

?>