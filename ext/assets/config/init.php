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

	// Basic paths
Todoyu::$CONFIG['EXT']['assets']['basePath']	= PATH_FILES . DIR_SEP . 'assets';
Todoyu::$CONFIG['EXT']['assets']['cachePath']	= PATH_CACHE . DIR_SEP . 'temp';
Todoyu::$CONFIG['EXT']['assets']['previewPath']	= PATH_CACHE . DIR_SEP . 'previews';

// Delete files on hard disk when deleted in database
Todoyu::$CONFIG['EXT']['assets']['deleteFiles']	= false;

	// Add allowed paths where files can be downloaded from
Todoyu::$CONFIG['sendFile']['allow'][]	= Todoyu::$CONFIG['EXT']['assets']['basePath'];
Todoyu::$CONFIG['sendFile']['allow'][]	= Todoyu::$CONFIG['EXT']['assets']['cachePath'];

	// Set max upload file size
Todoyu::$CONFIG['EXT']['assets']['max_file_size']	= 50000000; // 50MB
Todoyu::$CONFIG['EXT']['assets']['max_length_filename']	= 256;



/* --------------------------------------------
	Add asset content tab and context menu
   -------------------------------------------- */
if( Todoyu::allowed('assets', 'general:use') ) {
		// Add assets tab into task
	TodoyuContentItemTabManager::registerTab('project', 'task', 'assets', 'TodoyuAssetsTaskAssetViewHelper::getTabLabel', 'TodoyuAssetsTaskAssetViewHelper::getTabContent', 30);
		// Add "Add New > Asset" to task context menu
	TodoyuContextMenuManager::addFunction('Task', 'TodoyuAssetsAssetManager::getTaskContextMenuItems', 150);
}


	// Asset selector
TodoyuFormRecordsManager::addType('asset', 'TodoyuAssetsFormElement_RecordsAsset', 'TodoyuAssetsAssetManager::getMatchingAssets');

	// Task asset selector
TodoyuFormRecordsManager::addType('taskAsset', 'TodoyuAssetsFormElement_RecordsTaskAsset', 'TodoyuAssetsAssetManager::getMatchingAssets');

	// Project asset selector
TodoyuFormRecordsManager::addType('projectAsset', 'TodoyuAssetsFormElement_RecordsProjectAsset', 'TodoyuAssetsAssetManager::getMatchingAssets');

	// General asset selector
TodoyuFormManager::addFieldType('recordsSelectAsset', 'TodoyuAssetsFormElement_RecordSelectAsset', 'ext/assets/view/form/FormElement_SelectAsset.tmpl');

/* ---------------------------------------------
	Add quickInfo callback for assets
   --------------------------------------------- */
TodoyuQuickinfoManager::addFunction('asset', 'TodoyuAssetsAssetQuickinfoManager::addAssetInfos');

?>