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
 * Label Manager Tests
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuLabelManagerTest extends PHPUnit_Framework_TestCase {

	private static $oldLocale;


	public static function setUpBeforeClass() {
		self::$oldLocale = TodoyuLabelManager::getLocale();
	}


	public static function tearDownAfterClass() {
		TodoyuLabelManager::setLocale(self::$oldLocale);
	}

	public function testsetlocale() {
		$expect	= 'de_DE';

		TodoyuLabelManager::setLocale($expect);
		$result	= TodoyuLabelManager::getLocale();

		$this->assertEquals($expect, $result);
	}

	public function testgetlocale() {
		// Tested in setlocale
	}


	public function testaddcustompath() {
		TodoyuLabelManager::setLocale('de_DE');
		TodoyuLabelManager::addCustomPath('custom', 'core/test/files');

		$label	= TodoyuLabelManager::getLabel('custom.locale.label1');
		$expect	= 'Label One';

		$this->assertEquals($expect, $label);
	}

	public function testgetformatlabel() {
		TodoyuLabelManager::setLocale('de_DE');
		TodoyuLabelManager::addCustomPath('custom', 'core/test/files');

		$result	= TodoyuLabelManager::getFormatLabel('custom.locale.label.format', array('Placeholders'));
		$expect	= 'Label with Placeholders';

		$this->assertEquals($expect, $result);
	}


	public function testgetlabel() {
		TodoyuLabelManager::setLocale('en_GB');

		$result	= TodoyuLabelManager::getLabel('core.form.field.cancel');
		$expect	= 'Cancel';

		$this->assertEquals($expect, $result);
	}


	public function testgetlabelorempty() {
		$result	= TodoyuLabelManager::getLabelOrEmpty('does.not.exist');
		$expect	= '';

		$this->assertEquals($expect, $result);
	}


	public function testgetlocalepath() {
		$result	= TodoyuLabelManager::getLocalePath('core', 'de_DE');
		$expect	= TodoyuFileManager::pathAbsolute('core/locale/de_DE');

		$this->assertEquals($expect, $result);
	}


	public function testgetxmlfilelabels() {
		TodoyuLabelManager::addCustomPath('custom', 'core/test/files');

		$result	= TodoyuLabelManager::getXmlFileLabels('custom', 'locale', 'de_DE');
		$expect	= array(
			'label1'		=> 'Label One',
			'label.two'		=> 'Label 222222',
			'label.format'	=> 'Label with %s'
		);

		$this->assertEquals($expect, $result);
	}


	public function testclearcache() {
		TodoyuLabelManager::clearCache();

		$path		= TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['LOCALE']['labelCacheDir']);
		$elements	= TodoyuFileManager::getFolderContents($path);

		$this->assertEquals(0, sizeof($elements));
	}

}

?>