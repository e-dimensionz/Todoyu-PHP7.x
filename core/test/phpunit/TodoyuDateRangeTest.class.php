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
class TodoyuDateRangeTest extends PHPUnit_Framework_TestCase {

	protected static $dateStart;

	protected static $dateEnd;

	/**
	 * @var TodoyuDateRange
	 */
	protected $range;

	public static function setUpBeforeClass() {
		self::$dateStart	= mktime(0, 0, 0, 1, 1, 2011);
		self::$dateEnd		= mktime(0, 0, 0, 2, 1, 2011);
	}

	public function setUp() {
		$this->range = $this->getRange();
	}


	/**
	 * @return	TodoyuDateRange
	 */
	protected function getRange() {
		return new TodoyuDateRange(self::$dateStart, self::$dateEnd);
	}

	public function testGetId() {
		$result	= $this->range->getID();
		$expect	= '2011010100000020110102000000';

		$this->assertEquals($expect, $result);
	}

	public function testGetStart() {
		$this->assertEquals(self::$dateStart, $this->range->getStart());
	}


	public function testGetEnd() {
		$this->assertEquals(self::$dateEnd, $this->range->getEnd());
	}


	public function testSetStart() {
		$this->range->setStart(5);

		$this->assertEquals(5, $this->range->getStart());
	}


	public function testSetStartDate() {
		$date	= mktime(0, 0, 0, 1, 1, 2015);

		$this->range->setDateStart(2015, 1, 1);

		$this->assertEquals($date, $this->range->getStart());
	}

	public function testSetEnd() {
		$this->range->setEnd(5);

		$this->assertEquals(5, $this->range->getEnd());
	}


	public function testSetEndDate() {
		$date	= mktime(0, 0, 0, 1, 1, 2015);

		$this->range->setDateEnd(2015, 1, 1);

		$this->assertEquals($date, $this->range->getEnd());
	}

	public function testEndsBefore() {
		$this->range->setEnd(5);

		$this->assertTrue($this->range->endsBefore(6));
	}


	public function testStartsBefore() {
		$this->range->setStart(5);

		$this->assertTrue($this->range->startsBefore(6));
	}


	public function testSetRange() {
		$this->range->setRange(5, 6);

		$this->assertEquals(5, $this->range->getStart());
		$this->assertEquals(6, $this->range->getEnd());
	}

	public function testEndsAfter() {
		$this->range->setEnd(5);

		$this->assertTrue($this->range->endsAfter(4));
	}


	public function testStartsAfter() {
		$this->range->setStart(5);

		$this->assertTrue($this->range->startsAfter(4));
	}


	public function testIsActive() {
		$date1	= mktime(0,0,0,1,2,2011);
		$date2	= mktime(0,0,0,1,2,2012);

		$this->assertTrue($this->range->isActive($date1));
		$this->assertFalse($this->range->isActive($date2));
	}


	public function testIsPeriodInRange() {
		$date1	= mktime(0,0,0,1,2,2011);
		$date2	= mktime(0,0,0,1,3,2011);
		$date3	= mktime(0,0,0,2,3,2011);
		$date4	= mktime(0,0,0,2,5,2011);

		$this->assertTrue($this->range->isPeriodInRange($date1, $date2));
		$this->assertFalse($this->range->isPeriodInRange($date1, $date3));
		$this->assertTrue($this->range->isPeriodInRange($date1, $date3, true));
		$this->assertFalse($this->range->isPeriodInRange($date3, $date4, true));
	}

	public function testGetDiff() {
		$this->range->setStart(1);
		$this->range->setEnd(5);

		$this->assertEquals(4, $this->range->getDuration());
	}


	public function testSetStartLimit() {
		$this->range->setStart(5);

		$this->range->setStartLimit(6);

		$this->assertEquals(6, $this->range->getStart());
	}

	public function testSetEndLimit() {
		$this->range->setEnd(5);

		$this->range->setEndLimit(4);

		$this->assertEquals(4, $this->range->getEnd());
	}


	public function testIsFullYearRange() {
		$this->range->setDateStart(2011, 1, 1);
		$this->range->setDateEnd(2011, 12, 31);

		$this->assertTrue($this->range->isFullYearRange());

		$this->range->setDateEnd(2011, 12, 30);

		$this->assertFalse($this->range->isFullYearRange());
	}


	public function testIsFullMonthRange() {
		$this->range->setDateStart(2011, 1, 1);
		$this->range->setDateEnd(2011, 1, 31);

		$this->assertTrue($this->range->isFullMonthRange());

		$this->range->setDateEnd(2011, 1, 30);

		$this->assertFalse($this->range->isFullMonthRange());
	}

	public function testIsInOneYear() {
		$this->range->setDateStart(2011, 1, 1);
		$this->range->setDateEnd(2011, 8, 1);

		$this->assertTrue($this->range->isInOneYear());

		$this->range->setDateEnd(2012, 1, 1);

		$this->assertFalse($this->range->isInOneYear());
	}


