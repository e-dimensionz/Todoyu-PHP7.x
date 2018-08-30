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
 * Test for: TodoyuTime
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuTimeTest extends PHPUnit_Framework_TestCase {

	protected $timezone;

	protected $firstDayOfWeek;



	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
//		$this->timezone = Todoyu::getTimezone();
//
//		Todoyu::setTimezone('UTC');
		$this->firstDayOfWeek = Todoyu::$CONFIG['SYSTEM']['firstDayOfWeek'];
	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
//		Todoyu::setTimezone($this->timezone);
		Todoyu::$CONFIG['SYSTEM']['firstDayOfWeek'] = $this->firstDayOfWeek;
	}



	/**
	 * Test getStartOfDay
	 */
	public function testGetDayStart() {
		$timeAfternoon	= mktime(14, 0, 0, 1, 1, 2010);
		$timeDaystart	= mktime(0, 0, 0, 1, 1, 2010);

		$result1	= TodoyuTime::getDayStart($timeAfternoon);
		$result2	= TodoyuTime::getDayStart($timeDaystart);

		$this->assertEquals($timeDaystart, $result1);
		$this->assertEquals($result1, $result2);
	}

	public function testGetStartOfDay() {
		// no test
	}



	/**
	 * Test getEndOfDay
	 */
	public function testGetDayEnd() {
		$time		= mktime(14, 0, 0, 1, 1, 2010);
		$testDayend	= mktime(23, 59, 59, 1, 1, 2010);

		$timeEnd1	= TodoyuTime::getDayEnd($time);
		$timeEnd2	= TodoyuTime::getDayEnd($testDayend);

		$this->assertEquals($testDayend, $timeEnd1);
		$this->assertEquals($testDayend, $timeEnd2);
	}

	public function testGetEndOfDay() {
		// no test
	}



	/**
	 * Test getDayRange
	 */
	public function testGetDayRange() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 3, 2010);
		$testEnd	= mktime(23, 59, 59, 8, 3, 2010);
		$range		= TodoyuTime::getDayRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}



	/**
	 * Test getWeekRange
	 */
	public function testGetWeekRange() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 2, 2010);
		$testEnd	= mktime(23, 59, 59, 8, 8, 2010);
		$range		= TodoyuTime::getWeekRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}



	/**
	 * Test getMonthRange
	 */
	public function testGetMonthRange() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 1, 2010);
		$testEnd	= mktime(23, 59, 59, 8, 31, 2010);
		$range		= TodoyuTime::getMonthRange($time);

		$this->assertEquals($testStart, $range['start']);
		$this->assertEquals($testEnd, $range['end']);
	}



	/**
	 * Test getWeekStart
	 */
	public function testGetWeekStart() {
		Todoyu::$CONFIG['SYSTEM']['firstDayOfWeek']	= 1;

		$time		= mktime(14, 33, 59, 8, 3, 2012);
		$testStart	= mktime(0, 0, 0, 7, 30, 2012);
		$weekStart	= TodoyuTime::getWeekStart($time);

		$this->assertEquals($testStart, $weekStart);

		Todoyu::$CONFIG['SYSTEM']['firstDayOfWeek']	= 0;

		$time		= mktime(14, 33, 59, 8, 3, 2012);
		$testStart	= mktime(0, 0, 0, 7, 29, 2012);
		$weekStart	= TodoyuTime::getWeekStart($time);

		$this->assertEquals($testStart, $weekStart);
	}



	/**
	 * Test getMonthStart
	 */
	public function testGetMonthStart() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testStart	= mktime(0, 0, 0, 8, 1, 2010);
		$monthStart	= TodoyuTime::getMonthStart($time);

		$this->assertEquals($testStart, $monthStart);
	}



	/**
	 * Test getWeekday
	 */
	public function testGetWeekday() {
		$time		= mktime(14, 33, 59, 8, 3, 2010);
		$testWeekday= 1;
		$weekday	= TodoyuTime::getWeekday($time);

		$this->assertEquals($testWeekday, $weekday);
	}



	/**
	 * Test getTimeParts
	 */
	public function testGetTimeParts() {
		$time		= (14 * 3600) + (33 * 60) + (59); // 14:33:59
		$testHours	= 14;
		$testMinutes= 33;
		$testSeconds= 59;

		$timeParts	= TodoyuTime::getTimeParts($time);

		$this->assertEquals($testHours, $timeParts['hours']);
		$this->assertEquals($testMinutes, $timeParts['minutes']);
		$this->assertEquals($testSeconds, $timeParts['seconds']);
	}



	/**
	 * Test firstHourLeftOver
	 */
	public function testFirstHourLeftOver() {
//		$testHours1	= 1.0;
//		$testHours2	= 0.0;
//		$testHours3	= 0.7;
//
//		$hours1	= TodoyuTime::firstHourLeftOver(2.5);
//		$hours2	= TodoyuTime::firstHourLeftOver(-0.5);
//		$hours3	= TodoyuTime::firstHourLeftOver(0.7);
//
//		$this->assertEquals($testHours1, $hours1);
//		$this->assertEquals($testHours2, $hours2);
//		$this->assertEquals($testHours3, $hours3);
	}



	/**
	 * Test sec2hour
	 */
	public function testSec2hour() {
		$seconds1	= (14 * 3600) + (33 * 60) + (29); // 14:33:29
		$seconds2	= (14 * 3600) + (33 * 60) + (31); // 14:33:31
		$testString1= '14:33';
		$testString2= '14:34';

		$timeString1	= TodoyuTime::sec2hour($seconds1);
		$timeString2	= TodoyuTime::sec2hour($seconds2);

		$this->assertEquals($testString1, $timeString1);
		$this->assertEquals($testString2, $timeString2);
	}



	/**
	 * Test formatTime
	 */
	public function testFormatTime() {
		$seconds	= 18 * 3600 + 24 * 60 + 35;
		$testString1= '18:24:35';
		$testString2= '18:25';
		$testString3= '18:24';

		$timeString1= TodoyuTime::formatTime($seconds, true);
		$timeString2= TodoyuTime::formatTime($seconds, false);
		$timeString3= TodoyuTime::formatTime($seconds, false, false);

		$this->assertEquals($testString1, $timeString1);
		$this->assertEquals($testString2, $timeString2);
		$this->assertEquals($testString3, $timeString3);
	}



	/**
	 * Test format
	 */
	public function testFormat() {
		$currentLocale	= Todoyu::getLocale();

		Todoyu::setLocale('en_GB');

		$time	= mktime(14, 36, 5, 3, 9, 1984);

		$formattedEN= TodoyuTime::format($time, 'datetime');
		$expectedEN	= '09/03/84 14:36';

		$this->assertEquals($expectedEN, $formattedEN);


		Todoyu::setLocale('de_DE');

		$formattedDE= TodoyuTime::format($time, 'datetime');
		$expectedDE	= '09.03.1984 14:36';

		$this->assertEquals($expectedDE, $formattedDE);

		Todoyu::setLocale($currentLocale);
	}



	/**
	 * Test getFormat
	 */
	public function testGetFormat() {
		$currentLocale	= Todoyu::getLocale();

			// Test with german locale
		Todoyu::setLocale('de_DE');

		$format		= TodoyuTime::getFormat('DshortD2MlongY4');
		$expected	= '%a, %d. %B %Y';

		$this->assertEquals($expected, $format);


		$result		= TodoyuTime::getFormat('notavailableformatstring');
		$expected	= 'core.dateformat.notavailableformatstring';
		$this->assertEquals($expected, $result);

			// Restore settings
		Todoyu::setLocale($currentLocale);
	}



	/**
	 * Test parseDateString
	 */
	public function testParseDateString() {
		$time	= mktime(13, 46, 0, 4, 19, 2016);
		$date1	= date('r', $time);
		$date2	= date('Y-m-d H:i:s', $time);
		$date3	= TodoyuTime::format($time, 'datetime');

		$time1	= TodoyuTime::parseDateString($date1);
		$time2	= TodoyuTime::parseDateString($date2);
		$time3	= TodoyuTime::parseDateString($date3);

		$this->assertEquals($time, $time1);
		$this->assertEquals($time, $time2);
		$this->assertEquals($time, $time3);
	}



	/**
	 * Test parseDate
	 */
	public function testParseDate() {
		$oldLocale		= TodoyuLabelManager::getLocale();
		$dateCompare	= strtotime('2010-03-22');

		TodoyuLabelManager::setLocale('en_US');
		$dateString1	= '3/22/2010';
		$dateTime1		= TodoyuTime::parseDate($dateString1);

		TodoyuLabelManager::setLocale('en_GB');
		$dateString2	= '22/3/2010';
		$dateTime2		= TodoyuTime::parseDate($dateString2);

		TodoyuLabelManager::setLocale('de_DE');
		$dateString3	= '22.3.2010';
		$dateTime3		= TodoyuTime::parseDate($dateString3);

		TodoyuLabelManager::setLocale('pt_BR');
		$dateString4	= '22.3.2010';
		$dateTime4		= TodoyuTime::parseDate($dateString4);


		$this->assertEquals($dateCompare, $dateTime1);
		$this->assertEquals($dateCompare, $dateTime2);
		$this->assertEquals($dateCompare, $dateTime3);
		$this->assertEquals($dateCompare, $dateTime4);

		TodoyuLabelManager::setLocale($oldLocale);
	}



	/**
	 * Test parseDateTime
	 */
	public function testParseDateTime() {
		$dateCompare	= strtotime('2010-03-22 14:36');

		$oldLocale		= TodoyuLabelManager::getLocale();

		Todoyu::setLocale('de_DE');
		$dateStringDE	= '22.03.2010 14:36';
		$timeDE			= TodoyuTime::parseDateTime($dateStringDE);

		$this->assertEquals($dateCompare, $timeDE);

		TodoyuLabelManager::setLocale($oldLocale);
	}



	/**
	 * Test parseTime
	 */
	public function testParseTime() {
		$time_1	= '23:59';
		$sec_1	= 86340;
		$time_2	= '23:59:30';
		$sec_2	= 86370;
		$time_3	= '0:00:01';
		$sec_3	= 1;

		$res_1	= TodoyuTime::parseTime($time_1);
		$res_2	= TodoyuTime::parseTime($time_2);
		$res_3	= TodoyuTime::parseTime($time_3);

		$this->assertEquals($sec_1, $res_1);
		$this->assertEquals($sec_2, $res_2);
		$this->assertEquals($sec_3, $res_3);
	}



	/**
	 * Test parseDuration
	 */
	public function testParseDuration() {
		$dur_1	= '3:00';
		$sec_1	= 10800;
		$dur_2	= '0:00';
		$sec_2	= 0;
		$dur_3	= '1:';
		$sec_3	= 3600;
		$dur_4	= '100:00';
		$sec_4	= 360000;
		$dur_5	= ':59';
		$sec_5	= 3540;
		$dur_6	= '0:67';
		$sec_6	= 4020; // 1:07

		$res_1	= TodoyuTime::parseDuration($dur_1);
		$res_2	= TodoyuTime::parseDuration($dur_2);
		$res_3	= TodoyuTime::parseDuration($dur_3);
		$res_4	= TodoyuTime::parseDuration($dur_4);
		$res_5	= TodoyuTime::parseDuration($dur_5);
		$res_6	= TodoyuTime::parseDuration($dur_6);

		$this->assertEquals($sec_1, $res_1);
		$this->assertEquals($sec_2, $res_2);
		$this->assertEquals($sec_3, $res_3);
		$this->assertEquals($sec_4, $res_4);
		$this->assertEquals($sec_5, $res_5);
		$this->assertEquals($sec_6, $res_6);
	}



	/**
	 * Test getRoundedTime
	 */
	public function testGetRoundedTime() {
		$time	= 10 * 3600 + 33 * 60 + 31; // 10:33:31
		$test1	= 10 * 3600 + 35 * 60 + 0; // 10:35:00
		$test2	= 10 * 3600 + 30 * 60 + 0; // 10:30:00
		$test3	= 10 * 3600 + 40 * 60 + 0; // 10:40:00
		$test4	= 11 * 3600 + 0 + 0; // 11:00:00
		$test5	= 10 * 3600 + 33 * 60 + 0; // 10:33:00

		$rounded1	= TodoyuTime::getRoundedTime($time);
		$rounded2	= TodoyuTime::getRoundedTime($time, 5);
		$rounded3	= TodoyuTime::getRoundedTime($time, 10);
		$rounded4	= TodoyuTime::getRoundedTime($time, 15);
		$rounded5	= TodoyuTime::getRoundedTime($time, 20);
		$rounded6	= TodoyuTime::getRoundedTime($time, 30);
		$rounded7	= TodoyuTime::getRoundedTime($time, 60);
		$rounded8	= TodoyuTime::getRoundedTime($time, 1);

		$this->assertEquals($test1, $rounded1);
		$this->assertEquals($test1, $rounded2);
		$this->assertEquals($test2, $rounded3);
		$this->assertEquals($test2, $rounded4);
		$this->assertEquals($test3, $rounded5);
		$this->assertEquals($test2, $rounded6);
		$this->assertEquals($test4, $rounded7);
		$this->assertEquals($test5, $rounded8);
	}



	/**
	 * Test rangeOverlaps
	 */
	public function testRangeOverlaps() {
		$date1	= strtotime('2010-01-01 08:00:00');
		$date2	= strtotime('2010-01-01 10:00:00');
		$date3	= strtotime('2010-03-05 08:00:00');
		$date4	= strtotime('2010-05-25 02:00:00');
		$date5	= strtotime('2010-08-13 14:00:00');
		$date6	= strtotime('2011-01-01 12:00:00');

		$overlaps1	= TodoyuTime::rangeOverlaps($date1, $date2, $date3, $date4);
		$overlaps2	= TodoyuTime::rangeOverlaps($date1, $date3, $date2, $date4);
		$overlaps3	= TodoyuTime::rangeOverlaps($date1, $date6, $date3, $date4);
		$overlaps4	= TodoyuTime::rangeOverlaps($date1, $date6, $date5, $date6);
		$overlaps5	= TodoyuTime::rangeOverlaps($date1, $date3, $date3, $date6);

		$this->assertFalse($overlaps1);
		$this->assertTrue($overlaps2);
		$this->assertTrue($overlaps3);
		$this->assertTrue($overlaps4);
		$this->assertFalse($overlaps5);
	}



	/**
	 * Test getWeekend
	 */
	public function testGetWeekend() {
		$date1		= mktime(0, 0, 0, 3, 16, 2011);
		$weekend1	= mktime(23, 59, 59, 3, 20, 2011);
		$test1		= TodoyuTime::getWeekEnd($date1);

		$date2		= mktime(0, 0, 0, 2, 29, 2024);
		$weekend2	= mktime(23, 59, 59, 3, 3, 2024);
		$test2		= TodoyuTime::getWeekEnd($date2);

		$date3		= mktime(0, 0, 0, 12, 31, 1984);
		$weekend3	= mktime(23, 59, 59, 1, 6, 1985);
		$test3		= TodoyuTime::getWeekEnd($date3);

		$this->assertEquals($weekend1, $test1);
		$this->assertEquals($weekend2, $test2);
		$this->assertEquals($weekend3, $test3);
	}



	/**
	 * Test getMonthEnd
	 */
	public function testGetMonthEnd() {
		$test1	= mktime(0, 0, 0, 3, 1, 2011);
		$expect1= mktime(0, 0, 0, 4, 1, 2011)-1;
		$test2	= mktime(0, 0, 0, 2, 10, 2011);
		$expect2= mktime(0, 0, 0, 3, 1, 2011)-1;
		$test3	= mktime(23, 59, 59, 5, 31, 2011);
		$expect3= mktime(0, 0, 0, 6, 1, 2011)-1;

		$result1= TodoyuTime::getMonthEnd($test1);
		$result2= TodoyuTime::getMonthEnd($test2);
		$result3= TodoyuTime::getMonthEnd($test3);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
		$this->assertEquals($expect3, $result3);
	}



	/**
	 * Test getLastDayNumberInMonth
	 */
	public function testGetLastDayNumberInMonth() {
		$test1	= mktime(0, 0, 0, 3, 10, 2011);
		$expect1= 31;
		$result1= TodoyuTime::getLastDayNumberInMonth($test1);
		$this->assertEquals($expect1, $result1);

		$test2	= mktime(0, 0, 0, 4, 10, 2011);
		$expect2= 30;
		$result2= TodoyuTime::getLastDayNumberInMonth($test2);
		$this->assertEquals($expect2, $result2);

		$test3	= mktime(0, 0, 0, 2, 10, 2011);
		$expect3= 28;
		$result3= TodoyuTime::getLastDayNumberInMonth($test3);
		$this->assertEquals($expect3, $result3);
	}



	/**
	 * Test addDays
	 */
	public function testAddDays() {
		$date		= mktime(0, 0, 0, 3, 18, 2011);
		$expect1	= mktime(0, 0, 0, 3, 19, 2011);
		$expect2	= mktime(0, 0, 0, 3, 20, 2011);
		$expect5	= mktime(0, 0, 0, 3, 23, 2011);
		$expect10	= mktime(0, 0, 0, 3, 28, 2011);
		$expect20	= mktime(0, 0, 0, 4, 7, 2011);

		$result1	= TodoyuTime::addDays($date, 1);
		$result2	= TodoyuTime::addDays($date, 2);
		$result5	= TodoyuTime::addDays($date, 5);
		$result10	= TodoyuTime::addDays($date, 10);
		$result20	= TodoyuTime::addDays($date, 20);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
		$this->assertEquals($expect5, $result5);
		$this->assertEquals($expect10, $result10);
		$this->assertEquals($expect20, $result20);

			// Test with custom time set
		$date2		= mktime(20, 30, 30, 3, 1, 2011);
		$expectX1	= mktime(20, 30, 30, 3, 2, 2011);

		$resultX1	= TodoyuTime::addDays($date2, 1);

		$this->assertEquals($expectX1, $resultX1);
	}



	/**
	 * Test roundUpTime
	 */
	public function testRoundUpTime() {
		$time1	= 44 * 60;
		$round1	= 15;
		$expect1= 45 * 60;

		$result1= TodoyuTime::roundUpTime($time1, $round1);
		$this->assertEquals($expect1, $result1);


		$time2	= 20 * 60 + 5;
		$round2	= 10;
		$expect2= 30 * 60;

		$result2= TodoyuTime::roundUpTime($time2, $round2);
		$this->assertEquals($expect2, $result2);


		$time3	= 2 * 60 + 30;
		$round3	= 30;
		$expect3= 30 * 60;

		$result3= TodoyuTime::roundUpTime($time3, $round3);
		$this->assertEquals($expect3, $result3);
	}



	/**
	 * Test parsesqldate
	 */
	public function testparsesqldate() {
		$date	= '2010-01-01';
		$expect	= mktime(0, 0, 0, 1, 1, 2010);
		$result	= TodoyuTime::parseSqlDate($date);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test formatsqldate
	 */
	public function testformatsqldate() {
		Todoyu::setLocale('en_GB');

		$date	= '2010-01-01';
		$expect	= '01/01/2010';
		$result	= TodoyuTime::formatSqlDate($date);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test formatDuration
	 */
	public function testformatDuration() {
		Todoyu::setLocale('en_GB');

			// Check 1 second
		$duration60	= TodoyuTime::formatDuration(1);
		$expect60	= '1 Second';
		$this->assertEquals($expect60, $duration60);

			// Check 30 seconds
		$duration60	= TodoyuTime::formatDuration(30);
		$expect60	= '30 Seconds';
		$this->assertEquals($expect60, $duration60);

			// Check 1 minute
		$duration60	= TodoyuTime::formatDuration(60);
		$expect60	= '1 Minute';
		$this->assertEquals($expect60, $duration60);

			// Check 5 minutes
		$duration300	= TodoyuTime::formatDuration(300);
		$expect300	= '5 Minutes';
		$this->assertEquals($expect300, $duration300);

			// Check 1 hour
		$duration3600	= TodoyuTime::formatDuration(3600);
		$expect3600	= '1 Hour';
		$this->assertEquals($expect3600, $duration3600);

			// Check 5 hours
		$duration3600	= TodoyuTime::formatDuration(18000);
		$expect3600	= '5 Hours';
		$this->assertEquals($expect3600, $duration3600);

			// Check 1 day
		$duration172800	= TodoyuTime::formatDuration(86400);
		$expect172800	= '1 Day';
		$this->assertEquals($expect172800, $duration172800);

			// Check 2 days
		$duration172800	= TodoyuTime::formatDuration(172800);
		$expect172800	= '2 Days';
		$this->assertEquals($expect172800, $duration172800);
	}



	/**
	 * Test formattimespan
	 */
	public function testformattimespan() {
		Todoyu::setLocale('en_GB');

			// Check timespan within same day
		$startHours		= mktime(10, 0, 0, 1, 1, 2011);
		$endHours		= mktime(12, 0, 0, 1, 1, 2011);

		$expectHours	= 'Sat, Jan 01 11, 10:00 - 12:00';
		$resultHours	= TodoyuTime::formatRange($startHours, $endHours);

		$this->assertEquals($expectHours, $resultHours);

			// Check timespan over multiple days
		$startDays		= mktime(10, 0, 0, 1, 1, 2011);
		$endDays		= mktime(10, 0, 0, 1, 2, 2011);
		$expectDays		= 'Sat, Jan 01 11 - Sun, Jan 02 11';
		$resultDays		= TodoyuTime::formatRange($startDays, $endDays);

		$this->assertEquals($expectDays, $resultDays);

			// Check timespan over multiple days with duration
		$expectDays2	= 'Sat, Jan 01 11 - Sun, Jan 02 11';
		$resultDays2	= TodoyuTime::formatRange($startDays, $endDays);

		$this->assertEquals($expectDays2, $resultDays2);
	}



	/**
	 * Test gettimeofday
	 */
	public function testgettimeofday() {
		$time	= mktime(0, 0, 1, 1, 1, 2011);
		$expect	= 1;
		$result	= TodoyuTime::getTimeOfDay($time);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test isStandardDate
	 *
	 */
	public function testisstandarddate() {
		$this->assertTrue(TodoyuTime::isStandardDate('2011-08-05'));
		$this->assertTrue(TodoyuTime::isStandardDate('1999-01-01'));
		$this->assertFalse(TodoyuTime::isStandardDate('1999-1-01'));
	}


	public function testTime() {
		$time1	= TodoyuTime::time();
		$time2	= TodoyuTime::time(0);
		$time3	= TodoyuTime::time(NOW);

		$this->assertEquals(NOW, $time1);
		$this->assertEquals(NOW, $time2);
		$this->assertEquals(NOW, $time3);
	}


	public function testGetYearStart() {
		$date	= strtotime('2012-03-03');
		$expect	= strtotime('2012-01-01');

		$yearStart	= TodoyuTime::getYearStart($date);

		$this->assertEquals($expect, $yearStart);
	}

	public function testGetYearEnd() {
		$date	= strtotime('2012-03-03');
		$expect	= strtotime('2012-12-31 23:59:59');

		$yearEnd	= TodoyuTime::getYearEnd($date);

		$this->assertEquals($expect, $yearEnd);
	}


	public function testFormatHours() {
		$seconds1	= 3600;
		$format1	= TodoyuTime::formatHours($seconds1);
		$expect1	= '01:00';

		$seconds2	= 7200;
		$format2	= TodoyuTime::formatHours($seconds2, false);
		$expect2	= '2:00';

		$this->assertEquals($expect1, $format1);
		$this->assertEquals($expect2, $format2);
	}

	public function testCleanFormatForWindows() {
		$format	= '%e %Y';
		$expect	= TodoyuServer::isWindows() ? '%d %Y' : '%e %Y';
		$result	= TodoyuTime::cleanFormatForWindows($format);

		$this->assertEquals($expect, $result);
	}


	public function testIsWeekendDate() {
		$dateNotWeekend	= strtotime('2012-04-13');
		$dateWeekend	= strtotime('2012-04-14');

		$isWeekend		= TodoyuTime::isWeekendDate($dateWeekend);
		$notWeekend		= TodoyuTime::isWeekendDate($dateNotWeekend);

		$this->assertTrue($isWeekend);
		$this->assertFalse($notWeekend);
	}

	public function testGetWeekEndDayIndexes() {
		$indexes	= TodoyuTime::getWeekEndDayIndexes();

		$this->assertInternalType('array', $indexes);
		$this->assertEquals(6, $indexes[0]);
		$this->assertEquals(0, $indexes[1]);
	}

	public function testFormatRange() {
		$dateStart1	= strtotime('2012-01-01 14:15');
		$dateEnd1	= strtotime('2012-01-01 18:00');
		$dateEnd2	= strtotime('2012-01-06 20:00');

		$labelSame	= TodoyuTime::formatRange($dateStart1, $dateStart1);
		$expectSame	= 'Sun, Jan 01 12, 14:15';

		$labelSameDay	= TodoyuTime::formatRange($dateStart1, $dateEnd1);
		$expectSameDay	= 'Sun, Jan 01 12, 14:15 - 18:00';

		$labelLong		= TodoyuTime::formatRange($dateStart1, $dateEnd2, true);
		$expectLong		= 'Sun, Jan 01 12, 14:15 - Fri, Jan 06 12, 20:00';
		
		$this->assertEquals($expectSame, $labelSame);
		$this->assertEquals($expectSameDay, $labelSameDay);
		$this->assertEquals($expectLong, $labelLong);
	}

}

?>