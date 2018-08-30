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
 * Daytracks specific Dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 */



/**
 * Extract sum of hours of calendar week from given daytracks data
 *
 * @param	Dwoo			$dwoo
 * @param	Array			$tracks
 * @param	Integer			$timestamp		Date within the calendar week
 * @return	String
 */
function Dwoo_Plugin_sumTrackedCW(Dwoo $dwoo, $tracks, $timestamp) {
	$sum	= 0;
	$cw		= date('W', $timestamp);
	foreach($tracks as $timestamp => $daytracks) {
		if( date('W', $timestamp) === $cw ) {
			foreach($daytracks as $tracks) {
				if( is_array($tracks) > 0 ) foreach($tracks as $track) {
					$sum	+= $track['workload_tracked'];
				}
			}
		}
	}

	return TodoyuTime::formatHours($sum);
}



/**
 * Remove enclosing wrap from given string
 *
 * @param	Dwoo		$dwoo
 * @param	String		$str
 * @param	String		$wrap
 * @return	String
 */
function Dwoo_Plugin_unwrap(Dwoo $dwoo, $str, $wrap = '"') {
	if(TodoyuString::startsWith($str, $wrap) && TodoyuString::endsWith($str, $wrap)) {
		$wrapLen	= strlen($wrap);
		$str = substr($str, $wrapLen, strlen($str) - $wrapLen * 2);
	}

	return $str;
}

?>