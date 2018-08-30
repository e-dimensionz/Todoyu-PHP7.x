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
 * @package		Todyou
 * @subpackage	Comment
 */
class TodoyuCommentAssetManager {

	/**
	 *
	 */
	const TABLE = 'ext_comment_mm_comment_asset';



	/**
	 * Get IDs of assets which are attached to a comment
	 *
	 * @param	Integer		$idComment
	 * @return	Integer[]
	 */
	public static function getAssetIDs($idComment) {
		$idComment	= intval($idComment);

		$field	= '	a.id';
		$tables	= '	ext_comment_mm_comment_asset mm,
					ext_assets_asset a';
		$where	= '		mm.id_comment	= ' . $idComment
				. ' AND mm.id_asset		= a.id'
				. ' AND a.deleted		= 0';
		$order	= '	a.file_name';

		return Todoyu::db()->getColumn($field, $tables, $where, '', $order, '', 'id');
	}



	/**
	 * @static
	 * @param	TodoyuFormElement		$field
	 * @return	Array
	 */
	public static function getCommentAssetOptions(TodoyuFormElement $field){
		$formData	= $field->getForm()->getFormData();

		$idComment	= intval($formData['id']);
		$idTask		= intval($formData['id_task']);
		$idProject	= TodoyuProjectTaskManager::getProjectID($idTask);
		$options	= array();

		$groups = array(
			'project' => array(
				'id'			=> $idProject,
				'optGroupLabel'	=> Todoyu::Label('comment.ext.assets.optgroup.project'),
				'type'			=> ASSET_PARENTTYPE_PROJECT
			),
			'task' => array(
				'id'			=> $idTask,
				'optGroupLabel'	=> Todoyu::Label('comment.ext.assets.optgroup.task'),
				'type'			=> ASSET_PARENTTYPE_TASK
			),
			'comment' => array(
				'id'			=> $idComment,
				'optGroupLabel'	=> Todoyu::Label('comment.ext.assets.optgroup.comment'),
				'type'			=> ASSET_PARENTTYPE_COMMENT
			)
		);

		foreach( $groups as $group) {
			$assets = TodoyuAssetsAssetManager::getElementAssets($group['id'], $group['type']);

			foreach($assets as $key => $asset) {
				$assets[$key]['group'] = $group['optGroupLabel'];
			}

			$options = array_merge($options, TodoyuArray::reform($assets, array('id' => 'value', 'file_name' => 'label', 'group' => 'group')));
		}

		$tempFiles		= new TodoyuCommentTempUploader($idComment, $idTask);
		$fileInfos		= $tempFiles->getFilesInfos();

		foreach($fileInfos as $file) {
			$options[] = array(
				'value'	=> $file['key'],
				'label'	=> self::getTempFileLabel($file),
				'group'	=> $groups['comment']['optGroupLabel']
			);
		}

		return $options;
	}



	/**
	 * Get label for temporary uploaded file
	 *
	 * @param	Array		$fileData
	 * @return	String
	 */
	public static function getTempFileLabel(array $fileData) {
		return $fileData['name'] . ' (' . TodoyuTime::format($fileData['time'], 'timesec') . ', ' . TodoyuString::formatSize($fileData['size']) . ')';
	}



	/**
	 * Get records for record selector
	 *
	 * @param	TodoyuFormElement		$field
	 * @return	Array[]
	 */
	public static function getCommentAssetRecords(TodoyuFormElement $field){
		$formData	= $field->getForm()->getFormData();

		$idComment	= intval($formData['id']);
		$idTask		= intval($formData['id_task']);

		$tempUploader = new TodoyuCommentTempUploader($idComment, $idTask);

		$assetIDs = $field->getValue();
		$records	= array();

		foreach($assetIDs as $idAsset) {
			if( is_numeric($idAsset) ) {
				$info = array(
					'id'	=> $idAsset,
					'label'	=> TodoyuAssetsAssetManager::getAsset($idAsset)->getFilename()
				);
			} else {
				$file = $tempUploader->getFileInfo($idAsset);
				$info = array(
					'id'	=> $idAsset,
					'label'	=> self::getTempFileLabel($file)
				);
			}

			$records[] = $info;
		}

		return $records;
	}



