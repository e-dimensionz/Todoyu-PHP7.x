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
 *
 */
class TodoyuTimetrackingTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var	Array
	 */
	protected static $testCases = array();



	/**
	 *
	 */
	public static function setUpBeforeClass() {
		self::$testCases = array(
			0 => array(
				'idTask' => 20
			),
			1 => array(
				'idTask' => 138
			),
			2 => array(
				'idTask' => 144
			),
			3 => array(
				'idTask' => 146
			)
		);

		self::restoreDatabase();
	}



	/**
	 *
	 */
	protected static function restoreDatabase() {
		Todoyu::db()->truncateTable('ext_timetracking_track');
		TodoyuSQLManager::executeQueriesFromFile('ext/timetracking/test/data/data_demo.sql');
	}



	/**
	 *
	 */
	public function testExtConfTolerance() {
		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 5));
		$this->assertEquals(5, TodoyuTimetrackingSysmanagerManager::getExtConfTolerance());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 1.01));
		$this->assertEquals(1.01, TodoyuTimetrackingSysmanagerManager::getExtConfTolerance());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 0.99));
		$this->assertEquals(0.99, TodoyuTimetrackingSysmanagerManager::getExtConfTolerance());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 0));
		$this->assertEquals(0, TodoyuTimetrackingSysmanagerManager::getExtConfTolerance());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 60.5));
		$this->assertEquals(60.5, TodoyuTimetrackingSysmanagerManager::getExtConfTolerance());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 120));
		$this->assertEquals(120, TodoyuTimetrackingSysmanagerManager::getExtConfTolerance());
	}



	/**
	 *
	 */
	public function testGetTolreanceFactor() {
		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 5));
		$this->assertEquals(1.05, TodoyuTimetrackingTaskManager::getToleranceFactor());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 1.01));
		$this->assertEquals(1.0101, TodoyuTimetrackingTaskManager::getToleranceFactor());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 0.99));
		$this->assertEquals(1.0099, TodoyuTimetrackingTaskManager::getToleranceFactor());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 0));
		$this->assertEquals(1, TodoyuTimetrackingTaskManager::getToleranceFactor());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 60.5));
		$this->assertEquals(1.605, TodoyuTimetrackingTaskManager::getToleranceFactor());

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 120));
		$this->assertEquals(2.2, TodoyuTimetrackingTaskManager::getToleranceFactor());
	}



	/**
	 *
	 */
	public function getIsOverTolerance() {
		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 19.99));
		$this->assertTrue(TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance(100, 120));
		$this->assertTrue(TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance(14400, 17280));

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 20.01));
		$this->assertFalse(TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance(100, 120));
		$this->assertFalse(TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance(14400, 17280));

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 119.99));
		$this->assertTrue(TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance(100, 220));
		$this->assertTrue(TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance(14400, 31680));

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 120.01));
		$this->assertFalse(TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance(100, 220));
		$this->assertFalse(TodoyuTimetrackingTaskManager::isTrackedTimeOverTolerance(14400, 31680));

	}



	/**
	 *
	 */
	public function testGetIcons5Percent() {
		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 5));

		$iconsCase1	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[0]['idTask']);
		$iconsCase2	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[1]['idTask']);
		$iconsCase3	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[2]['idTask']);
		$iconsCase4	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[3]['idTask']);

		$this->assertArrayHasKey('timetracking', $iconsCase1);
		$this->assertArrayHasKey('timetracking', $iconsCase2);
		$this->assertArrayNotHasKey('timetracking', $iconsCase3);
		$this->assertArrayHasKey('timetracking', $iconsCase4);
	}



	/**
	 *
	 */
	public function testGetIcons0Percent() {
		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 0));

		$iconsCase1	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[0]['idTask']);
		$iconsCase2	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[1]['idTask']);
		$iconsCase3	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[2]['idTask']);
		$iconsCase4	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[3]['idTask']);

		$this->assertArrayNotHasKey('timetracking', $iconsCase1);
		$this->assertArrayNotHasKey('timetracking', $iconsCase2);
		$this->assertArrayNotHasKey('timetracking', $iconsCase3);
		$this->assertArrayNotHasKey('timetracking', $iconsCase4);
	}



	/**
	 *
	 */
	public function testGetIconsFloatValue() {
		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 19.99));
		$iconsCase1	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[1]['idTask']);
		$this->assertArrayHasKey('timetracking', $iconsCase1);

		TodoyuSysmanagerExtConfManager::setExtConf('timetracking', array('tolerance' => 20.01));
		$iconsCase2	= TodoyuTimetrackingTaskManager::hookGetTaskIcons(array(), self::$testCases[1]['idTask']);
		$this->assertArrayNotHasKey('timetracking', $iconsCase2);
	}
}

?>