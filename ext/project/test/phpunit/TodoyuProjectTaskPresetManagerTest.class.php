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
 * [Description]
 *
 * @package		Todoyu
 * @subpackage	[Subpackage]
 */
class TodoyuProjectTaskPresetManagerTest extends PHPUnit_Framework_TestCase {

	public function testGetDateFromDayDuration() {
		$dateStart1		= strtotime('2012-02-21');
		$dateEnd1Expect	= strtotime('2012-02-24');
		$dateEnd1		= TodoyuProjectTaskPresetManager::getDateFromDayDuration(4, $dateStart1);
		$this->assertEquals($dateEnd1Expect, $dateEnd1);

		$dateStart2		= strtotime('2012-02-21');
		$dateEnd2Expect	= strtotime('2012-03-01');
		$dateEnd2		= TodoyuProjectTaskPresetManager::getDateFromDayDuration('work_7', $dateStart2);
		$this->assertEquals($dateEnd2Expect, $dateEnd2);

		$dateStart3		= strtotime('2012-02-21');
		$dateEnd3Expect	= strtotime('2012-03-12');
		$dateEnd3		= TodoyuProjectTaskPresetManager::getDateFromDayDuration('work_14', $dateStart3);
		$this->assertEquals($dateEnd3Expect, $dateEnd3);

		$dateStart4		= strtotime('2012-02-18');
		$dateEnd4Expect	= strtotime('2012-03-09');
		$dateEnd4		= TodoyuProjectTaskPresetManager::getDateFromDayDuration('work_14', $dateStart4);
		$this->assertEquals($dateEnd4Expect, $dateEnd4);

		$dateStart5		= strtotime('2012-02-18');
		$dateEnd5Expect	= strtotime('2012-02-29');
		$dateEnd5		= TodoyuProjectTaskPresetManager::getDateFromDayDuration('work_7', $dateStart5);
		$this->assertEquals($dateEnd5Expect, $dateEnd5);

		$dateStart6		= strtotime('2012-02-21');
		$dateEnd6Expect	= strtotime('2012-03-01');
		$dateEnd6		= TodoyuProjectTaskPresetManager::getDateFromDayDuration('work_7', $dateStart6);
		$this->assertEquals($dateEnd6Expect, $dateEnd6);

	}

}

?>