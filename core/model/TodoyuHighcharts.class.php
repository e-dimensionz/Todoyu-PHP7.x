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
 * Todoyu Highcharts
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuHighcharts {

	/**
	 * Add required javascript for highcharts
	 *
	 * @todo	Remove jQuery if everything works fine with prototype adapter
	 */
	public static function addHighcharts() {
//		TodoyuPage::addJavascript('lib/js/highcharts/adapters/prototype-adapter.src.js', 30, false, false, false);
		TodoyuPage::addJavascript('lib/js/highcharts/adapters/prototype-adapter.js', 30, false, false, false);
//		TodoyuPage::addJavascript('lib/js/highcharts/highcharts.src.js', 31, false, false, false);
		TodoyuPage::addJavascript('lib/js/highcharts/highcharts.js', 31, false, false, false);
	}

}

?>