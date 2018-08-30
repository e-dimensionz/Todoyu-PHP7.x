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
 * Action controller for daytracks history
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */
class TodoyuDaytracksHistoryActionController extends TodoyuActionController {

	/**
	 * Init controller: restrict to rights
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('daytracks', 'general:use');
	}



	/**
	 * Update tracks history popup
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function historyAction(array $params) {
		$year	= intval($params['year']);
		$month	= intval($params['month']);
		$details= intval($params['details']) === 1;

		if( Todoyu::allowed('daytracks', 'daytracks:switchuser')) {
			$idPerson = intval($params['user']);
		} else {
			$idPerson = Todoyu::personid();
		}

		if( $year === 0 ) {
			$dateLastTracking	= TodoyuDaytracksHistoryManager::getDateLastTimetracking();
			$year	= date('Y', $dateLastTracking);
			$month	= date('n', $dateLastTracking);
		}

		return TodoyuDaytracksHistoryRenderer::renderHistory($year, $month, $details, $idPerson);
	}

}

?>