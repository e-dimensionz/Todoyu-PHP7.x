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
 * Manager for asset files
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsAssetManager {

	/**
	 * @var String		Default table for database requests
	 */
	const TABLE = 'ext_assets_asset';



	/**
	 * Session path for temporary storage path
	 *
	 * @var	String
	 */
	private static $sessionTempPath = 'assets/temppath';



	/**
	 * Get asset record
	 *
	 * @param	Integer		$idAsset
	 * @return	TodoyuAssetsAsset
	 */
	public static function getAsset($idAsset) {
		$idAsset	= intval($idAsset);

		return TodoyuRecordManager::getRecord('TodoyuAssetsAsset', $idAsset);
	}



	/**
	 * Get asset record array
	 *
	 * @param	Integer		$idAsset
	 * @return	Array
	 */
	public static function getAssetArray($idAsset) {
		$idAsset	= intval($idAsset);

		return Todoyu::db()->getRecord(self::TABLE, $idAsset);
	}



	/**
	 * Get a task asset
	 *
	 * @param	Integer				$idAsset
	 * @return	TodoyuAssetsTaskAsset
	 */
	public static function getTaskAsset($idAsset) {
		$idAsset	= intval($idAsset);

		return TodoyuRecordManager::getRecord('TodoyuAssetsTaskAsset', $idAsset);
	}



	/**
	 * Get the number of assets in a task
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function getNumTaskAssets($idTask) {
		$idTask	= intval($idTask);
		$assets	= self::getTaskAssets($idTask);

		$amount = 0;

		foreach($assets as $typeAssets) {
			$amount += sizeof($typeAssets);
		}

		return $amount;
	}



	/**
	 * Get IDs of assets of given parent element
	 *
	 * @param	Integer		$idParent		ID of parent element
	 * @param	Integer		$type			type of parent element, e.g. task
	 * @return	Array
	 */
	public static function getElementAssetIDs($idParent, $type = ASSET_PARENTTYPE_TASK) {
		$idParent	= intval($idParent);
		$type		= intval($type);

		$assets		= self::getElementAssets($idParent, $type);

		return TodoyuArray::getColumn($assets, 'id');
	}



	/**
	 * Get assets of given parent element
	 *
	 * @param	Integer		$idParent		ID of parent element
	 * @param	Integer		$type			type of parent element, e.g. task
	 * @return	Array
	 */
	public static function getElementAssets($idParent, $type = ASSET_PARENTTYPE_TASK) {
		$idParent	= intval($idParent);
		$type		= intval($type);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		id_parent	= ' . $idParent .
				  ' AND	parenttype	= ' . $type .
				  ' AND	deleted		= 0';
		$order	= 'date_create DESC';

			// If person can't see all assets, limit to public and own
		if( ! Todoyu::allowed('assets', 'asset:seeAll') ) {
			$where .= ' AND (is_public		= 1
							 OR id_person_create	= ' . Todoyu::personid() . ')';
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get the IDs of all assets of given task
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskAssetIDs($idTask) {
		$idTask	= intval($idTask);

		return self::getElementAssetIDs($idTask, ASSET_PARENTTYPE_TASK);
	}



	/**
	 * @param	Integer		$idProject
	 * @return	Array
	 */
	public static function getProjectAssets($idProject) {
		$idProject	= intval($idProject);

		$projectAssets['project'] = self::getElementAssets($idProject, ASSET_PARENTTYPE_PROJECT);

		$taskIDs = TodoyuProjectProjectManager::getTaskIDs($idProject);

		foreach($taskIDs as $idTask) {
			$projectAssets = array_merge_recursive($projectAssets, self::getTaskAssets($idTask));
		}

		return $projectAssets;
	}



	/**
	 * Get the assets of a task
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskAssets($idTask) {
		$idTask	= intval($idTask);

		$taskAssets['task']	= self::getElementAssets($idTask, ASSET_PARENTTYPE_TASK);

		$commentIDs	= TodoyuCommentCommentManager::getTaskCommentIDs($idTask);

		$taskAssets['comment'] = array();
		foreach($commentIDs as $idCommment) {
			$taskAssets['comment'] = array_merge($taskAssets['comment'], self::getElementAssets($idCommment, ASSET_PARENTTYPE_COMMENT));
		}

		return $taskAssets;
	}



	/**
	 * Get task ID of an asset
	 *
	 * @param	Integer		$idAsset
	 * @return	Integer
	 */
	public static function getTaskID($idAsset) {
		$idAsset	= intval($idAsset);

		$asset		= self::getAssetArray($idAsset);

		return intval($asset['id_parent']);
	}



	/**
	 * Add an uploaded file as task asset
	 *
	 * @param	Integer		$idTask			Task ID
	 * @param	String		$tempFile		Path to temporary file on server
	 * @param	String		$fileName		Filename on browser system
	 * @param	String		$mimeType		Submitted file type by browser
	 * @return	Integer		Asset ID
	 */
	public static function addTaskAsset($idTask, $tempFile, $fileName, $mimeType) {
		$idTask	= intval($idTask);

		return self::addAsset(ASSET_PARENTTYPE_TASK, $idTask, $tempFile, $fileName, $mimeType);
	}



	/**
	 * Add an uploaded file as project asset
	 *
	 * @param	Integer		$idProject		Project ID
	 * @param	String		$tempFile		Path to temporary file on server
	 * @param	String		$fileName		Filename on browser system
	 * @param	String		$mimeType		Submitted file type by browser
	 * @return	Integer		Asset ID
	 */
	public static function addProjectAsset($idProject, $tempFile, $fileName, $mimeType) {
		$idProject	= intval($idProject);

		return self::addAsset(ASSET_PARENTTYPE_PROJECT, $idProject, $tempFile, $fileName, $mimeType);
	}



	/**
	 * Add an uploaded file as comment asset
	 *
	 * @param	Integer			$idComment		Comment ID
	 * @param	String			$tempFile		Path to temporary file on server
	 * @param	String			$fileName		Filename on browser system
	 * @param	String			$mimeType		Submitted file type by browser
	 * @return	Integer|Boolean		Asset ID
	 */
	public static function addCommentAsset($idComment, $tempFile, $fileName, $mimeType) {
		$idComment	= intval($idComment);

		return self::addAsset(ASSET_PARENTTYPE_COMMENT, $idComment, $tempFile, $fileName, $mimeType);
	}



	/**
	 * Add a new file to the system.
	 *  - Copy the file to the file structure
	 *  - Add an asset record to the database
	 *
	 * @param	Integer			$type
	 * @param	Integer			$idParent
	 * @param	String			$tempFile		Absolute path to the temporary file
	 * @param	String			$fileName		Original file name
	 * @param	String			$mimeType		File mime type
	 * @return	Integer|Boolean		Asset ID or FALSE
	 */
	public static function addAsset($type, $idParent, $tempFile, $fileName, $mimeType) {
		$type		= intval($type);
		$idParent	= intval($idParent);
		$basePath	= self::getStorageBasePath();

			// Move temporary file to asset storage
		$storageDir	= self::getAssetStoragePath($type, $idParent);
		$filePath	= TodoyuFileManager::addFileToStorage($storageDir, $tempFile, $fileName);

		if( !$filePath ) {
			return false;
		}

			// Get storage path (relative to basePath)
		$relStoragePath	= str_replace($basePath . DIR_SEP, '', $filePath);

			// Get file size and file info
		$fileSize	= filesize($filePath);
		$info		= pathinfo($filePath);

			// Get mime type
		$types		= explode('/', $mimeType);
		$fileMime	= $types[0];
		$fileMimeSub= $types[1];

			// Add record to database
		$data		= array(
			'parenttype'			=> $type,
			'id_parent'				=> $idParent,
			'deleted'				=> 0,
			'is_public'				=> 0,
			'file_ext'				=> $info['extension'],
			'file_mime'				=> $fileMime,
			'file_mime_sub'			=> $fileMimeSub,
			'file_storage'			=> $relStoragePath,
			'file_name'				=> $fileName,
			'file_size'				=> $fileSize
		);

		$idAsset = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('assets', 'asset.add', array($idAsset));

		return $idAsset;
	}



	/**
	 * Download an asset. Send headers and data to the browser
	 *
	 * @param	Integer		$idAsset
	 * @return	Boolean
	 */
	public static function downloadAsset($idAsset) {
		$idAsset	= intval($idAsset);
		$asset		= self::getAsset($idAsset);

		return $asset->sendAsDownload();
	}



	/**
	 * Delete an asset (file stays in file system)
	 *
	 * @param	Integer		$idAsset
	 */
	public static function deleteAsset($idAsset) {
		$idAsset	= intval($idAsset);
		$update		= array(
			'deleted'		=> 1
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idAsset, $update);

			// Delete file on hard disk?
		if( Todoyu::$CONFIG['EXT']['assets']['deleteFiles'] ) {
			$asset		= self::getAsset($idAsset);
			$filePath	= $asset->getFileStoragePath();

			TodoyuFileManager::deleteFile($filePath);
		}

		TodoyuHookManager::callHook('assets', 'asset.delete', array($idAsset));
	}

	

	/**
	 * Toggle asset public flag
	 *
	 * @param	Integer		$idAsset
	 */
	public static function togglePublic($idAsset) {
		$idAsset	= intval($idAsset);

		Todoyu::db()->doBooleanInvert(self::TABLE, $idAsset, 'is_public');
	}



	/**
	 * Download assets zipped
	 *
	 * @param	Integer 	$idRecord
	 * @param	String 		$recordType
	 * @param	Array		$assetIDs
	 */
	public static function downloadAssetsZipped($idRecord, $recordType, array $assetIDs) {
		$idRecord		= intval($idRecord);
		$assetIDs		= TodoyuArray::intval($assetIDs);

		$pathZipFile= self::createAssetZip($idRecord, $assetIDs);

		if( ! is_file($pathZipFile) ) {
			die("Download of ZIP file failed");
		}

		$filename	= 'Assets_' . $idRecord . '.zip';
		$mimeType	= 'application/octet-stream';

		try {
			TodoyuHookManager::callHook('assets', 'asset.download.zip', array($idRecord, $assetIDs, $pathZipFile, $filename));
			TodoyuFileManager::sendFile($pathZipFile, $mimeType, $filename);
			unlink($pathZipFile);
		} catch(TodoyuExceptionFileDownload $e) {
			// @todo catch error
		}
	}



	/**
	 * Create ZIP file from assets
	 *
	 * @param	Integer		$idRecord
	 * @param	Array		$assetIDs
	 * @return	String		path to ZIP file
	 */
	private static function createAssetZip($idRecord, array $assetIDs) {
		$idRecord		= intval($idRecord);
		$assetIDs	= TodoyuArray::intval($assetIDs, true, true);

		TodoyuFileManager::makeDirDeep(Todoyu::$CONFIG['EXT']['assets']['cachePath']);

			// Build file path and name
		$zipName	= self::makeZipFileName($idRecord, $assetIDs);
		$zipPath	= TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['assets']['cachePath'] . DIR_SEP . $zipName);

			// Create ZIP file
		$zip	= new ZipArchive();
		$status	= $zip->open($zipPath, ZIPARCHIVE::CREATE);

		if( $status !== true ) {
			TodoyuLogger::logError('Can\'t create zip archive: ' . $zipPath);
		}

			// Get asset data
		$fields	= 'file_name, file_storage';
		$table	= self::TABLE;

		if( count($assetIDs) > 0 ) {
			$where	= 'id IN(' . implode(',', $assetIDs) . ')';
		} else {
			$where = 'id_parent = ' . $idRecord . ' AND deleted = 0';
		}

			// Get selected asset records
		$assets			= Todoyu::db()->getArray($fields, $table, $where);
			// Counter for identical file names
		$fileNameCounter= array();

			// Add assets
		foreach($assets as $asset) {
				// Handle duplicated file names
			$inZipName	= $asset['file_name'];
				// If filename is already in archive, post-file with a counter
			if( array_key_exists($inZipName, $fileNameCounter) ) {
				$index		= intval($fileNameCounter[$asset['file_name']]);
				$inZipName	= TodoyuFileManager::appendToFilename($inZipName, '_' . $index);
			}
				// Get path to file on server
			$storageFilePath= self::getStoragePath($asset['file_storage']);

				// Add file
			$success = $zip->addFile($storageFilePath, $inZipName);

				// Log error if adding failed
			if( $success !== true ) {
				$data	= array(
					'dir'		=> Todoyu::$CONFIG['EXT']['assets']['asset_dir'],
					'storage'	=> $asset['file_storage'],
					'file'		=> $asset['file_name']
				);
				TodoyuLogger::logError('Failed to add asset to zip file', $data);
			}

				// Count filename (check for duplicates)
			$fileNameCounter[$asset['file_name']]++;
		}

		$zip->close();

		return $zipPath;
	}



	/**
	 * Get path to file in storage
	 *
	 * @param	String		$storageFileName		Relative path from asset storage
	 * @return	String		Absolute path to file in asset storage
	 */
	public static function getStoragePath($storageFileName) {
		return Todoyu::$CONFIG['EXT']['assets']['basePath'] . DIR_SEP . $storageFileName;
	}



	/**
	 * Generate filename for ZIP file
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$assetIDs
	 * @return	String		filename of ZIP
	 */
	private static function makeZipFileName($idTask, array $assetIDs) {
		$idTask		= intval($idTask);
		$assetIDs	= TodoyuArray::intval($assetIDs, true, true);

		$field	= 'date_create';
		$table	= self::TABLE;
		if( count($assetIDs) > 0 ) {
			$where	= 'id IN(' . implode(',', $assetIDs) . ')';
		} else {
			$where = 'id_task = '.intval($idTask).' AND deleted = 0';
		}

		$dates	= Todoyu::db()->getColumn($field, $table, $where);
		$sum	= array_sum($dates);

		return 'Assets_' . $idTask . '_' . $sum . '.zip';
	}



	/**
	 * Get storage base path (absolute path)
	 *
	 * @return	String
	 */
	public static function getStorageBasePath() {
		return TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['assets']['basePath']);
	}



	/**
	 * Get storage path of assets of given task
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function getTaskAssetStoragePath($idTask) {
		$idTask		= intval($idTask);

		return self::getAssetStoragePath(ASSET_PARENTTYPE_TASK, $idTask);
	}



	/**
	 * Get (root) storage path of assets
	 *
	 * @param	Integer		$type			type of parent element, e.g. task
	 * @param	Integer		$idParent		ID of parent element
	 * @return	String		Path to storage folder
	 */
	public static function getAssetStoragePath($type, $idParent) {
		$type		= intval($type);
		$idParent	= intval($idParent);
		$basePath	= self::getStorageBasePath();
		$folder 	= self::getFolderNameByParentType($type, $idParent);

		$storagePath = TodoyuFileManager::pathAbsolute($basePath . DIR_SEP . ($folder ? $folder . DIR_SEP : '') . $idParent);

			// Create storage folder if it doesn't exist
		TodoyuFileManager::makeDirDeep($storagePath);

		return $storagePath;
	}



	/**
	 * @param	Integer		$type
	 * @param	Integer		$idParent
	 * @return	Integer|String
	 */
	public static function getFolderNameByParentType($type, $idParent) {
		switch($type) {
			case ASSET_PARENTTYPE_PROJECT:
				$folder		= '';
				break;

			case ASSET_PARENTTYPE_TASK:
					// Use project ID as parent folder
				$folder		= TodoyuProjectTaskManager::getProjectID($idParent);
				break;

			case ASSET_PARENTTYPE_COMMENT:
				$idTask		= TodoyuCommentCommentManager::getTaskID($idParent);
				$idProject	= TodoyuProjectTaskManager::getProjectID($idTask);
				$folder		= $idProject . DIR_SEP . $idTask;
				break;

			default:
				die('INVALID ASSET TYPE');
		}

		return $folder;
	}



	/**
	 * Check whether a task has assets
	 * @param	Integer		$idTask
	 * @return	Boolean
	 */
	public static function taskHasAssets($idTask) {
		$idTask		= intval($idTask);
		$assetIDs	= self::getTaskAssetIDs($idTask);

		return sizeof($assetIDs) > 0;
	}



	/**
	 * Add asset icon to task if it has assets
	 *
	 * @param	Array		$icons
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function hookAddTaskIcons(array $icons, $idTask) {
		$idTask	= intval($idTask);

		if( self::taskHasAssets($idTask) ) {
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
	 * Modify form for task creation - add assets fieldset
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idTask
	 * @param	Array			$params
	 * @return	TodoyuForm
	 */
	public static function hookAddAssetUploadToTaskCreateForm(TodoyuForm $form, $idTask, array $params) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);

		if( !$task->isContainer() ) {
				// Add assets fieldset
			$xmlPathSave	= 'ext/assets/config/form/task-inline-upload.xml';
			$assetForm		= TodoyuFormManager::getForm($xmlPathSave);
			$assetFieldset	= $assetForm->getFieldset('assets');

			$form->addFieldset('assets', $assetFieldset, 'before:buttons');
		}

		return $form;
	}



	/**
	 * Modify form for project creation - add assets fieldset
	 *
	 * @param	TodoyuForm		$form
	 * @param	Integer			$idProject
	 * @param	Array			$params
	 * @return	TodoyuForm
	 */
	public static function hookAddAssetUploadToProjectCreateForm(TodoyuForm $form, $idProject, array $params) {
			// Add assets fieldset
		$xmlPathSave	= 'ext/assets/config/form/project-inline-upload.xml';
		$assetForm		= TodoyuFormManager::getForm($xmlPathSave);
		$assetFieldset	= $assetForm->getFieldset('assets');

		$form->addFieldset('assets', $assetFieldset, 'before:buttons');

		return $form;
	}



	/**
	 * Save assets (uploaded inline from within task creation form) of new task
	 *
	 * @param	Array		$data
	 * @param	Integer		$idTask
	 * @param	Array		$params
	 * @return	Array
	 */
	public static function hookStoreUplodedTaskAssets(array $data, $idTask, array $params) {
		$idTaskOld	= intval($data['id']);
		$uploader	= new TodoyuAssetsTempUploaderTask($idTaskOld);
		$fileInfos	= $uploader->getFilesInfos();

			// Remove asset fields from form data
		unset($data['MAX_FILE_SIZE']);
		unset($data['assetlist']);
		unset($data['file']);

		foreach($fileInfos as $asset) {
			self::addTaskAsset($idTask, $asset['path'], $asset['name'], $asset['type']);
		}

		$uploader->clear();

		return $data;
	}



	/**
	 * @param	Array		$data
	 * @param	Integer		$idProject
	 * @param	Array		$params
	 * @return	Array
	 */
	public static function hookStoreUplodedProjectAssets(array $data, $idProject, array $params) {
		$idProjectOld	= intval($data['id']);
		$uploader	= new TodoyuAssetsTempUploaderProject($idProjectOld);
		$fileInfos	= $uploader->getFilesInfos();

			// Remove asset fields from form data
		unset($data['MAX_FILE_SIZE']);
		unset($data['assetlist']);
		unset($data['file']);

		foreach($fileInfos as $asset) {
			self::addProjectAsset($idProject, $asset['path'], $asset['name'], $asset['type']);
		}

		$uploader->clear();

		return $data;
	}



	/**
	 * Get asset file select options (temporary uploaded assets to be attached to to-be created task)
	 *
	 * @param	Integer		$idTask
	 * @return	Array
	 */
	public static function getTaskAssetFileOptions($idTask = 0) {
		$options	= array();
		$uploader	= new TodoyuAssetsTempUploaderTask($idTask);
		$fileInfos	= $uploader->getFilesInfos();
		$fileInfos	= TodoyuArray::sortByLabel($fileInfos, 'time', true);

		foreach($fileInfos as $file) {
			$options[] = array(
				'value'	=> $file['key'],
				'label'	=> $file['name'] . ' (' . TodoyuTime::format($file['time'], 'timesec') . ')'
			);
		}

		return $options;
	}



	/**
	 * Save temp path in session
	 *
	 * @param	String		$path
	 */
	public static function saveSessionTempPath($path) {
		TodoyuSession::set(self::$sessionTempPath, $path);
	}



	/**
	 * Get temp path from session
	 *
	 * @return	String
	 */
	public static function getSessionTempPath() {
		return TodoyuSession::get(self::$sessionTempPath);
	}



	/**
	 * Check whether session has a temp path
	 *
	 * @return	Boolean
	 */
	public static function hasSessionTempPath() {
		return TodoyuSession::isIn(self::$sessionTempPath);
	}



	/**
	 * Get items for the task context menu
	 *
	 * @param	Integer		$idTask
	 * @param	Array		$items
	 * @return	Array
	 */
	public static function getTaskContextMenuItems($idTask, array $items) {
		$idTask	= intval($idTask);
		$task	= TodoyuProjectTaskManager::getTask($idTask);
		$allowed= array();

		$allowUse	= Todoyu::allowed('assets', 'general:use');
		$isLocked	= $task->isLocked();
		$isTask		= $task->isTask();

		if( $allowUse && !$isLocked && $isTask ) {
			$ownItems	=& Todoyu::$CONFIG['EXT']['asset']['ContextMenu']['Task'];

			if( isset($items['add']) ) {
				$allowed['add']['submenu']['add-asset'] = $ownItems['add']['submenu']['add-asset'];
			}
		}

		return array_merge_recursive($items, $allowed);
	}



	/**
	 * Get asset label
	 *
	 * @param	Integer		$idAsset
	 * @return	String
	 */
	public static function getLabel($idAsset) {
		return self::getAsset($idAsset)->getLabel();
	}



	/**
	 * Get matching assets
	 *
	 * @param	Array		$searchWords
	 * @param	Array		$ignoreIDs
	 * @param	Array		$params
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getMatchingAssets(array $searchWords, array $ignoreIDs = array(), array $params = array(), $type = null) {
		$idTask		= intval($params['task']);
		$idProject	= intval($params['project']);
		$assetIDs	= self::searchAssets($searchWords, 30, 0, $ignoreIDs, $idTask, $idProject);
		$assetItems	= array();

		foreach($assetIDs as $idAsset) {
			$asset	= self::getAsset($idAsset);

			$assetItems[] = array(
				'id'	=> $idAsset,
				'label'	=> $asset->getLabel()
			);
		}

		return $assetItems;
	}



	/**
	 * Search assets and get matching IDS
	 *
	 * @param	String[]		$searchWords			Search keywords
	 * @param	Integer			$size					Maximum amount of results
	 * @param	Integer			$offset					Search offset
	 * @param	Integer[]		$ignoreIDs				Ignore assets with these IDs
	 * @param	Integer			$idTask					Limit search to assets of this task
	 * @param	Integer			$idProject				Limit search task assets of tasks in this project
	 * @return	Integer[]		Asset IDs
	 */
	public static function searchAssets(array $searchWords, $size = 100, $offset = 0, array $ignoreIDs = array(), $idTask = 0, $idProject = 0) {
		$ignoreIDs	= TodoyuArray::intval($ignoreIDs, true, true);
		$idTask		= intval($idTask);
		$idProject	= intval($idProject);

		$field	= '	a.id';
		$table	= self::TABLE . ' a';
		$where	= ' a.deleted = 0';
		$order	= '	a.file_name';
		$limit	= ($size != '') ? intval($offset) . ',' . intval($size) : '';

		if( sizeof($searchWords) > 0 ) {
			$searchFields	= array('a.file_ext', 'a.file_name');
			$where			.= ' AND ' . TodoyuSql::buildLikeQueryPart($searchWords, $searchFields);
		}

			// Add ignore IDs
		if( sizeof($ignoreIDs) > 0 ) {
			$where .= ' AND ' . TodoyuSql::buildInListQueryPart($ignoreIDs, 'a.id', true, true);
		}

		if( !Todoyu::allowed('assets', 'asset:seeAll') ) {
			$where .= ' AND a.is_public = 1';
		}

		// @todo Rights check for parent task/project

		if( $idTask !== 0 ) {
			$where .= ' AND a.parenttype = 1 AND a.id_parent = ' . $idTask;
		}

		if( $idProject !== 0 ) {
			$table .= ', ext_project_task t';
			$where .= ' AND a.id_parent	= t.id'
					. ' AND t.id_project= ' . $idProject;
		}

		return Todoyu::db()->getColumn($field, $table, $where, '', $order, $limit, 'id');
	}
}

?>