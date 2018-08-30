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
 * Portaltab action controller
 *
 * @package		Todoyu
 * @subpackage	Portal
 */
class TodoyuPortalTabActionController extends TodoyuActionController {

	/**
	 * Initialize: restrict access
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('portal', 'general:use');
	}



	/**
	 * Update tab content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		$tabKey	= $params['tab'];
		$extra	= isset($params['params']) ? TodoyuArray::assure(json_decode($params['params'], true)) : array();

		TodoyuPortalPreferences::saveActiveTab($tabKey);

		return TodoyuPortalRenderer::renderTabContent($tabKey, $extra);
	}

}

?>