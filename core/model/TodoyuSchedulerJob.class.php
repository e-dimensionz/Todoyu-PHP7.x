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
 * Base class for scheduler jobs
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
abstract class TodoyuSchedulerJob {

	/**
	 * Job options
	 *
	 * @var	Array
	 */
	protected $options = array();

	/**
	 * Configured frequency when added
	 *
	 * @var	Integer
	 */
	protected $frequency = 0;


	/**
	 * Initialize
	 *
	 * @param	Array	$options
	 * @param	Integer	$frequency
	 */
	public function __construct(array $options = array(), $frequency) {
		$this->options	= $options;
		$this->frequency= (int) $frequency;
	}



	/**
	 * Get next date of planned execution
	 *
	 * @return	Integer
	 */
	protected function getNextExecutionTime() {
		return NOW + $this->frequency * 60;
	}



	/**
	 * Execute job
	 *
	 * @abstract
	 * @throws	Exception
	 * @return	Boolean
	 */
	abstract public function execute();

}

?>