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
 * Test for: TodoyuString
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuStringTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Array
	 */
	private $array;



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
	 * Test TodoyuString::isUTF8
	 */
	public function testIsUTF8() {
		$utf8		= 'スノーフレイクは、プレ';

		$this->assertTrue(TodoyuString::isUTF8($utf8));

		//@todo	add more tests
	}



	/**
	 * Test TodoyuString::convertToUTF8
	 */
	public function testConvertToUTF8() {
//		$string		= 'Ä ä Ü ü';
//
//		$utf8String	= TodoyuString::convertToUTF8($string);
//		$expected	= '蓃쌠₤鳃쌠';
//
//		$this->assertEquals($expected, $utf8String);

		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}



	/**
	 * Test TodoyuString::isValidEmail
	 *
	 * @todo Implement testIsValidEmail().
	 */
	public function testIsValidEmail() {
		$emailValid		= 'testmann@snowflake.ch';

		$invalid	= array(
			'test.@ann@snowflake.ch',
			'www.snowflake.ch',
			'http://www.snowflake.ch/',
			'h@ttp://www.snowflake.ch/',
			'@snowflake.ch',
			'www@snowflakech'
		);

		$this->assertEquals(true, TodoyuString::isValidEmail($emailValid));

		foreach($invalid as $emailInvalid) {
			$this->assertFalse(TodoyuString::isValidEmail($emailInvalid), 'email: ' . $emailInvalid);
		}
	}



	/**
	 * Test TodoyuString::crop
	 *
	 */
	public function testCrop() {
		$text	= 'Open source is a development method for software that harnesses the power of distributed peer review and transparency of process. The promise of open source is better quality, higher reliability, more flexibility, lower cost, and an end to predatory vendor lock-in.';
		$expect	= 'Open source is a:::';
		$cropped= TodoyuString::crop($text, 20, ':::');

		$this->assertEquals($expect, $cropped);

		$text	= 'スノーフレイクは、プレミアム・オープンソース・ソフトウェア（※）の実装（※）を手がける情報通信  ソリュー';
		$expect	= 'スノーフレイクは、プレミアム・オープンソ..';
		$cropped= TodoyuString::crop($text, 20);

		$this->assertEquals($expect, $cropped);
	}



	/**
	 * Test TodoyuString::wrap
	 *
	 */
	public function testWrap() {
		$text	= 'This is a Test';
		$wrap	= '<strong>|</strong>';
		$result	= TodoyuString::wrap($text, $wrap);
		$expect	= '<strong>This is a Test</strong>';

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test TodoyuString::splitCamelCase
	 *
	 * @todo Implement testSplitCamelCase().
	 */
	public function testSplitCamelCase() {
		$camelCase	= 'TodoyuTaskAndProjectManagementSoftware';
		$parts		= TodoyuString::splitCamelCase($camelCase);

		$this->assertInternalType('array', $parts);
		$this->assertEquals(6, sizeof($parts));
	}



	/**
	 * Test TodoyuString::html2text
	 *
	 * @todo Implement testHtml2text().
	 */
	public function testHtml2text() {
		$html_1		= '<strong>strong</strong>';
		$expect_1	= 'strong';
		$html_2		= 'line1<br>line2<br />line3';
		$expect_2	= "line1\nline2\nline3";
		$html_3		= '<ul><li>繁体字</li></ul>';
		$expect_3	= "- 繁体字";

		$result_1	= TodoyuString::html2text($html_1);
		$result_2	= TodoyuString::html2text($html_2, true);
		$result_3	= TodoyuString::html2text($html_3);

		$this->assertEquals($expect_1, $result_1);
		$this->assertEquals($expect_2, $result_2);
		$this->assertEquals($expect_3, $result_3);
	}



	/**
	 * Test TodoyuString::getSubstring
	 *
	 * @todo Implement testGetSubstring().
	 */
	public function testGetSubstring() {
		$text	= 'Open source is a development method for software that harnesses the power of distributed peer review and transparency of process. The promise of open source is better quality, higher reliability, more flexibility, lower cost, and an end to predatory vendor lock-in.';
		$sub	= TodoyuString::getSubstring($text, 'power');
		$expect	= 'that harnesses the power of distributed peer';

		$this->assertEquals($expect, $sub);
	}



	/**
	 * Test TodoyuString::generatePassword
	 *
	 * @todo Implement testGeneratePassword().
	 */
	public function testGeneratePassword() {
		$password	= TodoyuString::generatePassword();

		$this->assertEquals(8, strlen($password));

		$password	= TodoyuString::generatePassword(10, false, false);

		$this->assertEquals($password, strtolower($password));
		$this->assertNotRegExp('/\d/', $password);
	}



	/**
	 * Test TodoyuString::addToList
	 *
	 */
	public function testAddToList() {
		$list	= '1,2,3';

		$newList	= TodoyuString::addToList($list, 4);

		$this->assertEquals('1,2,3,4', $newList);
	}



	/**
	 * Test TodoyuString::isInList($item, $listString, $listSeparator = ',')
	 *
	 */
	public function testIsInList() {
		$list	= '3,test,345345.44,boarders,4,2,6,3';

		$this->assertTrue(TodoyuString::isInList(3, $list));
		$this->assertTrue(TodoyuString::isInList('test', $list));
		$this->assertTrue(TodoyuString::isInList(345345.44, $list));
		$this->assertFalse(TodoyuString::isInList(4444444, $list));
	}



	/**
	 * Test TodoyuString::formatSize($fileSize, array $labels = null, $noLabel = false)
	 *
	 */
	public function testFormatSize() {
		$sizeB	= 756;			// 756 B
		$sizeKB	= 38242;		// 38.2 KB
		$sizeMB	= 34556789; 	// 34.5..MB
		$sizeGB	= 2560593443;	// 2.5 GB

		$expectB	= '756 B';
		$expectKB	= '37 KB';
		$expectMB	= '33 MB';
		$expectGB	= '2.4 GB';

		$formatB	= TodoyuString::formatSize($sizeB);
		$formatKB	= TodoyuString::formatSize($sizeKB);
		$formatMB	= TodoyuString::formatSize($sizeMB);
		$formatGB	= TodoyuString::formatSize($sizeGB);

		$this->assertEquals($expectB, $formatB);
		$this->assertEquals($expectKB, $formatKB);
		$this->assertEquals($expectMB, $formatMB);
		$this->assertEquals($expectGB, $formatGB);


		$noLabel	= TodoyuString::formatSize($sizeB, null, true);
		$expectNoL	= '756';

		$this->assertEquals($expectNoL, $noLabel);
	}



	/**
	 * Test br2nl
	 */
	public function testbr2nl() {
		$text	= 'this<br>string<br />contains<br >html<br/>linebreaks';
		$expect	= "this\nstring\ncontains\nhtml\nlinebreaks";
		$result	= TodoyuString::br2nl($text);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test listUnique
	 */
	public function testListUnique() {
		$list	= '1,2,3,3,4,5,1,2,3';
		$expect	= '1,2,3,4,5';
		$result	= TodoyuString::listUnique($list, ',');

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test wrapscript
	 */
	public function testWrapscript() {
		$script	= 'var x = 44;';
		$expect	= '<script type="text/javascript">' . $script . '</script>';
		$result	= TodoyuString::wrapscript($script);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test md5short
	 */
	public function testMd5short() {
		$text	= 'this text will be hashed';
		$expect	= substr(md5($text), 0, 10);
		$result	= TodoyuString::md5short($text);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test trimExplode
	 */
	public function testTrimExplode() {
		$text	= 'hello,   world,this  ,  text, will,be        ,trimmed';
		$expect	= array('hello', 'world', 'this', 'text', 'will', 'be', 'trimmed');
		$result	= TodoyuString::trimExplode(',', $text);

		$this->assertEquals($expect, $result);
	}


	public function testToPhpCodeString() {
		// no test, see testToPhpCode()
	}


	/**
	 * Test phpCodeString
	 */
	public function testToPhpCode() {
		$var1	= 'already a string';
		$expect1= "'already a string'";
		$result1= TodoyuString::toPhpCode($var1);

		$var2	= 12345;
		$expect2= '12345';
		$result2= TodoyuString::toPhpCode($var2);

		$var3	= 123.45;
		$expect3= '123.45';
		$result3= TodoyuString::toPhpCode($var3);

		$var4	= array(1,2,3);
		$expect4= 'array(0=>1,1=>2,2=>3)';
		$result4= TodoyuString::toPhpCode($var4);

		$var5	= array('a' => 1, 'b' => 'test', 3 => 'xxx');
		$expect5= 'array(\'a\'=>1,\'b\'=>\'test\',3=>\'xxx\')';
		$result5= TodoyuString::toPhpCode($var5);

		$var6	= new stdClass();
		$var6->member	= 'tes\'t';
		$expect6= 'unserialize(\'O:8:"stdClass":1:{s:6:"member";s:5:"tes\'t";}\')';
		$result6= TodoyuString::toPhpCode($var6);
		
		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
		$this->assertEquals($expect3, $result3);
		$this->assertEquals($expect4, $result4);
		$this->assertEquals($expect5, $result5);
		$this->assertEquals($expect6, $result6);
	}


	public function testToPhpCodeArray() {
		$array	= array('a' => 1, 'b' => 'test', 3 => 'xxx');
		$expect	= 'array(\'a\'=>1,\'b\'=>\'test\',3=>\'xxx\')';

		$result	= TodoyuString::toPhpCodeArray($array);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test buildUrl
	 */
	public function testBuildUrl() {
		$params	= array(
			'a'	=> 'alpha',
			'b'	=> 'beta',
			'g'	=> 'gamma'
		);
		$hash	= 'task-123';

			// Check relative URL
		$result1	= TodoyuString::buildUrl($params, $hash, false, true);
		$expect1	= PATH_WEB . '/index.php?a=alpha&b=beta&g=gamma#task-123';

		$this->assertEquals($expect1, $result1);

			// Check absolute URL
		$result2	= TodoyuString::buildUrl($params, $hash, true);
		$expect2	= TODOYU_URL . '/index.php?a=alpha&amp;b=beta&amp;g=gamma#task-123';

		$this->assertEquals($expect2, $result2);
	}



	/**
	 * Test getImgTag
	 */
	public function testGetImgTag() {
		$src	= 'assets/test.png';
		$width	= 300;
		$height	= 200;
		$alt	= 'Alternative text';

		$expect	= '<img src="' . $src . '" width="' . $width . '" height="' . $height . '" alt="' . $alt . '" />';
		$result	= TodoyuString::getImgTag($src, $width, $height, $alt);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test getATag
	 */
	public function testbuildATag() {
		$url		= 'unit/test.html';
		$label		= 'Link Text';

		$expect1	= '<a href="' . $url . '">' . $label . '</a>';
		$result1	= TodoyuString::buildATag($url, $label);

		$expect2	= '<a href="' . $url . '" target="_customFrame">' . $label . '</a>';
		$result2	= TodoyuString::buildATag($url, $label, '_customFrame');

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
	}



	/**
	 * Test getMailtoTag
	 */
	public function testBuildMailtoATag() {
		$email	= 'team@todoyu.com';
		$label	= 'Send message to todoyu team';
		$subject= 'Mail Subject';
		$content= 'Hello, I am a mail body';
		$cc		= 'sales@todoyu.com';

		$expect	= '<a href="mailto:' . $email . '?subject=' . urlencode($subject) . '&body=' . urlencode($content) . '&cc=' . $cc . '">' . $label . '</a>';
		$result	= TodoyuString::buildMailtoATag($email, $label, false, $subject, $content, $cc);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test buildHtmlTag
	 */
	public function testBuildHtmlTag() {
		$tag	= 'a';
		$params	= array(
			'href'		=> 'test/url.html',
			'onclick'	=> 'doSomething()'
		);
		$content	= 'Click me';
		$expect	= '<a href="test/url.html" onclick="doSomething()">Click me</a>';
		$result	= TodoyuString::buildHtmlTag($tag, $params, $content);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test cleanRteText
	 */
	public function testCleanRteText() {
		$text1	= '<p>&nbsp;</p><p>Second paragraph</p>';
		$expect1= '<p>Second paragraph</p>';
		$result1= TodoyuString::cleanRTEText($text1);

		$text2	= "<pre>Preformatted text\nwith linebreaks and whitespaces                    </pre>";
		$expect2= 'Preformatted text<br />with linebreaks and whitespaces';
		$result2= TodoyuString::cleanRTEText($text2);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
	}



	/**
	 * Test extractHttpHeaders
	 */
	public function testExtractHttpHeaders() {
		$responseContent = "200\r\n"
						. "Date: Fri, 18 Mar 2011 16:07:16 GMT\r\n"
						. "Server: Apache/2.2.14 (Win32) DAV/2 mod_ssl/2.2.14 OpenSSL/0.9.8l mod_autoindex_color PHP/5.3.1 mod_apreq2-20090110/2.7.1 mod_perl/2.0.4 Perl/v5.10.1"
						. "\r\n\r\n"
						. "Response Content";

		$headers	= TodoyuString::extractHttpHeaders($responseContent);

		$this->assertEquals(200, $headers['status']);
		$this->assertEquals('Fri, 18 Mar 2011 16:07:16 GMT', $headers['Date']);
		$this->assertEquals('Apache/2.2.14 (Win32) DAV/2 mod_ssl/2.2.14 OpenSSL/0.9.8l mod_autoindex_color PHP/5.3.1 mod_apreq2-20090110/2.7.1 mod_perl/2.0.4 Perl/v5.10.1', $headers['Server']);
	}



	/**
	 * Test extractHeadersFromString
	 */
	public function testExtractHeadersFromString() {
		$headerContent = "200\r\n"
						. "Date: Fri, 18 Mar 2011 16:07:16 GMT\r\n"
						. "Server: Apache/2.2.14 (Win32) DAV/2 mod_ssl/2.2.14 OpenSSL/0.9.8l mod_autoindex_color PHP/5.3.1 mod_apreq2-20090110/2.7.1 mod_perl/2.0.4 Perl/v5.10.1";

		$headers	= TodoyuString::extractHttpHeaders($headerContent);

		$this->assertEquals(200, $headers['status']);
		$this->assertEquals('Fri, 18 Mar 2011 16:07:16 GMT', $headers['Date']);
		$this->assertEquals('Apache/2.2.14 (Win32) DAV/2 mod_ssl/2.2.14 OpenSSL/0.9.8l mod_autoindex_color PHP/5.3.1 mod_apreq2-20090110/2.7.1 mod_perl/2.0.4 Perl/v5.10.1', $headers['Server']);
	}



	/**
	 * Test getversioninfo
	 */
	public function testgetversioninfo() {
		$version	= '2.3.43-alpha';
		$result		= TodoyuString::getVersionInfo($version);

		$this->assertEquals(2, $result['major']);
		$this->assertEquals(3, $result['minor']);
		$this->assertEquals(43, $result['revision']);
		$this->assertEquals('alpha', $result['status']);
	}



	/**
	 * Test replaceUrlWithLink
	 */
	public function testReplaceUrlWithLink() {
		$text	= 'This is plaintext with www.todoyu.com links in it. http://www.snowflake.ch You can also mail: team@todoyu.com';
		$expect	= 'This is plaintext with <a href="http://www.todoyu.com" target="_blank">www.todoyu.com</a> links in it. <a href="http://www.snowflake.ch" target="_blank">http://www.snowflake.ch</a> You can also mail: <a href="mailto:team@todoyu.com">team@todoyu.com</a>';

		$result	= TodoyuString::replaceUrlWithLink($text);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test replaceUrlWithLink
	 */
	public function testReplaceUrlWithLinkEmail() {
		$text		= 'Test an embedded <a href="mailto:team@todoyu.com" target="_blank">team@todoyu.com</a> Email address';

		$text1		= 'Test an embedded <b><a href="mailto:team@todoyu.com" target="_blank">team@todoyu.com</a></b> Email address';

		$text2		= 'Test an team@todoyu.com Email address';
		$expected2	= 'Test an <a href="mailto:team@todoyu.com">team@todoyu.com</a> Email address';

		$text3		= 'Test <span>Email: team@todoyu.com </span> with surrounding other tags';
		$expected3	= 'Test <span>Email: <a href="mailto:team@todoyu.com">team@todoyu.com</a> </span> with surrounding other tags';

		$text4		= 'Test <span>team@todoyu.com</span> Email address';
		$expected4	= 'Test <span><a href="mailto:team@todoyu.com">team@todoyu.com</a></span> Email address';

		$result	= TodoyuString::replaceUrlWithLink($text);
		$this->assertEquals($text, $result);

		$result	= TodoyuString::replaceUrlWithLink($text1);
		$this->assertEquals($text1, $result);

		$result	= TodoyuString::replaceUrlWithLink($text2);
		$this->assertEquals($expected2, $result);

		$result	= TodoyuString::replaceUrlWithLink($text3);
		$this->assertEquals($expected3, $result);

		$result	= TodoyuString::replaceUrlWithLink($text4);
		$this->assertEquals($expected4, $result);
	}



	/**
	 * Test Extracthttpstatuscode
	 */
	public function testExtracthttpstatuscode() {
		$test200	= 'HTTP/1.1 200 OK';
		$test404	= 'HTTP/1.1 404 Not Found';

		$result200	= TodoyuString::extractHttpStatusCode($test200);
		$result404	= TodoyuString::extractHttpStatusCode($test404);

		$this->assertEquals(200, $result200);
		$this->assertEquals(404, $result404);
	}



	/**
	 * Test enableJsFunctionInJSON
	 */
	public function testenableJsFunctionInJSON() {
		$array	= array(
			'func' => 'function(arg){return arg;}'
		);
		$json		= json_encode($array);
		$enabled	= TodoyuString::enableJsFunctionInJSON($json);
		$expected	= '{"func":function(arg){return arg;}}';

		$this->assertEquals($expected, $enabled);
	}



	/**
	 * Test wrapwithtag
	 */
	public function testwrapwithtag() {
		$result	= TodoyuString::wrapWithTag('strong', 'Bold');
		$expect	= '<strong>Bold</strong>';

		$this->assertEquals($result, $expect);
	}



	/**
	 * Test wraptodoyulink
	 */
	public function testwraptodoyulink() {
			// Check wrapped link w/o hash parameter and target attribute
		$params1	= array(
			'ext'		=> 'project',
			'controller'=> 'test',
		);
		$expect1	= '<a href="' . PATH_WEB . '/index.php?ext=project&amp;controller=test">Link Text</a>';
		$result1	= TodoyuString::wrapTodoyuLink('Link Text', 'project', $params1);

		$this->assertEquals($expect1, $result1);

			// Check wrapped link with hash parameter and target attribute
		$params2	= array(
			'ext'			=> 'project',
			'controller'	=> 'test',
			'action'		=> 'foo'
		);
		$expect2	= '<a href="' . PATH_WEB . '/index.php?ext=project&amp;controller=test&amp;action=foo#myHash" target="_blank">Link</a>';
		$result2	= TodoyuString::wrapTodoyuLink('Link', 'project', $params2, 'myHash', '_blank');

		$this->assertEquals($expect2, $result2);


//		$params3	= array();
//		$expect3	= 'not linked';
//		$result3	= TodoyuString::wrapTodoyuLink('not linked', 'notanextensionkey', $params3);
//
//		$this->assertEquals($expect3, $result3);
	}



	/**
	 * Test substitutelinkableelements
	 */
	public function testsubstitutelinkableelements() {
		$testHttp	= 'visit http://www.todoyu.com for more informations';
		$expectHttp	= 'visit <a href="http://www.todoyu.com" target="_blank">http://www.todoyu.com</a> for more informations';
		$resultHttp	= TodoyuString::substituteLinkableElements($testHttp);

		$this->assertEquals($expectHttp, $resultHttp);

		$testPlain		= 'visit www.todoyu.com for more informations';
		$expectPlain	= 'visit <a href="http://www.todoyu.com" target="_blank">www.todoyu.com</a> for more informations';
		$resultPlain	= TodoyuString::substituteLinkableElements($testPlain);

		$this->assertEquals($expectPlain, $resultPlain);

		$testEmail		= 'contact us at team@todoyu.com for more informations';
		$expectEmail	= 'contact us at <a href="mailto:team@todoyu.com">team@todoyu.com</a> for more informations';
		$resultEmail	= TodoyuString::substituteLinkableElements($testEmail);

		$this->assertEquals($expectEmail, $resultEmail);
	}



	/**
	 * Test htmlentities
	 */
	public function testhtmlentities() {
		$test	= '<strong>test äöü</strong>';
		$expect	= '&lt;strong&gt;test &auml;&ouml;&uuml;&lt;/strong&gt;';
		$result	= TodoyuString::htmlentities($test);

		$this->assertEquals($expect, $result);
	}



	/**
	 * Test generategoodpassword
	 */
	public function testgenerategoodpassword() {
		Todoyu::$CONFIG['SETTINGS']['passwordStrength'] = array(
			'minLength'			=> 8,
			'hasNumbers'		=> true,
			'hasLowerCase'		=> true,
			'hasUpperCase'		=> true,
			'hasSpecialChars'	=> true
		);

		$password	= TodoyuString::generateGoodPassword();

		$this->assertEquals(8, strlen($password));
		$this->assertRegExp('/\d/', $password);
		$this->assertRegExp('/[a-z]/', $password);
		$this->assertRegExp('/[A-Z]/', $password);
		$this->assertRegExp('/[#&@$_%?+-]/', $password);
	}


	public function testRemovePathParts() {
		$input1		= '/etc/passwd';
		$expect1	= 'passwd';
		$result1	= TodoyuString::removePathParts($input1);

		$input2		= '../../../other/folder/file.txt';
		$expect2	= 'file';
		$result2	= TodoyuString::removePathParts($input2);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
	}


	public function testEndsWith() {
		$text	= 'Hallo World';

		$isEnding	= TodoyuString::endsWith($text, 'World');
		$notEnding	= TodoyuString::endsWith($text, 'todoyu');

		$this->assertTrue($isEnding);
		$this->assertFalse($notEnding);
	}

	public function testIsUcFirst() {
		$text1	= 'Hello World';
		$text2	= 'hello world';

		$isUcFirst	= TodoyuString::isUcFirst($text1);
		$notUcFirst	= TodoyuString::isUcFirst($text2);

		$this->assertTrue($isUcFirst);
		$this->assertFalse($notUcFirst);
	}


	public function testIsContainingHtml() {
		$textHtml	= 'test <strong>strong</strong>';
		$textPlain	= 'test not strong';

		$hasHtml	= TodoyuString::isContainingHTML($textHtml);
		$noHtml		= TodoyuString::isContainingHTML($textPlain);

		$this->assertTrue($hasHtml);
		$this->assertFalse($noHtml);
	}


	public function testRemoveAllWhitespace() {
		$text	= ' 	 d df ad	sdfasdf   ';
		$expect	= 'ddfadsdfasdf';
		$result	= TodoyuString::removeAllWhitespace($text);

		$this->assertEquals($expect, $result);
	}

}

?>