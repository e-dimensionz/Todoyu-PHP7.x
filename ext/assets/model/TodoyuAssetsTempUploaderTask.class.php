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
 * Task asset uploader
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTempUploaderTask extends TodoyuAssetsTempUploader {

	/**
	 * Initialize
	 *
	 * @param	Integer		$idProject
	 */
	public function __construct($idProject) {
		parent::__construct('task', $idProject);
	}



	/**
	 * Hook. Removed temp files
	 *
	 * @param	Integer		$idProject
	 */
	public static function hookClearNewTaskFiles($idProject) {
		$idTask		= 0;
		$uploader	= new self($idTask);

		$uploader->clear();

	}


	/**
	 * Hook: Task create
	 * Clear temp uploads for new tasks (id=0)
	 *
	 * @param	Integer		$idParentTask
	 * @param	Integer		$idProject
	 * @param	Integer		$type
	 */
	public static function hookTaskCreate($idParentTask, $idProject, $type) {
		self::clearTask(0);
	}



	/**
	 * Hook: Task edit
	 * Clear temp uploads for this task
	 *
	 * @param	Integer		$idTask
	 */
	public static function hookTaskEdit($idTask) {
		self::clearTask($idTask);
	}



	/**
	 * Clear task assets (statis helper)
	 *
	 * @param	Integer		$idTask
	 */
	public static function clearTask($idTask) {
		$uploader	= new self($idTask);
		$uploader->clear();
	}

}

?>