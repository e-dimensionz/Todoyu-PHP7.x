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
 * Manager for fallbacks
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentFallbackManager {

	/**
	 * @var String		Default table for database requests
	 */
	const TABLE = 'ext_comment_fallback';



	/**
	 * @param	Integer		$idFallback
	 * @return	TodoyuCommentFallback
	 */
	public static function getFallback($idFallback) {
		return TodoyuRecordManager::getRecord('TodoyuCommentFallback', $idFallback);
	}



	/**
	 * Get records for sysmanager listing
	 *
	 * @return	Array
	 */
	public static function getRecords() {
		$fallbacks	= self::getAllFallbacks();
		$reform	= array(
			'id'	=> 'id',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($fallbacks, $reform);
	}



	/**
	 * Get all fallback records
	 *
	 * @return	Array
	 */
	public static function getAllFallbacks() {
		return TodoyuRecordManager::getAllRecords(self::TABLE);
	}



	/**
	 * Save a fallback record
	 *
	 * @param	Array	$data
	 * @return	Integer
	 */
	public static function saveFallback(array $data) {
		$idFallback	= intval($data['id']);
		$xmlPath	= 'ext/comment/config/form/admin/fallback.xml';

		if( $idFallback === 0 ) {
			$idFallback = self::addFallback();
		}

			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idFallback);

		self::updateFallback($idFallback, $data);

		return $idFallback;
	}



	/**
	 * Add a fallback record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addFallback(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a fallback record
	 *
	 * @param	Integer		$idFallback
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateFallback($idFallback, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idFallback, $data);
	}



	/**
	 * Delte a fallback record
	 *
	 * @param	Integer		$idFallback
	 */
	public static function deleteFallback($idFallback) {
		TodoyuRecordManager::deleteRecord(self::TABLE, $idFallback);
	}



	/**
	 * Check whether a fallback record is deleteable
	 *
	 * @param	Integer		$idFallback
	 * @return	Boolean
	 */
	public static function isDeletable($idFallback) {
		$idFallback			= intval($idFallback);
		$idGlobalFallback	= self::getGlobalFallbackID();

		if( $idFallback === $idGlobalFallback ) {
			return false;
		}

		$field	= 'id';
		$table	= 'ext_project_project';
		$where	= 'ext_comment_fallback = ' . $idFallback;

		return Todoyu::db()->hasResult($field, $table, $where, '', 1) === false;
	}



	/**
	 * Check whether global fallback is defined
	 *
	 * @return	Boolean
	 */
	public static function hasGlobalFallback() {
		$idFallback	= self::getGlobalFallbackID();

		return $idFallback !== 0 && TodoyuRecordManager::isRecord(self::TABLE, $idFallback);
	}



	/**
	 * Get ID of the global fallback
	 *
	 * @return	Integer
	 */
	public static function getGlobalFallbackID() {
		$extConf	= TodoyuSysmanagerExtConfManager::getExtConf('comment');

		return intval($extConf['globalFallback']);
	}



	/**
	 * Get the global fallback
	 *
	 * @return	TodoyuCommentFallback
	 */
	public static function getGlobalFallback() {
		$idFallback	= self::getGlobalFallbackID();

		return $idFallback !== 0 ? self::getFallback($idFallback) : false;
	}



	/**
	 * Add the fallback selection field into project form
	 *
	 * @param	TodoyuForm	$form
	 * @param	Integer		$idProject
	 * @param	Array		$params
	 * @return	TodoyuForm
	 */
	public static function hookAddFallbackField(TodoyuForm $form, $idProject, array $params) {
		$xmlPath	= 'ext/comment/config/form/project-fallback.xml';
		$baseForm	= TodoyuFormManager::getForm($xmlPath, $idProject, $params);
		$field		= $baseForm->getField('ext_comment_fallback');

		$form->getFieldset('presets')->addField('ext_comment_fallback', $field);

		return $form;
	}



	/**
	 * Add global fallback as default for new projects
	 *
	 * @param	Array		$data
	 * @param	Integer		$idProject
	 * @param	Array		$params
	 * @return	Array
	 */
	public static function hookSetProjectDefaultData(array $data, $idProject, array $params = array()) {
		$idProject	= intval($idProject);

		if( $idProject === 0 ) {
			if( !isset($data['ext_comment_fallback']) ) {
				$idFallback	= self::getGlobalFallbackID();
				if( $idFallback !== 0 ) {
					$data['ext_comment_fallback'] = $idFallback;
				}
			}
		}

		return $data;
	}

}

?>