	public function testIsInOneMonth() {
		$this->range->setDateStart(2011, 1, 1);
		$this->range->setDateEnd(2011, 1, 20);

		$this->assertTrue($this->range->isInOneMonth());

		$this->range->setDateEnd(2012, 2, 1);

		$this->assertFalse($this->range->isInOneMonth());
	}


	public function testIsStartStartOfMonth() {
		$this->range->setDateStart(2011, 1, 1);

		$this->assertTrue($this->range->isStartStartOfMonth());

		$this->range->setDateStart(2011, 1, 2);

		$this->assertFalse($this->range->isStartStartOfMonth());
	}


	public function testIsEndEndOfMonth() {
		$this->range->setDateEnd(2011, 1, 31);

		$this->assertTrue($this->range->isEndEndOfMonth());

		$this->range->setDateEnd(2011, 1, 30);

		$this->assertFalse($this->range->isEndEndOfMonth());
	}

	public function testGetLabel() {
		Todoyu::setLocale('en_GB');

		$this->range->setDateStart(2011, 1, 1);
		$this->range->setDateEnd(2011, 12, 31);
		$expect	= '2011';
		$this->assertEquals($expect, $this->range->getLabel());

		$this->range->setDateStart(2011, 1, 1);
		$this->range->setDateEnd(2011, 1, 31);
		$expect	= 'January 2011';
		$this->assertEquals($expect, $this->range->getLabel());

		$this->range->setDateStart(2011, 1, 1);
		$this->range->setDateEnd(2011, 3, 31);
		$expect	= 'January 2011 - March 2011';
		$this->assertEquals($expect, $this->range->getLabel());

		$this->range->setDateStart(2011, 1, 2);
		$this->range->setDateEnd(2011, 3, 31);
		$expect	= 'January 02 2011 - March 2011';
		$this->assertEquals($expect, $this->range->getLabel());

		$this->range->setDateStart(2011, 1, 2);
		$this->range->setDateEnd(2012, 3, 30);
		$expect	= 'January 02 2011 - March 30 2012';
		$this->assertEquals($expect, $this->range->getLabel());
	}


	public function test__toString() {
		$this->range->setDateStart(2011, 1, 1);
		$this->range->setDateEnd(2011, 1, 2);

		$expected	= 'Sat, 01 Jan 2011 00:00:00 +0100 - Sun, 02 Jan 2011 00:00:00 +0100';

		$this->assertEquals($expected, trim($this->range));
	}


	public function testSetMaxRanges() {
		$this->range->setMaxRanges();

		$this->assertEquals(PHP_INT_MIN, $this->range->getStart());
		$this->assertEquals(PHP_INT_MAX, $this->range->getEnd());
	}

	public function testSetDateStart() {
		$this->range->setDateStart(2012, 8, 1);
		$expect	= mktime(0, 0, 0, 8, 1, 2012);
		$result	= $this->range->getStart();

		$this->assertEquals($expect, $result);
	}


	public function testSetDateEnd() {
		$this->range->setDateEnd(2012, 8, 1);
		$expect	= mktime(0, 0, 0, 8, 1, 2012);
		$result	= $this->range->getEnd();

		$this->assertEquals($expect, $result);
	}

	public function testSetMinLength() {
		$this->range->setDateStart(2012, 1, 1, 0);
		$this->range->setDateEnd(2012, 1, 1, 2);

		$minLenght	= 3 * TodoyuTime::SECONDS_HOUR;
		$this->range->setMinLength($minLenght);

		$expect		= mktime(3, 0, 0, 1, 1, 2012);
		$result		= $this->range->getEnd();

		$this->assertEquals($expect, $result);
	}

	public function testIsInRange() {
		$inRangeDate	= mktime(0, 0, 0, 1, 14, 2011);
		$notInRangeDate	= mktime(0, 0, 0, 2, 2, 2011);

		$isInRange		= $this->range->isInRange($inRangeDate);
		$isNotInRange	= $this->range->isInRange($notInRangeDate);

		$this->assertTrue($isInRange);
		$this->assertFalse($isNotInRange);

		$limitDate		= mktime(0, 0, 0, 1, 1, 2011);
		$withLimit		= $this->range->isInRange($limitDate);
		$withoutLimit	= $this->range->isInRange($limitDate, false);

		$this->assertTrue($withLimit);
		$this->assertFalse($withoutLimit);
	}


	public function testContains() {
		$containedRange		= new TodoyuDateRange(strtotime('2011-01-02'), strtotime('2011-01-15'));
		$notContainedRange	= new TodoyuDayRange(strtotime('2011-01-20'), strtotime('2011-02-10'));

		$isConained		= $this->range->contains($containedRange);
		$notContained	= $this->range->contains($notContainedRange);

		$this->assertTrue($isConained);
		$this->assertFalse($notContained);
	}


