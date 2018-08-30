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
 * Task specific asset functions
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTaskAssetViewHelper {

	/**
	 * Get label text for the asset tab in the task
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getTabLabel($idTask) {
		$idTask	= intval($idTask);

		$numAssets	= TodoyuAssetsAssetManager::getNumTaskAssets($idTask);

		if( $numAssets === 0 ) {
			$label	= Todoyu::Label('assets.ext.tab.noAssets');
		} elseif( $numAssets === 1 ) {
			$label	= '1 ' . Todoyu::Label('assets.ext.tab.asset');
		} else {
			$label	= $numAssets . ' ' . Todoyu::Label('assets.ext.tab.assets');
		}

		return $label;
	}



	/**
	 * Get the content for the asset tab in the task
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getTabContent($idTask) {
		return TodoyuAssetsAssetRenderer::renderTabContent($idTask);
	}



	/**
	 * Get selector options of task asset files
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getTaskAssetsFilesOptions(TodoyuFormElement $field) {
		$formData	= $field->getForm()->getFormData();
		$idTask		= intval($formData['id']);

		return TodoyuAssetsAssetManager::getTaskAssetFileOptions($idTask);
	}

}

?>