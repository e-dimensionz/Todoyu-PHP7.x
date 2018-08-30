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
 * Workload history renderer
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */
class TodoyuDaytracksHistoryRenderer {

	/**
	 * Render the history in the popUp
	 *
	 * @param	Integer		$year
	 * @param	Integer		$month
	 * @param	Boolean		$details
	 * @return	String
	 */
	public static function renderHistory($year = 0, $month = 0, $details = false, $idPerson = 0) {
		$year		= intval($year);
		$month		= intval($month);
		$idPerson	= Todoyu::personid($idPerson);

			// Use current date if none set
		$year	= $year === 0 ? date('Y') : $year;
		$month	= $month === 0 ? date('n') : $month;

		$timestamp	= mktime(0, 0, 0, $month, 1, $year);

		$tracks	= TodoyuDaytracksHistoryManager::getRangeTracks($year, $month, $details, $idPerson);

		$tmpl	= 'ext/daytracks/view/history.tmpl';
		$data	= array(
			'id'				=> 'daytracks-history',
			'currentYear'		=> $year,
			'currentMonth'		=> $month,
			'labelCurrentPeriod'=> TodoyuTime::format($timestamp, 'MlongY4'),
			'showDetails'		=> $details,
			'tracking'			=> $tracks,
			'ranges'			=> TodoyuDaytracksHistoryManager::getMonthSelectorOptions(),
			'users'				=> TodoyuDaytracksHistoryManager::getUserOptions(),
			'currentUser'		=> $idPerson
		);

		TodoyuHookManager::callHookDataModifier('daytracks', 'tracksHistory', $data);

		return Todoyu::render($tmpl, $data);
	}

}
?>