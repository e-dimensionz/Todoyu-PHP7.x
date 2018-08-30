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
 * [Add class description]
 *
 * @package		Todoyu
 * @subpackage	Hosting
 */
class TodoyuDayRangeTest extends PHPUnit_Framework_TestCase {

	protected static $dateStart;

	protected static $dateEnd;

	public static function setUpBeforeClass() {
		self::$dateStart	= mktime(0, 0, 0, 1, 1, 2011);
		self::$dateEnd		= mktime(23, 59, 59, 2, 1, 2011);
	}


	/**
	 * @return	TodoyuDayRange
	 */
	protected function getRange() {
		return new TodoyuDayRange(self::$dateStart, self::$dateEnd);
	}

	public function testSetStart() {
		$range	= $this->getRange();
		$date	= mktime(12, 54, 20, 1, 1, 2011);

		$range->setStart($date);

		$this->assertEquals(self::$dateStart, $range->getStart());
	}

	public function testSetEnd() {
		$range	= $this->getRange();
		$date	= mktime(12, 54, 20, 2, 1, 2011);

		$range->setEnd($date);

		$this->assertEquals(self::$dateEnd, $range->getEnd());
	}


	public function testSetStartDate() {
		$range	= $this->getRange();
		$date	= mktime(0, 0, 0, 2, 1, 2011);

		$range->setDateStart(2011, 2, 1);

		$this->assertEquals($date, $range->getStart());
	}

	public function testSetEndDate() {
		$range	= $this->getRange();
		$date	= mktime(23, 59, 59, 2, 1, 2011);

		$range->setDateEnd(2011, 2, 1);

		$this->assertEquals($date, $range->getEnd());
	}




	public function testGetDiffInDays() {
		$range	= $this->getRange();

		$this->assertEquals(32, $range->getDurationInDays());
	}


	public function testGetDayTimestamps() {
		$range	= $this->getRange();

		$timestamps	= $range->getDayTimestamps();

		$this->assertInternalType('array', $timestamps);
		$this->assertEquals(32, sizeof($timestamps));
		$this->assertEquals($range->getStart(), $timestamps[0]);
		$this->assertLessThan($range->getEnd(), $timestamps[31]);


		$range	= $this->getRange();

		$timestamps	= $range->getDayTimestamps('Y-m-d');

		$this->assertEquals('2011-01-01', $timestamps[0]);
		$this->assertEquals('2011-01-02', $timestamps[1]);
	}


	public function testGetDayTimestampsMap() {
		$range	= $this->getRange();
		$map	= $range->getDayTimestampsMap('Ymd', true);

		$this->assertArrayHasKey('20110101', $map);
		$this->assertArrayHasKey('20110102', $map);
		$this->assertTrue($map['20110101']);
	}

}

?>