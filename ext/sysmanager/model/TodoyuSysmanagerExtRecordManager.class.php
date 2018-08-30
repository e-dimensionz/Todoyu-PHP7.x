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
 * Extension record manager
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerExtRecordManager {

	/**
	 * Get configuration for the tabs
	 *
	 * @param	String		$ext
	 * @param	String		$type
	 * @param	Integer		$idRecord
	 * @return	Array
	 */
	public static function getTabsConfig($ext = '', $type = '', $idRecord = 0) {
		$ext		= trim($ext);
		$type		= trim($type);
		$idRecord	= intval($idRecord);
		$tabs		= array();

			// List
		$tabs[] = array(
			'id'		=> 'all',
			'label'		=> 'sysmanager.ext.records.tab.all'
		);

			// Extension
		if( $ext !== '' ) {
			$extLabel	= Todoyu::Label($ext . '.ext.ext.title');

			$tabs[] = array(
				'id'	=> $ext,
				'label'	=> $extLabel,
				'class'	=> 'extTypes'
			);
		}

			// Type
		if( $type !== '' ) {
			$typeConfig	= TodoyuSysmanagerExtManager::getRecordConfig($ext, $type);
			$tabs[] = array(
				'id'	=> $ext . '-' . $type,
				'label'	=> Todoyu::Label($typeConfig['label']),
				'class'	=> 'typeRecords'
			);
		}

			// Record
		if( $idRecord !== 0 ) {
			if( $idRecord === -1 ) {
				$recordLabel	= Todoyu::Label('core.global.createNew');
			} else {
				$recordLabel	= TodoyuSysmanagerExtManager::getRecordObjectLabel($ext, $type, $idRecord);
			}

			$tabs[] = array(
				'id'	=> $ext . '-' . $type . '-record',
				'label'	=> $recordLabel,
				'class'	=> 'openRecord'
			);
		}

		return $tabs;
	}




	/**
	 * Get infos about all record types (of extensions the current user is allowed to use)
	 *
	 * @return	Array
	 */
	public static function getAllRecordsList() {
		$info				= array();
		$allRecordsConfig	= TodoyuSysmanagerExtManager::getAllRecordsConfig();

		foreach($allRecordsConfig as $extKey => $records) {
			if( Todoyu::allowed($extKey, 'general:use') ) {
				$info[$extKey]['title']		= Todoyu::Label($extKey . '.ext.ext.title');
				$info[$extKey]['records']	= array();

				foreach($records as $type => $config) {
					$info[$extKey]['records'][$type]['type']		= $type;
					$info[$extKey]['records'][$type]['config']		= $config;

					if( isset($config['table']) ) {
						$info[$extKey]['records'][$type]['count']	= TodoyuSysmanagerExtRecordManager::getRecordCount($config['table']);
					} else {
						$info[$extKey]['records'][$type]['count']	= '???';
					}
				}
			}
		}

		return $info;
	}



	/**
	 * Get record form object with injected save buttons
	 *
	 * @param	String		$ext
	 * @param	String		$type
	 * @param	Integer		$idRecord
	 * @return	TodoyuForm
	 */
	public static function getRecordForm($ext, $type, $idRecord = 0) {
		$idRecord	= intval($idRecord);
		$config		= TodoyuSysmanagerExtManager::getRecordConfig($ext, $type);
		
			// Record form
		$form		= TodoyuFormManager::getForm($config['form'], $idRecord);
		$form->setAction('index.php?ext=sysmanager&amp;controller=records');
		$form->setName('record');
		$form->setFormData(array(
			'id' => $idRecord
		));

			// Save buttons form
		$buttonsXmlPath	= 'ext/sysmanager/config/form/record-save.xml';
		$buttonsForm	= TodoyuFormManager::getForm($buttonsXmlPath, $idRecord);
		$saveButtons	= $buttonsForm->getFieldset('buttons');

			// Add save buttons
		$form->addFieldset('buttons', $saveButtons);

			// Load record data
		$data	= $form->getFormData();

		if( $idRecord !== 0 ) {
			if( isset($config['object']) ) {
				$className	= $config['object'];
				$record		= new $className($idRecord);
			} elseif( isset($config['table']) ) {
				TodoyuLogger::logError('Record in table ' . $config['table'] . ' has no object class!');
//				$record = new TodoyuBaseObject($idRecord, $config['table']);
			}

				// If record object created, get data
			if( is_object($record) ) {
				$data	= $record->getTemplateData(true);
			}
		}

		if( $config['onRecordDisplayJsCallback'] ) {
			$form->addOnDisplayJsCallback($config['onRecordDisplayJsCallback']);
		}

		$data	= TodoyuFormHook::callLoadData($config['form'], $data, $idRecord);

		$data['record-extkey']	= $ext;
		$data['record-type']	= $type;

		$form->setFormData($data);

		return $form;
	}



	/**
	 * Save extension record
	 *
	 * @param	String		$ext
	 * @param	String		$type
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function saveRecord($ext, $type, array $data) {
		$config		= TodoyuSysmanagerExtManager::getRecordConfig($ext, $type);
		$idRecord	= intval($data['id']);

		if( TodoyuFunction::isFunctionReference($config['save']) ) {
			$idRecord = TodoyuFunction::callUserFunction($config['save'], $data);
		} else {
			TodoyuLogger::logError('Save function for record ' . $ext . '/' . $type . ' is missing');
		}

		return $idRecord;
	}



	/**
	 * Delete record
	 *
	 * @param	String		$ext
	 * @param	String		$type
	 * @param	Integer		$idRecord
	 */
	public static function deleteRecord($ext, $type, $idRecord) {
		$config		= TodoyuSysmanagerExtManager::getRecordConfig($ext, $type);
		$idRecord	= intval($idRecord);

		if( TodoyuFunction::isFunctionReference($config['delete']) ) {
			TodoyuFunction::callUserFunction($config['delete'], $idRecord);
		} else {
			TodoyuLogger::logError('Delete function for record ' . $ext . '/' . $type . ' is missing');
		}
	}



	/**
	 * Get row count of a table
	 *
	 * @param	String		$table
	 * @return	Integer
	 */
	public static function getRecordCount($table) {
		$fields	= 'id';
		$where	= 'deleted = 0';

		$result	= Todoyu::db()->doSelect($fields, $table, $where);

		return Todoyu::db()->getNumRows($result);
	}

}

?>