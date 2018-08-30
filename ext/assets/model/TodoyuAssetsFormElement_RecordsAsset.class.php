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
 * Assets implementation for record selector
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuAssetsFormElement_RecordsAsset extends TodoyuFormElement_Records {

	/**
	 * Initialize
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('asset', $name, $fieldset, $config);
	}



	/**
	 * Get task ID based on form record ID
	 * The form record has this format: taskID-CommentID
	 *
	 * @return	Integer
	 */
	protected function getTaskID() {
		list($idTask, $idComment) = explode('-', $this->getForm()->getRecordID());

		return intval($idTask);
	}



	/**
	 * Get project ID
	 *
	 * @return	Integer
	 */
	protected function getProjectID() {
		$idTask	= $this->getTaskID();

		return TodoyuProjectTaskManager::getProjectID($idTask);
	}



	/**
	 * @return	Integer
	 */
	protected function getCommentID() {
		$string = $this->getForm()->getRecordID();
		list($idTask, $idComment) = explode('-', $string);

		return intval($idComment);
	}



	/**
	 * Get record data
	 *
	 * @return	Array[]
	 */
	protected function getRecords() {
		$assetIDs	= $this->getValue();
		$records	= array();

		foreach($assetIDs as $idAsset) {
			$records[] = array(
				'id'	=> $idAsset,
				'label'	=> TodoyuAssetsAssetManager::getLabel($idAsset)
			);
		}

		return $records;
	}

}

?>