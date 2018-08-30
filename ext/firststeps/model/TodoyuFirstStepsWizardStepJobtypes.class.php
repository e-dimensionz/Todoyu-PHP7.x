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
 * Wizard step: job types
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepJobtypes extends TodoyuFirstStepsWizardStep {

	/**
	 * Initialize
	 */
	protected function init() {
		$this->table = 'ext_contact_jobtype';
	}



	/**
	 * Save job types
	 *
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		$jobTypes	= TodoyuArray::assure($data['jobtype']);
		$jobTypes	= TodoyuArray::trim($jobTypes, true);

		$this->saveJobTypes($jobTypes);

		$this->data	= $jobTypes;

		return true;
	}



	/**
	 * Render step content
	 *
	 * @return	String
	 */
	public function getContent() {
		if( $this->data === null ) {
			$this->data = $this->getJobtypes();
		}

		return TodoyuFirstStepsRenderer::renderItemList($this->data, 'jobtype', 'jobtypes');
	}



	/**
	 * Get available job types from db
	 *
	 * @return	Array
	 */
	private function getJobtypes() {
		$jobTypes	= TodoyuContactJobTypeManager::getAllJobTypes();
		$labels		= TodoyuArray::getColumn($jobTypes, 'title');

		return $labels;
	}



	/**
	 * Save job types
	 *
	 * @param	Array		$submittedJobTypes
	 */
	private function saveJobTypes(array $submittedJobTypes) {
		$dbJobTypes	= $this->getJobtypes();

		TodoyuFirstStepsManager::saveLabelRecords($submittedJobTypes, $dbJobTypes, $this->table);
	}
}

?>