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
 * Firststeps helper functions
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsManager {

	/**
	 * Save records which are identified only by a (title) field
	 *
	 * @param	Array		$newRecords		List of records which should be active after update
	 * @param	Array		$dbRecords		List of records which are currently active in the DB
	 * @param	String		$table
	 * @param	String		$field			Field name which contains the 'title'
	 * @param	Array		$extraFields	Extra field values to add to the record
	 */
	public static function saveLabelRecords(array $newRecords, array $dbRecords, $table, $field = 'title', array $extraFields = array()) {
		$labelsToDelete	= TodoyuArray::diffLeft($dbRecords, $newRecords);
		$labelToAdd		= TodoyuArray::diffLeft($newRecords, $dbRecords);

			// Delete removed records
		if( !empty($labelsToDelete) ) {
			$titleList	= TodoyuArray::implodeQuoted($labelsToDelete);
			$where		= $field . ' IN(' . $titleList . ')';

			Todoyu::db()->setDeleted($table, $where);
		}

			// Add missing records
		foreach($labelToAdd as $record) {
			$data	= array(
				$field	=> $record
			);
			$data	= array_merge($data, $extraFields);
			TodoyuRecordManager::addRecord($table, $data);
		}
	}



	/**
	 * Check whether the first steps wizard is enabled
	 *
	 * @return	Boolean
	 */
	public static function isNotDisabled() {
		$extConf	= TodoyuSysmanagerExtConfManager::getExtConf('firststeps');
		$disabled	= intval($extConf['disableWizard']) === 1;

		return !$disabled;
	}



	/**
	 * Add JavaScript onload-function for opening first steps wizard
	 */
	public static function addJsToOpenWizardOnLoad() {
		TodoyuPage::addJsInit('Todoyu.Ext.firststeps.openWizard()', 100);
	}



	/**
	 * Register first steps wizard in TodoyuWizardManager
	 */
	public static function addFirstStepsWizard() {
		TodoyuWizardManager::addWizard('firststeps', 'TodoyuFirstStepsWizard');

		require_once( PATH_EXT_FIRSTSTEPS . '/config/wizard-steps.php' );

		foreach(Todoyu::$CONFIG['EXT']['firststeps']['wizardsteps'] as $wizardStep) {
			TodoyuWizardManager::addStep('firststeps', $wizardStep);
		}
	}



	/**
	 * Set first steps wizard disabled
	 */
	public static function disableWizard() {
		$update	=	array(
			'disableWizard'	=> 1
		);

		TodoyuSysmanagerExtConfManager::updateExtConf('firststeps', $update);
	}

}

?>