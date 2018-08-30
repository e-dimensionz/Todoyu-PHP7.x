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
 * [Description]
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuSqlTest extends PHPUnit_Framework_TestCase {

	public function testBuildSelectQuery() {
		$expect1	= 'SELECT id FROM task WHERE status = 5';
		$result1	= TodoyuSql::buildSELECTquery('id', 'task', 'status = 5');

		$expect2	= 'SELECT id FROM task WHERE status = 5 GROUP BY project';
		$result2	= TodoyuSql::buildSELECTquery('id', 'task', 'status = 5', 'project');

		$expect3	= 'SELECT id FROM task WHERE status = 5 GROUP BY project ORDER BY date';
		$result3	= TodoyuSql::buildSELECTquery('id', 'task', 'status = 5', 'project', 'date');

		$expect4	= 'SELECT id FROM task WHERE status = 5 GROUP BY project ORDER BY date LIMIT 100';
		$result4	= TodoyuSql::buildSELECTquery('id', 'task', 'status = 5', 'project', 'date', 100);

		$expect5	= 'SELECT id FROM task LIMIT 100';
		$result5	= TodoyuSql::buildSELECTquery('id', 'task', '', '', '', 100);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
		$this->assertEquals($expect3, $result3);
		$this->assertEquals($expect4, $result4);
		$this->assertEquals($expect5, $result5);
	}

	public function testBuildInsertQuery() {
		$fields	= array(
			'a'	=> 'john',
			'b'	=> 'mike',
			'c'	=> 'NOT(\'escaped\')',
			'd'	=> 55.3
		);
		$expect1	= "INSERT INTO `table` (`a`,`b`,`c`,`d`) VALUES('john','mike',NOT('escaped'),'55.3')";
		$result1	= TodoyuSql::buildINSERTquery('table', $fields, array('c'));

		$this->assertEquals($expect1, $result1);
	}


	public function testBuildDeleteQuery() {
		$where	= 'status IN(1,2,3)';
		$expect	= 'DELETE FROM `table` WHERE status IN(1,2,3) LIMIT 3';
		$result	= TodoyuSql::buildDELETEquery('table', $where, 3);

		$this->assertEquals($expect, $result);
	}

	public function testBuildUpdateQuery() {
		$fields	= array(
			'a'	=> 'john',
			'b'	=> 'mike',
			'c'	=> 'NOT(\'escaped\')',
			'd'	=> 55.3
		);
		$expect	= "UPDATE `table` SET `a` = 'john', `b` = 'mike', `c` = NOT('escaped'), `d` = '55.3' WHERE id = 3";
		$result	= TodoyuSql::buildUPDATEquery('table', 'id = 3', $fields, array('c'));

		$this->assertEquals($expect, $result);
	}

	public function testBuildInListQueryPart() {
		$items1		= array(1,2,3);
		$field1		= 'id';
		$expect1	= '`id` IN(1,2,3)';
		$result1	= TodoyuSql::buildInListQueryPart($items1, $field1);

		$items2		= array('a', 'b', 'c');
		$field2		= 'task.id';
		$expect2	= "`task`.`id` IN('a','b','c')";
		$result2	= TodoyuSql::buildInListQueryPart($items2, $field2, false);

		$items3		= array(1,2,3);
		$field3		= 'task.id';
		$expect3	= "`task`.`id` NOT IN(1,2,3)";
		$result3	= TodoyuSql::buildInListQueryPart($items3, $field3, true, true);
		
		$items4		= array("'1'","'b'","'5'");
		$field4		= 'task.id';
		$expect4	= "`task`.`id` IN('1','b','5')";
		$result4	= TodoyuSql::buildInListQueryPart($items4, $field4, false, false, false);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
		$this->assertEquals($expect3, $result3);
		$this->assertEquals($expect4, $result4);
	}


	public function testBacktick() {
		$value1		= 'id';
		$expect1	= '`id`';
		$result1	= TodoyuSql::backtick($value1);

		$value2		= 'task.id';
		$expect2	= '`task`.`id`';
		$result2	= TodoyuSql::backtick($value2);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
	}



	public function testBacktickArray() {
		$items = array(
			'id',
			'task.title'
		);

		$result	= TodoyuSql::backtickArray($items);

		$this->assertEquals('`id`', $result[0]);
		$this->assertEquals('`task`.`title`', $result[1]);
	}

	public function testQuoteTablename() {
		$value	= 'task';
		$expect	= '`task`';
		$result	= TodoyuSql::quoteTablename($value);

		$this->assertEquals($expect, $result);
	}

	public function testBuildBooleanInvertQueryPart() {
		$field1		= 'visible';
		$expect1	= '`visible` XOR 1';
		$result1	= TodoyuSql::buildBooleanInvertQueryPart($field1);

		$field2		= 'visible';
		$table2		= 'task';
		$expect2	= '`task`.`visible` XOR 1';
		$result2	= TodoyuSql::buildBooleanInvertQueryPart($field2, $table2);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
	}


	public function testBuildFindInSetQueryPart() {
		$value	= 'a';
		$field	= 'title';
		$expect	= "FIND_IN_SET('a', `title`) != 0";
		$result	= TodoyuSql::buildFindInSetQueryPart($value, $field);
		
		$this->assertEquals($expect, $result);
	}


	public function testBuildLikeQueryPart() {
		$searchWords	= array('a', 'c\'+');
		$searchFields	= array('id', 'task.title');

		$expect1	= "((`id` LIKE '%a%' OR `task`.`title` LIKE '%a%') AND (`id` LIKE '%c\'+%' OR `task`.`title` LIKE '%c\'+%'))";
		$result1	= TodoyuSql::buildLikeQueryPart($searchWords, $searchFields);

		$expect2	= "((`id` NOT LIKE '%a%' AND `task`.`title` NOT LIKE '%a%') AND (`id` NOT LIKE '%c\'+%' AND `task`.`title` NOT LIKE '%c\'+%'))";
		$result2	= TodoyuSql::buildLikeQueryPart($searchWords, $searchFields, true);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
	}


	public function testQuoteFieldname() {
		$field1		= 'id';
		$expect1	= '`id`';
		$result1	= TodoyuSql::quoteFieldname($field1);

		$field2		= 'id';
		$table2		= 'task';
		$expect2	= '`task`.`id`';
		$result2	= TodoyuSql::quoteFieldname($field2, $table2);

		$field3		= 'id';
		$table3		= 'todoyu.task';
		$expect3	= '`todoyu`.`task`.`id`';
		$result3	= TodoyuSql::quoteFieldname($field3, $table3);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
		$this->assertEquals($expect3, $result3);
	}

	public function testEscape() {
		$value1		= '1';
		$expect1	= '1';
		$result1	= TodoyuSql::escape($value1);

		$value2		= "a'b";
		$expect2	= "a\'b";
		$result2	= TodoyuSql::escape($value2);

		$value3		= 55.4;
		$expect3	= '55.4';
		$result3	= TodoyuSql::escape($value3);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
		$this->assertEquals($expect3, $result3);
	}

	public function testEscapeArray() {
		$items	= array(
			'a'	=> 'a',
			'b'	=> "b'c",
			'c'	=> 'MAX(price)'
		);
		$noQuote	= array('c');

		$result1	= TodoyuSql::escapeArray($items);
		$result2	= TodoyuSql::escapeArray($items, true);
		$result3	= TodoyuSql::escapeArray($items, true, $noQuote);

		$this->assertEquals("b\'c", $result1['b']);
		$this->assertEquals("MAX(price)", $result1['c']);
		$this->assertEquals("'a'", $result2['a']);
		$this->assertEquals("'MAX(price)'", $result2['c']);
		$this->assertEquals("MAX(price)", $result3['c']);
	}


	public function testQuote() {
		$value1		= '1';
		$expect1	= "'1'";
		$result1	= TodoyuSql::quote($value1);

		$value2		= "a'b";
		$expect2	= "'a'b'";
		$result2	= TodoyuSql::quote($value2);
		
		$value3		= "a'b";
		$expect3	= "'a\'b'";
		$result3	= TodoyuSql::quote($value3, true);

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
		$this->assertEquals($expect3, $result3);
	}

	public function testQuoteArray() {
		$items	= array(
			'a' 	=> 'a',
			'b' 	=> 'b',
			'c' 	=> "c'+",
			'num' 	=> '45.2',
			'max' 	=> 'MAX(price)'
		);
		$noQuote	= array('max');

		$result1	= TodoyuSql::quoteArray($items);
		$result2	= TodoyuSql::quoteArray($items, $noQuote);
		$result3	= TodoyuSql::quoteArray($items, $noQuote, false);

		$this->assertEquals("'a'", $result1['a']);
		$this->assertEquals("'c\'+'", $result1['c']);
		$this->assertEquals("'MAX(price)'", $result1['max']);

		$this->assertEquals("'a'", $result2['a']);
		$this->assertEquals("'c\'+'", $result2['c']);
		$this->assertEquals("MAX(price)", $result2['max']);

		$this->assertEquals("'a'", $result3['a']);
		$this->assertEquals("'c'+'", $result3['c']);
		$this->assertEquals("MAX(price)", $result3['max']);
	}

}

?>