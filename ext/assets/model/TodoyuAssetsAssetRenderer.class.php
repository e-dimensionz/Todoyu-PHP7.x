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
 * Renderer for assets
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsAssetRenderer {

	/**
	 * Render content for the task tab
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTabContent($idTask) {
		Todoyu::restrict('assets', 'general:use');

		$idTask		= intval($idTask);
		$numAssets	= TodoyuAssetsAssetManager::getNumTaskAssets($idTask);
		$hasAssets	= $numAssets > 0;
		$locked		= TodoyuProjectTaskManager::isLocked($idTask);

		if( $locked ) {
			if( !$hasAssets ) {
				$content = self::renderLockedMessage();
			} else {
				$content = self::renderTaskList($idTask);
			}
		} else {
			$content = self::renderListControl($idTask, 'task');

			if( $hasAssets ) {
				$content .= self::renderTaskList($idTask);
			}
		}

		return $content;
	}



	/**
	 * Render locked message
	 *
	 * @return	String
	 */
	public static function renderLockedMessage() {
		$tmpl	= 'ext/comment/view/locked.tmpl';

		return Todoyu::render($tmpl);
	}



	/**
	 * Render asset list view
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderTaskList($idTask) {
		$idTask	= intval($idTask);
		return self::renderList($idTask, TodoyuAssetsAssetManager::getTaskAssets($idTask), 'task');
	}



	/**
	 * @param $idProject
	 * @return String
	 */
	public static function renderProjectList($idProject) {
		$idProject	= intval($idProject);
		return self::renderList($idProject, TodoyuAssetsAssetManager::getProjectAssets($idProject), 'project');
	}



	/**
	 * @param	Integer		$idRecord
	 * @param	Array		$assets
	 * @param	String		$recordType
	 * @return	String
	 */
	protected static function renderList($idRecord, $assets, $recordType) {
		$tmpl	= 'ext/assets/view/list.tmpl';

		$data	= array(
			'idRecord'		=> $idRecord,
			'assets'		=> $assets,
			'recordType'	=> $recordType
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render list control elements
	 *
	 * @param	Integer		$idRecord
	 * @param	String		$recordType
	 * @return	String
	 */
	public static function renderListControl($idRecord, $recordType) {
		$idRecord	= intval($idRecord);

		$tmpl	= 'ext/assets/view/list-control.tmpl';
		$data	= array(
			'idRecord'		=> $idRecord,
			'recordType'	=> $recordType
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render upload-frame content
	 *
	 * @param	Integer		$idRecord
	 * @param	String		$recordType
	 * @param	String		$fileName
	 * @return	String
	 */
	public static function renderUploadframeContent($idRecord, $recordType, $fileName) {
		$idRecord	= intval($idRecord);
		$tabLabel	= TodoyuAssetsTaskAssetViewHelper::getTabLabel($idRecord);

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.assets.Upload.uploadFinished(' . $idRecord . ', \''. $recordType . '\', \'' . $tabLabel . '\');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render upload-frame content if upload has failed
	 *
	 * @param	Integer		$idTask
	 * @param	Integer		$error
	 * @param	String		$fileName
	 * @return	String
	 */
	public static function renderUploadframeContentFailed($idTask, $error, $fileName) {
		$error		= intval($error);
		$maxFileSize= TodoyuString::formatSize(intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']));

		$commands	= 'window.parent.Todoyu.Ext.assets.Upload.uploadFailed(' . $idTask . ', ' . $error . ', \'' . $fileName . '\', \'' . $maxFileSize . '\');';

		return TodoyuRenderer::renderUploadIFrameJsContent($commands);
	}



	/**
	 * @param	Integer	$idAsset
	 * @return	String
	 */
	public static function renderPreview($idAsset) {
		$idAsset			= intval($idAsset);
		$previewAttributes	= TodoyuAssetsPreviewManager::getPreviewImage($idAsset);

		$path = file_exists($previewAttributes['path']) ? $previewAttributes['path'] : false;

		$tmpl	= 'ext/assets/view/preview.tmpl';
		$data	= array(
			'idAsset'	=>	$idAsset,
			'path'		=>	$path,
			'width'		=>	$previewAttributes['width'],
			'height'	=>	$previewAttributes['height'],
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>