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
 * Wizard step: activities
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepActivities extends TodoyuFirstStepsWizardStep {

	/**
	 * Initialize
	 */
	protected function init() {
		$this->table = 'ext_project_activity';
	}



	/**
	 * Save activities
	 *
	 * @param	Array	$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		$activities	= TodoyuArray::assure($data['activity']);
		$activities	= TodoyuArray::trim($activities, true);

		$this->saveActivities($activities);

		$this->data	= $activities;

		return true;
	}



	/**
	 * Render content
	 *
	 * @return	String
	 */
	public function getContent() {
		if( $this->data === null ) {
			$this->data = $this->getActivities();
		}

		return TodoyuFirstStepsRenderer::renderItemList($this->data, 'activity', 'activities');
	}



	/**
	 * Get available activities
	 *
	 * @return	Array
	 */
	private function getActivities() {
		$activities	= TodoyuProjectActivityManager::getAllActivities();

		return TodoyuArray::getColumn($activities, 'title');
	}



	/**
	 * Save activities
	 *
	 * @param	Array	$submittedActivities
	 */
	private function saveActivities(array $submittedActivities) {
		$dbActivities	= $this->getActivities();

		TodoyuFirstStepsManager::saveLabelRecords($submittedActivities, $dbActivities, $this->table);
	}

}

?>