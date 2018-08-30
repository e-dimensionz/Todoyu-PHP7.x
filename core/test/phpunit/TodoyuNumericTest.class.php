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
 * Test for: TodoyuNumeric
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuNumericTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Array
	 */
	private $array;



	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->array = array(

		);

	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}



	/**
	 * Test TodoyuNumeric::intInRange($integer, $min = 0, $max = 2000000000)
	 */
	public function testIntInRange() {
		$this->assertEquals(50, TodoyuNumeric::intInRange(50, 0, 100));
		$this->assertEquals(-50, TodoyuNumeric::intInRange(-50, -100, 100));

		$this->assertEquals(100, TodoyuNumeric::intInRange(500, 1, 100));
		$this->assertEquals(-100, TodoyuNumeric::intInRange(-500, -100, 100));
	}



	/**
	 * Test TodoyuNumeric::intPositive
	 *
	 */
	public function testIntPositive() {
		$this->assertEquals(0, TodoyuNumeric::intPositive(0));
		$this->assertEquals(0, TodoyuNumeric::intPositive(-10));
		$this->assertEquals(10, TodoyuNumeric::intPositive(10));
	}



	/**
	 * Test TodoyuNumeric::percent($percent, $value)
	 *
	 * @todo Implement testPercent().
	 */
	public function testPercent() {
		$result_1	= TodoyuNumeric::percent(100, 47.4);
		$result_2	= TodoyuNumeric::percent(100, 101);
		$result_3	= TodoyuNumeric::percent(100, 0.5, true);
		$result_4	= TodoyuNumeric::percent(100, 0.5);
		$result_5	= TodoyuNumeric::percent(100, 0.001, true);

		$this->assertEquals(47.4, $result_1);
		$this->assertEquals(101.0, $result_2);
		$this->assertEquals(50.0, $result_3);
		$this->assertEquals(0.5, $result_4);
		$this->assertEquals(0.1, $result_5);
	}


	public function testIsversionatleast() {
		$result_1	= TodoyuNumeric::isVersionAtLeast('1.0.0', '1.0.0');

		// xx - dev - a - b - rc - # - p

		$result_2	= TodoyuNumeric::isVersionAtLeast('2.0.0dev', '2.0.0xx');
		$result_3	= TodoyuNumeric::isVersionAtLeast('2.0.0a', '2.0.0dev');
		$result_4	= TodoyuNumeric::isVersionAtLeast('2.0.0b', '2.0.0alpha');
		$result_5	= TodoyuNumeric::isVersionAtLeast('2.0.0rc', '2.0.0beta');
		$result_6	= TodoyuNumeric::isVersionAtLeast('2.0.0', '2.0.0RC');

		$result_7	= TodoyuNumeric::isVersionAtLeast('3alpha', '2.9');
		$result_8	= TodoyuNumeric::isVersionAtLeast('2.1.0', '2.1.0-alpha');

		$this->assertTrue($result_1);
		$this->assertTrue($result_2);
		$this->assertTrue($result_3);
		$this->assertTrue($result_4);
		$this->assertTrue($result_5);
		$this->assertTrue($result_6);
		$this->assertTrue($result_7);
		$this->assertTrue($result_8);
	}


	public function testRatio() {
		$value1	= 100;
		$value2	= 53.4;

		$result_1	= TodoyuNumeric::ratio($value2, $value1);
		$result_2	= TodoyuNumeric::ratio($value2, $value1, true);
		$result_3	= TodoyuNumeric::ratio($value2, $value1, true, 0);
		$result_4	= TodoyuNumeric::ratio($value2, 0, true, 0, 'no result');

		$this->assertEquals(0.534, $result_1);
		$this->assertEquals(53.4, $result_2, '', 0.1);
		$this->assertEquals(53.0, $result_3);
		$this->assertEquals('no result', $result_4);
	}


}

?>