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
 * Task asset object
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTaskAsset extends TodoyuAssetsAsset {

	/**
	 * Get task ID
	 *
	 * @return	Integer
	 */
	public function getTaskID() {
		return $this->getParentID();
	}



	/**
	 * Get task object
	 *
	 * @return	Task
	 */
	public function getTask() {
		return TodoyuProjectTaskManager::getTask($this->getTaskID());
	}

}

?>