	public function testIsOverlapping() {
		$overlappingRange		= new TodoyuDateRange(strtotime('2010-12-02'), strtotime('2011-01-15'));
		$notOverlappingRange	= new TodoyuDayRange(strtotime('2011-02-20'), strtotime('2011-02-25'));

		$isOverlapping		= $this->range->isOverlapping($overlappingRange);
		$notOverlapping		= $this->range->isOverlapping($notOverlappingRange);

		$this->assertTrue($isOverlapping);
		$this->assertFalse($notOverlapping);
	}


	public function testGetDuration() {
		$expectedDuration	= 31 * TodoyuTime::SECONDS_DAY;
		$duration			= $this->range->getDuration();

		$this->assertEquals($expectedDuration, $duration);
	}


	public function testSetRangeLimits() {
		$dateStart	= strtotime('2011-01-05');
		$dateEnd	= strtotime('2011-01-10');
		$limitRange	= new TodoyuDateRange($dateStart, $dateEnd);

		$this->range->setRangeLimits($limitRange);

		$newDateStart	= $this->range->getStart();
		$newDateEnd		= $this->range->getEnd();

		$this->assertEquals($dateStart, $newDateStart);
		$this->assertEquals($dateEnd, $newDateEnd);
	}


	public function testIsInOneDay() {
		$oneDayRange	= new TodoyuDateRange(strtotime('2011-01-01 15:00:00'), strtotime('2011-01-01 16:00:00'));
		$multiDayRange	= new TodoyuDateRange(strtotime('2011-01-01 15:00:00'), strtotime('2011-01-02 16:00:00'));

		$isOneDay		= $oneDayRange->isInOneDay();
		$isNotOneDay	= $multiDayRange->isInOneDay();

		$this->assertTrue($isOneDay);
		$this->assertFalse($isNotOneDay);
	}

	public function testGetDates() {
		$dates		= $this->range->getDates();
		$dateStart	= $this->range->getStart();
		$dateEnd	= $this->range->getEnd();

		$this->assertInternalType('array', $dates);
		$this->assertArrayHasKey('start', $dates);
		$this->assertArrayHasKey('end', $dates);

		$this->assertEquals($dateStart, $dates['start']);
		$this->assertEquals($dateEnd, $dates['end']);
	}

	public function testGetDayTimestamps() {
		$timestamps	= $this->range->getDayTimestamps();
		$dateStart	= $this->range->getStart();

		$this->assertInternalType('array', $timestamps);
		$this->assertEquals($dateStart, $timestamps[0]);
		$this->assertEquals(32, sizeof($timestamps));


		$timestampsFormat	= $this->range->getDayTimestamps('Y-m-d');
		$this->assertEquals('2011-01-02', $timestampsFormat[1]);
		$this->assertEquals('2011-02-01', $timestampsFormat[31]);
	}


	public function testGetDayTimestampsMap() {
		// no tests
		// @see testGetDayMap()
	}

	public function testGetDayMap() {
		$dayMap1	= $this->range->getDayMap();
		$dateStart	= $this->range->getStart();

		$this->assertEquals(32, sizeof($dayMap1));
		$this->assertArrayHasKey($dateStart, $dayMap1);

		$dayMap2	= $this->range->getDayMap('Ymd', 2);

		$this->assertInternalType('array', $dayMap2);
		$this->assertEquals(64, array_sum($dayMap2));
		$this->assertArrayHasKey('20110101', $dayMap2);
		$this->assertEquals(2, $dayMap2['20110115']);
	}


	public function testGetOverlappingRange() {
		$otherRange			= new TodoyuDateRange(strtotime('2011-01-20'), strtotime('2011-02-10'));
		$overlappingRange	= $this->range->getOverlappingRange($otherRange);

		$dateStart	= strtotime('2011-01-20');
		$dateEnd	= strtotime('2011-02-01');

		$this->assertInstanceOf('TodoyuDateRange', $overlappingRange);
		$this->assertEquals($dateStart, $overlappingRange->getStart());
		$this->assertEquals($dateEnd, $overlappingRange->getEnd());
	}


	public function testGetAmountOfDays() {
		$amountDays	= $this->range->getAmountOfDays();

		$this->assertEquals(32, $amountDays);
	}


	public function testGetLabelWithTime() {
		$oneDayRange	= new TodoyuDateRange(strtotime('2011-01-01 15:20:00'), strtotime('2011-01-01 18:00:00'));
		$multiDayRange	= new TodoyuDateRange(strtotime('2011-01-01 11:15:00'), strtotime('2011-01-06 22:01:00'));

		$oneDayLabel	= $oneDayRange->getLabelWithTime();
		$multiDayLabel	= $multiDayRange->getLabelWithTime();

		$expectedOneDayLabel	= 'January 01 2011, 15:20 - 18:00';
		$expectedMultiDayLabel	= 'January 01 2011 11:15 - January 06 2011 22:01';

		$this->assertEquals($expectedOneDayLabel, $oneDayLabel);
		$this->assertEquals($expectedMultiDayLabel, $multiDayLabel);

	}


}

?>