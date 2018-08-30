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
 * Test for: TodoyuHtmlFilter
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuHtmlFilterTest extends PHPUnit_Framework_TestCase {


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {

	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}



	/**
	 * Test TodoyuHtmlFilter::clean
	 */
	public function testClean() {
			// Test cleaning of allowed HTML (no change expected)
		$htmlNormal		= '<p>test</p>';
		$expectNormal	= $htmlNormal;
		$cleanNormal	= TodoyuHtmlFilter::clean($htmlNormal);

		$this->assertEquals($expectNormal, $cleanNormal);

			// Test escaping of script tag
		$htmlScript		= '<script>alert("test");</script>';
		$expectScript	= '&lt;script&gt;alert("test");&lt;/script&gt;';
		$cleanScript	= TodoyuHtmlFilter::clean($htmlScript);

		$this->assertEquals($expectScript, $cleanScript);

			// Test escaping of injecting iframe
		$htmlIframe		= '<iframe src="http://evil.server.com"></iframe>';
		$expectIframe	= '&lt;iframe src="http://evil.server.com"&gt;&lt;/iframe&gt;';
		$cleanIframe	= TodoyuHtmlFilter::clean($htmlIframe);

		$this->assertEquals($expectIframe, $cleanIframe);
	}

}

?>