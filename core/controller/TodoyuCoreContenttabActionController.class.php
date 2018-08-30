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
 * Controller to load tabs in content area generic
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCoreContenttabActionController extends TodoyuActionController {

	/**
	 * Load tab
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function tabloadAction(array $params) {
		$idItem		= intval($params['idItem']);
		$tab		= $params['tabKey'];
		$itemKey	= $params['itemKey'];
		$extKey		= $params['extKey'];

		TodoyuContentItemTabPreferences::saveActiveTab($extKey, $itemKey, $idItem, $tab);

		return TodoyuContentItemTabRenderer::renderTabContent($extKey, $itemKey, $idItem, $tab);
	}



	/**
	 * Select tab
	 *
	 * @param	Array		$params
	 */
	public function tabselectedAction(array $params) {
		$idItem		= intval($params['idItem']);
		$tab		= $params['tabKey'];
		$itemKey	= $params['itemKey'];
		$extKey		= $params['extKey'];

		TodoyuContentItemTabPreferences::saveActiveTab($extKey, $itemKey, $idItem, $tab);
	}
}

?>