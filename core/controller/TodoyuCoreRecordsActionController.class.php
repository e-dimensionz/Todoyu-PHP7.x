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
 * General records selector form element
 * Use callback for the given type
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCoreRecordsActionController extends TodoyuActionController {

	/**
	 * Get json encoded list of matching records
	 *
	 * @param	Array	$params
	 */
	public function listAction(array $params) {
		$type			= trim($params['type']);
		$searchWords	= TodoyuArray::trimExplode(' ', $params['search'], true);
		$ignoreKeys		= TodoyuArray::intExplode(',', $params['ignore']);
		$params			= TodoyuArray::assure($params['params']);

		if( sizeof($searchWords) > 0 ) {
			$listItems	= TodoyuFormRecordsManager::getListItems($type, $searchWords, $ignoreKeys, $params);
		} else {
			$listItems	= array();
		}

		TodoyuHeader::sendTypeJSON();

		echo json_encode($listItems);
	}

}

?>