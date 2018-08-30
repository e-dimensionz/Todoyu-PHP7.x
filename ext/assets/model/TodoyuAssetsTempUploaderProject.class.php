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
 * Project asset uploader
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTempUploaderProject extends TodoyuAssetsTempUploader {

	/**
	 * Initialize
	 *
	 * @param	Integer		$idProject
	 */
	public function __construct($idProject) {
		parent::__construct('project', $idProject);
	}



	/**
	 * Hook. Removed temp files
	 *
	 * @param	Integer		$idProject
	 */
	public static function hookClearNewProjectFiles($idProject) {
		$idProject		= 0;
		$uploader	= new self($idProject);

		$uploader->clear();

	}


	/**
	 * Hook: Project create
	 * Clear temp uploads for new project (id=0)
	 *
	 * @param	Integer		$idProject
	 * @param	Integer		$type
	 */
	public static function hookProjectCreate($idParentTask, $idProject, $type) {
		self::clearProject(0);
	}



	/**
	 * Hook: Project edit
	 * Clear temp uploads for this project
	 *
	 * @param	Integer		$idProject
	 */
	public static function hookProjectEdit($idProject) {
		self::clearProject($idProject);
	}



	/**
	 * Clear project assets (statis helper)
	 *
	 * @param	Integer		$idProject
	 */
	public static function clearProject($idProject) {
		$uploader	= new self($idProject);
		$uploader->clear();
	}
}

?>