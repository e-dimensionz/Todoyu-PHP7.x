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
 * Wizard step with special fields for Firststeps wizard
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 * @abstract
 */
abstract class TodoyuFirstStepsWizardStep extends TodoyuWizardStep {

	/**
	 * Used database table
	 *
	 * @var	String
	 */
	protected $table;

	/**
	 * Path to used form xml
	 *
	 * @var	String
	 */
	protected $formXml;

	/**
	 * Form instance
	 *
	 * @var	TodoyuForm	$form
	 */
	protected $form;



	/**
	 * Get form instance
	 * Set data if given
	 *
	 * @param	Array		$data
	 * @return	TodoyuForm
	 */
	protected function getForm(array $data = null) {
		if( is_null($this->form) ) {
			$this->form	= TodoyuFormManager::getForm($this->formXml);
		}

		if( is_array($data) ) {
			$this->form->setFormData($data);
		}

		return $this->form;
	}

}

?>