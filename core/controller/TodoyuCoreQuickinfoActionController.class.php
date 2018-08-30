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
 * Core Action Controller
 * Quickinfo
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCoreQuickinfoActionController extends TodoyuActionController {

	/**
	 * Render quickinfo JSON
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function getAction(array $params) {
			// Get name of quickinfo element, e.g. 'person'
		$recordType	= trim($params['quickinfo']);
			// Get element item ID
		$element= trim($params['element']);

		$quickInfo	= new TodoyuQuickinfo($recordType, $element);

		$quickInfo->printJSON();
	}

}

?>