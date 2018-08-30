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
 * Manage right quickinfo
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerRightQuickInfoManager {

	/**
	 * Add items to right quickinfo
	 *
	 * @param	TodoyuQuickinfo		$quickinfo
	 * @param	Integer				$idElement
	 */
	public static function addRightInfos(TodoyuQuickinfo $quickinfo, $idElement) {
		list($prefix, $extKey, $sectionName, $right) = explode('-', $idElement);

			// Right label
		$labelSection	= Todoyu::Label($extKey . '.rights.' . $sectionName);
		$labelRight		= Todoyu::Label($extKey . '.rights.' . $sectionName . '.' . $right);
		$quickinfo->addInfo('right', $labelSection . ': ' . $labelRight, 10, false);

		$quickinfo->addInfo('dependent', Todoyu::Label('sysmanager.ext.quickinfo.right.depends'), 10, false);

			// Add info for required rights of right
		$rightRequirement	= TodoyuRightsManager::getRightRequirement($extKey, $sectionName, $right);
		if( $rightRequirement !== false ) {
			list($sectionName, $right)	= explode(':', $rightRequirement);
			$labelRequiredSection	= Todoyu::Label($extKey . '.rights.' . $sectionName);
			$labelRequired			= Todoyu::Label($extKey . '.rights.' . $sectionName . '.' . $right);
			$quickinfo->addInfo('requiredright', $labelRequiredSection . ': ' . $labelRequired, 10, false);
		} else {
				// Add info for required rights of section of right
			$sectionRequirement	= TodoyuRightsManager::getSectionRequirement($extKey, $sectionName);
			if( $sectionRequirement !== false ) {
				list($sectionName, $right)	= explode(':', $sectionRequirement);
				$labelRequiredSection	= Todoyu::Label($extKey . '.rights.' . $sectionName);
				$labelRequired			= Todoyu::Label($extKey . '.rights.' . $sectionName . '.' . $right);
				$quickinfo->addInfo('requiredsection', $labelRequiredSection . ': ' . $labelRequired, 10, false);
			}
		}

	}



	/**
	 * Add JS onload function to page (hooked into TodoyuPage::render())
	 */
	public static function addJSonloadFunction() {
		TodoyuPage::addJsInit('Todoyu.Ext.sysmanager.QuickinfoRight.init()');
	}

}

?>