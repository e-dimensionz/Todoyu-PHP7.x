<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * Command line script to call all unit tests of the projectbilling extension
 * Used for jenkins CI server / phpStorm
 */

ob_start();

chdir(dirname(dirname(dirname(dirname(__FILE__)))));


require_once('core/inc/global.php');

TodoyuCli::assertShell();
TodoyuCli::setCliMode();
TodoyuCli::init();

require_once( PATH_CORE . '/inc/init.php' );

	// Load all extensions
TodoyuExtensions::loadAllExtensions();

if( !function_exists('phpunit_autoload') ) {
	require_once('PHPUnit/Autoload.php');
}


/**
 * Class with collects all test suites of todoyu and its extensions
 *
 * @package		Todoyu
 * @subpackage	Unittest
 */
class ProjectTest {

	/**
	 * Run test suite
	 *
	 * @return	PHPUnit_Framework_TestSuite
	 */
	public static function suite() {
		$suite		= new PHPUnit_Framework_TestSuite('PHPUnit');
		$testClasses= TodoyuUnittestManager::getExtensionTestClasses('project', true);

			// Add all classes to the test suite
		foreach($testClasses as $testClass) {
			$suite->addTestSuite($testClass);
		}

		return $suite;
	}

}

	// Send output
ob_end_flush();

?>