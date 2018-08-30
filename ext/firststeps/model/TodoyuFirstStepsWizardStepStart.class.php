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
 * Wizard step: start
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepStart extends TodoyuFirstStepsWizardStep {

	/**
	 * Nothing so save, always go to the next step
	 *
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		return true;
	}



	/**
	 * Render start screen
	 *
	 * @return	String
	 */
	public function getContent() {
		$tmpl	= 'ext/firststeps/view/wizard-step-start.tmpl';
		$data	= array();

		return Todoyu::render($tmpl, $data);
	}

}

?>