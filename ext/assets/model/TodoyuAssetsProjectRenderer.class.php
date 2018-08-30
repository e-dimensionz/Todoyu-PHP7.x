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
 *
 */
class TodoyuAssetsProjectRenderer {

	/**
	 * @param	Integer		$idProject
	 * @return	String
	 */
	public static function renderProjectDetailsTab($idProject){
		$idProject	= intval($idProject);
		$tmpl		= 'ext/assets/view/project-header.tmpl';

		$data = array(
			'listControl'	=> TodoyuAssetsAssetRenderer::renderListControl($idProject, 'project'),
			'assetList'		=> TodoyuAssetsAssetRenderer::renderProjectList($idProject)
		);

		return Todoyu::render($tmpl, $data);
	}


	/**
	 * Render asset upload iFrame content
	 *
	 * @param	String		$fileName
	 * @param	Integer		$idProject
	 * @return	String
	 */
	public static function renderUploadframeContent($fileName, $idProject) {
		$idProject	= intval($idProject);

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.assets.RecordEdit.uploadFinished(' . $idProject . ', \'project\');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render content of asset uploader iFrame when upload failed
	 *
	 * @param	String		$error
	 * @param	String		$fileName
	 * @param	Integer		$idProject
	 * @return	String
	 */
	public static function renderUploadframeContentFailed($error, $fileName, $idProject) {
		$idProject	= intval($idProject);

		$maxFileSize	= TodoyuString::formatSize(intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']));

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.assets.RecordEdit.uploadFailed(' . $idProject . ', \'project\', ' . $error . ', \'' . $fileName . '\', \'' . $maxFileSize . '\');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render options for files uploaded during project creation
	 *
	 * @param	Integer		$idProject
	 * @return	String		Rendered <option> elements
	 */
	public static function renderSessionFileOptions($idProject) {
		$uploader	= new TodoyuAssetsTempUploaderProject($idProject);
		$fileInfos	= $uploader->getFilesInfos();
		$options	= array();
		$fileInfos	= TodoyuArray::sortByLabel($fileInfos, 'time', true);

		foreach($fileInfos as $file) {
			$options[] = array(
				'value'	=> $file['key'],
				'label'	=> $file['name'] . ' (' . TodoyuTime::format($file['time'], 'timesec') . ', ' . TodoyuString::formatSize($file['size']) . ')'
			);
		}

		$tmpl	= 'core/view/form/FormElement_Select_Options.tmpl';
		$data	= array(
			'options'	=> $options,
			'value'		=> array()
		);

		return Todoyu::render($tmpl, $data);
	}
}

?>