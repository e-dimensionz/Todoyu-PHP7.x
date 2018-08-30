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
 * Test for: TodoyuLocale
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuLocaleTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Array
	 */
	private $array;

	/**
	 * @var	originalLocale
	 */
	protected	$originalLocale;


	protected $testLocaleKeys;




	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->originalLocale	= TodoyuLocaleManager::getLocale();
		$this->testLocaleKeys	= array('en_GB', 'en_US', 'de_DE', 'fr_FR');
	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		$this->resetLocale();
	}



	/**
	 * Reset locale
	 */
	protected function resetLocale() {
		TodoyuLocaleManager::setSystemLocale($this->originalLocaleLocale);
	}



	/**
	 * Test system locales
	 */
	public function testGetSystemLocales() {
		$locales	= TodoyuLocaleManager::getSystemLocales();

			// Assert system locales present at all
		$expected	= 'array';
		$this->assertInternalType($expected, $locales);

			// Assert locale keys
		foreach($this->testLocaleKeys as $expectedKey) {
			$this->assertArrayHasKey($expectedKey, $locales);
		}
	}



	/**
	 * Test locale presence check
	 */
	public function testHasSystemLocale() {
		foreach($this->testLocaleKeys as $expectedLocale) {
			$this->assertTrue(TodoyuLocaleManager::hasSystemLocale($expectedLocale));
		}
	}



	/**
	 * Test getting options config array of available languages
	 */
	public function testGetAvailableLocales() {
		$availableLocales	= TodoyuLocaleManager::getAvailableLocales();
			// Assert system locales present at all
		$expected	= 'array';
		$this->assertInternalType($expected, $availableLocales);

			// Assert at least one language contained
		$amountLocales	= count($availableLocales);
		$this->assertTrue($amountLocales > 0);
	}



	/**
	 * Test get all codes (encoding type description, language abbreviation, language name) of a locale which may exists on a system
	 */
	public function testGetSystemLocaleCodes() {
			// Test en_GB locale codes
		$locale	= 'en_GB';
		$codes	= TodoyuLocaleManager::getSystemLocaleCodes($locale);

			// Assert anything found
		$expected	= 'array';
		$this->assertInternalType($expected, $codes);

			// Assert correct codes returned	en_GB
		$this->assertContains('en_GB.utf8', $codes);
		$this->assertContains('en_GB', $codes);
		$this->assertContains('English_GB', $codes);

			// Test for de_DE locale codes
		$locale	= 'de_DE';
		$codes	= TodoyuLocaleManager::getSystemLocaleCodes($locale);

		$expected	= 'array';
		$this->assertInternalType($expected, $codes);

		$this->assertContains('de_DE.utf8', $codes);
		$this->assertContains('de_DE', $codes);
		$this->assertContains('de', $codes);
		$this->assertContains('de_DE@euro', $codes);
		$this->assertContains('de_DE.utf8@euro', $codes);
		$this->assertContains('German_Germany.1252', $codes);
		$this->assertContains('deu_deu', $codes);
	}



	/**
	 * Test setting system locale
	 * @note	Can't test this, because have not all locales are available on different systems
	 */
	public function testSetSystemLocale() {
//			// Set various locale languages and verify them being set after
//		foreach($this->testLocaleKeys as $localeKey) {
//			$localeLangUTF8	= $localeKey . '.utf8';
//				// Test setting locale
//			$this->assertEquals($localeLangUTF8, TodoyuLocaleManager::setSystemLocale($localeKey));
//				// Test being set
//			$this->assertEquals($localeLangUTF8, TodoyuLocaleManager::getLocale());
//		}
//
//			// Assert setting bogus locale language fails
//		$bogusLocaleKey	= 'xy_zz';
//		$this->assertEquals(false, TodoyuLocaleManager::setSystemLocale($bogusLocaleKey));
//
//			// Reset to original locale language
//		$this->resetLocale();
	}



	/**
	 * Test getting locale options
	 */
	public function testGetLocaleOptions() {
		$localeOptions	=	TodoyuLocaleManager::getLocaleOptions();

			// Assert anything returned
		$expected	= 'array';
		$this->assertInternalType($expected, $localeOptions);
			// Assert at least one language contained
		$amountOptions	= count($localeOptions);
		$this->assertTrue($amountOptions > 0);

			// Formally test all options
		foreach($localeOptions as $localeOption) {
				// Type: array
			$expected	= 'array';
			$this->assertInternalType($expected, $localeOption);

				// Assert each option to have value and key
			$this->assertArrayHasKey('value', $localeOption);
			$this->assertArrayHasKey('label', $localeOption);
		}
	}


	/**
	 * Test setting of locale cookie
	 *
	 * @todo Implement testSetLocaleCookie().
	 */
	public function testSetLocaleCookie() {
//		foreach( $this->localeKeys as $localeKey ) {
//			TodoyuLocaleManager::setLocaleCookie($localeKey);
//
//			$cookieLocale	= TodoyuLocaleManager::getCookieLocale();
//
//			$this->assertEquals($localeKey, $cookieLocale);
//		}
	}

}

?>