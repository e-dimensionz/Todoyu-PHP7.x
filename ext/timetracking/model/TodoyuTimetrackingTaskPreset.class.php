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
 * Task preset from timetracking
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 */
class TodoyuTimetrackingTaskPreset extends TodoyuProjectTaskPreset {

	/**
	 * Get start timetracking
	 *
	 * @return	Integer
	 */
	public function getStartTimetracking() {
		return $this->getInt('ext_timetracking_start_tracking');
	}



	/**
	 * Get label for start timetracking
	 *
	 * @return	String
	 */
	public function getStartTimetrackingLabel() {
		$label	= $this->getStartTimetracking() ? 'core.global.yes' : 'core.global.no';

		return Todoyu::Label($label);
	}



	/**
	 * Get workload done
	 *
	 * @return	Integer
	 */
	public function getWorkloadDone() {
		return $this->getInt('ext_timetracking_workload_done');
	}



	/**
	 * Check whether workload done is set in preset
	 *
	 * @return	Boolean
	 */
	public function hasWorkloadDone() {
		return $this->getWorkloadDone() !== 0;
	}



	/**
	 * Get timetracking preset data
	 *
	 * @return	Array
	 */
	public function getTimetrackingPresetData() {
		$data	= array();

		if( $this->hasWorkloadDone() ) {
			$data['workload_done'] = $this->getWorkloadDone();
		}
		$data['start_tracking'] = $this->getStartTimetracking();

		return $data;
	}



	/**
	 * Get all preset data
	 *
	 * @return	Array
	 */
	public function getPresetData() {
		$data			= parent::getPresetData();
		$timetracking	= $this->getTimetrackingPresetData();

		return array_merge($data, $timetracking);
	}

}

?>