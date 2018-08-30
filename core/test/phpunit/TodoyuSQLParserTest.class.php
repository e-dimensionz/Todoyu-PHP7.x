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
 * Test SQL parser
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuSQLParserTest extends PHPUnit_Framework_TestCase {

	public function testParseCreateQuery() {
		$queries	= TodoyuSQLManager::getQueriesFromFile('core/test/files/table.sql');
		$testQuery	= $queries[0];
		$createInfo	= TodoyuSQLParser::parseCreateQuery($testQuery);

		$this->assertEquals(9, sizeof($createInfo['columns']));
		$this->assertEquals('tablename', $createInfo['table']);
		$this->assertArrayHasKey('id', $createInfo['columns']);
		$this->assertEquals('decimal(5,2)', $createInfo['columns']['price']['type']);
		$this->assertEquals('DEFAULT 0.0', $createInfo['columns']['price']['default']);
		$this->assertEquals('NOT NULL', $createInfo['columns']['deleted']['null']);
	}

}

?>