	/**
	 * Save comment assets
	 *
	 * @param	Integer		$idCommentOld
	 * @param	Integer		$idCommentNew
	 * @param	Integer		$idTask
	 * @param	Array		$assets
	 * @todo	Only remove assets which are no longer attached
	 */
	public static function saveAssets($idCommentOld, $idCommentNew, $idTask, array $assets) {
		self::removeAllAssets($idCommentNew);

		foreach($assets as $idAsset) {
				// Create assets from temporary uploaded files
			if( !is_numeric($idAsset) ) {
				$uploader	= new TodoyuCommentTempUploader($idCommentOld, $idTask);
				$fileInfo	= $uploader->getFileInfo($idAsset);
				$idAsset	= self::addNewAssetFromTempFile($idCommentNew, $fileInfo);
			}

			self::addAssetToComment($idCommentNew, $idAsset);
		}
	}



	/**
	 * Link asset with comment
	 *
	 * @param	Integer		$idComment
	 * @param	Integer		$idAsset
	 * @return	Integer
	 */
	public static function addAssetToComment($idComment, $idAsset) {
		$idComment	= intval($idComment);
		$idAsset	= intval($idAsset);

		$data = array(
			'id_asset'	=> $idAsset,
			'id_comment'=> $idComment
		);

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Add a new asset for comment from a temporary uploaded file
	 *
	 * @param	Integer		$idComment
	 * @param	Array		$fileInfo
	 * @return	Integer
	 */
	protected static function addNewAssetFromTempFile($idComment, array $fileInfo) {
		return self::addAsset($idComment, $fileInfo['path'], $fileInfo['name'], $fileInfo['type']);
	}



	/**
	 * Add a new comment asset
	 *
	 * @param	Integer		$idComment
	 * @param	String		$tempFile
	 * @param	String		$fileName
	 * @param	String		$mimeType
	 * @return	Integer
	 */
	public static function addAsset($idComment, $tempFile, $fileName, $mimeType) {
		return TodoyuAssetsAssetManager::addAsset(ASSET_PARENTTYPE_COMMENT, $idComment, $tempFile, $fileName, $mimeType);
	}



	/**
	 * Remove all assets from comment
	 * Only the link is removed, they stay attached to to task
	 *
	 * @param	Integer		$idComment
	 */
	protected static function removeAllAssets($idComment) {
		$idComment	= intval($idComment);
		$where		= 'id_comment = ' . $idComment;

		Todoyu::db()->doDelete(self::TABLE, $where);
	}



	/**
	 * Add asset icon to task if any comment of it has assets
	 *
	 * @param	Array		$icons
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function hookAddTaskIcons(array $icons, $idTask) {
		$idTask	= intval($idTask);

		if( self::commentOfTaskHasAssets($idTask) ) {
			$icons['assets'] = array(
				'id'		=> 'task-' . $idTask . '-assets',
				'class'		=> 'assets',
				'label'		=> 'assets.ext.task.icon',
				'position'	=> 80
			);
		}

		return $icons;
	}



	/**
	 * Check if any comment of given task has an asset attached
	 *
	 * @param	Integer		$idTask
	 */
	protected static function commentOfTaskHasAssets($idTask) {
		$idTask	= intval($idTask);

		$commentIDs = TodoyuCommentCommentManager::getTaskCommentIDs($idTask);

		foreach($commentIDs as $idComment) {
			if( count(TodoyuAssetsAssetManager::getElementAssetIDs($idComment, ASSET_PARENTTYPE_COMMENT)) > 0) {
				return true;
			}
		}

		return false;
	}
}

?>