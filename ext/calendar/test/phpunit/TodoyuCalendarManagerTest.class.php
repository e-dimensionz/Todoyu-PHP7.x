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
 * Test for: TodoyuAdminManager
 *
 * @package		Todoyu
 * @subpackage	AdminManager
 */
class TodoyuCalendarManagerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Array
	 */
	private $array;

	/**
	 * @var	Array
	 */
	protected $testCalendarModes;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
			// Keys of sysmanager modules
		$this->testCalendarModes	= array(
			CALENDAR_MODE_DAY	=> 'day',
			CALENDAR_MODE_WEEK	=> 'week',
			CALENDAR_MODE_MONTH	=> 'month'
		);
	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}



	/**
	 * Test getModeName
	 */
	public function testGetModeName() {
			// Assert basic mode constants to be resolved to correct name strings
		foreach($this->testCalendarModes as $testModeConstant => $expectedModeName)
		$modeName	= TodoyuCalendarManager::getModeName($testModeConstant);

		$this->assertEquals($expectedModeName, $modeName);
	}



	/**
	 * Test getActiveModule
	 *
	 * @todo	implement	testGetHolidays
	 */
	public function testGetHolidays() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getHolidaysForDay
	 *
	 * @todo	implement	testGetHolidaysForDay
	 */
	public function testGetHolidaysForDay() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getAmountOfDaysInbetweenWeekdayNums
	 *
	 * @todo	improve, test more
	 */
	public function testGetAmountOfDaysInbetweenWeekdayNums() {
			// Day 1 to 1 = 1 days
		$expected	= 1;
		$amount		= TodoyuCalendarManager::getAmountOfDaysInbetweenWeekdayNums(1, 1, true);

		$this->assertEquals($expected, $amount);

			// Day 1 to 7 = 7 days
		$expected	= 7;
		$amount		= TodoyuCalendarManager::getAmountOfDaysInbetweenWeekdayNums(1, 7, true);

		$this->assertEquals($expected, $amount);

			// Day 360 to 365 = 6 days
		$expected	= 6;
		$amount		= TodoyuCalendarManager::getAmountOfDaysInbetweenWeekdayNums(360, 365, true);

		$this->assertEquals($expected, $amount);
	}



	/**
	 * Test getVisibleWeeksAmount
	 *
	 * @todo	implement testGetVisibleWeeksAmount
	 */
	public function testGetVisibleWeeksAmount() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getMonthDisplayRange
	 *
	 * @todo	implement	testGetMonthDisplayRange
	 */
	public function testGetMonthDisplayRange() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getDayTimestampsForMonth (timestamps of days inside 5 shown weeks including the given month)
	 */
	public function testGetDayTimestampsForMonth() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);

//			// Get timestamps of days at 0:0:0 of january 1970
//		$timestamp	= 1;
//		$dayTimestamps	= TodoyuCalendarManager::getDayTimestampsForMonth($timestamp);
//
//			// Assert value data type
//		$expected	= 'array';
//		$this->assertInternalType($expected, $dayTimestamps);
//
//			// Assert correct amount of days in array
//		$amountDays	= sizeof($dayTimestamps);
//		$expected	= 35;
//		$this->assertEquals($expected, $amountDays);
//
//			// Count amount of weeks inside timestamps
//		$amountWeeks= 0;
//		$weekNum	= -1;
//		foreach($dayTimestamps as $dayTimestamp) {
//			if( $weekNum != date('W', $dayTimestamp) ) {
//				$amountWeeks++;
//			}
//			$weekNum	= date('W', $dayTimestamp);
//		}
//			// Assert five weeks to be contained
//		$expected	= 5;
//		$this->assertEquals($expected, $amountWeeks);
	}



	/**
	 * Test isOverbookingAllowed
	 *
	 * @todo	implement	testIsOverbookingAllowed
	 */
	public function testIsOverbookingAllowed() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getContextMenuItems
	 *
	 * @todo	implement	testGetContextMenuItems
	 */
	public function testGetContextMenuItems() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getCalendarTabsConfig
	 *
	 * @todo	implement	testGetCalendarTabsConfig
	 */
	public function testGetCalendarTabsConfig() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getSelectedPersons
	 *
	 * @todo	implement	testGetSelectedPersons
	 */
	public function testGetSelectedPersons() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getSelectedEventTypes
	 *
	 * @todo	implement	testGetSelectedEventTypes
	 */
	public function testGetSelectedEventTypes() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getSelectedHolidaySets
	 *
	 * @todo	implement	testGetSelectedHolidaySets
	 */
	public function testGetSelectedHolidaySets() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}




	/**
	 * Test getBirthdaysByDay
	 *
	 * @todo	implement	testGetBirthdaysByDay
	 */
	public function testGetBirthdaysByDay() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test getDayKeys
	 *
	 * @todo	implement	testGetDayKeys
	 */
	public function testGetDayKeys() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

}

?>