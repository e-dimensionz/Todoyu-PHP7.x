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
 * Test for: TodoyuArray
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuArrayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Array
	 */
	private $array;



	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->array = array(
			array(
				'id'		=> 32,
				'firstname'	=> 'Max',
				'lastname'	=> 'Miller',
				'street'	=> 'Franklin Street'
			),
			array(
				'id'		=> 45,
				'firstname'	=> 'John',
				'lastname'	=> 'Doe',
				'street'	=> 'Roosewelt Road'
			),
			array(
				'id'		=> 12,
				'firstname'	=> 'Michael',
				'lastname'	=> 'Schumacher',
				'street'	=> 'Fist In Your Face Blvd 123'
			),
			array(
				'id'		=> 284,
				'firstname'	=> 'Juck',
				'lastname'	=> 'Norris',
				'street'	=> 'Fist In Your Face Blvd 23'
			),
			array(
				'id'		=> 15,
				'firstname'	=> 'Luke',
				'lastname'	=> 'Skywalker',
				'street'	=> 'Star Freeway \" Sub Galaxy'
			)
		);

	}



	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}



	/**
	 * Test TodoyuArray::getColumn
	 */
	public function testGetColumn() {
		$idColumn	= TodoyuArray::getColumn($this->array, 'id');

		$this->assertEquals(32, $idColumn[0]);
	}



	/**
	 * Test TodoyuArray::getColumnUnique
	 */
	public function testGetColumnUnique() {
		$array	= array(
			array(
				'name'	=> 'John'
			),
			array(
				'name'	=> 'Jim'
			),
			array(
				'name'	=> 'Jack'
			),
			array(
				'name'	=> 'Jack'
			)
		);

		$column	= TodoyuArray::getColumnUnique($array, 'name');

		$this->assertEquals(3, sizeof($column));
		$this->assertEquals($column, array_unique($column));
	}



	/**
	 * Test TodoyuArray::getFirstKey
	 */
	public function testGetFirstKey() {
		$firstKey	= TodoyuArray::getFirstKey($this->array[0]);

		$this->assertEquals('id', $firstKey);
	}



	/**
	 * Test TodoyuArray::getLastKey
	 */
	public function testGetLastKey() {
		$last	= TodoyuArray::getLastKey($this->array[0]);

		$this->assertEquals('street', $last);
	}



	/**
	 * Test TodoyuArray::intval
	 */
	public function testIntval() {
		$ids	= TodoyuArray::getColumn($this->array, 'id');

		$ids	= TodoyuArray::intval($ids, true, true);
		$sum	= array_sum($ids);

		$this->assertEquals(388, $sum);

		$names	= TodoyuArray::getColumn($this->array, 'name');

		$names	= TodoyuArray::intval($names, true, true);
		$sum	= array_sum($names);

		$this->assertEquals(0, $sum);
	}



	/**
	 * Test TodoyuArray::floatVal
	 */
	public function testFloatval() {
		$array	= array(
			1,
			2,
			'3.3',
			'4',
			5.5,
			'a'
		);

		$floats	= TodoyuArray::floatval($array);

		$this->assertEquals(1.0, $floats[0]);
		$this->assertEquals(2.0, $floats[1]);
		$this->assertEquals(3.3, $floats[2]);
		$this->assertEquals(4.0, $floats[3]);
		$this->assertEquals(5.5, $floats[4]);
		$this->assertEquals(0.0, $floats[5]);
	}



	/**
	 * Test TodoyuArray::intImplode
	 */
	public function testIntImplode() {
		$implode= TodoyuArray::intImplode($this->array[0], '.');
		$expect	= '32.0.0.0';

		$this->assertEquals($expect, $implode);

		$ids	= TodoyuArray::getColumn($this->array, 'id');
		$implode= TodoyuArray::intImplode($ids, ',');
		$expect	= '32,45,12,284,15';

		$this->assertEquals($expect, $implode);
	}



	/**
	 * Test TodoyuArray::reform
	 */
	public function testReform() {
		$reformConfig	= array(
			'id'		=> 'value',
			'firstname'	=> 'label'
		);
		$new	= TodoyuArray::reform($this->array, $reformConfig);

		$this->assertInternalType('array', $new);
		$this->assertTrue(isset($new[0]['value']));
		$this->assertEquals('Max', $new[0]['label']);
	}



	/**
	 * Test TodoyuArray::reformWithFieldAsIndex
	 */
	public function testReformWithFieldAsIndex() {
		$reform	= array(
			'id'		=> 'id',
			'firstname'	=> 'first',
			'lastname'	=> 'last'
		);
		$index	= 'id';

		$reformed	= TodoyuArray::reformWithFieldAsIndex($this->array, $reform, false, $index);

		$this->assertEquals('Max', $reformed[32]['first']);
		$this->assertEquals('Doe', $reformed[45]['last']);
		$this->assertEquals(15, $reformed[15]['id']);
	}



	/**
	 * Test TodoyuArray::stripslashes
	 */
	public function testStripslashes() {
		$streets	= TodoyuArray::getColumn($this->array, 'street');
		$streets	= TodoyuArray::stripslashes($streets);

		$this->assertEquals('Star Freeway " Sub Galaxy', $streets[4]);
	}



	/**
	 * Test TodoyuArray::sortByLabel
	 */
	public function testSortByLabel() {
		$sorted	= TodoyuArray::sortByLabel($this->array, 'firstname');
		$this->assertEquals('Luke', $sorted[2]['firstname']);

		$sorted	= TodoyuArray::sortByLabel($this->array, 'id');
		$this->assertEquals('Michael', $sorted[0]['firstname']);

		$sorted	= TodoyuArray::sortByLabel($this->array, 'lastname', true);
		$this->assertEquals('Luke', $sorted[0]['firstname']);

		$sorted	= TodoyuArray::sortByLabel($this->array, 'street', false, true, false);
		$this->assertEquals('Schumacher', $sorted[0]['lastname']);

		$sorted	= TodoyuArray::sortByLabel($this->array, 'street', false, true, true);
		$this->assertEquals('Norris', $sorted[0]['lastname']);
	}



	/**
	 * Test TodoyuArray::filter
	 */
	public function testFilter() {
		$filter	= array(
			'id'	=> array(12, 88)
		);
		$filtered = TodoyuArray::filter($this->array, $filter);
		$this->assertEquals(1, sizeof($filtered));

		$filter	= array(
			'id'		=> array(45, 15),
			'firstname'	=> array('John', 'Michael')
		);
		$filtered = TodoyuArray::filter($this->array, $filter);
		$this->assertEquals('Doe', $filtered[0]['lastname']);
	}



	/**
	 * Test TodoyuArray::prefix
	 */
	public function testPrefix() {
		$names		= TodoyuArray::getColumn($this->array, 'firstname');
		$prefixed	= TodoyuArray::prefix($names, 'xxx');

		$this->assertEquals('xxxJohn', $prefixed[1]);
	}



	/**
	 * Test TodoyuArray::insertElement
	 */
	public function testInsertElement() {
		$assoc	= array();
		foreach($this->array as $person) {
			$assoc[$person['lastname']] = $person;
		}
		$new	= array(
			'id'		=> 666,
			'firstname'	=> 'George',
			'lastname'	=> 'Bush',
			'street'	=> 'Ex Presents Alley'
		);

		$assoc = TodoyuArray::insertElement($assoc, 'georgy', $new, 'after', 'Doe');
	}



	/**
	 * Test TodoyuArray::fromSimpleXml
	 *
	 * @todo Implement testFromSimpleXml().
	 */
	public function testFromSimpleXml() {
		$simpleXml	= simplexml_load_file(PATH_CORE . '/test/files/xml.xml');

		$array		= TodoyuArray::fromSimpleXml($simpleXml);


		$this->assertEquals('John Resig', $array['book'][1]['author']);
		$this->assertEquals('CHF 72.20', $array['book'][0]['price']);
		$this->assertEquals('1-59059-727-3', $array['book'][1]['@attributes']['isbn']);
	}



	/**
	 * Test TodoyuArray::toArray
	 */
	public function testToArray() {
		$array	= array(
			1,
			array(1,2,3),
		);
		$class	= new stdClass();
		$class->test = 4;
		$class->field= 'text';
		$array[] = $class;

		$clearArray	= TodoyuArray::toArray($array);

		$this->assertEquals('text', $clearArray[2]['field']);
		$this->assertEquals('4', $clearArray[2]['test']);
		$this->assertEquals(3, $clearArray[1][2]);
	}



	/**
	 * Test TodoyuArray::removeKey
	 *
	 * @todo Implement testRemoveKeys().
	 */
	public function testRemoveKeys() {
		$array	= array(
			'switzerland'	=> 'berne',
			'england'		=> 'london',
			'france'		=> 'paris',
			'italy'			=> 'rome',
			'spain'			=> 'madrid'
		);

		$remove	= array(
			'france',
			'italy'
		);

		$result	= TodoyuArray::removeKeys($array, $remove);

		$this->assertTrue(isset($result['switzerland']));
		$this->assertTrue(isset($result['england']));
		$this->assertTrue(isset($result['spain']));
		$this->assertFalse(isset($result['france']));
		$this->assertFalse(isset($result['italy']));
	}



	/**
	 * Test TodoyuArray::implodeQuoted
	 *
	 * @todo Implement testImplodeQuoted().
	 */
	public function testImplodeQuoted() {
		$array	= array(1,'fun', 'test"with\'quotes', array());

		$quoted	= TodoyuArray::implodeQuoted($array);
		$expect	= "'1','fun','test\\\"with\\'quotes'";

		$this->assertEquals($expect, $quoted);
	}



	/**
	 * Test TodoyuArray::wrapItems
	 */
	public function testWrapItems() {
		$array	= array(
			1,
			2,
			'string',
			array(),
			null
		);

		$wrapped	= TodoyuArray::wrapItems($array, 'A', 'Z');

		$this->assertEquals(3, sizeof($wrapped));
		$this->assertEquals('A1Z', $wrapped[0]);
	}



	/**
	 * Test TodoyuArray::assure
	 *
	 * @todo Implement testAssure().
	 */
	public function testAssure() {
		$var_1	= array();
		$var_2	= array(1,2,3);
		$var_3	= 3;
		$var_4	= 'string';
		$var_5	= 4.234343;
		$var_6	= null;
		$var_7	= false;

		$this->assertTrue(is_array(TodoyuArray::assure($var_1)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_2)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_3)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_4)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_5)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_6)));
		$this->assertTrue(is_array(TodoyuArray::assure($var_7)));

		$res_2	= TodoyuArray::assure($var_2);
		$res_5	= TodoyuArray::assure($var_5, true);

		$this->assertEquals($var_2, $res_2);
		$this->assertEquals($var_5, $res_5[0]);
	}



	/**
	 * Test TodoyuArray::mergeSubArrays
	 *
	 * @todo Implement testMergeSubArrays().
	 */
	public function testMergeSubArrays() {
		$array = array(
			array(1,2,3),
			array(4,5,6)
		);

		$result	= TodoyuArray::mergeSubArrays($array);

		$this->assertEquals(4, $result[3]);
		$this->assertEquals(6, sizeof($result));

		$array[] = array(6,7,8);

		$result2= TodoyuArray::mergeSubArrays($array);

		$this->assertEquals(9, sizeof($result2));
		$this->assertEquals(6, $result2[6]);
	}



	/**
	 * Test TodoyuArray::mergeUnique
	 *
	 * @todo Implement testMergeUnique().
	 */
	public function testMergeUnique() {
		$array1 = array(1,2,3);
		$array2 = array(4,5,6);
		$array3 = array(6,2,7);

		$result	= TodoyuArray::mergeUnique($array1, $array2, $array3);

		$this->assertTrue(is_array($result));
		$this->assertEquals(7, sizeof($result));
		$this->assertEquals(28, array_sum($result));
	}



	/**
	 * Test TodoyuArray::mergeRecursive
	 */
	public function testMergeRecursive() {
		$arrayOne	= array(
			'type'		=> 'area',
			'height'	=> 500,
			'opacity'	=> 0.8,
			'events'	=> array(
				'close'	=> false,
				'resize'=> true
			)
		);
		$arrayTwo	= array(
			'height'	=> 600,
			'width'		=> 400,
			'events'	=> array(
				'move'	=> true,
				'close'	=> true,
				'max'	=> false
			)
		);

		$merged	= TodoyuArray::mergeRecursive($arrayOne, $arrayTwo);

		$this->assertEquals('area', $merged['type']);
		$this->assertEquals(600, $merged['height']);
		$this->assertEquals(0.8, $merged['opacity']);
		$this->assertEquals(true, $merged['events']['resize']);
		$this->assertEquals(false, $merged['events']['max']);
		$this->assertEquals(true, $merged['events']['close']);
	}



	/**
	 * Test TodoyuArray::merge
	 */
	public function testMerge() {
		$array1	= array(1,2,3);
		$array2	= array(3,4,5);

		$merged	= TodoyuArray::merge($array1, $array2);

		$this->assertInternalType('array', $merged);
		$this->assertEquals(6, sizeof($merged));

		$merged	= TodoyuArray::merge($array1, null);

		$this->assertInternalType('array', $merged);
		$this->assertEquals(3, sizeof($merged));

		$merged	= TodoyuArray::merge(false, null);

		$this->assertInternalType('array', $merged);
		$this->assertEquals(0, sizeof($merged));
	}



	/**
	 * Test TodoyuArray::flatten
	 *
	 * @todo Implement testFlatten().
	 */
	public function testFlatten() {
		$array = array(
			array(1,2,3),
			array(4,5,6),
			array(7,8,array(
				9, 10, 11
			))
		);

		$result	= TodoyuArray::flatten($array);

		$this->assertTrue(is_array($result));
		$this->assertEquals(11, sizeof($result));
		$this->assertEquals(11, $result[10]);
	}



	/**
	 * Test TodoyuArray::removeByValue
	 *
	 * @todo Implement testRemoveByValue().
	 */
	public function testRemoveByValue() {
		$array	= array(1,2,3,4,5,6,7,8,9);
		$remove	= array(3,4,6,10);

		$result	= TodoyuArray::removeByValue($array, $remove);

		$this->assertEquals(6, sizeof($result));
		$this->assertEquals(7, $result[3]);
	}



	/**
	 * Test TodoyuArray::removeDuplicates
	 *
	 * @todo Implement testRemoveDuplicates().
	 */
	public function testRemoveDuplicates() {
		$array = array(
			array('id'	=> 44),
			array('id'	=> 45),
			array('id'	=> 46),
			array('id'	=> 46)
		);

		$result	= TodoyuArray::removeDuplicates($array, 'id');

		$this->assertEquals(3, sizeof($result));
	}



	/**
	 * Test TodoyuArray::intExplode
	 *
	 * @todo Implement testIntExplode().
	 */
	public function testIntExplode() {
		$string	= '1,2,3,4,a,,5,-7,';
		$array1	= TodoyuArray::intExplode(',', $string);

		$this->assertEquals(9, sizeof($array1));
		$this->assertEquals(0, $array1[8]);
		$this->assertEquals(-7, $array1[7]);

		$array2	= TodoyuArray::intExplode(',', $string, true);

		$this->assertEquals(9, sizeof($array2));
		$this->assertEquals(0, $array2[7]);

		$array3	= TodoyuArray::intExplode(',', $string, true, true);

		$this->assertEquals(5, sizeof($array3));
		$this->assertEquals(5, $array3[4]);
	}



	/**
	 * Test TodoyuArray::trimExplode
	 *
	 * @todo Implement testTrimExplode().
	 */
	public function testTrimExplode() {
		$string	= ' test    ,   4,2, sdfasdfasdf asdfasdfasdfa            ,d,';
		$array1	= TodoyuArray::trimExplode(',', $string);

		$this->assertEquals('test', $array1[0]);

		$array2	= TodoyuArray::trimExplode(',', $string, true);

		$this->assertEquals(5, sizeof($array2));
	}



	/**
	 * Test TodoyuArray::trimImplode
	 */
	public function testTrimImplode() {
		$array	= array(
			3,
			'   text1 ',
			' test2 ',
			'342',
			4.5
		);

		$string	= TodoyuArray::trimImplode(',', $array);

		$this->assertEquals('3,text1,test2,342,4.5', $string);
	}



	/**
	 * Test TodoyuArray::trim
	 *
	 * @todo Implement testTrim().
	 */
	public function testTrim() {
		$array	= array(
			' asdf asdf   ',
			'         ',
			'test         ',
			's s s s '
		);
		$result	= TodoyuArray::trim($array);

		$this->assertEquals('test', $result[2]);
	}



	/**
	 * Test TodoyuArray::useFieldAsIndex
	 *
	 * @todo Implement testUseFieldAsIndex().
	 */
	public function testUseFieldAsIndex() {
		$array = array(
			'a'	=> array(
				'id'	=> 1,
				'name'	=> 'Elvis'
			),
			'b'	=> array(
				'id'	=> 50,
				'name'	=> 'Jackson'
			),
			'c'	=> array(
				'id'	=> 3,
				'name'	=> 'Jagger'
			)
		);

		$result	= TodoyuArray::useFieldAsIndex($array, 'id');

		$this->assertEquals('Jagger', $result[3]['name']);
		$this->assertEquals(3, sizeof($result));
		$this->assertEquals('a', $result[1]['_oldIndex']);
	}



	/**
	 * Test TodoyuArray::htmlspecialchars
	 */
	public function testHtmlspecialchars() {
		$array	= array(
			1,
			array(),
			'normal',
			'<strong>"\'',
			'<&>'
		);

		$clean	= TodoyuArray::htmlspecialchars($array);

		$this->assertEquals(1, $clean[0]);
		$this->assertEquals(array(), $clean[1]);
		$this->assertEquals('normal', $clean[2]);
		$this->assertEquals('&lt;strong&gt;&quot;&#039;', $clean[3]);
		$this->assertEquals('&lt;&amp;&gt;', $clean[4]);
	}



	/**
	 * Test TodoyuArray::diffLeft
	 */
	public function testDiffLeft() {
		$arrayOne	= array(1,2,3,4,5);
		$arrayTwo	= array(3,4,5,6,7);

		$diffOne	= TodoyuArray::diffLeft($arrayOne, $arrayTwo);
		$diffTwo	= TodoyuArray::diffLeft($arrayTwo, $arrayOne);

		$this->assertEquals(array(1,2), $diffOne);
		$this->assertEquals(array(6,7), $diffTwo);
	}


	public function testStrtolower() {
		$input	= array(
			'Hello',
			'WORLD',
			'lowercase'
		);
		$output	= TodoyuArray::strtolower($input);

		$this->assertEquals('hello', $output[0]);
		$this->assertEquals('world', $output[1]);
		$this->assertEquals('lowercase', $output[2]);
	}

	public function testImplodeassoc() {
		$data	= array(
			'width'		=> 200,
			'height'	=> '300px',
			'display'	=> 'none'
		);

		$cssOutput	= TodoyuArray::implodeAssoc($data, ':', ';');
		$expect		= 'width:200;height:300px;display:none';

		$this->assertEquals($expect, $cssOutput);
	}

	public function testAssurefromjson() {
		$data	= array(
			'key'	=> 'value'
		);
		$json	= json_encode($data);
		$parsed	= TodoyuArray::assureFromJSON($json);

		$this->assertEquals('value', $parsed['key']);

		$parsedFail	= TodoyuArray::assureFromJSON('x');
		$this->assertInternalType('array', $parsedFail);
	}

	public function testCreatemap() {
		$mapKeys	= array('a', 'b', 'c');
		$map		= TodoyuArray::createMap($mapKeys, 3);

		$this->assertEquals(3, $map['c']);
	}


	public function testGroupbyfield() {
		$data = array(
			'tom'	=> array(
				'country'	=> 'switzerland',
				'age'		=> 35
			),
			'lisa'	=> array(
				'country'	=> 'germany',
				'age'		=> 20
			),
			'paul'	=> array(
				'country'	=> 'switzerland',
				'age'		=> 44
			)
		);

		$groupped	= TodoyuArray::groupByField($data, 'country');

		$this->assertEquals(35, $groupped['switzerland']['tom']['age']);
	}

	public function testPrependselectoption() {
		$options	= array(
			array(
				'value'	=> 'a',
				'label'	=> 'The A'
			),
			array(
				'value'	=> 'b',
				'label'	=> 'The B'
			),
			array(
				'value'	=> 'c',
				'label'	=> 'The C'
			)
		);

		$newOptions	= TodoyuArray::prependSelectOption($options);

		$this->assertEquals(0, $newOptions[0]['value']);
	}



	public function testGetAllowedItems() {
		$items	= array(
			array(
				'name'		=> 'No check'
			),
			array(
				'name'		=> 'Allowed',
				'require'	=> 'portal.general:use'
			),
			array(
				'name'		=> 'Not Allowed',
				'require'	=> 'no.ext:right'
			)
		);

		$allowed	= TodoyuArray::getAllowedItems($items);

		// rights check for admin is not useful
		// @todo implement way to simulate user for a unittest
	}


	public function testMergeEmptyFields() {
		$base	= array(
			'a'	=> 1,
			'b'	=> '',
			'c'	=> 0,
			'd'	=> 'xxxxxxxxx'
		);
		$fallback = array(
			'a'	=> 'override',
			'b'	=> 'override',
			'c'	=> 'override',
			'd'	=> 'override',
		);

		$merged	= TodoyuArray::mergeEmptyFields($base, $fallback);

		$this->assertEquals(1, $merged['a']);
		$this->assertEquals('override', $merged['b']);
		$this->assertEquals('override', $merged['c']);
		$this->assertEquals('xxxxxxxxx', $merged['d']);
	}

}

?>