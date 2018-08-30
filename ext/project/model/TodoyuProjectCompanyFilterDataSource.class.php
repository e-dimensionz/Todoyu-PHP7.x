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
 * Handles the data source for filter widgets
 *
 * @package		Todoyu
 * @subpackage	project
 */
class TodoyuProjectCompanyFilterDataSource {

	/**
	 * Dynamic date options
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getDynamicDateOptions($definitions) {
		$definitions['options'] =  TodoyuSearchFilterHelper::getDynamicDateOptions();

		return $definitions;
	}



	/**
	 * Get filterset options for project
	 *
	 * @param	Array		$definitions
	 * @return	Array
	 */
	public static function getProjectFilterSetSelectionOptions(array $definitions) {
		$allFiltersets	= TodoyuSearchFiltersetManager::getTypeFiltersets('PROJECT', Todoyu::personid(), true);

		$options	= array();

		foreach($allFiltersets as $filterset) {
			$options[] = array(
				'value'		=> $filterset['id'],
				'label'		=> $filterset['title']
			);
		}

		$definitions['options'] = $options;
		return $definitions;
	}

}

?>