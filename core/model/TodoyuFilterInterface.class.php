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
 * Filter interface
 * All filter main classes have to implement this interface, so they can be called automatically
 *
 * @package		Todoyu
 * @subpackage	Core
 */
interface TodoyuFilterInterface {

	public function __construct(array $activeFilters = array(), $conjunction = 'AND');

	public function getItemIDs($sortingFallback = 'sorting', $limit = 100);

}

?>