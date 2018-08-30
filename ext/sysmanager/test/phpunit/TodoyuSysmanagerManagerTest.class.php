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
 * Test for: TodoyuSysmanagerManager
 *
 * @package		Todoyu
 * @subpackage	SysmanagerManager
 */
class TodoyuSysmanagerManagerTest extends PHPUnit_Framework_TestCase {


	protected $testModuleKeys;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 * @todo reset db table
	 */
	protected function setUp() {
			// Keys of sysmanager modules
		$this->testModuleKeys	= array('extensions', 'records', 'rights', 'config', 'unittest');

		TodoyuAuth::setPersonID(1);
	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		TodoyuAuth::setPersonID(0);
	}



	/**
	 * Test add module
	 *
	 */
	public function testAddModule() {
		$this->assertFalse(TodoyuSysmanagerManager::isModule('newmodule'));
		$modulesBeforeAdd = TodoyuSysmanagerManager::getModules();
		TodoyuSysmanagerManager::addModule('newmodule', 'New Module', 'TodoyuExtensionRenderClass::renderFunction', 200);
		$modulesAfterAdd = TodoyuSysmanagerManager::getModules();
		$this->assertTrue(sizeof($modulesAfterAdd) == (sizeof($modulesBeforeAdd) + 1));
		$this->assertTrue(TodoyuSysmanagerManager::isModule('newmodule'));

	}



	/**
	 * Test getActiveModule
	 */
	public function testGetActiveModule() {
		// test if defaultModuleConfig is taken. Workaround until reset of db is done
		TodoyuAuth::setPersonID(0);
		$this->assertEquals(Todoyu::$CONFIG['EXT']['sysmanager']['defaultModule'], TodoyuSysmanagerManager::getActiveModule());
		TodoyuAuth::setPersonID(1);
		TodoyuSysmanagerPreferences::saveActiveModule('config');

		$activeModule	= TodoyuSysmanagerManager::getActiveModule();

		$expected	= 'config';
		$this->assertEquals($expected, $activeModule);
	}



	/**
	 * Test getModules
	 */
	public function testGetModules() {
		$activeModules	= TodoyuSysmanagerManager::getModules();

			// Assert modules present at all
		$expected	= 'array';
		$this->assertInternalType($expected, $activeModules);

			// Assert modles are present
		$this->assertGreaterThan(0, sizeof($activeModules));

			// Assert config of each module to contain: key, label and render callback
		foreach($activeModules as $activeModule) {
			$expected	= 'array';
			$this->assertInternalType($expected, $activeModule);

			$this->assertArrayHasKey('key', $activeModule);
			$this->assertArrayHasKey('label', $activeModule);
			$this->assertArrayHasKey('render', $activeModule);
		}
	}



	/**
	 * Test getModuleRenderFunction
	 */
	public function testGetModuleRenderFunction() {
		$modules	= TodoyuSysmanagerManager::getModules();

		foreach($modules as $module) {
			$renderFunction	= TodoyuSysmanagerManager::getModuleRenderFunction($module['key']);

			$this->assertNotNull($renderFunction);

			$pattern	= '/Todoyu.{3,50}\:\:.{3,50}/';
			$this->assertRegExp($pattern, $renderFunction);
		}
	}



	/**
	 * Test checking whether the key belongs to a registered module
	 */
	public function testIsModule() {
			// Check sysmanager modules
		$this->assertTrue(TodoyuSysmanagerManager::isModule('extensions'));

			// Check bogus module to fail verification
		$this->assertFalse(TodoyuSysmanagerManager::isModule('definitelynomodulekey'));
	}

}

?>