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
 * Manager class for the quick create
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuQuickCreateManager {

	/**
	 * Add a new create engine and register needed functions
	 *
	 * @param	String		$ext
	 * @param	String		$type
	 * @param	String		$label
	 * @param	Integer		$position
	 * @param	Array		$primaryAreas	Areas where to list this type as primary
	 * @param	Boolean		$areaOnly		show type within resp. area only?
	 */
	public static function addEngine($ext, $type, $label = '', $position = 100, array $primaryAreas = array(), $areaOnly = false) {
		Todoyu::$CONFIG['CREATE']['engines'][] = array(
			'ext'		=> $ext,
			'type'		=> $type,
			'label'		=> $label,
			'position'	=> (int) $position,
			'primary'	=> $primaryAreas,
			'areaOnly'	=> $areaOnly ? true : false
		);
	}



	/**
	 * Get registered creation engines
	 *
	 * @return	Array
	 */
	public static function getEngines() {
			// Load /config/create.php configfiles of all loaded extensions)
		TodoyuExtensions::loadAllCreate();

		$engines= TodoyuArray::sortByLabel(Todoyu::$CONFIG['CREATE']['engines'], 'position');
		$area	= Todoyu::getAreaKey();
		$data	= array(
			'primary'	=> false,
			'all'		=> array()
		);

		foreach($engines as $index => $engine) {
				// If onlyArea flag is set and area is not in primary types, remove
			if( $engine['areaOnly'] && ! in_array($area, $engine['primary']) ) {
				unset($engines[$index]);
				continue;
			}

				// Find primary type
			if( !$data['primary'] && in_array($area, $engine['primary']) ) {
				$data['primary'] = $engine;
				$data['primary']['isPrimary'] = true;
			}
		}

			// Add all not removed types
		$data['all'] = $engines;

		return $data;
	}

}

?>