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
 * Search filter controller
 *
 * @todo		Move this controller functions to another controller
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFiltercontrollerActionController extends TodoyuActionController {

	/**
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('search', 'general:area');
	}



	/**
	 * Autocomplete filter widget input (configuration is stored in filter config)
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function autocompletionAction(array $params) {
		$widgetKey	= $params['completionID'];
		$filterType	= $params['filtertype'];
		$searchWord	= $params['sword'];

		$results = TodoyuSearchFilterWidgetManager::getAutocompletionResults($filterType, $searchWord, $widgetKey);

		return TodoyuRenderer::renderAutocompleteResults($results);
	}

}

?>