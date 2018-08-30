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
class TodoyuRecordManagerTest extends PHPUnit_Framework_TestCase {

	public function testGetRecord() {
		$role	= TodoyuRecordManager::getRecord('TodoyuRole', 1);

		$this->assertInstanceOf('TodoyuRole', $role);
	}


	public function testGetRecordList() {
		$records	= TodoyuRecordManager::getRecordList('TodoyuRole', array(1,2,3));

		$this->assertInstanceOf('TodoyuRole', $records[1]);
	}


	public function testRemoveRecordCache() {
		$role		= TodoyuRecordManager::getRecord('TodoyuRole', 1);
		$cacheKey	= TodoyuRecordManager::makeClassKey('TodoyuRole', 1);

		$isInCache	= TodoyuCache::isIn($cacheKey);
		$this->assertTrue($isInCache);

		TodoyuRecordManager::removeRecordCache('TodoyuRole', 1);

		$isInCache	= TodoyuCache::isIn($cacheKey);
		$this->assertFalse($isInCache);
	}


	public function testRemoveRecordQueryCache() {
		$role		= TodoyuRecordManager::getRecord('TodoyuRole', 1);
		$cacheKey	= TodoyuRecordManager::makeRecordQueryKey('system_role', 1);

		$isInCache	= TodoyuCache::isIn($cacheKey);
		$this->assertTrue($isInCache);

		TodoyuRecordManager::removeRecordQueryCache('system_role', 1);

		$isInCache	= TodoyuCache::isIn($cacheKey);
		$this->assertFalse($isInCache);
	}

	public function testMakeRecordQueryKey() {
		$recordQueryKey	= TodoyuRecordManager::makeRecordQueryKey('system_role', 1);

		$this->assertEquals('system_role:1', $recordQueryKey);
	}


	public function testMakeClassKey() {
		$classKey	= TodoyuRecordManager::makeClassKey('TodoyuRole', 1);

		$this->assertEquals('TodoyuRole:1', $classKey);
	}


	public function testGetAllRecords() {
		$allRecords	= TodoyuRecordManager::getAllRecords('system_role');

		$this->assertInternalType('array', $allRecords);
		$this->assertInternalType('array', $allRecords[0]);
	}


	public function testGetRecordData() {
		$recordData	= TodoyuRecordManager::getRecordData('system_role', 1);

		$this->assertInternalType('array', $recordData);
	}

	public function testSaveRecord() {
		$idRecord = TodoyuRecordManager::saveRecord('system_role', array(
			'title'	=> 'test'
		));

		/**
		 * @var	TodoyuRole	$role
		 */
		$role	= TodoyuRecordManager::getRecord('TodoyuRole', $idRecord);

		$this->assertInstanceOf('TodoyuRole', $role);
		$this->assertEquals('test', $role->getTitle());

		$idRecordNew	= TodoyuRecordManager::saveRecord('system_role', array(
			'id'	=> $idRecord,
			'title'	=> 'test2'
		));

		$this->assertEquals($idRecord, $idRecordNew);

		TodoyuRecordManager::removeRecordCache('TodoyuRole', $idRecord);
		TodoyuRecordManager::removeRecordQueryCache('system_role', $idRecord);

		$role	= TodoyuRecordManager::getRecord('TodoyuRole', $idRecord);

		$this->assertInstanceOf('TodoyuRole', $role);
		$this->assertEquals('test2', $role->getTitle());
	}

	public function testAddRecord() {
		$idRecord = TodoyuRecordManager::addRecord('system_role', array(
			'title'	=> 'testing',
			'id'	=> -1000
		));
		/**
		 * @var	TodoyuRole	$role
		 */
		$role	= TodoyuRecordManager::getRecord('TodoyuRole', $idRecord);

		$this->assertEquals('testing', $role->getTitle());
	}

	public function testUpdateRecord() {
		TodoyuCache::disable();
		TodoyuRecordManager::updateRecord('system_role', 3, array(
			'title'	=> 'testUpdateRecord'
		));
		$role	= TodoyuRecordManager::getRecord('TodoyuRole', 3);

		$this->assertEquals('testUpdateRecord', $role->getTitle());

		TodoyuCache::enable();
	}

	public function testIsRecord() {
		$isRole	= TodoyuRecordManager::isRecord('system_role', 1);
		$notRole= TodoyuRecordManager::isRecord('system_role', 999999);

		$this->assertTrue($isRole);
		$this->assertFalse($notRole);
	}


	public function testUpdateRecords() {
		TodoyuRecordManager::updateRecords('system_role', 'id=2', array(
			'deleted'	=> 1
		));
	}

	public function testDeleteRecord() {
		$idRecord = TodoyuRecordManager::addRecord('system_role', array(
			'title'	=> 'testDeleteRecord'
		));
		TodoyuRecordManager::deleteRecord('system_role', $idRecord);

		$role	= TodoyuRecordManager::getRecord('TodoyuRole', $idRecord);

		$this->assertTrue($role->isDeleted());
	}


	public function testDeleteRecords() {
		$idRecord = TodoyuRecordManager::addRecord('system_role', array(
			'title'	=> 'testDeleteRecords'
		));

		TodoyuRecordManager::deleteRecords('system_role', 'id IN(' . $idRecord . ',99,100,101)');

		$role	= TodoyuRecordManager::getRecord('TodoyuRole', $idRecord);

		$this->assertTrue($role->isDeleted());
	}


	public function testDeleteRecordsByID() {
		TodoyuCache::disable();
		TodoyuRecordManager::deleteRecordsByID('system_role', array(1,2));

		$role	= TodoyuRecordManager::getRecord('TodoyuRole', 2);

		$this->assertTrue($role->isDeleted());
		TodoyuCache::enable();
	}

}

?>